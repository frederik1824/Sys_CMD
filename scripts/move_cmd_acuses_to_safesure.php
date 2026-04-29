<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Models\Estado;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

$acuse_id = Estado::where('nombre', 'Acuse recibido')->value('id') ?? 7;
$cmd_id = 1;      // ARS CMD
$safesure_id = 2; // SAFESURE Responsable

$safesureEmpresa = Empresa::where('nombre', 'like', '%SAFEONE%')->first();
if (!$safesureEmpresa) {
    echo "Error: Safesure Empresa not found by name SAFEONE.\n";
    exit(1);
}

try {
    DB::beginTransaction();

    $afiliados = Afiliado::where('responsable_id', $cmd_id)
        ->where('estado_id', $acuse_id)
        ->get();

    echo "Moving " . $afiliados->count() . " records to SAFESURE...\n";

    foreach ($afiliados as $a) {
        $a->responsable_id = $safesure_id;
        $a->empresa_id = $safesureEmpresa->id;
        $a->empresa = $safesureEmpresa->nombre;
        $a->rnc_empresa = $safesureEmpresa->rnc;
        $a->save();

        \App\Models\HistorialEstado::create([
            'afiliado_id' => $a->id,
            'estado_anterior_id' => $a->estado_id,
            'estado_nuevo_id' => $a->estado_id,
            'user_id' => 1,
            'observacion' => "Reasignación masiva: Movido de ARS CMD a SAFESURE por solicitud de usuario."
        ]);
    }

    DB::commit();
    echo "Successfully moved {$afiliados->count()} records.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
