<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$rnc = '130940632';

$byEmpresaName = Afiliado::where('rnc_empresa', $rnc)
    ->select('empresa', \DB::raw('count(*) as qty'))
    ->groupBy('empresa')
    ->get();

echo "Empresa Name breakdown for RNC {$rnc}:\n";
foreach ($byEmpresaName as $item) {
    $name = $item->empresa ?: 'SIN NOMBRE';
    echo " - Nombre en Excel: {$name} -> {$item->qty} registros\n";
}
