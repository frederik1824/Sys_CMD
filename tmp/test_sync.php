<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\FirebaseSyncService();
$mapped = $service->getDocument('afiliados', '881-9118311-1');

if (!$mapped) {
    die("Firebase record not found for 881-9118311-1\n");
}

$isCmd = env('FIREBASE_SYNC_ROLE') === 'CMD';
$incomingEstadoId = $mapped['estado_id'] ?? null;

$finalEstadoId = $incomingEstadoId;
if ($isCmd && $incomingEstadoId == 9) {
    $finalEstadoId = 7;
}

echo "Detected State ID from Firebase: $incomingEstadoId\n";
echo "Final State ID for Local: $finalEstadoId\n";

$afiliado = \App\Models\Afiliado::where('cedula', '881-9118311-1')->first();
echo "Current Local State ID: " . ($afiliado ? $afiliado->estado_id : 'NOT FOUND') . "\n";

if ($afiliado) {
    \App\Models\Afiliado::withoutEvents(function() use ($mapped, $finalEstadoId) {
        \App\Models\Afiliado::updateOrCreate(['cedula' => $mapped['cedula']], [
            'nombre_completo' => $mapped['nombre_completo'] ?? null,
            'telefono' => $mapped['telefono'] ?? null,
            'direccion' => $mapped['direccion'] ?? null,
            'poliza' => $mapped['poliza'] ?? null,
            'contrato' => $mapped['contrato'] ?? null,
            'empresa' => $mapped['empresa'] ?? null,
            'rnc_empresa' => $mapped['rnc_empresa'] ?? null,
            'estado_id' => $finalEstadoId,
            'firebase_synced_at' => now()
        ]);
    });
    
    $afiliado->refresh();
    echo "New Local State ID after sync: " . $afiliado->estado_id . "\n";
}
