<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Services\FirebaseSyncService;

class FirebaseSyncAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:sync-all {--force : Forzar la subida de todos los registros} {--log-id= : ID de la bitácora existente} {--intensity=50 : Velocidad de carga (10-500)} {--dry-run : Solo simular} {--force-mass : Saltar chequeo de volumen masivo} {--companies : Subir solo empresas} {--affiliates : Subir solo afiliados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza la base de datos local con Firebase Firestore (Diferencial)';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $syncService)
    {
        $force = $this->option('force');
        $logId = $this->option('log-id');
        $isDryRun = $this->option('dry-run');
        $intensity = (int)$this->option('intensity', 50);

        if ($isDryRun) $this->warn("⚠️ MODO SIMULACIÓN: No se enviarán cambios a Firebase.");
        
        $this->info('🚀 Iniciando sincronización masiva (PUSH) con Firebase...');
        
        if ($logId) {
            $log = \App\Models\FirebaseSyncLog::find($logId);
            if ($log) {
                $log->update(['status' => 'running', 'started_at' => now()]);
            }
        }

        if (!isset($log) || !$log) {
            $log = \App\Models\FirebaseSyncLog::create([
                'type' => 'Push',
                'status' => 'running',
                'started_at' => now(),
                'performed_by' => 'System/Manual',
                'summary' => ['is_dry_run' => $isDryRun]
            ]);
        }

        try {
            $stats = [
                'empresas' => ['updated' => 0, 'errors' => 0],
                'afiliados' => ['updated' => 0, 'errors' => 0],
            ];

            // Mark as active
            \Illuminate\Support\Facades\Cache::put('firebase_sync_active', true, 600);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_progress', 0);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_label', 'Iniciando subida a Cloud...');
            \Illuminate\Support\Facades\Cache::put('firebase_sync_control', 'running', 600);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_stats', $stats, 600);

            $syncCompanies = $this->option('companies');
            $syncAffiliates = $this->option('affiliates');
            
            // Si ninguno está seleccionado, sincronizar ambos (comportamiento por defecto)
            if (!$syncCompanies && !$syncAffiliates) {
                $syncCompanies = true;
                $syncAffiliates = true;
            }

            // 1. Sincronizar Empresas
            if ($syncCompanies) {
                $this->updateProgress(5, 'Analizando empresas para subir...');
                $queryEmpresas = Empresa::with(['provinciaRel', 'municipioRel']); 
                if (!$force) {
                $queryEmpresas->where(function($q) {
                    $q->whereNull('firebase_synced_at')
                    ->orWhereColumn('updated_at', '>', 'firebase_synced_at');
                });
            }

            $totalEmpresas = $queryEmpresas->count();
            $totalBase = Empresa::count();
            
            // Safe Guard: Anomalía de volumen
            if ($totalEmpresas > ($totalBase * 0.5) && $totalBase > 10 && !$force && !$this->option('force-mass') && $this->input->isInteractive()) {
                $this->updateProgress(5, '⚠️ Anomalía Detectada: Volumen de cambios inusual. Pausado por seguridad.', $stats, "ALERTA: Se detectaron {$totalEmpresas} empresas para subir (>50% del total).");
                \Illuminate\Support\Facades\Cache::put('firebase_sync_control', 'paused');
            }

            if ($totalEmpresas > 0) {
                $this->info("- Sincronizando {$totalEmpresas} Empresas...");
                $processed = 0;
                $queryEmpresas->chunkById(50, function ($empresas) use ($syncService, &$stats, &$processed, $totalEmpresas, $isDryRun, $intensity) {
                    foreach ($empresas as $empresa) {
                        $this->checkSyncControl($stats);
                        $documentId = $empresa->getFirebaseDocumentId();
                        
                        $success = true;
                        if (!$isDryRun) {
                            $success = $syncService->syncModel($empresa, 'empresas', $documentId);
                        }

                        if ($success) {
                            $stats['empresas']['updated']++;
                            $this->addToFeed("PUSH: [Empresa] {$empresa->nombre} OK");
                            if (!$isDryRun) {
                                $empresa->updateQuietly(['firebase_synced_at' => now()]);
                            }
                        } else {
                            $stats['empresas']['errors']++;
                            $this->addToFeed("ERROR: [Empresa] {$empresa->nombre} FAIL", 'rose');
                        }
                        
                        // Throttling
                        usleep(max(100, (500 - $intensity) * 300));
                        
                        $processed++;
                        
                        if ($processed % 5 === 0) {
                            $prog = 5 + (($processed / $totalEmpresas) * 25); // 5% to 30%
                            $this->updateProgress($prog, "Subiendo empresas ($processed/$totalEmpresas)...", $stats);
                        }
                    }
                });
            } else {
                $this->updateProgress(30, 'No hay empresas nuevas para subir.', $stats, "Información: Nada que subir en empresas.");
            }
            }

