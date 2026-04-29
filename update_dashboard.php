<?php
$file = 'app/Http/Controllers/DashboardController.php';
$content = file_get_contents($file);

$prefix = '        $cachePrefix = (auth()->check() && auth()->user()->responsable_id && !auth()->user()->hasRole(['Admin'])) ? "rep_" . auth()->user()->responsable_id . "_" : "global_";' . PHP_EOL;

$content = str_replace(
    '        $ttl = 300; // 5 minutos de Caché Térmica',
    '        $ttl = 300; // 5 minutos de Caché Térmica' . PHP_EOL . $prefix,
    $content
);

$content = str_replace("'dashboard_", "\$cachePrefix . 'dashboard_", $content);

file_put_contents($file, $content);
echo "Dashboard updated.\n";
