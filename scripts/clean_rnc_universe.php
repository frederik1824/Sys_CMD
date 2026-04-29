<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use Illuminate\Support\Facades\DB;

$rnc = '130940632';

try {
    DB::beginTransaction();

    $count = Afiliado::where('rnc_empresa', $rnc)->count();
    echo "Records to clean: {$count}\n";

    $updated = Afiliado::where('rnc_empresa', $rnc)->update([
        'empresa_id' => null,
        'rnc_empresa' => null,
        'empresa' => 'PENDIENTE DE ASIGNAR',
        'responsable_id' => null, // Regresarlos al pool de pendientes global
        'reasignado' => true      // Flag para auditoría
    ]);

    DB::commit();
    echo "Successfully cleaned {$updated} records.\n";
    echo "They are now in the 'Módulo de Asignaciones (Pendientes)' pool.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
