<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Models\Responsable;
use App\Services\FirebaseSyncService;

$empresaNombre = 'CONSORCIO AZUCARERO CENTRAL S A';
$rnc = '101809302';
$newResponsableId = 2; // SAFESURE

echo "Starting re-assignment for company: {$empresaNombre}...\n";

$afiliados = Afiliado::withoutGlobalScopes()
    ->where(function($q) use ($empresaNombre, $rnc) {
        $q->where('empresa', $empresaNombre)
          ->orWhere('rnc_empresa', $rnc);
    })->get();

$count = $afiliados->count();
echo "Found {$count} affiliates to re-assign.\n";

$service = new FirebaseSyncService();

foreach ($afiliados as $af) {
    echo "Processing [{$af->cedula}] {$af->nombre_completo}...\n";
    
    // Update locally
    $af->responsable_id = $newResponsableId;
    $af->reasignado = true;
    $af->save();
    
    // Push to Firebase
    $data = $af->toArray();
    $success = $service->push('afiliados', $af->cedula, $data);
    
    if ($success) {
        echo "  - Local & Firebase UPDATED.\n";
    } else {
        echo "  - Local updated, but Firebase FAILED.\n";
    }
}

echo "Correction finished successfully.\n";