            // 2. Sincronizar Afiliados
            if ($syncAffiliates) {
            $this->updateProgress(30, 'Analizando afiliados para subir...');
            $queryAfiliados = Afiliado::with([
                'empresaModel.provinciaRel', 
                'empresaModel.municipioRel', 
                'estado', 
                'provinciaRel', 
                'municipioRel', 
                'proveedor', 
                'responsable'
            ])->whereNotNull('cedula');
            if (!$force) {
                $queryAfiliados->where(function($q) {
                    $q->whereNull('firebase_synced_at')
                    ->orWhereColumn('updated_at', '>', 'firebase_synced_at');
                });
            }

            $totalAfiliados = $queryAfiliados->count();
            if ($totalAfiliados > 0) {
                $this->info("- Sincronizando {$totalAfiliados} Afiliados...");
                $processedAf = 0;
                $queryAfiliados->chunkById(100, function ($afiliados) use ($syncService, &$stats, &$processedAf, $totalAfiliados, $isDryRun, $intensity) {
                    foreach ($afiliados as $afiliado) {
                        $this->checkSyncControl($stats);
                        $documentId = $afiliado->cedula;
                        
                        $success = true;
                        if (!$isDryRun) {
                            $success = $syncService->syncModel($afiliado, 'afiliados', $documentId);
                        }

                        if ($success) {
                            $stats['afiliados']['updated']++;
                            $this->addToFeed("PUSH: [Afiliado] {$afiliado->nombre} OK");
                            if (!$isDryRun) {
                                $afiliado->updateQuietly(['firebase_synced_at' => now()]);
                            }
                        } else {
                            $stats['afiliados']['errors']++;
                            $this->addToFeed("ERROR: [Afiliado] {$afiliado->nombre} FAIL", 'rose');
                        }
                        
                        // Throttling
                        usleep(max(100, (500 - $intensity) * 300));
                        
                        $processedAf++;

                        if ($processedAf % 25 === 0) {
                            $prog = 30 + (($processedAf / $totalAfiliados) * 65); // 30% to 95%
                            $this->updateProgress($prog, "Subiendo afiliados ($processedAf/$totalAfiliados)...", $stats);
                        }
                    }
                });
            } else {
                $this->updateProgress(95, 'No hay afiliados nuevos para subir.', $stats);
            }
            }

            $log->update([
                'status' => 'success',
                'summary' => $stats,
                'items_count' => $stats['empresas']['updated'] + $stats['afiliados']['updated'],
                'finished_at' => now()
            ]);

            $this->updateProgress(100, '✅ Subida finalizada correctamente', $stats);
            $this->info('✅ Sincronización diferencial finalizada correctamente.');

        } catch (\Throwable $e) {
            $errorMsg = ($e->getMessage() === 'CANCELLED') ? 'Sincronización cancelada por el usuario.' : $e->getMessage();
            
            $itemsProcessed = 0;
            if (isset($stats['empresas']['updated'])) $itemsProcessed += $stats['empresas']['updated'];
            if (isset($stats['afiliados']['updated'])) $itemsProcessed += $stats['afiliados']['updated'];

            $log->update([
                'status' => 'failed',
                'summary' => array_merge($stats ?? [], ['error' => $errorMsg]),
                'items_count' => $itemsProcessed,
                'finished_at' => now()
            ]);
            $this->error("❌ Detenido: " . $errorMsg);
            $this->updateProgress(0, "❌ $errorMsg", $stats ?? []);
            if ($e->getMessage() !== 'CANCELLED') throw $e;
        } finally {
            \Illuminate\Support\Facades\Cache::put('firebase_sync_active', false);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_control', 'stopped');
        }
    }

    private function checkSyncControl(&$stats)
    {
        $control = \Illuminate\Support\Facades\Cache::get('firebase_sync_control', 'running');

        if ($control === 'cancelled') {
            throw new \Exception('CANCELLED');
        }

        while ($control === 'paused') {
            sleep(2);
            $control = \Illuminate\Support\Facades\Cache::get('firebase_sync_control', 'running');
            if ($control === 'cancelled') throw new \Exception('CANCELLED');
        }
    }

    private function updateProgress($percentage, $label, $stats = [], $logMsg = null)
    {
        \Illuminate\Support\Facades\Cache::put('firebase_sync_progress', round($percentage), 600);
        \Illuminate\Support\Facades\Cache::put('firebase_sync_label', $label, 600);
        if (!empty($stats)) {
            \Illuminate\Support\Facades\Cache::put('firebase_sync_stats', $stats, 600);
        }
        if ($logMsg) $this->addToFeed($logMsg, 'cyan');
    }

    private function addToFeed($message, $color = 'slate')
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
