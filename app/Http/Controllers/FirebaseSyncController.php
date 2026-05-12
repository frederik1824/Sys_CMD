<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Models\User;
use App\Models\FirebaseSyncLog;
use App\Services\FirebaseSyncService;
use App\Jobs\FirebaseSyncJob;
use App\Jobs\FirebasePushJob;
use Illuminate\Support\Facades\Cache;

class FirebaseSyncController extends Controller
{
    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Display the Sync Center Dashboard
     */
    public function index()
    {
        $logs = FirebaseSyncLog::orderBy('created_at', 'desc')->paginate(10);
        $liveStats = Cache::get('firebase_sync_stats', []);
        
        // --- Operative Metrics for the New Dashboard ---
        $stats = [
            'overview' => [
                'total' => Afiliado::count(),
                'synced' => Afiliado::whereNotNull('firebase_synced_at')->where('sync_status', 'synced')->count(),
                'pending' => Afiliado::where('sync_status', 'pending')->count(),
                'error' => Afiliado::where('sync_status', 'error')->count(),
                'conflicts' => Afiliado::where('sync_status', 'conflict')->count(),
                'last_sync' => FirebaseSyncLog::where('status', 'success')->latest()->first()?->finished_at,
            ],
            'connectivity' => [
                'firebase' => $this->syncService->checkConnection() ? 'connected' : 'disconnected',
                'server' => 'stable',
                'safe' => Cache::get('safe_node_status', 'connected'), // Simulated for demo
                'cmd' => 'connected',
            ],
            'quota' => [
                'reads_today' => $this->syncService->getReadCount(),
                'writes_today' => $this->syncService->getWriteCount(),
                'limit_reads' => 50000,
                'limit_writes' => 20000,
            ],
            'realtime' => [
                'in_process' => Cache::get('firebase_sync_active', false) ? 'active' : 'idle',
                'queue_count' => \DB::table('jobs')->where('queue', 'default')->count(),
            ]
        ];

        // Audit Activity for Timeline
        $audits = \App\Models\CloudSyncAudit::orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('firebase.sync-center', compact('logs', 'stats', 'audits'));
    }

    public function pull(Request $request)
    {
        $data = $request->isJson() ? $request->all() : $request->all();

        // ── Anti-Ghost Guard ─────────────────────────────────────────────────
        // Verificar si hay un proceso REAL activo (no solo el caché fantasma).
        // El caché puede quedar "pegado" si el job murió sin limpiar el flag.
        if (Cache::get('firebase_sync_active')) {
            $hasRealRunningLog  = FirebaseSyncLog::where('status', 'running')->exists();
            $hasJobInQueue      = \DB::table('jobs')->where('payload', 'like', '%FirebaseSyncJob%')->count() > 0;

            if (!$hasRealRunningLog && !$hasJobInQueue) {
                // El caché está "fantasma" — limpiar automáticamente
                Cache::forget('firebase_sync_active');
                Cache::forget('firebase_sync_progress');
                Cache::forget('firebase_sync_label');
                Cache::forget('firebase_sync_stats');
                Cache::put('firebase_sync_control', 'stopped');
                \Log::info('FirebaseSyncController: Ghost sync state detected and auto-cleaned.');
            } else {
                // Hay un proceso REAL corriendo — rechazar
                return response()->json([
                    'success' => false,
                    'error'   => 'Ya hay una sincronización activa en progreso. Espera a que finalice o cancélala antes de iniciar una nueva.'
                ], 409);
            }
        }
        // ─────────────────────────────────────────────────────────────────────

        $log = FirebaseSyncLog::create([
            'type'       => 'Pull',
            'status'     => 'running',
            'started_at' => now(),
            'performed_by' => auth()->user()->name ?? 'Manual',
            'summary'    => [
                'mode'      => (isset($data['full']) && $data['full']) ? 'Full' : 'Targeted',
                'intensity' => $data['intensity'] ?? 50,
                'dry_run'   => (bool)($data['simulation'] ?? false),
                'snapshot'  => (bool)($data['snapshot'] ?? true)
            ]
        ]);

        Cache::put('firebase_sync_active', true, 7200);
        Cache::put('firebase_sync_progress', 0, 7200);
        Cache::put('firebase_sync_label', 'Preparando descarga...', 7200);

        $options = [
            '--intensity' => $data['intensity'] ?? 50,
            '--log-id'    => $log->id
        ];

        if (isset($data['afiliados']) && $data['afiliados']) $options['--affiliates'] = true;
        if (isset($data['empresas'])  && $data['empresas'])  $options['--companies']  = true;
        if (isset($data['catalogs'])  && $data['catalogs'])  $options['--catalogs']   = true;
        if (isset($data['full'])      && $data['full'])      $options['--full']        = true;
        if (isset($data['simulation'])&& $data['simulation'])$options['--dry-run']     = true;
        if (isset($data['snapshot'])  && $data['snapshot'])  $options['--snapshot']    = true;

        // Si no viene nada específico, descargar todo por defecto
        if (empty($options['--affiliates']) && empty($options['--companies']) &&
            empty($options['--catalogs'])   && empty($options['--full'])) {
            $options['--full'] = true;
        }

        \App\Jobs\FirebaseSyncJob::dispatch(
            $options, $log->id, $data['intensity'] ?? 50, 'firebase:pull-all', auth()->id()
        );

        return response()->json(['success' => true, 'log_id' => $log->id]);
    }

