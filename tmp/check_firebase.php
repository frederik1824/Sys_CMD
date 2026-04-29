<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$service = new \App\Services\FirebaseSyncService();
$data = $service->getDocument('afiliados', '881-9118311-1');

echo "UPDATED_AT: " . ($data['updated_at'] ?? 'NOT FOUND') . "\n";
