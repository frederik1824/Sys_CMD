<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Afiliado;
use App\Models\CloudSyncCheckpoint;
use App\Models\FirebaseSyncLog;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FirebaseSyncPull extends Command
{
    protected $signature = 'firebase:pull-all 
                            {--full : Sync all data including companies} 
                            {--affiliates : Sync affiliates only} 
                            {--companies : Sync companies only} 
                            {--catalogs : Sync catalogs only} 
                            {--log-id= : Existing log ID to update} 
                            {--intensity=50 : Speed intensity (10-500)} 
                            {--dry-run : Simulate only} 
                            {--snapshot : Create DB backup before full sync} 
                            {--batch-size=500 : Documents per Firebase request}
                            {--read-limit=45000 : Maximum reads allowed for this execution}';

    protected $description = 'Incremental and Checkpoint-aware sync FROM Firebase TO local database.';

    protected $stats = [
        'roles' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'users' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'empresas' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'afiliados' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'evidencias' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'notas' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
        'catalogs' => ['created' => 0, 'updated' => 0, 'skipped' => 0],
    ];

    public function handle(FirebaseSyncService $firebase)
    {
        $isFull = $this->option('full');
        $isDryRun = $this->option('dry-run');
        $logId = $this->option('log-id');
        $batchSize = (int) $this->option('batch-size');
        $readLimit = (int) $this->option('read-limit');

        $firebase->setReadBudget($readLimit);

        if ($isDryRun) $this->warn("⚠️ DRY RUN MODE ACTIVE");

        // 1. Initial Logging & Flags
        $log = $this->getOrCreateLog($logId, $isFull, $isDryRun);
        
        // 🛡️ Disable outbound triggers
        \App\Traits\FirebaseSyncable::$isSyncingDisabled = true;

        try {
            $this->initCacheState($isFull);

            // 2. Sync Logic by Modules
            $this->syncAuthModule($firebase, $isFull, $batchSize, $isDryRun);
            $this->syncCompaniesModule($firebase, $isFull, $batchSize, $isDryRun);
            $this->syncCatalogsModule($firebase, $isFull, $isDryRun);
            $this->syncAffiliatesModule($firebase, $isFull, $batchSize, $isDryRun);
            $this->syncEvidencesModule($firebase, $isFull, $batchSize, $isDryRun);
            $this->syncNotesModule($firebase, $isFull, $batchSize, $isDryRun);

            // 3. Finalize
            $this->finalizeLog($log, 'success');
            $this->updateProgress(100, '✅ Sincronización finalizada');
            $this->info("✅ Firebase Sync Pull Completed!");

        } catch (\Throwable $e) {
            $this->handleFailure($log, $e);
        } finally {
            \App\Traits\FirebaseSyncable::$isSyncingDisabled = false;
            Cache::put('firebase_sync_active', false);
            Cache::put('firebase_sync_control', 'stopped');
        }

        return 0;
    }

    protected function syncAuthModule($firebase, $isFull, $batchSize, $isDryRun)
    {
        if (!$isFull && !$this->option('catalogs')) return;
        
        $this->syncCollection($firebase, 'roles', Role::class, $isFull, $batchSize, $isDryRun, 'roles', function($mapped) {
            return ['name' => $mapped['name']];
        }, function($mapped) {
            return ['guard_name' => $mapped['guard_name'] ?? 'web'];
        });

        $this->syncCollection($firebase, 'users', User::class, $isFull, $batchSize, $isDryRun, 'users', function($mapped) {
            return ['email' => $mapped['email']];
        }, function($mapped) {
            return [
                'name' => $mapped['name'] ?? 'Usuario Firebase',
                'password' => Hash::make('Password')
            ];
        }, function($user, $mapped) {
             if (isset($mapped['roles'])) {
                $roles = is_array($mapped['roles']) ? $mapped['roles'] : json_decode($mapped['roles'], true);
                if (is_array($roles)) $user->syncRoles($roles);
            }
        });
    }

    protected function syncCompaniesModule($firebase, $isFull, $batchSize, $isDryRun)
    {
        if (!$isFull && !$this->option('companies')) return;

        $this->syncCollection($firebase, 'empresas', Empresa::class, $isFull, $batchSize, $isDryRun, 'empresas', function($mapped) {
            return ['uuid' => $mapped['uuid'] ?? $mapped['firebase_id']];
        }, function($mapped) {
            return [
                'nombre' => $mapped['nombre'] ?? 'Empresa sin nombre',
                'rnc' => $mapped['rnc'] ?? null,
                'email' => $mapped['email'] ?? null,
                'telefono' => $mapped['telefono'] ?? null,
                'direccion' => $mapped['direccion'] ?? null,
                'es_real' => (bool)($mapped['es_real'] ?? false),
                'es_filial' => (bool)($mapped['es_filial'] ?? false),
                'es_verificada' => (bool)($mapped['es_verificada'] ?? false),
                'provincia_id' => $mapped['provincia_id'] ?? null,
                'municipio_id' => $mapped['municipio_id'] ?? null,
            ];
        });
    }

    protected function syncCatalogsModule($firebase, $isFull, $isDryRun)
    {
        if (!$isFull && !$this->option('catalogs')) return;

        $catalogs = ['provincias', 'municipios', 'cortes', 'estados', 'responsables', 'proveedores'];
        foreach ($catalogs as $cat) {
            $modelClass = "App\\Models\\" . ucfirst(substr($cat, 0, -1));
            if ($cat === 'proveedores') $modelClass = "App\\Models\\Proveedor";
            
            $this->syncCollection($firebase, $cat, $modelClass, $isFull, 500, $isDryRun, 'catalogs', function($mapped) {
                return ['id' => $mapped['id'] ?? $mapped['firebase_id']];
            }, function($mapped) {
                return $mapped;
            });
        }
    }

    protected function syncAffiliatesModule($firebase, $isFull, $batchSize, $isDryRun)
    {
        if (!$isFull && !$this->option('affiliates') && !empty(array_filter($this->options()))) {
             // If other options are set but not affiliates, skip. 
             // But if NO options are set, we do affiliates by default (incremental).
             if ($this->option('companies') || $this->option('catalogs')) return;
        }

        $this->syncCollection($firebase, 'afiliados', Afiliado::class, $isFull, $batchSize, $isDryRun, 'afiliados', function($mapped) {
            return ['cedula' => $mapped['cedula']];
        }, function($mapped, $model) {
            $dataToUpdate = $model->applyGatingRule($mapped);
            
            // Validate FKs
            foreach (['lote_id', 'proveedor_id', 'responsable_id'] as $fk) {
                if (isset($dataToUpdate[$fk]) && $dataToUpdate[$fk]) {
                    $table = str_replace('_id', 's', $fk);
                    if (!\DB::table($table)->where('id', $dataToUpdate[$fk])->exists()) {
                        $dataToUpdate[$fk] = null;
                    }
                }
            }

            return [
                'nombre_completo'         => $dataToUpdate['nombre_completo'] ?? null,
                'telefono'                => $dataToUpdate['telefono'] ?? null,
                'direccion'               => $dataToUpdate['direccion'] ?? null,
                'poliza'                  => $dataToUpdate['poliza'] ?? null,
                'contrato'                => $dataToUpdate['contrato'] ?? null,
                'empresa'                 => $dataToUpdate['empresa'] ?? null,
                'rnc_empresa'             => $dataToUpdate['rnc_empresa'] ?? null,
                'estado_id'               => $dataToUpdate['estado_id'] ?? null,
                'lote_id'                 => $dataToUpdate['lote_id'] ?? null,
                'proveedor_id'            => $dataToUpdate['proveedor_id'] ?? null,
                'responsable_id'          => $dataToUpdate['responsable_id'] ?? null,
                'corte_id'                => $dataToUpdate['corte_id'] ?? null,
                'fecha_entrega_proveedor' => $dataToUpdate['fecha_entrega_proveedor'] ?? null,
                'costo_entrega'           => $dataToUpdate['costo_entrega'] ?? 0,
                'firebase_synced_at'      => now(),
                'firebase_updated_at'     => $mapped['updated_at'] ?? null,
                'sync_status'             => 'synced'
            ];
        });
    }

    /**
     * Core Sync Engine with Pagination and Checkpoints
     */
    protected function syncCollection($firebase, $collection, $modelClass, $isFull, $batchSize, $isDryRun, $statKey, $keyResolver, $dataResolver, $afterSave = null)
    {
        $this->info("--- Syncing $collection ---");
        
        $checkpoint = CloudSyncCheckpoint::firstOrCreate(
            ['process_name' => "pull_{$collection}"],
            ['sync_type' => $isFull ? 'full' : 'incremental', 'batch_size' => $batchSize]
        );

        // Resume or Start
        if ($checkpoint->status === 'running' && $checkpoint->updated_at->diffInMinutes(now()) < 30) {
            $this->warn("⚠️ Resuming $collection from last checkpoint...");
        } else {
            $checkpoint->update([
                'status' => 'running',
                'started_at' => now(),
                'processed_count' => 0,
                'failed_count' => 0,
                'sync_type' => $isFull ? 'full' : 'incremental'
            ]);
        }

        $pageToken = $checkpoint->last_document_id;
        $sinceDate = null;

        if (!$isFull) {
            $lastSuccess = CloudSyncCheckpoint::where('process_name', "pull_{$collection}")
                ->where('status', 'success')
                ->value('finished_at');
            $sinceDate = $lastSuccess ? $lastSuccess->subMinutes(30)->toDateTimeString() : null;
        }

        do {
            $this->checkSyncControl();
            
            $this->updateProgress(null, "Descargando lote de $collection...");
            
            $batch = $sinceDate 
                ? $firebase->getCollectionIncrementalPaged($collection, $sinceDate, $batchSize, $pageToken)
                : $firebase->getCollectionPaged($collection, $batchSize, $pageToken);

            $count = count($batch);
            if ($count === 0) break;

            $this->info("Processing batch of $count $collection...");

            foreach ($batch as $index => $mapped) {
                $this->checkSyncControl();
                
                try {
                    $searchKey = $keyResolver($mapped);
                    $model = $modelClass::withoutGlobalScopes()->where($searchKey)->first() ?? new $modelClass();
                    
                    // Checksum Logic
                    $incomingHash = $this->calculateIncomingHash($mapped);
                    if ($model->exists && $model->hash_checksum === $incomingHash) {
                        $this->stats[$statKey]['skipped']++;
                        $checkpoint->last_document_id = $mapped['firebase_id'] ?? null;
                        continue;
                    }

                    if (!$isDryRun) {
                        $modelClass::withoutEvents(function() use ($model, $mapped, $dataResolver, $incomingHash, $afterSave) {
                            $updateData = $dataResolver($mapped, $model);
                            $updateData['hash_checksum'] = $incomingHash;
                            $model->fill($updateData)->save();
                            if ($afterSave) $afterSave($model, $mapped);
                        });
                    }

                    $model->wasRecentlyCreated ? $this->stats[$statKey]['created']++ : $this->stats[$statKey]['updated']++;
                    
                } catch (\Throwable $e) {
                    Log::error("Error syncing $collection doc: " . $e->getMessage());
                    $checkpoint->increment('failed_count');
                }

                $checkpoint->last_document_id = $mapped['firebase_id'] ?? null;
                $checkpoint->increment('processed_count');
                
                if ($index % 50 === 0) {
                    $this->updateProgress(null, "Sincronizando $collection (" . $checkpoint->processed_count . ")...");
                    $checkpoint->save();
                }
            }

            // Intensity control (throttling)
            usleep(max(1000, (500 - (int)$this->option('intensity')) * 500));

            // If we received less than pageSize, we are at the end
            if ($count < $batchSize) break;

        } while (true);

        $checkpoint->update([
            'status' => 'success',
            'finished_at' => now(),
            'last_document_id' => null, // Clear cursor for next time
            'read_count' => $firebase->getReadCount()
        ]);
    }

    protected function calculateIncomingHash($mapped)
    {
        // Simple hash of critical fields
        return hash('sha256', json_encode($mapped));
    }

    /**
     * Helpers for Command State
     */
    protected function getOrCreateLog($logId, $isFull, $isDryRun)
    {
        if ($logId) return FirebaseSyncLog::find($logId);
        
        return FirebaseSyncLog::create([
            'type' => 'Pull',
            'status' => 'running',
            'started_at' => now(),
            'performed_by' => auth()->user()->name ?? 'System/Console',
            'summary' => ['mode' => $isFull ? 'Full' : 'Incremental', 'dry_run' => $isDryRun]
        ]);
    }

    protected function initCacheState($isFull)
    {
        Cache::put('firebase_sync_active', true, 7200);
        Cache::put('firebase_sync_progress', 0, 7200);
        Cache::put('firebase_sync_label', 'Iniciando sincronización incremental...', 7200);
        Cache::put('firebase_sync_control', 'running', 7200);
        Cache::put('firebase_sync_stats', $this->stats, 7200);
        
        $this->addToFeed($isFull ? "🚀 Iniciando descarga completa" : "🔄 Sincronización incremental activa", 'cyan');
    }

    protected function checkSyncControl()
    {
        $control = Cache::get('firebase_sync_control', 'running');
        if ($control === 'cancelled') throw new \Exception('CANCELLED');
        while ($control === 'paused') {
            sleep(3);
            $control = Cache::get('firebase_sync_control', 'running');
            if ($control === 'cancelled') throw new \Exception('CANCELLED');
        }
    }

    protected function updateProgress($percentage, $label)
    {
        if ($percentage) Cache::put('firebase_sync_progress', round($percentage), 7200);
        Cache::put('firebase_sync_label', $label, 7200);
        Cache::put('firebase_sync_stats', $this->stats, 7200);
    }

    protected function addToFeed($msg, $color = 'slate')
    {
        $feed = Cache::get('firebase_sync_feed', []);
        array_unshift($feed, ['time' => now()->format('H:i:s'), 'msg' => $msg, 'color' => $color]);
        Cache::put('firebase_sync_feed', array_slice($feed, 0, 50), 600);
    }

    protected function finalizeLog($log, $status)
    {
        $totalItems = 0;
        foreach ($this->stats as $mod) $totalItems += ($mod['created'] ?? 0) + ($mod['updated'] ?? 0);
        
        $log->update([
            'status' => $status,
            'summary' => $this->stats,
            'items_count' => $totalItems,
            'finished_at' => now()
        ]);
    }

    protected function handleFailure($log, $e)
    {
        $msg = $e->getMessage();
        if ($msg === 'CANCELLED') {
            $msg = 'Cancelado por usuario';
        } elseif (str_contains($msg, '429') || str_contains($msg, 'budget')) {
            $msg = '🛑 LÍMITE DE CUOTA ALCANZADO';
        }

        $this->error("❌ Fallo: $msg");
        $this->updateProgress(0, "❌ Error: $msg");
        $this->finalizeLog($log, 'failed');
        
        // Update Checkpoint
        CloudSyncCheckpoint::where('status', 'running')->update([
            'status' => 'failed',
            'error_message' => $e->getMessage()
        ]);

        if (!str_contains($msg, 'Cancelado')) throw $e;
    }

    protected function syncEvidencesModule($firebase, $isFull, $batchSize, $isDryRun)
    {
        if (!$isFull && !$this->option('affiliates')) return;

        $this->syncCollection($firebase, 'afiliados_evidencias', \App\Models\EvidenciaAfiliado::class, $isFull, $batchSize, $isDryRun, 'evidencias', function($mapped) {
            $afiliadoCedula = $mapped['afiliado_id'] ?? null;
            $tipo = $mapped['type'] ?? 'Diverso';
            
            // We search by affiliate ID and type to avoid duplicates if possible
            $afiliado = Afiliado::where('cedula', $afiliadoCedula)->first();
            return ['afiliado_id' => $afiliado?->id, 'tipo_documento' => $tipo];
        }, function($mapped) {
            $afiliado = Afiliado::where('cedula', $mapped['afiliado_id'] ?? null)->first();
            return [
                'afiliado_id' => $afiliado?->id,
                'tipo_documento' => $mapped['type'] ?? 'Diverso',
                'status' => $mapped['status'] ?? 'pending',
                'file_path' => $mapped['file_url'] ?? null,
                'observaciones' => $mapped['notes'] ?? null,
                'firebase_synced_at' => now(),
            ];
        });
    }

    protected function syncNotesModule($firebase, $isFull, $batchSize, $isDryRun)
    {
        if (!$isFull && !$this->option('affiliates')) return;

        $this->syncCollection($firebase, 'afiliados_notas', \App\Models\NotaAfiliado::class, $isFull, $batchSize, $isDryRun, 'notas', function($mapped) {
             $afiliado = Afiliado::where('cedula', $mapped['afiliado_id'] ?? null)->first();
             return ['afiliado_id' => $afiliado?->id, 'contenido' => $mapped['text'] ?? ''];
        }, function($mapped) {
            $afiliado = Afiliado::where('cedula', $mapped['afiliado_id'] ?? null)->first();
            return [
                'afiliado_id' => $afiliado?->id,
                'contenido' => $mapped['text'] ?? '',
                'created_at' => isset($mapped['timestamp']) ? \Carbon\Carbon::parse($mapped['timestamp']) : now(),
                'firebase_synced_at' => now(),
            ];
        });
    }
}
