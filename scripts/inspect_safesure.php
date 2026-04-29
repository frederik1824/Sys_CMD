<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Services\FirebaseSyncService;

$af = Afiliado::whereHas('responsable', fn($q) => $q->where('nombre', 'SAFESURE'))->first();

if (!$af) {
    echo "No local SAFESURE affiliate found.\n";
    exit;
}

echo "Found local SAFESURE affiliate: {$af->cedula} ({$af->nombre_completo})\n";

$service = new FirebaseSyncService();
$doc = $service->getDocument('afiliados', $af->cedula);

if (!$doc) {
    echo "Not found in Firebase.\n";
} else {
    echo "Firebase Data:\n";
    print_r($doc);
}
