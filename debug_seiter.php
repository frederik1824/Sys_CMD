<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empresa;
use App\Models\Afiliado;

$empresa = Empresa::where('nombre', 'like', '%SEITER%')->first();
if ($empresa) {
    echo "Empresa: {$empresa->nombre} (ID: {$empresa->id})\n";
    echo "Es Verificada: " . ($empresa->es_verificada ? 'SI' : 'NO') . "\n";
    
    $afiliados = Afiliado::where('empresa_id', $empresa->id)
        ->whereNull('fecha_entrega_safesure')
        ->get();
        
    echo "Afiliados Pendientes: " . $afiliados->count() . "\n";
    foreach ($afiliados as $af) {
        echo "- {$af->nombre_completo} (ID: {$af->id}) Estado: {$af->estado->nombre}\n";
    }
} else {
    echo "Empresa SEITER no encontrada.\n";
}
