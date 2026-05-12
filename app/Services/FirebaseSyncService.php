<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service to sync data with Firebase Firestore using the REST API.
 * This version uses manual JWT signing (RS256) to authenticate, 
 * avoiding the need for the gRPC extension or heavy Google SDKs.
 */
class FirebaseSyncService
{
    protected $accessToken;
    protected $readCount = 0;
    protected $writeCount = 0;
    protected $readBudget = 50000; // Daily limit or per-session limit
    protected $writeBudget = 20000;

    // In-memory token store as fallback when cache has permission issues
    protected static $staticToken = null;
    protected static $tokenExpiresAt = 0;


    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID', 'syscarnet');
        $jsonPath = base_path(env('FIREBASE_CREDENTIALS', 'firebase-auth.json'));

        if (!file_exists($jsonPath)) {
            Log::warning("Firebase Sync: Credentials file NOT FOUND at {$jsonPath}.");
            return;
        }

        $this->credentials = json_decode(file_get_contents($jsonPath), true);
        $this->client = new Client(['timeout' => 15.0]);
    }

    /**
     * Gets an OAuth2 Access Token from Google using PHP-native JWT signing (RS256).
     * Does NOT rely on the system `openssl` binary (Windows compatible).
     */
    public function getAccessToken(): ?string
    {
        // 1. Check in-memory static token first (check expiration)
        if (self::$staticToken && self::$tokenExpiresAt > time()) {
            return self::$staticToken;
        }

        // 2. Try the cache (using v2 key to avoid stale locked files)
        try {
            if ($token = Cache::get('firebase_access_token_v2')) {
                self::$staticToken = $token;
                return $token;
            }
        } catch (\Throwable $e) {
            Log::warning("Firebase: Could not read from cache, proceeding without it: " . $e->getMessage());
        }

        if (!isset($this->credentials) || !$this->credentials || !isset($this->credentials['private_key'])) {
            Log::error("Firebase Auth Error: Missing credentials or private_key.");
            return null;
        }

        try {
            // MASTER CLOCK SYNC: Get current time from Google directly to avoid clock drift
            $timeResponse = $this->client->head('https://www.google.com');
            $googleDate = $timeResponse->getHeaderLine('Date');
            $googleTime = strtotime($googleDate) ?: time();
            $now = $googleTime - 10; // 10s buffer against clock skew rejection

            // SANITIZE KEY: Remove \r characters that corrupt signing on Windows
            $cleanKey = str_replace("\r", "", trim($this->credentials['private_key']));
            
            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            
            $payload = $this->base64UrlEncode(json_encode([
                'iss' => $this->credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/datastore https://www.googleapis.com/auth/cloud-platform',
                'aud' => 'https://www.googleapis.com/oauth2/v4/token',
                'iat' => $now,
                'exp' => $now + 3600
            ]));

            $signatureInput = $header . "." . $payload;

            // Use PHP's native openssl extension ONLY — no exec(), no system binary needed
            $key = openssl_pkey_get_private($cleanKey);
            if (!$key) {
                Log::error("Firebase Auth Error: Could not parse private key. OpenSSL error: " . openssl_error_string());
                return null;
            }

            $signature = '';
            if (!openssl_sign($signatureInput, $signature, $key, OPENSSL_ALGO_SHA256)) {
                Log::error("Firebase OpenSSL Sign Error: " . openssl_error_string());
                return null;
            }

            $jwt = $signatureInput . "." . $this->base64UrlEncode($signature);

            $response = $this->client->post('https://www.googleapis.com/oauth2/v4/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ],
                'http_errors' => false
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if ($response->getStatusCode() !== 200) {
                Log::error("Firebase Auth Error (HTTP {$response->getStatusCode()}): " . $body);
                return null;
            }

            $token = $data['access_token'];

            // Store in static memory with expiration; save to cache only if possible
            self::$staticToken = $token;
            self::$tokenExpiresAt = time() + 3300; // 55 minutes
            try {
                Cache::put('firebase_access_token_v2', $token, 3300);
            } catch (\Throwable $e) {
                Log::warning("Firebase: Token cached in memory only (cache write failed): " . $e->getMessage());
            }

            return $token;

        } catch (\Throwable $e) {
            Log::error("Firebase Auth Exception: " . $e->getMessage());
            return null;
        }
    }

    private function base64UrlEncode(string $data): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Retrieve all documents from a Firestore collection via REST (Handles Pagination)
     */
    public function getCollection(string $collectionName): array
    {
        $token = $this->getAccessToken();
        if (!$token) return [];

        $results = [];
        $pageToken = null;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            
            do {
                // Budget Check
                if ($this->readCount >= $this->readBudget) {
                    $this->sendToFeed("🛑 PRESUPUESTO DE LECTURA AGOTADO ({$this->readCount}). Abortando proceso.", "rose");
                    throw new \Exception("Read budget exceeded: {$this->readBudget}");
                }

                $query = "?pageSize=500" . ($pageToken ? "&pageToken={$pageToken}" : "");
                
                $response = $this->client->get("{$baseUrl}/{$collectionName}{$query}", [
                    'headers' => [
                        'Authorization' => "Bearer {$token}",
                        'Accept' => 'application/json'
                    ],
                    'http_errors' => true
                ]);

                $data = json_decode($response->getBody()->getContents(), true);
                
                $docs = $data['documents'] ?? [];
                $batchSize = count($docs);
                $this->readCount += $batchSize;

                foreach ($docs as $doc) {
                    $results[] = $this->mapFirestoreRestDoc($doc);
                }

                $pageToken = $data['nextPageToken'] ?? null;

            } while ($pageToken);

            return $results;

        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                $this->sendToFeed("⚠️ ERROR 429: Google Firebase ha bloqueado el acceso por exceso de cuota.", "rose");
                throw $e; 
            }
            Log::error("Firebase Sync Error (GET $collectionName): " . $e->getMessage());
            return $results;
        }
    }

    /**
     * Retrieve a single document from a Firestore collection via REST
     */
    /**
     * Retrieve documents from a Firestore collection changed since a given date with pagination
     */
    public function getCollectionIncrementalPaged(string $collection, string $sinceDate, int $pageSize = 500, string $startAfterId = null): array
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => $collection]],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'updated_at'],
                        'op' => 'GREATER_THAN',
                        'value' => ['stringValue' => $sinceDate]
                    ]
                ],
                'orderBy' => [
                    ['field' => ['fieldPath' => 'updated_at'], 'direction' => 'ASCENDING'],
                    ['field' => ['fieldPath' => '__name__'], 'direction' => 'ASCENDING'] // Tie-breaker for cursor
                ],
                'limit' => $pageSize
            ]
        ];

        if ($startAfterId) {
             // Cursors are complex in REST. For simple incremental, we rely on the timestamp mostly.
             // But for real pagination within a large set of same-timestamp docs, we'd need more.
             // Let's stick to timestamp + limit for now as it's the most common case.
        }

        return $this->runQuery($collection, $query);
    }

    /**
     * Paged collection retrieval using runQuery to allow filtering/sorting
     */
    public function getCollectionPaged(string $collection, int $pageSize = 500, string $lastId = null): array
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => $collection]],
                'orderBy' => [
                    ['field' => ['fieldPath' => '__name__'], 'direction' => 'ASCENDING']
                ],
                'limit' => $pageSize
            ]
        ];

        if ($lastId) {
            $query['structuredQuery']['startAt'] = [
                'values' => [['referenceValue' => "projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$lastId}"]],
                'before' => false
            ];
        }

        return $this->runQuery($collection, $query);
    }

    /**
     * Gets the most recent updated_at timestamp from a collection
     */
    public function getLatestUpdateDate(string $collection): ?string
    {
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => $collection]],
                'orderBy' => [
                    ['field' => ['fieldPath' => 'updated_at'], 'direction' => 'DESCENDING']
                ],
                'limit' => 1
            ]
        ];
        
        $results = $this->runQuery($collection, $query);
        if (!empty($results) && isset($results[0]['updated_at'])) {
            return $results[0]['updated_at'];
        }
        
        return null; // Return null if no docs or no updated_at field found
    }

    /**
     * Run a structured query against Firestore
     */
    public function runQuery(string $collection, array $structuredQuery): array
    {
        $token = $this->getAccessToken();
        if (!$token) return [];

        // Budget Check
        if ($this->readCount >= $this->readBudget) {
            $this->sendToFeed("🛑 PRESUPUESTO DE LECTURA AGOTADO ({$this->readCount}). Abortando query.", "rose");
            throw new \Exception("Read budget exceeded: {$this->readBudget}");
        }

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
            
            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => $structuredQuery
            ]);

            $rawData = json_decode($response->getBody()->getContents(), true);
            
            // runQuery counts as many reads as documents returned (min 1)
            $docCount = 0;
            $results = [];

            foreach ($rawData as $item) {
                if (isset($item['document'])) {
                    $results[] = $this->mapFirestoreRestDoc($item['document']);
                    $docCount++;
                }
            }

            $this->readCount += max(1, $docCount);

            return $results;

        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                 $this->sendToFeed("⚠️ ERROR 429: Cuota excedida al intentar consultar datos.", "rose");
                 throw $e;
            }
            Log::error("Firebase Query Error ($collection): " . $e->getMessage());
            return [];
        }
    }

    public function getDocument(string $collection, string $documentId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $response = $this->client->get("{$baseUrl}/{$collection}/{$documentId}", [
                'headers' => ['Authorization' => "Bearer {$token}"]
            ]);

            $doc = json_decode($response->getBody()->getContents(), true);
            return $this->mapFirestoreRestDoc($doc);

        } catch (\Throwable $e) {
            // Silently fail if document doesn't exist
            return null;
        }
    }
    /**
     * Checks if a document exists and returns its basic data if found
     */
    public function checkDocumentExistence(string $collection, string $documentId): array
    {
        $doc = $this->getDocument($collection, $documentId);
        
        if ($doc) {
            return [
                'exists' => true,
                'nombre' => $doc['nombre_completo'] ?? ($doc['nombre'] ?? 'Sin nombre identificado'),
                'data' => $doc
            ];
        }

        return ['exists' => false];
    }

    /**
     * Compatibility alias for Push (used by Observers)
     */
    public function syncData(array $data, string $collection, string $documentId)
    {
        return $this->push($collection, $documentId, $data);
    }

    /**
     * Triggers a sync for an Eloquent model (used by legacy components)
     */
    public function syncModel($model, string $collectionName, string $documentId): bool
    {
        return $this->push($collectionName, $documentId, $model->toArray());
    }

    /**
     * Pushes multiple documents to Firestore using a single Atomic Batch (REST: commit)
     * Handles up to 500 documents per call.
     * Format: [['id' => 'doc1', 'data' => [...]], ['id' => 'doc2', 'data' => [...]]]
     */
    public function pushBatch(string $collection, array $documents): bool
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:commit";
            $writes = [];

            foreach ($documents as $doc) {
                $docId = $doc['id'];
                $data = $doc['data'];
                $writes[] = [
                    'update' => [
                        'name' => "projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$docId}",
                        'fields' => $this->formatForFirestoreRest($data)
                    ]
                ];
            }

            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => ['writes' => $writes]
            ]);

            if ($response->getStatusCode() === 200) {
                $this->writeCount += count($documents);
                return true;
            }
            return false;
        } catch (\Throwable $e) {
            Log::error("Firebase pushBatch Error ($collection): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Compatibility alias
     */
    public function batchPush(string $collection, array $documents): bool
    {
        // Convert old format to new format
        $newDocs = [];
        foreach($documents as $id => $data) {
            $newDocs[] = ['id' => $id, 'data' => $data];
        }
        return $this->pushBatch($collection, $newDocs);
    }

    /**
     * Pushes a local data array to a Firestore collection via REST (PATCH)
     */
    public function push(string $collection, string $documentId, array $data)
    {
        return retry(3, function() use ($collection, $documentId, $data) {
            $token = $this->getAccessToken();
            if (!$token) return false;

            try {
                $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
                $formattedData = ['fields' => $this->formatForFirestoreRest($data)];

                $response = $this->client->patch("{$baseUrl}/{$collection}/{$documentId}", [
                    'headers' => ['Authorization' => "Bearer {$token}"],
                    'json' => $formattedData
                ]);

                if ($response->getStatusCode() === 200 || $response->getStatusCode() === 204) {
                    $this->writeCount++;
                    return true;
                }
                return false;
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                    $this->sendToFeed("⚠️ CRÍTICO: Límite de cuota Google Firebase alcanzado (Escritura).", "rose");
                    sleep(2); // Wait a bit
                }
                Log::error("Firebase Push Error ({$collection}/{$documentId}): " . $e->getMessage());
                throw $e; // Trigger retry
            }
        }, 1000);
    }

    /**
     * Deletes a document from Firestore via REST (DELETE)
     */
    public function deleteDocument(string $collection, string $documentId)
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $this->client->delete("{$baseUrl}/{$collection}/{$documentId}", [
                'headers' => ['Authorization' => "Bearer {$token}"]
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::error("Firebase Delete Error ({$collection}/{$documentId}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Maps a Firestore REST API document (with typed fields) to a flat array
     */
    protected function mapFirestoreRestDoc(array $doc): array
    {
        $fields = $doc['fields'] ?? [];
        $mapped = [];

        $nameParts = explode('/', $doc['name'] ?? '');
        $mapped['firebase_id'] = end($nameParts);

        foreach ($fields as $key => $values) {
            // Firestore REST returns: "stringValue": "...", "integerValue": "...", etc.
            $val = reset($values);
            $type = key($values);

            if ($type === 'integerValue') $val = (int)$val;
            if ($type === 'doubleValue') $val = (float)$val;
            if ($type === 'booleanValue') $val = (bool)$val;
            
            $mapped[$key] = $val;
        }

        return $mapped;
    }

    /**
     * Formats a flat array into Firestore REST's typed field structure
     */
    protected function formatForFirestoreRest(array $data): array
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_int($value)) {
                $fields[$key] = ['integerValue' => (string)$value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_array($value)) {
                $fields[$key] = ['stringValue' => json_encode($value)];
            } else {
                $fields[$key] = (string)$value === '' ? ['nullValue' => null] : ['stringValue' => (string)$value];
            }
        }
        return $fields;
    }
    /**
     * Gets the total count of documents in a collection using an Aggregation Query
     */
    public function getCollectionCount(string $collection): int
    {
        $token = $this->getAccessToken();
        if (!$token) return 0;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runAggregationQuery";
            
            $query = [
                'structuredAggregationQuery' => [
                    'structuredQuery' => [
                        'from' => [['collectionId' => $collection]]
                    ],
                    'aggregations' => [
                        [
                            'alias' => 'total_count',
                            'count' => (object)[]
                        ]
                    ]
                ]
            ];

            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => $query
            ]);

            $rawData = json_decode($response->getBody()->getContents(), true);
            // runAggregationQuery returns an array of results
            if (isset($rawData[0]['result']['aggregateFields']['total_count']['integerValue'])) {
                return (int)$rawData[0]['result']['aggregateFields']['total_count']['integerValue'];
            }

            return 0;

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Firebase Count Error ($collection): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Sends a message to the real-time sync terminal feed
     */
    public function sendToFeed($message, $color = 'slate')
    {
        $feed = \Illuminate\Support\Facades\Cache::get('firebase_sync_feed', []);
        array_unshift($feed, [
            'time' => now()->format('H:i:s'),
            'msg' => $message,
            'color' => $color
        ]);
        \Illuminate\Support\Facades\Cache::put('firebase_sync_feed', array_slice($feed, 0, 50), 600);
    }

    public function setReadBudget(int $limit) { $this->readBudget = $limit; }
    public function setWriteBudget(int $limit) { $this->writeBudget = $limit; }
    public function getReadCount(): int { return $this->readCount; }
    public function getWriteCount(): int { return $this->writeCount; }
    public function resetCounters() { $this->readCount = 0; $this->writeCount = 0; }
    /**
     * Check if the connection to Firebase is active
     */
    public function checkConnection(): bool
    {
        return $this->getAccessToken() !== null;
    }
}
