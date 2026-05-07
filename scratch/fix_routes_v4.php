<?php

$directories = [
    __DIR__ . '/../resources/views',
    __DIR__ . '/../app/Http/Controllers'
];

$replacements = [
    "route('carnetizacion.callcenter." => "route('callcenter.",
    "route('carnetizacion.logistica." => "route('logistica.",
    "route('carnetizacion.mensajeros." => "route('mensajeros.",
    "route('carnetizacion.rutas." => "route('rutas.",
    "route('carnetizacion.despachos." => "route('despachos.",
    "route('carnetizacion.lotes." => "route('lotes.",
    "route('carnetizacion.evidencias." => "route('evidencias.",
    "route('carnetizacion.cierre." => "route('cierre.",
    "route('carnetizacion.liquidacion." => "route('liquidacion.",
    
    // Also cover potential 'routeIs' calls
    "routeIs('carnetizacion.callcenter." => "routeIs('callcenter.",
    "routeIs('carnetizacion.logistica." => "routeIs('logistica.",
    "routeIs('carnetizacion.mensajeros." => "routeIs('mensajeros.",
    "routeIs('carnetizacion.rutas." => "routeIs('rutas.",
    "routeIs('carnetizacion.despachos." => "routeIs('despachos.",
    "routeIs('carnetizacion.lotes." => "routeIs('lotes.",
    "routeIs('carnetizacion.evidencias." => "routeIs('evidencias.",
    "routeIs('carnetizacion.cierre." => "routeIs('cierre.",
    "routeIs('carnetizacion.liquidacion." => "routeIs('liquidacion.",

    // Also cover direct strings used in redirects or other places
    "'carnetizacion.callcenter." => "'callcenter.",
    "'carnetizacion.logistica." => "'logistica.",
    "'carnetizacion.mensajeros." => "'mensajeros.",
    "'carnetizacion.rutas." => "'rutas.",
    "'carnetizacion.despachos." => "'despachos.",
    "'carnetizacion.lotes." => "'lotes.",
    "'carnetizacion.evidencias." => "'evidencias.",
    "'carnetizacion.cierre." => "'cierre.",
    "'carnetizacion.liquidacion." => "'liquidacion.",

    '"carnetizacion.callcenter.' => '"callcenter.',
    '"carnetizacion.logistica.' => '"logistica.',
    '"carnetizacion.mensajeros.' => '"mensajeros.',
    '"carnetizacion.rutas.' => '"rutas.',
    '"carnetizacion.despachos.' => '"despachos.',
    '"carnetizacion.lotes.' => '"lotes.',
    '"carnetizacion.evidencias.' => '"evidencias.',
    '"carnetizacion.cierre.' => '"cierre.',
    '"carnetizacion.liquidacion.' => '"liquidacion.',
];

$count = 0;

function processDirectory($dir, $replacements, &$count) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            processDirectory($path, $replacements, $count);
        } elseif (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($path);
            $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
            if ($content !== $newContent) {
                file_put_contents($path, $newContent);
                echo "Updated: $path\n";
                $count++;
            }
        }
    }
}

foreach ($directories as $dir) {
    processDirectory($dir, $replacements, $count);
}

echo "Done. Updated $count files.\n";
