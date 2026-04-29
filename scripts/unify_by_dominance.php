<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use Illuminate\Support\Facades\DB;
use App\Services\FirebaseSyncService;

echo "--- INICIO DE PROCESO DE UNIFICACIÓN POR REGLA DOMINANTE ---\n";

// 1. Encontrar RNCs con conflicto (más de un responsable distinto de null)
$confictingRncs = DB::table('afiliados')
    ->select('rnc_empresa')
    ->whereNotNull('rnc_empresa')
    ->whereNotNull('responsable_id')
    ->groupBy('rnc_empresa')
    ->havingRaw('count(distinct responsable_id) > 1')
    ->pluck('rnc_empresa');

$totalConflicts = count($confictingRncs);
echo "Se han detectado {$totalConflicts} RNCs con delegaciones mixtas.\n";

$service = new FirebaseSyncService();
$globalCount = 0;

foreach ($confictingRncs as $rnc) {
    // 2. Determinar el ganador por mayoría para este RNC
    $counts = DB::table('afiliados')
        ->select('responsable_id', DB::raw('count(*) as total'))
        ->where('rnc_empresa', $rnc)
        ->whereNotNull('responsable_id')
        ->groupBy('responsable_id')
        ->orderBy('total', 'desc')
        ->get();

    if ($counts->isEmpty()) continue;

    $winnerId = $counts->first()->responsable_id;
    $winnerTotal = $counts->first()->total;
    
    // Obtener nombre del ganador para el log
    $winnerName = DB::table('responsables')->where('id', $winnerId)->value('nombre') ?? "ID: $winnerId";

    echo "\nRNC: {$rnc} -> El ganador es [{$winnerName}] con {$winnerTotal} casos.\n";

    // 3. Obtener los afiliados "perdedores" para actualizar
    $losers = Afiliado::withoutGlobalScopes()
        ->where('rnc_empresa', $rnc)
        ->where('responsable_id', '!=', $winnerId)
        ->get();

    foreach ($losers as $af) {
        $oldId = $af->responsable_id;
        $af->responsable_id = $winnerId;
        $af->reasignado = true;
        $af->save();

        // 4. Sincronizar con Firebase cada cambio individual
        $service->push('afiliados', $af->cedula, $af->toArray());
        
        echo "   [!] Afiliado {$af->cedula}: Reasignado de ID {$oldId} a ID {$winnerId} (Sincronizado)\n";
        $globalCount++;
    }
}

echo "\n--- PROCESO FINALIZADO ---\n";
echo "Total de afiliados reasignados para consistencia: {$globalCount}\n";
