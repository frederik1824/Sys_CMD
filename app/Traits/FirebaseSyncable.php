<?php

namespace App\Traits;

use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Log;

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
        static::saved(function ($model) {
            // No sincronizar si el flag global está activo (evita desbordamientos en mass pulls)
            if (static::$isSyncingDisabled) {
                return;
            }

            if ($model->getFirebaseDocumentId()) {
                $success = $model->pushToFirebase();
                
                if ($success) {
                    if (\Schema::hasColumn($model->getTable(), 'firebase_synced_at')) {
                        $now = now();
                        $model->timestamps = false;
                        $model->firebase_synced_at = $now;
                        $model->saveQuietly();
                        
                        Log::channel('single')->info("Firebase Push Success [" . $model->getTable() . "/" . $model->getFirebaseDocumentId() . "] for ID {$model->id}");
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
            $syncService = app(FirebaseSyncService::class);
            $collection = $this->getFirebaseCollection();
            $docId = $this->getFirebaseDocumentId();

            if (!$docId) return false;

            // Try fetching
            $docData = $syncService->getDocument($collection, $docId);

            // En el caso de afiliados, la búsqueda ahora prioriza los guiones
            if (!$docData && $collection === 'afiliados') {
                // Si el ID actual tiene guiones (o es el objeto completo), intentamos la versión "limpia" como fallback legacy
                $docIdRaw = preg_replace('/[^0-9]/', '', $docId);
                if ($docIdRaw !== $docId) {
                    $docData = $syncService->getDocument($collection, $docIdRaw);
                }
            }

            if ($docData) {
                $mapping = $this->getFirebaseMapping();
                $updateData = [];

                foreach ($mapping as $localField => $firebaseField) {
                    if (array_key_exists($firebaseField, $docData)) {
                        $updateData[$localField] = $docData[$firebaseField];
                    }
                }

                if (!empty($updateData)) {
                    // Aplicar reglas de negocio específicas del modelo (si existen)
                    if (method_exists($this, 'applyGatingRule')) {
                        $updateData = $this->applyGatingRule($updateData);
                    }

                    // Update without triggering observers to avoid loop
                    self::withoutEvents(function() use ($updateData) {
                        $this->update($updateData);
                    });
                    
                    // Specific log for debugging sync success
                    Log::channel('single')->info("Firebase Pull Success [{$collection}/{$docId}] for ID {$this->id}");
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

            if (!empty($dataToPush)) {
                return $syncService->push($collection, $docId, $dataToPush);
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
