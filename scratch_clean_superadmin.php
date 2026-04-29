<?php

$dir = __DIR__;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isDir()) continue;
    
    $path = $file->getRealPath();
    if (str_contains($path, 'vendor') || str_contains($path, '.git') || str_contains($path, 'node_modules')) continue;
    if (pathinfo($path, PATHINFO_EXTENSION) !== 'php' && pathinfo($path, PATHINFO_EXTENSION) !== 'blade.php') continue;

    $content = file_get_contents($path);
    $original = $content;

    // Reemplazos comunes
    $content = str_replace(['Admin', 'Admin'], 'Admin', $content);
    $content = str_replace([''Admin'', "'Admin'"], "'Admin'", $content);
    $content = str_replace([''Admin'', "'Admin'"], "'Admin'", $content);
    $content = str_replace('Admin', $content);

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Modificado: $path\n";
    }
}

echo "Limpieza de código completada.\n";
