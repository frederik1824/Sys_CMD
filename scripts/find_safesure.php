<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empresa;

$safesures = Empresa::where('nombre', 'like', '%SAFE%')->get();
foreach($safesures as $e) {
    echo "Nombre: {$e->nombre} | RNC: {$e->rnc} | UUID: {$e->uuid}\n";
}
