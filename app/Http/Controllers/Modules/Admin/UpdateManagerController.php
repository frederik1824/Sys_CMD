<?php

namespace App\Http\Controllers\Modules\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemUpdate;
use App\Models\SystemBackup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use ZipArchive;

class UpdateManagerController extends Controller
{
    public function index()
    {
        $updates = SystemUpdate::with('executor')->orderBy('build_number', 'desc')->get();
        $backups = SystemBackup::with('creator')->orderBy('created_at', 'desc')->get();
        $currentVersion = $updates->where('status', 'success')->first();
        $requirements = $this->checkRequirements();
        $cronStatus = $this->getCronStatus();
        $activeSessions = $this->getActiveSessionsCount();
        
        // Obtener archivos generados en /releases
        $releasesPath = base_path('releases');
        $generatedReleases = [];
        if (File::exists($releasesPath)) {
            $files = File::files($releasesPath);
            foreach ($files as $file) {
                if ($file->getExtension() === 'zip') {
                    $generatedReleases[] = [
                        'name' => $file->getFilename(),
                        'size' => round($file->getSize() / 1024 / 1024, 2) . ' MB',
                        'date' => date('d/m/Y H:i', $file->getMTime()),
                        'timestamp' => $file->getMTime()
                    ];
                }
            }
            // Ordenar por fecha descendente
            usort($generatedReleases, function($a, $b) {
                return $b['timestamp'] - $a['timestamp'];
            });
        }

        return view('modules.admin.updates.index', compact(
            'updates', 'backups', 'currentVersion', 
            'generatedReleases', 'requirements', 'cronStatus', 'activeSessions'
        ));
    }

