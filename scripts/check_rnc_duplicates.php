<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$rnc = '130940632';

$duplicates = Afiliado::where('rnc_empresa', $rnc)
    ->select('cedula', \DB::raw('count(*) as qty'))
    ->groupBy('cedula')
    ->having('qty', '>', 1)
    ->get();

echo "Duplicate Check for RNC {$rnc}:\n";
echo "Total distinct cedulas with duplicates: " . $duplicates->count() . "\n";

if ($duplicates->count() > 0) {
    echo "\nSample of duplicates:\n";
    foreach ($duplicates->take(10) as $d) {
        $names = Afiliado::where('cedula', $d->cedula)->pluck('nombre_completo')->unique()->implode(', ');
        $cortes = Afiliado::where('cedula', $d->cedula)->get()->map(fn($a) => $a->corte->nombre)->implode(', ');
        echo " - [{$d->cedula}] {$names} -> Found in: {$cortes}\n";
    }
} else {
    echo "No duplicates found by cedula.\n";
}
