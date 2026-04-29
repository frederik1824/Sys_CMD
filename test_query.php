<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$count = Afiliado::whereHas('empresaModel', function($q){
    $q->where('es_verificada', true);
})->whereNull('fecha_entrega_safesure')->count();

echo "TOTAL SALIDA INMEDIATA: {$count}\n";

$afiliados = Afiliado::whereHas('empresaModel', function($q){
    $q->where('es_verificada', true);
})->whereNull('fecha_entrega_safesure')->limit(5)->get();

foreach ($afiliados as $af) {
    echo "- {$af->nombre_completo} (Empresa: " . ($af->empresaModel->nombre ?? 'N/A') . ")\n";
}
