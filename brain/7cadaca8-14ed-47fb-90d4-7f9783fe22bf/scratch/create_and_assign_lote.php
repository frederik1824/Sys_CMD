<?php

use App\Models\Lote;
use App\Models\Afiliado;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🛠️ Creando lote de rescate...\n";

$lote = new Lote();
$lote->nombre = 'Sincronización Firebase - Rescate';
$lote->corte_id = 8;
$lote->empresa_tipo = 'CMD';
$lote->user_id = 1;
$lote->total_registros = 575;
$lote->save();

echo "✅ Lote creado con ID: {$lote->id}\n";

echo "📦 Asignando registros al lote...\n";

$count = Afiliado::withoutGlobalScopes()
    ->whereIn('estado_id', [9, 10])
    ->whereNull('lote_id')
    ->update([
        'lote_id' => $lote->id,
        'corte_id' => 8,
        'responsable_id' => 1
    ]);

echo "🚀 Proceso finalizado. {$count} registros asignados y visibles ahora.\n";
