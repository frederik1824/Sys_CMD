<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Models\Estado;
use App\Models\Responsable;
use Illuminate\Support\Facades\DB;

$completado_id = Estado::where('nombre', 'Completado')->value('id') ?? 9;
$safesure_id = 2; // SAFESURE

try {
    DB::beginTransaction();

    $afiliados = Afiliado::with(['responsable', 'estado'])
        ->whereNull('responsable_id')
        ->where('estado_id', $completado_id)
        ->get();

    echo "Found " . $afiliados->count() . " records to reassign.\n";

    foreach ($afiliados as $afiliado) {
        $afiliado->responsable_id = $safesure_id;
        $afiliado->save();
        
        \App\Models\HistorialEstado::create([
            'afiliado_id' => $afiliado->id,
            'estado_anterior_id' => $afiliado->estado_id,
            'estado_nuevo_id' => $afiliado->estado_id,
            'user_id' => 1,
            'observacion' => 'Asignación automática: Expediente completado asignado a SAFESURE.'
        ]);
        
        echo " - Reassigned: {$afiliado->nombre_completo} ({$afiliado->cedula})\n";
    }

    DB::commit();
    echo "Done.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
