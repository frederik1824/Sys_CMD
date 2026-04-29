<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Models\Lote;

$rnc = '130940632';

$count = Afiliado::where('rnc_empresa', $rnc)->count();
echo "Total affiliates with RNC {$rnc}: {$count}\n\n";

$byLot = Afiliado::where('rnc_empresa', $rnc)
    ->select('lote_id', \DB::raw('count(*) as qty'))
    ->groupBy('lote_id')
    ->get();

echo "Breakdown by Lote:\n";
foreach ($byLot as $item) {
    if ($item->lote_id) {
        $lote = Lote::find($item->lote_id);
        $nombre = $lote ? $lote->nombre : "ID: {$item->lote_id}";
        $creado = $lote ? $lote->created_at->format('Y-m-d H:i') : 'N/A';
        echo " - Lote: {$nombre} (Creado: {$creado}) -> {$item->qty} registros\n";
    } else {
        echo " - Sin Lote (Orphans) -> {$item->qty} registros\n";
    }
}

$first = Afiliado::where('rnc_empresa', $rnc)->orderBy('created_at', 'asc')->first();
$last = Afiliado::where('rnc_empresa', $rnc)->orderBy('created_at', 'desc')->first();

if ($first) {
    echo "\nRange of Creation:\n";
    echo " - Earliest: " . $first->created_at->format('Y-m-d H:i') . "\n";
    echo " - Latest: " . $last->created_at->format('Y-m-d H:i') . "\n";
}
