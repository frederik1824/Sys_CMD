<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\FirebaseSyncService();
$data = $service->getDocument('afiliados', '881-9118311-1');

echo "FIREBASE UPDATED_AT: " . ($data['updated_at'] ?? 'NOT FOUND') . "\n";
echo "FIREBASE ESTADO_ID: " . ($data['estado_id'] ?? 'NOT FOUND') . "\n";
echo "FIREBASE ESTADO_NOMBRE: " . ($data['estado_nombre'] ?? 'NOT FOUND') . "\n";

$afiliado = \App\Models\Afiliado::where('cedula', '881-9118311-1')->first();
echo "LOCAL ESTADO_ID: " . ($afiliado ? $afiliado->estado_id : 'NOT FOUND') . "\n";