    public function push(Request $request)
    {
        $data = $request->isJson() ? $request->all() : $request->all();
        $isForce = (bool)($data['force'] ?? false);
        $intensity = $data['intensity'] ?? 200; // Default 200 = sin throttling significativo

        $log = FirebaseSyncLog::create([
            'type' => 'Push',
            'status' => 'running',
            'started_at' => now(),
            'performed_by' => auth()->user()->name ?? 'Manual',
            'summary' => [
                'mode' => $isForce ? 'Force-Total' : 'Targeted',
                'intensity' => $intensity,
                'dry_run' => (bool)($data['simulation'] ?? false)
            ]
        ]);

        Cache::put('firebase_sync_active', true, 7200);
        Cache::put('firebase_sync_progress', 0, 7200);
        Cache::put('firebase_sync_label', $isForce ? 'Iniciando subida TOTAL...' : 'Iniciando subida...', 7200);

        $options = [
            '--intensity' => $intensity,
            '--log-id' => $log->id
        ];

        if (isset($data['afiliados']) && $data['afiliados']) $options['--affiliates'] = true;
        if (isset($data['empresas']) && $data['empresas']) $options['--companies'] = true;
        if (isset($data['simulation']) && $data['simulation']) $options['--dry-run'] = true;
        if ($isForce) {
            $options['--force'] = true;
        }
        
        if (empty($options['--affiliates']) && empty($options['--companies'])) {
             return response()->json(['success' => false, 'error' => 'Los catálogos solo pueden ser descargados (Pull). Seleccione Afiliados o Empresas para subir.']);
        }

        \App\Jobs\FirebaseSyncJob::dispatch($options, $log->id, $intensity, 'firebase:sync-all', auth()->id());

        return response()->json(['success' => true, 'log_id' => $log->id]);
    }

