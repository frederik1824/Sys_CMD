<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZipArchive;
use Illuminate\Support\Facades\File;

class ReleasePackerCommand extends Command
{
    protected $signature = 'release:pack {--ver= : Especificar versión manual}';
    protected $description = 'Empaqueta la aplicación para distribución on-premise';

    public function handle()
    {
        $this->info('🚀 Iniciando empaquetado de SysCarnet Release...');

        // 1. Actualizar version.json
        $versionPath = base_path('version.json');
        $versionData = json_decode(File::get($versionPath), true);
        
        $newVersion = $this->option('ver') ?: $versionData['version'];
        $newBuild = $versionData['build'] + 1;
        
        $versionData['version'] = $newVersion;
        $versionData['build'] = $newBuild;
        $versionData['last_update'] = now()->toDateTimeString();
        
        File::put($versionPath, json_encode($versionData, JSON_PRETTY_PRINT));
        $this->comment("Versión actualizada a: $newVersion (Build: $newBuild)");

        // 2. Definir nombre del archivo
        $zipName = "SysCarnet_Release_v{$newVersion}_b{$newBuild}.zip";
        $zipPath = base_path('releases/' . $zipName);

        if (!File::exists(base_path('releases'))) {
            File::makeDirectory(base_path('releases'));
        }

        // 3. Crear ZIP
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            
            $foldersToInclude = [
                'app', 'bootstrap', 'config', 'database', 
                'public', 'resources', 'routes', 'lang'
            ];

            $filesToInclude = [
                'composer.json', 'composer.lock', 'package.json', 
                'artisan', 'version.json', 'vite.config.js'
            ];

            // Añadir Carpetas
            foreach ($foldersToInclude as $folder) {
                if (File::exists(base_path($folder))) {
                    $this->addFolderToZip($zip, base_path($folder), $folder);
                }
            }

            // Añadir Archivos
            foreach ($filesToInclude as $file) {
                if (File::exists(base_path($file))) {
                    $zip->addFile(base_path($file), $file);
                }
            }

            $zip->close();
            $this->info("✅ Release generado con éxito: releases/$zipName");
            $this->comment("Tamaño: " . round(filesize($zipPath) / 1024 / 1024, 2) . " MB");
        } else {
            $this->error('❌ Falló la creación del archivo ZIP.');
        }
    }

    private function addFolderToZip($zip, $folderPath, $zipPath)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($folderPath) + 1);
                
                // Excluir archivos específicos si es necesario
                if (str_contains($relativePath, '.sql') || str_contains($relativePath, 'node_modules')) {
                    continue;
                }

                $zip->addFile($filePath, $relativePath);
            }
        }
    }
}
