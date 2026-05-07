<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Empresa;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class FirebaseSyncPull extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'firebase:pull-all {--full : Sync all data including companies} {--affiliates : Sync affiliates only} {--companies : Sync companies only} {--catalogs : Sync catalogs only (provincias, municipios, etc)} {--log-id= : Existing log ID to update} {--intensity=50 : Speed intensity (10-500)} {--dry-run : Simulate only} {--snapshot : Create DB backup before full sync} {--province_id= : Sync specific province ID only} {--force-mass : Skip large volume volume check}';

    /**
     * The console command description.
     */
    protected $description = 'Syncs Companies, Roles, and Users FROM Firebase Firestore TO local database.';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $firebase)
    {
        $isFull = $this->option('full');
        $onlyAffiliates = $this->option('affiliates');
        $onlyCompanies = $this->option('companies');
        $isDryRun = $this->option('dry-run');
        $shouldSnapshot = $this->option('snapshot');
        $logId = $this->option('log-id');
        
        if ($isDryRun) $this->warn("⚠️ DRY RUN MODE ACTIVE - No changes will be saved to MySQL.");

        // Snapshots (Sólo si es Full o Companies y se solicita)
        if ($shouldSnapshot && ($isFull || $onlyCompanies)) {
            $this->info("📸 Creating Table Snapshots...");
            $timestamp = now()->format('Ymd_His');
            try {
                if (\Schema::hasTable('afiliados')) {
                    \DB::statement("CREATE TABLE z_backup_afiliados_{$timestamp} AS SELECT * FROM afiliados");
                }
                if (\Schema::hasTable('empresas')) {
                    \DB::statement("CREATE TABLE z_backup_empresas_{$timestamp} AS SELECT * FROM empresas");
                }
                $this->info("✅ Snapshots created: z_backup_..._{$timestamp}");
            } catch (\Exception $e) {
                $this->error("❌ Snapshot failed: " . $e->getMessage());
            }
        }
        
        // Determinar qué sincronizar
        $syncAll        = $isFull;
        $syncAffiliates = $isFull || $onlyAffiliates;
        $syncCompanies  = $isFull || $onlyCompanies;
        $syncCatalogs   = $isFull || $onlyCompanies || $this->option('catalogs');
        $syncAuth       = $isFull; // Roles y Usuarios solo en Full

        $lastSync = \App\Models\FirebaseSyncLog::where('type', 'Pull')
            ->where('status', 'success')
            ->orderBy('finished_at', 'desc')
            ->first();

        $sinceDate = null;

        if ($isFull) {
            // --full explícito: siempre descarga todo sin filtro de fecha
            $this->info("🚀 Modo FULL activo — descargando todos los registros...");

        } elseif ($lastSync) {
            // Modo INCREMENTAL: aplica a targeted (--affiliates, --companies) Y al modo automático
            // Descargamos con un margen de 60 min para cubrir solapamientos de red
            $sinceDate = $lastSync->finished_at->subMinutes(60)->toDateTimeString();
            $this->info("🔄 Modo Incremental activo — solo cambios desde: {$sinceDate}");

            // Si no hay flags específicos, sincronizar todo en modo incremental
            if (!$onlyAffiliates && !$onlyCompanies) {
                $syncAffiliates = true;
                $syncCompanies  = true;
                $syncAuth       = true;
            }
        } else {
            // Primera vez: sin historial de syncs previos → descargar todo
            $this->info("🚀 Primera sincronización — descargando todos los registros...");
        }

        if ($logId) {
            $log = \App\Models\FirebaseSyncLog::find($logId);
            if ($log) {
                $log->update(['status' => 'running', 'started_at' => now()]);
            }
        }

        if (!isset($log) || !$log) {
            $log = \App\Models\FirebaseSyncLog::create([
                'type' => 'Pull',
                'status' => 'running',
                'started_at' => now(),
                'performed_by' => 'System/Manual',
                'summary' => ['mode' => $sinceDate ? 'Incremental' : ($isFull ? 'Full' : 'Targeted'), 'is_dry_run' => $isDryRun]
            ]);
        }

        // 🛡️ DESACTIVAR TRIGGERS AUTOMÁTICOS 
        // Durante un PULL masivo, no queremos que el evento saved() de los modelos
        // dispare un PUSH de vuelta a Firebase. Esto ahorra 50% de cuota.
        \App\Traits\FirebaseSyncable::$isSyncingDisabled = true;

        try {
            $stats = [
                'roles' => ['created' => 0, 'updated' => 0],
                'users' => ['created' => 0, 'updated' => 0],
                'empresas' => ['created' => 0, 'updated' => 0],
                'afiliados' => ['created' => 0, 'updated' => 0],
                'catalogs' => ['created' => 0, 'updated' => 0],
            ];

            // Mark as active — TTL largo (2h) para syncs grandes con muchos registros
            $modeLabel = $sinceDate
                ? "🔄 Incremental desde " . \Carbon\Carbon::parse($sinceDate)->format('d/M H:i')
                : "🚀 Descarga completa iniciando...";

            \Illuminate\Support\Facades\Cache::put('firebase_sync_active', true, 7200);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_progress', 0, 7200);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_label', $modeLabel, 7200);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_control', 'running', 7200);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_stats', $stats, 7200);
            $this->addToFeed($modeLabel, 'cyan');


            // 🛡️ SYNC ROLES
            if ($syncAuth) {
                $this->info("--- Roles ---");
                $this->updateProgress(2, 'Escaneando roles...');
                $rolesData = $sinceDate ? $firebase->getCollectionIncremental('roles', $sinceDate) : $firebase->getCollection('roles');
                foreach ($rolesData as $mapped) {
                    $this->checkSyncControl($stats);
                    $isNew = !Role::where('name', $mapped['name'])->exists();
                    if (!$isDryRun) {
                        Role::updateOrCreate(['name' => $mapped['name']], ['guard_name' => $mapped['guard_name'] ?? 'web']);
                    }
                    $isNew ? $stats['roles']['created']++ : $stats['roles']['updated']++;
                }
                $this->updateProgress(5, 'Roles listos...');

                // 👤 SYNC USERS
                $this->info("--- Users ---");
                $this->updateProgress(6, 'Escaneando usuarios...');
                $usersData = $sinceDate ? $firebase->getCollectionIncremental('users', $sinceDate) : $firebase->getCollection('users');
                foreach ($usersData as $mapped) {
                    $this->checkSyncControl($stats);
                    if (!isset($mapped['email'])) continue;
                    $isNew = !User::where('email', $mapped['email'])->exists();
                    if (!$isDryRun) {
                        $user = User::updateOrCreate(['email' => $mapped['email']], [
                            'name' => $mapped['name'] ?? 'Usuario Firebase',
                            'password' => Hash::make('Password')
                        ]);
                        if (isset($mapped['roles'])) {
                            $roles = is_array($mapped['roles']) ? $mapped['roles'] : json_decode($mapped['roles'], true);
                            if (is_array($roles)) $user->syncRoles($roles);
                        }
                    }
                    $isNew ? $stats['users']['created']++ : $stats['users']['updated']++;
                }
                $this->updateProgress(10, 'Usuarios listos...');
            }

            // 🏢 SYNC COMPANIES (Only if full sync, incremental, or explicitly requested)
            if ($syncCompanies) {
                $this->info("--- Companies ---");
                $this->updateProgress(12, 'Analizando empresas...');
                
                $companiesData = $sinceDate ? $firebase->getCollectionIncremental('empresas', $sinceDate) : $firebase->getCollection('empresas');
                $total = count($companiesData);
                
                foreach ($companiesData as $index => $mapped) {
                    $this->checkSyncControl($stats);
                    if (!isset($mapped['uuid']) && !isset($mapped['firebase_id'])) continue;
                    $uuid = $mapped['uuid'] ?? $mapped['firebase_id'];
                    $isNew = !Empresa::where('uuid', $uuid)->exists();
                    
                    if (!$isDryRun) {
                        Empresa::withoutEvents(function() use ($mapped, $uuid) {
                            Empresa::updateOrCreate(['uuid' => $uuid], [
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
                            ]);
                        });
                    }
                    $isNew ? $stats['empresas']['created']++ : $stats['empresas']['updated']++;

                    if ($total > 0 && $index % 5 === 0) {
                        $prog = 12 + (($index / $total) * 15); // 12% to 27%
                        $this->updateProgress($prog, "Sincronizando empresas ($index/$total)...", $stats);
                    }

                    // Throttling
                    usleep(max(100, (500 - (int)$this->option('intensity')) * 300));
                }
            }

            // --- CATALOGS ---
            if ($syncCatalogs) {
                foreach (['provincias', 'municipios', 'cortes', 'estados', 'responsables', 'proveedores'] as $cat) {
                    $this->checkSyncControl($stats);
                    $this->updateProgress(27 + array_search($cat, ['provincias', 'municipios', 'cortes', 'estados', 'responsables', 'proveedores']) * 2, "Cargando $cat...", $stats);
                    $stats['catalogs']['created'] += $this->pullCatalog($firebase, $cat, "App\\Models\\".ucfirst(substr($cat, 0, -1)), $sinceDate, $stats, $isDryRun);
                }
            }

            // 👥 SYNC AFFILIATES
            if ($syncAffiliates) {
                $provinceId = $this->option('province_id');
                $this->info("--- Affiliates ---");
                $startProg = $syncCompanies ? 45 : 10;
                $this->updateProgress($startProg, 'Obteniendo afiliados...', $stats);
                
                $affiliatesData = $sinceDate ? $firebase->getCollectionIncremental('afiliados', $sinceDate) : $firebase->getCollection('afiliados');
                $totalAf = count($affiliatesData);
                $localTotal = \App\Models\Afiliado::count();
                
                // Safe Guard: Detección de anomalías en descarga masiva
                if ($totalAf > ($localTotal * 0.5) && $localTotal > 100 && !$isFull && !$this->option('force-mass') && $this->input->isInteractive()) {
                    $this->updateProgress($startProg, '⚠️ Anomalía Detectada', $stats, "SEGURIDAD: Intento de descarga masiva ({$totalAf} registros). Pausado.");
                    \Illuminate\Support\Facades\Cache::put('firebase_sync_control', 'paused');
                }

                $this->info("Processing " . $totalAf . " affiliates...");

                if ($totalAf > 0) {
                    foreach ($affiliatesData as $index => $doc) {
                        $this->checkSyncControl($stats);
                        
                        // Handle both full objects and mapped arrays (inc)
                        $mapped = isset($doc['fields']) ? $firebase->mapFromFirestore($doc['fields']) : $doc;

                        // Filter by province
                        if ($provinceId && (!isset($mapped['provincia_id']) || $mapped['provincia_id'] != $provinceId)) {
                            continue;
                        }

                        if (!isset($mapped['cedula'])) continue;
                    
                        $isNew = !\App\Models\Afiliado::withoutGlobalScopes()->where('cedula', $mapped['cedula'])->exists();
                        $afiliado = \App\Models\Afiliado::withoutGlobalScopes()->where('cedula', $mapped['cedula'])->first() ?? new \App\Models\Afiliado();

                        if (!$isDryRun) {
                            \App\Models\Afiliado::withoutEvents(function() use ($mapped, $afiliado) {
                                $dataToUpdate = $afiliado->applyGatingRule($mapped);

                                // ── Validación de FK locales ───────────────────────────────────────
                                // Firebase puede tener IDs de lotes/proveedores/responsables que no
                                // existen localmente. Anulamos los IDs inválidos en vez de crashear.
                                $loteId       = $dataToUpdate['lote_id']       ?? null;
                                $proveedorId  = $dataToUpdate['proveedor_id']  ?? null;
                                $responsableId= $dataToUpdate['responsable_id'] ?? null;

                                if ($loteId && !\DB::table('lotes')->where('id', $loteId)->exists()) {
                                    $loteId = null;
                                }
                                if ($proveedorId && !\DB::table('proveedores')->where('id', $proveedorId)->exists()) {
                                    $proveedorId = null;
                                }
                                if ($responsableId && !\DB::table('responsables')->where('id', $responsableId)->exists()) {
                                    $responsableId = null;
                                }
                                // ──────────────────────────────────────────────────────────────────

                                $afiliado->fill([
                                    'cedula'                  => $mapped['cedula'],
                                    'nombre_completo'         => $dataToUpdate['nombre_completo'] ?? null,
                                    'telefono'                => $dataToUpdate['telefono'] ?? null,
                                    'direccion'               => $dataToUpdate['direccion'] ?? null,
                                    'poliza'                  => $dataToUpdate['poliza'] ?? null,
                                    'contrato'                => $dataToUpdate['contrato'] ?? null,
                                    'empresa'                 => $dataToUpdate['empresa'] ?? null,
                                    'rnc_empresa'             => $dataToUpdate['rnc_empresa'] ?? null,
                                    'estado_id'               => $dataToUpdate['estado_id'] ?? null,
                                    'lote_id'                 => $loteId,
                                    'proveedor_id'            => $proveedorId,
                                    'responsable_id'          => $responsableId,
                                    'corte_id'                => $dataToUpdate['corte_id'] ?? null,
                                    'fecha_entrega_proveedor' => $dataToUpdate['fecha_entrega_proveedor'] ?? null,
                                    'costo_entrega'           => $dataToUpdate['costo_entrega'] ?? 0,
                                    'firebase_synced_at'      => now()
                                ])->save();
                            });
                        }
                    
                        $isNew ? $stats['afiliados']['created']++ : $stats['afiliados']['updated']++;
                        
                        // Live Terminal entry
                        if ($index % 5 === 0) {
                            $this->addToFeed("PULL: [Afiliado] " . ($mapped['nombre_completo'] ?? $mapped['cedula']) . ($isNew ? " NEW" : " UPD"), $isNew ? 'emerald' : 'slate');
                        }

                        if ($totalAf > 0 && $index % 50 === 0) {
                            $prog = $startProg + (($index / $totalAf) * (98 - $startProg));
                            $this->updateProgress($prog, "Sincronizando afiliados ($index/$totalAf)...", $stats);
                        }

                        // Throttling
                        usleep(max(100, (500 - (int)$this->option('intensity')) * 300));
                    }
                } else {
                    $this->updateProgress($startProg + 10, 'No hay afiliados nuevos que procesar.', $stats);
                }
            }

            $totalItems = $stats['roles']['created'] + $stats['roles']['updated'] + 
                        $stats['users']['created'] + $stats['users']['updated'] + 
                        $stats['empresas']['created'] + $stats['empresas']['updated'] + 
                        $stats['afiliados']['created'] + $stats['afiliados']['updated'] + 
                        $stats['catalogs']['created'];

            $log->update([
                'status' => 'success',
                'summary' => $stats,
                'items_count' => $totalItems,
                'finished_at' => now()
            ]);

            $this->updateProgress(100, '✅ Sincronización finalizada', $stats);
            $this->info("✅ Firebase Cloud PULL completed!");

        } catch (\Throwable $e) {
            $errorMsg = ($e->getMessage() === 'CANCELLED') ? 'Sincronización cancelada por el usuario.' : $e->getMessage();
            
            $log->update([
                'status' => 'failed',
                'summary' => array_merge($stats ?? [], ['error' => $errorMsg]),
                'finished_at' => now()
            ]);
            $this->error("❌ Detenido: " . $errorMsg);
            $this->updateProgress(0, "❌ $errorMsg", $stats ?? []);
            if ($e->getMessage() !== 'CANCELLED') throw $e;
        } finally {
            // 🛡️ RE-ACTIVAR TRIGGERS AUTOMÁTICOS
            \App\Traits\FirebaseSyncable::$isSyncingDisabled = false;

            \Illuminate\Support\Facades\Cache::put('firebase_sync_active', false);
            \Illuminate\Support\Facades\Cache::put('firebase_sync_control', 'stopped');
        }

        return 0;
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
        // Renovar TTL del flag activo en cada actualización para evitar expiración durante syncs largas
        \Illuminate\Support\Facades\Cache::put('firebase_sync_active', true, 7200);
        \Illuminate\Support\Facades\Cache::put('firebase_sync_progress', round($percentage), 7200);
        \Illuminate\Support\Facades\Cache::put('firebase_sync_label', $label, 7200);
        if (!empty($stats)) {
            \Illuminate\Support\Facades\Cache::put('firebase_sync_stats', $stats, 7200);
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

    private function pullCatalog($firebase, $collection, $modelClass, $sinceDate = null, &$stats, $isDryRun = false)
    {
        $this->comment("- Pulling {$collection}...");
        $data = $sinceDate ? $firebase->getCollectionIncremental($collection, $sinceDate) : $firebase->getCollection($collection);
        $newCount = 0;
        foreach ($data as $mapped) {
            $this->checkSyncControl($stats);
            $id = $mapped['id'] ?? ($mapped['firebase_id'] ?? null);
            if (!$id) continue;
            
            $isNew = !$modelClass::where('id', $id)->exists();
            if (!$isDryRun) {
                $modelClass::withoutEvents(function() use ($modelClass, $mapped, $id) {
                    $modelClass::updateOrCreate(['id' => $id], $mapped);
                });
            }
            if ($isNew) $newCount++;
        }
        return $newCount;
    }
}