    public function progress()
    {
        $active = (bool) Cache::get('firebase_sync_active', false);
        
        // Anti-desync: Si el caché dice inactivo pero hay un log corriendo, re-activar
        if (!$active) {
            $active = FirebaseSyncLog::where('status', 'running')->exists()
                   || \DB::table('jobs')->where('payload', 'like', '%FirebaseSyncJob%')->count() > 0;
            if ($active) {
                // Usar 7200 (2h) para no causar otro ghost inmediatamente
                Cache::put('firebase_sync_active', true, 7200);
            }
        }

        $progress = (int) Cache::get('firebase_sync_progress', 0);
        $label = (string) Cache::get('firebase_sync_label', '');
        $control = (string) Cache::get('firebase_sync_control', 'running');
        $liveStats = Cache::get('firebase_sync_stats', []);

        if (!$active) {
            $runningLog = FirebaseSyncLog::where('status', 'running')->latest()->first();
            if ($runningLog) {
                $active = true;
                $progress = $progress ?: 5; 
                $label = $label ?: "Detectada actividad ($runningLog->type)...";
            }
        }

        // Live Quota logic
        $dbWrites = FirebaseSyncLog::whereDate('created_at', now()->toDateString())->where('type', 'Push')->sum('items_count');
        $dbReads = FirebaseSyncLog::whereDate('created_at', now()->toDateString())->where('type', 'Pull')->sum('items_count');
        
        $currentLiveCount = 0;
        foreach ($liveStats as $m) if (is_array($m)) $currentLiveCount += ($m['created'] ?? 0) + ($m['updated'] ?? 0);

        $performedBy = 'Desconocido';
        $runningLog = FirebaseSyncLog::where('status', 'running')->latest()->first();
        if ($runningLog) {
            $performedBy = $runningLog->performed_by;
        }

        $eta = null;
        if ($active && $progress > 0 && $progress < 100) {
            $runningLog = FirebaseSyncLog::where('status', 'running')->latest()->first();
            if ($runningLog && $runningLog->started_at) {
                $elapsedSeconds = now()->diffInSeconds($runningLog->started_at);
                if ($elapsedSeconds > 5) {
                    $itemsProcessed = 0;
                    foreach ($liveStats as $m) if (is_array($m)) $itemsProcessed += ($m['created'] ?? 0) + ($m['updated'] ?? 0);
                    
                    if ($itemsProcessed > 0) {
                        $speed = $itemsProcessed / $elapsedSeconds; // items per second
                        $totalExpected = ($itemsProcessed * 100) / $progress;
                        $remainingItems = $totalExpected - $itemsProcessed;
                        $remainingSeconds = $speed > 0 ? ($remainingItems / $speed) : 0;
                        
                        if ($remainingSeconds > 0) {
                            $eta = $remainingSeconds > 60 
                                ? round($remainingSeconds / 60) . 'm' 
                                : round($remainingSeconds) . 's';
                        }
                    }
                }
            }
        }

        return response()->json([
            'active' => $active,
            'progress' => $progress,
            'label' => $label,
            'control' => $control,
            'stats' => $liveStats,
            'performedBy' => $performedBy,
            'live_feed' => Cache::get('firebase_sync_feed', []),
            'eta' => $eta,
            'quota' => [
                'writes' => $dbWrites + (Cache::get('firebase_sync_active') && FirebaseSyncLog::where('status', 'running')->where('type', 'Push')->exists() ? $currentLiveCount : 0),
                'reads' => $dbReads + (Cache::get('firebase_sync_active') && FirebaseSyncLog::where('status', 'running')->where('type', 'Pull')->exists() ? $currentLiveCount : 0),
                'read_limit' => 50000,
                'write_limit' => 20000
            ],
            'checkpoints' => \App\Models\CloudSyncCheckpoint::where('status', 'running')->get()
        ]);
    }

    /**
     * Limpiar snapshots de la base de datos
     */
    public function cleanupSnapshots()
    {
        $tables = \DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $keyName = "Tables_in_" . $dbName;
        $deletedCount = 0;

        foreach ($tables as $table) {
            $name = $table->$keyName;
            if (str_starts_with($name, 'z_backup_')) {
                \Schema::dropIfExists($name);
                $deletedCount++;
            }
        }

        auth()->user()->notify(new \App\Notifications\FirebaseSyncNotification(
            "Limpieza de Snapshots",
            "Se han purgado $deletedCount respaldos antiguos del sistema.",
            "task_alt"
        ));

        return response()->json(['success' => true, 'deleted_count' => $deletedCount]);
    }

    /**
     * Pause sync
     */
    public function pause()
    {
        Cache::put('firebase_sync_control', 'paused', 600);
        return response()->json(['success' => true]);
    }

