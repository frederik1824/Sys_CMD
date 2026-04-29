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
    protected $client;
    protected $projectId;
    protected $credentials;
    protected $accessToken;

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
     * Gets an OAuth2 Access Token from Google using the Service Account JSON
     */
    protected function getAccessToken(): ?string
    {
        // Check cache first to avoid repetitive auth calls
        if ($token = Cache::get('firebase_access_token')) {
            return $token;
        }

        try {
            $now = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iss' => $this->credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/datastore',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600
            ]));

            // Helper for base64UrlEncode
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], trim($header, '='));
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], trim($payload, '='));

            $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
            $signature = '';
            
            openssl_sign($signatureInput, $signature, $this->credentials['private_key'], 'sha256');
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], trim(base64_encode($signature), '='));

            $jwt = $signatureInput . "." . $base64UrlSignature;

            $response = $this->client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $token = $data['access_token'];

            // Cache for 55 minutes
            Cache::put('firebase_access_token', $token, 3300);
            return $token;

        } catch (\Throwable $e) {
            Log::error("Firebase Auth Error: " . $e->getMessage());
            return null;
        }
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
                $query = $pageToken ? "?pageToken={$pageToken}" : "";
                $response = $this->client->get("{$baseUrl}/{$collectionName}{$query}", [
                    'headers' => ['Authorization' => "Bearer {$token}"]
                ]);

                $data = json_decode($response->getBody()->getContents(), true);
                
                foreach ($data['documents'] ?? [] as $doc) {
                    $results[] = $this->mapFirestoreRestDoc($doc);
                }

                $pageToken = $data['nextPageToken'] ?? null;

            } while ($pageToken);

            return $results;

        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), '429') || str_contains($e->getMessage(), 'quota')) {
                $this->sendToFeed("⚠️ ERROR 429: Google Firebase ha bloqueado el acceso por exceso de cuota.", "rose");
                throw $e; // Throw to stop command
            }
            Log::error("Firebase Sync Error (GET $collectionName): " . $e->getMessage());
            return $results;
        }
    }

    /**
     * Retrieve a single document from a Firestore collection via REST
     */
    /**
     * Retrieve documents from a Firestore collection changed since a given date
     */
    public function getCollectionIncremental(string $collection, string $sinceDate): array
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
                ]
            ]
        ];

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

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
            
            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => $structuredQuery
            ]);

            $rawData = json_decode($response->getBody()->getContents(), true);
            $results = [];

            foreach ($rawData as $item) {
                if (isset($item['document'])) {
                    $results[] = $this->mapFirestoreRestDoc($item['document']);
                }
            }

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
     */
    public function batchPush(string $collection, array $documents): bool
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:commit";
            $writes = [];

            foreach ($documents as $docId => $data) {
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

            return $response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            Log::error("Firebase Batch Push Error ($collection): " . $e->getMessage());
            return false;
        }
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

                return $response->getStatusCode() === 200 || $response->getStatusCode() === 204;
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
}
