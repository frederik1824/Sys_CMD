<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Models\Estado;

$acuse_id = Estado::where('nombre', 'Acuse recibido')->value('id') ?? 7;
$cmd_id = 1; // ARS CMD

$afiliados = Afiliado::with('corte')
    ->where('responsable_id', $cmd_id)
    ->where('estado_id', $acuse_id)
    ->get();

echo "Afiliados ARS CMD con 'Acuse recibido' (Total: " . $afiliados->count() . "):\n";
echo str_repeat("-", 80) . "\n";
printf("%-40s | %-15s | %-20s\n", "Nombre", "Cédula", "Corte");
echo str_repeat("-", 80) . "\n";

foreach ($afiliados as $a) {
    printf("%-40s | %-15s | %-20s\n", 
        substr($a->nombre_completo, 0, 40), 
        $a->cedula, 
        $a->corte->nombre ?? 'N/A'
    );
}
