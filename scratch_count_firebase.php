<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$sync = app(FirebaseSyncService::class);
$docs = $sync->getCollection('empresas');

$rCount = 0;
$vCount = 0;

foreach ($docs as $d) {
    if (isset($d['es_real']) && $d['es_real'] == true) $rCount++;
    if (isset($d['es_verificada']) && $d['es_verificada'] == true) $vCount++;
}

echo "Firebase - Real: $rCount | Verificada: $vCount\n";