    public function purgeCache()
    {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        
        auth()->user()->notify(new \App\Notifications\FirebaseSyncNotification(
            "Caché Purgada",
            "El motor de sincronización ha refrescado su caché local.",
            "task_alt"
        ));

        return response()->json(['success' => true]);
    }

    /**
     * Resume sync
     */
    public function resume()
    {
        Cache::put('firebase_sync_control', 'running', 600);
        return response()->json(['success' => true]);
    }

    public function cancel()
    {
        Cache::put('firebase_sync_control', 'cancelled', 600);
        Cache::forget('firebase_sync_active');
        
        // Force fully cancel in DB if job is dead/stuck
        FirebaseSyncLog::where('status', 'running')->update([
            'status' => 'failed',
            'summary' => ['error' => 'Sincronización cancelada por el usuario']
        ]);
        
        return response()->json(['success' => true]);
    }

    public function purgeQueue()
    {
        try {
            $count = \DB::table('jobs')->count();
            \DB::table('jobs')->delete();
            
            auth()->user()->notify(new \App\Notifications\FirebaseSyncNotification(
                "Cola Purgada",
                "Se han eliminado $count tareas pendientes de la cola del sistema.",
                "delete_sweep"
            ));

            return response()->json(['success' => true, 'count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function healthCheck()
    {
        $cacheKey = 'firebase_health_audit_result';
        
        return Cache::remember($cacheKey, 300, function() {
            $start = microtime(true);
            $remoteCountAfiliados = $this->syncService->getCollectionCount('afiliados');
            $remoteCountEmpresas = $this->syncService->getCollectionCount('empresas');
            
            $remoteLatestAfiliado = $this->syncService->getLatestUpdateDate('afiliados');
            $remoteLatestEmpresa = $this->syncService->getLatestUpdateDate('empresas');
            
            $latency = round((microtime(true) - $start) * 1000); 

            $localCountAfiliados = Afiliado::count();
            $localCountEmpresas = Empresa::count();
            
            $localLatestAfiliado = Afiliado::max('updated_at');
            $localLatestEmpresa = Empresa::max('updated_at');
            
            $formatDate = function($dateString) {
                if (!$dateString) return 'Nunca';
                try {
                    return \Carbon\Carbon::parse($dateString)->diffForHumans();
                } catch (\Exception $e) { return 'Desconocido'; }
            };

            $unnormalized = Afiliado::where('cedula', 'NOT REGEXP', '^[0-9]{3}-[0-9]{7}-[0-9]{1}$')->count();

            // Calcular discrepancias críticas (Muestreo rápido)
            $orphansInLocal = Afiliado::whereNull('firebase_synced_at')->limit(5)->pluck('cedula')->toArray();

            return [
                'local' => [
                    'afiliados' => $localCountAfiliados,
                    'empresas' => $localCountEmpresas,
                    'afiliados_updated' => $formatDate($localLatestAfiliado),
                    'empresas_updated' => $formatDate($localLatestEmpresa),
                    'orphans' => $orphansInLocal
                ],
                'remote' => [
                    'afiliados' => $remoteCountAfiliados,
                    'empresas' => $remoteCountEmpresas,
                    'afiliados_updated' => $formatDate($remoteLatestAfiliado),
                    'empresas_updated' => $formatDate($remoteLatestEmpresa),
                ],
                'diff' => [
                    'afiliados' => $remoteCountAfiliados - $localCountAfiliados,
                    'empresas' => $remoteCountEmpresas - $localCountEmpresas,
                ],
                'health' => [
                    'unnormalized' => $unnormalized,
                    'latency' => $latency,
                    'status' => $latency < 500 ? 'optimal' : ($latency < 1000 ? 'degraded' : 'critical')
                ]
            ];
        });
    }

    /**
     * Lanzar una reconciliación dirigida basada en el gap detectado
     */
    public function reconcile(Request $request)
    {
        $type = $request->get('type', 'afiliados');
        
        // Creamos un log para esta operación quirúrgica
        $log = FirebaseSyncLog::create([
            'type' => 'Pull',
            'status' => 'running',
            'performed_by' => auth()->user()->name ?? 'Sistema (Auto-Fix)',
            'items_count' => 0,
            'summary' => ['mode' => 'Reconciliación Quirúrgica']
        ]);

        // Lanzamos el nuevo JOB específico para reconciliación
        \App\Jobs\FirebaseReconcileJob::dispatch($type, $log->id);

        return response()->json(['success' => true, 'log_id' => $log->id]);
    }

    /**
     * Comparar un registro local vs Firebase
     */
    public function compare(Request $request)
    {
        try {
            $id = $request->get('id');
            $type = $request->get('type', 'afiliado'); // 'afiliado' o 'empresa'
            
            if (!$id) return response()->json(['success' => false, 'error' => 'ID no proporcionado']);

            $local = null;
            $remote = null;
            $collection = ($type === 'afiliado') ? 'afiliados' : 'empresas';

            if ($type === 'afiliado') {
                $local = \App\Models\Afiliado::where('cedula', $id)->first();
                $remote = $this->syncService->getDocument($collection, $id);
            } else {
                $local = \App\Models\Empresa::where('rnc', $id)->first();
                if (!$local && strlen($id) > 15) $local = \App\Models\Empresa::where('uuid', $id)->first();
                
                $remoteId = $local ? $local->uuid : $id;
                $remote = $this->syncService->getDocument($collection, $remoteId);
            }

            return response()->json([
                'success' => true,
                'local' => $local,
                'remote' => $remote,
                'type' => $type
            ]);
        } catch (\Exception $e) {
            \Log::error("FirebaseSyncController@compare error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Error técnico al comparar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar snapshots disponibles
     */
    public function listSnapshots()
    {
        $tables = \DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $keyName = "Tables_in_" . $dbName;
        $snapshots = [];

        foreach ($tables as $table) {
            $name = $table->$keyName;
            if (str_starts_with($name, 'z_backup_')) {
                $snapshots[] = [
                    'table' => $name,
                    'size' => \DB::table($name)->count()
                ];
            }
        }

        return response()->json(['success' => true, 'snapshots' => $snapshots]);
    }

    /**
     * Restaurar un snapshot
     */
    public function restoreSnapshot(Request $request)
    {
        $table = $request->get('table');
        if (!$table || !str_starts_with($table, 'z_backup_')) {
            return response()->json(['success' => false, 'error' => 'Tabla de respaldo no válida']);
        }

        try {
            $target = str_contains($table, 'afiliados') ? 'afiliados' : (str_contains($table, 'empresas') ? 'empresas' : null);
            if (!$target) return response()->json(['success' => false, 'error' => 'No se pudo identificar la tabla destino']);

            \DB::statement("TRUNCATE TABLE {$target}");
            \DB::statement("INSERT INTO {$target} SELECT * FROM {$table}");

            auth()->user()->notify(new \App\Notifications\FirebaseSyncNotification(
                "Restauración Exitosa",
                "El sistema se ha revertido a la versión del backup: $table",
                "task_alt"
            ));

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            auth()->user()->notify(new \App\Notifications\FirebaseSyncNotification(
                "Fallo en Restauración",
                "Error crítico al intentar restaurar $table: " . $e->getMessage(),
                "error"
            ));
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Display synchronized records with advanced filters
     */
    public function records(Request $request)
    {
        $query = Afiliado::query();

        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre_completo', 'like', '%' . $request->q . '%')
                  ->orWhere('cedula', 'like', '%' . $request->q . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('sync_status', $request->status);
        }

        $records = $query->orderBy('firebase_synced_at', 'desc')->paginate(20);

        return view('firebase.records', compact('records'));
    }

    /**
     * Display sync conflicts
     */
    public function conflicts()
    {
        $conflicts = Afiliado::where('sync_status', 'conflict')->paginate(20);
        return view('firebase.conflicts', compact('conflicts'));
    }
}
