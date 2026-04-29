<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empresa;
use App\Models\Afiliado;
use Illuminate\Support\Facades\DB;

echo "--- INICIO DE UNIFICACIÓN DE EMPRESAS (RNC DUPLICADOS) ---\n";

$dupeRncs = DB::table('empresas')
    ->select('rnc')
    ->whereNotNull('rnc')
    ->where('rnc', '!=', '')
    ->groupBy('rnc')
    ->havingRaw('count(*) > 1')
    ->pluck('rnc');

echo "Se han detectado " . count($dupeRncs) . " RNCs duplicados en la tabla de empresas.\n";

$totalMerged = 0;

foreach ($dupeRncs as $rnc) {
    // 1. Obtener todas las empresas con este RNC y su conteo de afiliados
    $records = Empresa::where('rnc', $rnc)
        ->withCount('afiliados')
        ->orderBy('afiliados_count', 'desc')
        ->orderBy('id', 'asc')
        ->get();

    if ($records->count() <= 1) continue;

    $survivor = $records->shift(); // El primero es el que tiene más afiliados o es el más viejo
    $victims = $records;

    echo "\nRNC: {$rnc} -> Conservando Empresa ID: {$survivor->id} ({$survivor->nombre})\n";

    foreach ($victims as $victim) {
        echo "   [!] Fusionando ID: {$victim->id} -> ID: {$survivor->id}\n";
        
        // Reasignar afiliados
        $affected = Afiliado::withoutGlobalScopes()
            ->where('empresa_id', $victim->id)
            ->update(['empresa_id' => $survivor->id]);
            
        echo "       - {$affected} afiliados movidos.\n";
        
        // Eliminar empresa duplicada
        $victim->delete();
        $totalMerged++;
    }
}

echo "\n--- PROCESO FINALIZADO ---\n";
echo "Total de empresas duplicadas eliminadas: {$totalMerged}\n";
