<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empresa;
use App\Models\Afiliado;

$empresa = Empresa::where('nombre', 'like', '%SEITER%')->first();
if ($empresa) {
    $empresa->es_verificada = true;
    $empresa->save();
    
    $afiliados = Afiliado::where('empresa_id', $empresa->id)
        ->whereNull('fecha_entrega_safesure')
        ->get();
    
    foreach ($afiliados as $af) {
        if ($af->estado_id != 9) {
            $af->estado_id = 9;
            $af->save();
        }
    }
    echo "SUCCESS: SEITER SRL verified and affiliates updated to Completado.\n";
} else {
    echo "ERROR: SEITER SRL not found.\n";
}
