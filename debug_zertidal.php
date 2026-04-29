<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empresa;
use App\Models\Afiliado;

$empresa = Empresa::where('nombre', 'like', '%ZERTIDAL%')->first();
if ($empresa) {
    echo "Empresa: {$empresa->nombre} (ID: {$empresa->id})\n";
    echo "Verificada: " . ($empresa->es_verificada ? 'SI' : 'NO') . "\n";
    
    $count = Afiliado::where('empresa_id', $empresa->id)
        ->whereNull('fecha_entrega_safesure')
        ->count();
    echo "Afiliados Pendientes: {$count}\n";
} else {
    echo "ERROR: Empresa no encontrada.\n";
}
