<?php

namespace App\Traits;

use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Log;
use App\Models\CloudSyncAudit;

/**
 * Trait to handle bidirectional sync with Firebase Firestore
 */
trait FirebaseSyncable
{
    /**
     * Flag to globally disable sync during mass operations
     */
    public static $isSyncingDisabled = false;

    /**
     * Boot the trait to handle automatic syncing on save
     */
    public static function bootFirebaseSyncable()
    {
        static::saving(function ($model) {
            if (static::$isSyncingDisabled) return;

            // Detect real changes before marking as pending
            if ($model->isDirty()) {
                if (\Schema::hasColumn($model->getTable(), 'sync_status')) {
                    $model->sync_status = 'pending';
                }
            }
        });

        static::saved(function ($model) {
            if (static::$isSyncingDisabled) return;

            // Sincronización en segundo plano (Job) si hay colas configuradas
            if (config('firebase.immediate_push', true)) {
                if ($model->getFirebaseDocumentId()) {
                    if (config('queue.default') !== 'sync') {
                        \App\Jobs\FirebasePushJob::dispatch($model);
                    } else {
                        $model->pushToFirebase();
                    }
                }
            }
        });
    }

    /**
     * Pull data from Firebase ONLY if it's stale (older than $hours).
     * This saves a massive amount of read quota.
     */
    public function pullIfStale(int $hours = 2): bool
    {
        if ($this->firebase_synced_at && $this->firebase_synced_at->diffInHours(now()) < $hours) {
            return false; // Already fresh
        }

        return $this->pullFromFirebase();
    }

    /**
     * Pull data from Firebase and update local model attributes.
     * Use this in show/edit methods to ensure data freshness.
     */
    public function pullFromFirebase(): bool
    {
        try {
            // Protection: If we have pending local changes, DO NOT pull (to avoid overwriting our own unsynced work)
            if (\Schema::hasColumn($this->getTable(), 'sync_status') && $this->sync_status === 'pending') {
                Log::channel('single')->info("Firebase Pull Skipped [{$this->getFirebaseDocumentId()}] - Model has pending local changes.");
                return false;
            }

            $syncService = app(FirebaseSyncService::class);
            $collection = $this->getFirebaseCollection();
            $docId = $this->getFirebaseDocumentId();

            if (!$docId) return false;

            // Try fetching
            $docData = $syncService->getDocument($collection, $docId);

            // Fallback for affiliates
            if (!$docData && $collection === 'afiliados') {
                $docIdRaw = preg_replace('/[^0-9]/', '', $docId);
                if ($docIdRaw !== $docId) {
                    $docData = $syncService->getDocument($collection, $docIdRaw);
                }
            }

            if ($docData) {
                // Conflict Resolution & Versioning
                $remoteUpdatedAt = isset($docData['updated_at']) ? \Carbon\Carbon::parse($docData['updated_at']) : null;
                $localUpdatedAt = $this->updated_at;
                
                // If remote is older than local, we might want to skip or flag
                if ($remoteUpdatedAt && $localUpdatedAt && $remoteUpdatedAt->lt($localUpdatedAt->subSeconds(5))) {
                    if (\Schema::hasColumn($this->getTable(), 'conflict_status')) {
                        $this->conflict_status = 'remote_is_older';
                        $this->saveQuietly();
                    }
                    // Continue anyway if we want "Firebase is source of truth", 
                    // or return if we want to protect local edits. 
                    // For SAFE <-> CMD, Firebase is the bridge.
                }

                $mapping = $this->getFirebaseMapping();
                $updateData = [];
                $changes = [];

                foreach ($mapping as $localField => $firebaseField) {
                    if (array_key_exists($firebaseField, $docData)) {
                        $newValue = $docData[$firebaseField];
                        $oldValue = $this->{$localField};

                        // Comparison for auditing
                        if ($newValue != $oldValue) {
                            $updateData[$localField] = $newValue;
                            $changes[$localField] = [
                                'old' => $oldValue,
                                'new' => $newValue
                            ];
                        }
                    }
                }

                if (!empty($updateData)) {
                    // Business Rules (Gating)
                    if (method_exists($this, 'applyGatingRule')) {
                        $updateData = $this->applyGatingRule($updateData);
                    }

                    // Perform update without triggering events
                    self::withoutEvents(function() use ($updateData) {
                        $this->update($updateData);
                    });

                    // Log Audits for traceability
                    $origin = env('FIREBASE_SYNC_ROLE') === 'CMD' ? 'SAFE' : 'CMD'; // If I am CMD, source is SAFE
                    foreach ($changes as $field => $val) {
                        CloudSyncAudit::create([
                            'auditable_type' => get_class($this),
                            'auditable_id' => $this->id,
                            'field' => $field,
                            'old_value' => is_array($val['old']) ? json_encode($val['old']) : $val['old'],
                            'new_value' => is_array($val['new']) ? json_encode($val['new']) : $val['new'],
                            'company_origin' => $origin,
                            'user_name' => 'Firebase Sync',
                            'synced_at' => now()
                        ]);
                    }
                    
                    Log::channel('single')->info("Firebase Pull Success [{$collection}/{$docId}] - " . count($changes) . " fields updated.");
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Firebase Pull Error on model " . get_class($this) . ": " . $e->getMessage());
            return false;
        }
    }

    /**
     * Push current model data to Firebase.
     */
    public function pushToFirebase(): bool
    {
        try {
            $syncService = app(FirebaseSyncService::class);
            $collection = $this->getFirebaseCollection();
            $docId = $this->getFirebaseDocumentId();

            if (!$docId) return false;

            $mapping = $this->getFirebaseMapping();
            $dataToPush = [];

            foreach ($mapping as $localField => $firebaseField) {
                $dataToPush[$firebaseField] = $this->{$localField};
            }

            // Add timestamp for cloud ordering
            $dataToPush['updated_at'] = now()->toIso8601String();

            if (!empty($dataToPush)) {
                $success = $syncService->push($collection, $docId, $dataToPush);
                
                if ($success) {
                    self::withoutEvents(function() {
                        $this->timestamps = false;
                        $this->firebase_synced_at = now();
                        if (\Schema::hasColumn($this->getTable(), 'sync_status')) {
                            $this->sync_status = 'synced';
                        }
                        $this->saveQuietly();
                    });
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Firebase Push Error on model " . get_class($this) . ": " . $e->getMessage());
            return false;
        }
    }

    /**
     * Default Firebase Collection naming convention
     */
    public function getFirebaseCollection(): string
    {
        return strtolower(class_basename($this)) . 's';
    }

    /**
     * Default Field Mapping (Most fields are 1:1)
     */
    public function getFirebaseMapping(): array
    {
        // Default to all fillable fields if not defined
        $fields = $this->getFillable();
        return array_combine($fields, $fields);
    }
}