    public function pack(Request $request)
    {
        try {
            $version = $request->input('version');
            $changelog = $request->input('changelog', 'Actualización general del sistema.');

            // Ejecutar el comando artisan
            $exitCode = Artisan::call('release:pack', [
                '--ver' => $version
            ]);

            if ($exitCode === 0) {
                return response()->json(['success' => true, 'message' => 'Paquete generado exitosamente en /releases']);
            }

            throw new \Exception("Error al ejecutar el empaquetador.");
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function downloadRelease($filename)
    {
        $path = base_path('releases/' . $filename);
        if (File::exists($path)) {
            return response()->download($path);
        }
        abort(404);
    }

    public function createBackup(Request $request)
    {
        try {
            $filename = 'backup_' . now()->format('Y_m_d_His') . '.sql';
            $path = 'backups/' . $filename;
            
            if (!Storage::disk('local')->exists('backups')) {
                Storage::disk('local')->makeDirectory('backups');
            }

            // Comando para mysqldump
            $dbConfig = config('database.connections.mysql');
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['database']),
                Storage::disk('local')->path($path)
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception("Fallo al ejecutar mysqldump. Verifica permisos.");
            }

            $backup = SystemBackup::create([
                'filename' => $filename,
                'path' => $path,
                'type' => 'manual',
                'size_bytes' => Storage::disk('local')->size($path),
                'status' => 'success',
                'created_by' => auth()->id(),
            ]);

            $this->cleanupOldBackups();

            return response()->json(['success' => true, 'message' => 'Snapshot creado correctamente.', 'backup' => $backup]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function rollback(Request $request, SystemBackup $backup)
    {
        try {
            if (!Storage::disk('local')->exists($backup->path)) {
                throw new \Exception("El archivo de backup no existe físicamente.");
            }

            // 1. Bloqueo
            \Illuminate\Support\Facades\Cache::put('system_update_lock', true, now()->addMinutes(10));

            // 2. Restaurar DB
            $dbConfig = config('database.connections.mysql');
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < %s',
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['database']),
                Storage::disk('local')->path($backup->path)
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception("Fallo al restaurar la base de datos.");
            }

            // 3. Limpiar Caché
            Artisan::call('optimize:clear');

            // 4. Desbloqueo
            \Illuminate\Support\Facades\Cache::forget('system_update_lock');

            return response()->json(['success' => true, 'message' => 'Sistema restaurado con éxito al punto: ' . $backup->created_at->format('d/m/Y H:i')]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Cache::forget('system_update_lock');
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function cleanupOldBackups()
    {
        $oldBackups = SystemBackup::orderBy('created_at', 'desc')->skip(15)->get();
        foreach ($oldBackups as $old) {
            Storage::disk('local')->delete($old->path);
            $old->delete();
        }
    }

    public function uploadUpdate(Request $request)
    {
        $request->validate([
            'update_file' => 'required|file|mimes:zip|max:102400', // 100MB
        ]);

        try {
            if (!Storage::disk('local')->exists('updates_temp')) {
                Storage::disk('local')->makeDirectory('updates_temp');
            }

            $file = $request->file('update_file');
            $path = $file->store('updates_temp', 'local');
            
            // Aquí iría la lógica de validación de integridad (SHA256, etc)
            
            return response()->json([
                'success' => true, 
                'message' => 'Paquete recibido y validado. Listo para aplicar.',
                'temp_path' => $path
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function applyUpdate(Request $request)
    {
        $tempPath = $request->input('temp_path');
        $logs = [];
        
        if (!Storage::disk('local')->exists($tempPath)) {
            return response()->json(['success' => false, 'message' => 'Archivo temporal no encontrado.'], 404);
        }

        try {
            // 1. Bloqueo
            \Illuminate\Support\Facades\Cache::put('system_update_lock', true, now()->addMinutes(30));
            $logs[] = "🔒 Sistema bloqueado para mantenimiento.";

            // 2. Backup de Base de Datos
            if (!$request->has('skip_backup')) {
                $logs[] = "🗄️ Iniciando backup de base de datos...";
                $backupResponse = $this->createBackup($request);
                if (!$backupResponse->getData()->success) {
                    throw new \Exception("Fallo al crear el backup preventivo: " . $backupResponse->getData()->message);
                }
                $logs[] = "✅ Backup de DB completado.";
            }

            // 3. NUEVO: Backup de Código Fuente (Snapshot)
            $logs[] = "📦 Creando snapshot del código fuente actual...";
            $codeBackupPath = $this->createCodeSnapshot();
            $logs[] = "✅ Snapshot de código guardado en storage/app/$codeBackupPath";

            // 4. Extraer ZIP
            $logs[] = "📂 Extrayendo paquete de actualización...";
            $zipPath = Storage::disk('local')->path($tempPath);
            $extractPath = storage_path('app/updates_extract_' . time());
            
            $zip = new ZipArchive;
            if ($zip->open($zipPath) === TRUE) {
                $zip->extractTo($extractPath);
                $zip->close();
            } else {
                throw new \Exception("No se pudo abrir el archivo ZIP.");
            }
            $logs[] = "✅ Paquete extraído correctamente.";

            // 5. Sincronización de archivos
            $logs[] = "🚀 Sincronizando archivos al núcleo...";
            $this->recursiveCopy($extractPath, base_path());
            $logs[] = "✅ Archivos actualizados.";

            // 6. Migraciones y Limpieza
            $logs[] = "⚙️ Ejecutando migraciones de base de datos...";
            Artisan::call('migrate', ['--force' => true]);
            $logs[] = "🧹 Limpiando caché del sistema...";
            Artisan::call('optimize:clear');
            $logs[] = "✅ Tareas de mantenimiento terminadas.";

            // 7. Registro
            $versionInfo = json_decode(file_get_contents(base_path('version.json')), true);
            SystemUpdate::create([
                'version' => $versionInfo['version'] ?? 'unknown',
                'build_number' => $versionInfo['build'] ?? time(),
                'type' => 'patch',
                'status' => 'success',
                'completed_at' => now(),
                'executed_by' => auth()->id(),
            ]);

            \Illuminate\Support\Facades\Cache::forget('system_update_lock');
            $logs[] = "🔓 Sistema desbloqueado. Proceso finalizado.";

            return response()->json([
                'success' => true, 
                'message' => 'Actualización aplicada con éxito.',
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Cache::forget('system_update_lock');
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'logs' => $logs], 500);
        }
    }

    private function createCodeSnapshot()
    {
        $filename = 'code_snapshot_' . now()->format('Y_m_d_His') . '.zip';
        $path = 'backups/' . $filename;
        $fullPath = storage_path('app/' . $path);

        if (!is_dir(storage_path('app/backups'))) mkdir(storage_path('app/backups'), 0755, true);

        $zip = new ZipArchive;
        if ($zip->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $folders = ['app', 'resources', 'routes', 'config', 'public/js', 'public/css'];
            foreach ($folders as $folder) {
                $folderPath = base_path($folder);
                if (is_dir($folderPath)) {
                    $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folderPath), \RecursiveIteratorIterator::LEAVES_ONLY);
                    foreach ($files as $name => $file) {
                        if (!$file->isDir()) {
                            $filePath = $file->getRealPath();
                            $relativePath = substr($filePath, strlen(base_path()) + 1);
                            $zip->addFile($filePath, $relativePath);
                        }
                    }
                }
            }
            $zip->close();
            
            SystemBackup::create([
                'filename' => $filename,
                'path' => $path,
                'type' => 'code', // Nuevo tipo
                'size_bytes' => filesize($fullPath),
                'status' => 'success',
                'created_by' => auth()->id(),
            ]);

            return $path;
        }
        throw new \Exception("No se pudo crear el snapshot de código.");
    }

    private function checkRequirements()
    {
        $freeSpace = disk_free_space(base_path());
        return [
            'php' => [
                'version' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '8.2.0', '>=') ? 'ok' : 'warning',
                'label' => 'PHP 8.2+'
            ],
            'extensions' => [
                'zip' => extension_loaded('zip'),
                'gd' => extension_loaded('gd'),
                'pdo' => extension_loaded('pdo_mysql'),
            ],
            'disk' => [
                'free' => round($freeSpace / 1024 / 1024 / 1024, 2) . ' GB',
                'status' => $freeSpace > (500 * 1024 * 1024) ? 'ok' : 'danger', // Mínimo 500MB
            ],
            'writable' => [
                'storage' => is_writable(storage_path()),
                'bootstrap' => is_writable(base_path('bootstrap/cache')),
            ]
        ];
    }

    public function getLogs()
    {
        $logPath = storage_path('logs/laravel.log');
        if (!File::exists($logPath)) return response()->json(['logs' => 'No hay logs registrados.']);

        $file = file($logPath);
        $lastLines = array_slice($file, -100); // Últimas 100 líneas
        return response()->json(['logs' => implode("", $lastLines)]);
    }

    public function purgeSystem()
    {
        try {
            // 1. Artisan Clear
            Artisan::call('optimize:clear');
            
            // 2. Limpiar carpetas temporales
            $tempDirs = [
                storage_path('app/updates_extract_*'),
            ];
            foreach ($tempDirs as $pattern) {
                foreach (glob($pattern) as $dir) {
                    File::deleteDirectory($dir);
                }
            }

            return response()->json(['success' => true, 'message' => 'Sistema optimizado y archivos temporales purgados.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function getCronStatus()
    {
        $lastRun = Cache::get('cron_last_heartbeat');
        if (!$lastRun) return ['status' => 'offline', 'label' => 'No detectado'];
        
        $diff = now()->diffInMinutes($lastRun);
        return [
            'status' => $diff < 5 ? 'online' : 'warning',
            'label' => $diff < 5 ? 'Activo' : 'Retrasado (' . $diff . ' min)',
            'last_run' => $lastRun->format('H:i:s')
        ];
    }

    private function getActiveSessionsCount()
    {
        // Si usamos file driver
        if (config('session.driver') === 'file') {
            return count(File::files(storage_path('framework/sessions')));
        }
        // Si usamos DB
        if (config('session.driver') === 'database') {
            return DB::table('sessions')->count();
        }
        return 0;
    }

    private function recursiveCopy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}
