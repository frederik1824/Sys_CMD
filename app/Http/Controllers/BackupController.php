<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;
use App\Models\BackupSetting;

class BackupController extends Controller
{
    protected $disk = 'local';
    protected $backupPath = 'backups';

    private function getSettings()
    {
        return BackupSetting::firstOrCreate(
            ['id' => 1],
            ['is_automated' => false, 'schedule_frequency' => 'daily', 'schedule_time' => '02:00', 'max_backups' => 5, 'custom_path' => null]
        );
    }

    public function index()
    {
        $settings = $this->getSettings();
        
        // Determinar path
        $path = $settings->custom_path ?: Storage::disk($this->disk)->path($this->backupPath);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $files = File::files($path);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'path' => $file->getPathname(),
                    'size' => $this->formatSize($file->getSize()),
                    'date' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                    'timestamp' => $file->getMTime()
                ];
            }
        }

        // Sort by newest first
        usort($backups, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return view('sistema.backups.index', compact('backups', 'settings'));
    }

    public function saveSettings(Request $request)
    {
        $request->validate([
            'is_automated' => 'boolean',
            'schedule_frequency' => 'required|in:daily,weekly,monthly',
            'schedule_time' => 'required',
            'max_backups' => 'required|integer|min:1|max:50',
            'custom_path' => 'nullable|string'
        ]);

        $settings = $this->getSettings();
        $settings->update([
            'is_automated' => $request->has('is_automated'),
            'schedule_frequency' => $request->schedule_frequency,
            'schedule_time' => $request->schedule_time,
            'max_backups' => $request->max_backups,
            'custom_path' => $request->custom_path,
        ]);

        // Validate custom path
        if ($settings->custom_path && !File::exists($settings->custom_path)) {
            try {
                File::makeDirectory($settings->custom_path, 0755, true);
            } catch (\Exception $e) {
                return back()->with('error', 'No se pudo crear la ruta personalizada de respaldos. Verifica los permisos.');
            }
        }

        return back()->with('success', 'Configuración de copias de seguridad actualizada.');
    }

    public function create()
    {
        $settings = $this->getSettings();
        $path = $settings->custom_path ?: Storage::disk($this->disk)->path($this->backupPath);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $fullPath = $path . DIRECTORY_SEPARATOR . $filename;

        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $passwordArg = $dbPass ? "-p{$dbPass}" : "";
        $dumpBinary = 'mysqldump'; 

        $fullPathStr = str_replace('/', DIRECTORY_SEPARATOR, $fullPath);
        $command = "{$dumpBinary} -h {$dbHost} -P {$dbPort} -u {$dbUser} {$passwordArg} {$dbName} > \"{$fullPathStr}\"";

        try {
            exec($command . ' 2>&1', $output, $returnVar);

            if ($returnVar !== 0) {
                $isRecognizedError = implode(" ", $output);
                if (str_contains(strtolower($isRecognizedError), 'not recognized')) {
                    $command = "C:\\laragon\\bin\\mysql\\mysql-5.7.33-winx64\\bin\\mysqldump.exe -h {$dbHost} -P {$dbPort} -u {$dbUser} {$passwordArg} {$dbName} > \"{$fullPathStr}\"";
                    exec($command . ' 2>&1', $output, $returnVar);
                }
            }

            if ($returnVar !== 0) {
                \Log::error("Backup failed. Command: {$command} | Output: " . implode("\n", $output));
                return response()->json([
                    'success' => false, 
                    'message' => 'Error al generar la copia de seguridad. Verifique los logs.'
                ], 500);
            }

            if (class_exists(\App\Models\AuditLog::class) && auth()->check()) {
                \App\Models\AuditLog::create([
                    'user_id' => auth()->id(),
                    'event' => 'BACKUP_CREATED',
                    'model_type' => 'Database',
                    'model_id' => '0',
                    'new_values' => ['file' => $filename],
                    'ip_address' => request()->ip()
                ]);
            }

            // Clean old backups
            $this->cleanOldBackups($settings, $path);

            return response()->json([
                'success' => true,
                'message' => 'Copia de seguridad generada con éxito.',
                'filename' => $filename
            ]);

        } catch (\Exception $e) {
            \Log::error("Backup exception: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Excepción al generar la copia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($name)
    {
        $settings = $this->getSettings();
        $path = $settings->custom_path ?: Storage::disk($this->disk)->path($this->backupPath);
        $fullPath = $path . DIRECTORY_SEPARATOR . $name;
        
        if (!File::exists($fullPath)) {
            abort(404, "El archivo de copia de seguridad no existe.");
        }

        if (class_exists(\App\Models\AuditLog::class) && auth()->check()) {
            \App\Models\AuditLog::create([
                'user_id' => auth()->id(),
                'event' => 'BACKUP_DOWNLOADED',
                'model_type' => 'Database',
                'model_id' => '0',
                'new_values' => ['file' => $name],
                'ip_address' => request()->ip()
            ]);
        }

        return response()->download($fullPath);
    }

    public function destroy($name)
    {
        $settings = $this->getSettings();
        $path = $settings->custom_path ?: Storage::disk($this->disk)->path($this->backupPath);
        $fullPath = $path . DIRECTORY_SEPARATOR . $name;
        
        if (File::exists($fullPath)) {
            File::delete($fullPath);
            
            if (class_exists(\App\Models\AuditLog::class) && auth()->check()) {
                \App\Models\AuditLog::create([
                    'user_id' => auth()->id(),
                    'event' => 'BACKUP_DELETED',
                    'model_type' => 'Database',
                    'model_id' => '0',
                    'old_values' => ['file' => $name],
                    'ip_address' => request()->ip()
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Copia de seguridad eliminada.']);
        }

        return response()->json(['success' => false, 'message' => 'El archivo no existe.'], 404);
    }

    private function cleanOldBackups($settings, $path)
    {
        $files = File::files($path);
        $backups = [];
        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $backups[] = $file;
            }
        }

        if (count($backups) > $settings->max_backups) {
            usort($backups, function($a, $b) {
                return $b->getMTime() <=> $a->getMTime();
            });

            // Keep only max_backups
            $toDelete = array_slice($backups, $settings->max_backups);
            foreach ($toDelete as $file) {
                File::delete($file->getPathname());
            }
        }
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
