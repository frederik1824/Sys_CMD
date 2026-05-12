<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Models\FirebaseSyncLog;
use Illuminate\Support\Facades\Cache;

class FirebaseSyncPush extends Command
{
    protected $signature = 'firebase:sync-all 
                            {--companies : Push only companies} 
                            {--affiliates : Push only affiliates} 
                            {--all : Sync everything} 
                            {--force : Force push all records regardless of sync status}
                            {--intensity=50 : Speed intensity}
                            {--log-id= : Log ID}';

    protected $description = 'Efficiently pushes pending local changes to Firebase Firestore using batch operations.';

    public function handle(FirebaseSyncService $firebase)
    {
        $force = $this->option('force');
        $logId = $this->option('log-id');
        $intensity = (int)$this->option('intensity');

        $log = null;
        if ($logId) {
            $log = FirebaseSyncLog::find($logId);
        }
        if (!$log) {
            $log = FirebaseSyncLog::create([
                'type' => 'Push',
                'status' => 'running',
                'started_at' => now(),
                'performed_by' => 'System/Console'
            ]);
        }

        $this->info("🚀 Starting Optimized Firebase PUSH...");
        
        Cache::put('firebase_sync_active', true, 7200);
        Cache::put('firebase_sync_control', 'running', 7200);

        try {
            $stats = [
                'empresas' => ['updated' => 0], 
                'afiliados' => ['updated' => 0],
                'evidencias' => ['updated' => 0],
                'notas' => ['updated' => 0]
            ];

            // 🏢 SYNC COMPANIES
            if ($this->option('companies') || $this->option('all') || (!$this->option('affiliates') && !$this->option('companies'))) {
                $this->syncModelBatch($firebase, Empresa::class, 'empresas', $force, $stats, 'empresas', function($emp) {
                    return [
                        'id' => $emp->uuid,
                        'data' => [
                            'uuid' => $emp->uuid,
                            'nombre' => $emp->nombre,
                            'rnc' => $emp->rnc,
                            'es_verificada' => (bool)$emp->es_verificada,
                            'updated_at' => now()->toIso8601String()
                        ]
                    ];
                });
            }

            // 📝 SYNC AFILIADOS
            if ($this->option('affiliates') || $this->option('all') || (!$this->option('affiliates') && !$this->option('companies'))) {
                $this->syncModelBatch($firebase, Afiliado::class, 'afiliados', $force, $stats, 'afiliados', function($af) {
                    return [
                        'id' => (string)$af->cedula,
                        'data' => [
                            'uuid' => $af->uuid,
                            'cedula' => $af->cedula,
                            'nombre_completo' => $af->nombre_completo,
                            'empresa_id' => $af->empresa_id,
                            'estado_id' => $af->estado_id,
                            'responsable_id' => $af->responsable_id,
                            'updated_at' => now()->toIso8601String()
                        ]
                    ];
                });

                // Nested: Evidencias
                $this->syncModelBatch($firebase, \App\Models\EvidenciaAfiliado::class, 'afiliados_evidencias', $force, $stats, 'evidencias', function($ev) {
                    return [
                        'id' => ($ev->afiliado?->cedula ?? $ev->afiliado_id) . '_' . ($ev->tipo_documento ?? 'doc'),
                        'data' => [
                            'afiliado_id' => $ev->afiliado?->cedula,
                            'type' => $ev->tipo_documento,
                            'status' => $ev->status,
                            'file_url' => $ev->file_path,
                            'notes' => $ev->observaciones,
                            'updated_at' => now()->toIso8601String()
                        ]
                    ];
                });

                // Nested: Notas
                $this->syncModelBatch($firebase, \App\Models\NotaAfiliado::class, 'afiliados_notas', $force, $stats, 'notas', function($nota) {
                    return [
                        'id' => ($nota->afiliado?->cedula ?? $nota->afiliado_id) . '_' . $nota->id,
                        'data' => [
                            'afiliado_id' => $nota->afiliado?->cedula,
                            'text' => $nota->contenido,
                            'timestamp' => $nota->created_at->toIso8601String(),
                            'updated_at' => now()->toIso8601String()
                        ]
                    ];
                });
            }

            $log->update([
                'status' => 'success',
                'summary' => $stats,
                'items_count' => array_sum(array_column($stats, 'updated')),
                'finished_at' => now()
            ]);

            $this->info("✅ Push completed successfully.");

        } catch (\Throwable $e) {
            if ($log) $log->update(['status' => 'failed', 'summary' => ['error' => $e->getMessage()], 'finished_at' => now()]);
            throw $e;
        } finally {
            Cache::put('firebase_sync_active', false);
            Cache::put('firebase_sync_control', 'stopped');
        }

        return 0;
    }

    protected function syncModelBatch($firebase, $modelClass, $collection, $force, &$stats, $statKey, $mapper)
    {
        $query = $modelClass::query();
        if (method_exists($modelClass, 'withoutGlobalScopes')) $query = $modelClass::withoutGlobalScopes();
        
        if (!$force) $query->where('sync_status', 'pending');
        
        $count = $query->count();
        if ($count === 0) {
            $firebase->sendToFeed("No hay {$statKey} pendientes para sincronizar.", "slate");
            return;
        }

        $this->info("Pushing $count pending $statKey...");
        $query->chunk(100, function($items) use ($firebase, &$stats, $statKey, $collection, $mapper) {
            $batch = [];
            foreach ($items as $item) {
                $batch[] = $mapper($item);
            }
            if ($firebase->pushBatch($collection, $batch)) {
                $stats[$statKey]['updated'] += count($batch);
                // Mark as synced
                $ids = collect($items)->pluck('id');
                $items[0]->newQuery()->whereIn('id', $ids)->update(['sync_status' => 'synced', 'firebase_synced_at' => now()]);
            }
            Cache::put('firebase_sync_stats', $stats, 7200);
        });
    }
}
