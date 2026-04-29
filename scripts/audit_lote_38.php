<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$lote_id = 38;

$stats = Afiliado::where('lote_id', $lote_id)
    ->select('empresa', \DB::raw('count(*) as qty'))
    ->groupBy('empresa')
    ->get();

echo "Empresa distribution for Lote #{$lote_id}:\n";
foreach($stats as $s) {
    echo " - " . ($s->empresa ?: 'NULL') . " -> {$s->qty} registros\n";
}
