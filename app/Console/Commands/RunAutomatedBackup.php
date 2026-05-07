<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BackupSetting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class RunAutomatedBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:automated';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Executes an automated database backup according to settings.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = BackupSetting::first();
        if (!$settings || !$settings->is_automated) {
            $this->info("Automated backups are disabled. Aborting.");
            return;
        }

        $this->info("Starting automated backup...");

        $path = $settings->custom_path ?: Storage::disk('local')->path('backups');
        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $filename = 'backup_auto_' . date('Y-m-d_H-i-s') . '.sql';
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
                \Log::error("Automated Backup failed. Command: {$command} | Output: " . implode("\n", $output));
                $this->error("Automated Backup failed.");
                return;
            }

            if (class_exists(\App\Models\AuditLog::class)) {
                \App\Models\AuditLog::create([
                    'user_id' => null, // System
                    'event' => 'BACKUP_CREATED',
                    'model_type' => 'Database',
                    'model_id' => '0',
                    'new_values' => ['file' => $filename, 'type' => 'automated'],
                    'ip_address' => '127.0.0.1'
                ]);
            }

            $this->cleanOldBackups($settings, $path);

            $this->info("Backup {$filename} created successfully.");

        } catch (\Exception $e) {
            \Log::error("Automated Backup exception: " . $e->getMessage());
            $this->error("Exception: " . $e->getMessage());
        }
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

            $toDelete = array_slice($backups, $settings->max_backups);
            foreach ($toDelete as $file) {
                File::delete($file->getPathname());
            }
        }
    }
}
