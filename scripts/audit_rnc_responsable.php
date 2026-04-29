<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$rnc = '130940632';

$byResp = Afiliado::where('rnc_empresa', $rnc)
    ->select('responsable_id', \DB::raw('count(*) as qty'))
    ->groupBy('responsable_id')
    ->get();

echo "Responsable breakdown for RNC 130940632:\n";
foreach ($byResp as $item) {
    if ($item->responsable_id) {
        $resp = \App\Models\Responsable::find($item->responsable_id);
        $nombre = $resp ? $resp->nombre : "ID: {$item->responsable_id}";
        echo " - Responsable: {$nombre} -> {$item->qty} registros\n";
    } else {
        echo " - Sin Responsable -> {$item->qty} registros\n";
    }
}
