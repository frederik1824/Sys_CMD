<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Models\Corte;

$rnc = '130940632';

$byCorte = Afiliado::where('rnc_empresa', $rnc)
    ->select('corte_id', \DB::raw('count(*) as qty'))
    ->groupBy('corte_id')
    ->get();

echo "Corte breakdown for RNC {$rnc}:\n";
foreach ($byCorte as $item) {
    if ($item->corte_id) {
        $corte = Corte::find($item->corte_id);
        $nombre = $corte ? $corte->nombre : "ID: {$item->corte_id}";
        echo " - Corte: {$nombre} -> {$item->qty} registros\n";
    } else {
        echo " - Sin Corte -> {$item->qty} registros\n";
    }
}
