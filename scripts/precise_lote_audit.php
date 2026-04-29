<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$lote_id = 38;

$totalInLote = Afiliado::where('lote_id', $lote_id)->count();
$cmdInLote = Afiliado::where('lote_id', $lote_id)->where('empresa', 'ARS CMD')->count();
$rncCmdInLote = Afiliado::where('lote_id', $lote_id)->where('rnc_empresa', '130940632')->count();

echo "Lote #{$lote_id} Audit:\n";
echo " - Total records in lote: {$totalInLote}\n";
echo " - Records with Name 'ARS CMD': {$cmdInLote}\n";
echo " - Records with RNC '130940632': {$rncCmdInLote}\n";

if ($cmdInLote > 0) {
    echo "Sample individual record ID from CMD in this lote: " . Afiliado::where('lote_id', $lote_id)->where('empresa', 'ARS CMD')->first()->id . "\n";
}
