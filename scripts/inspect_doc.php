<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$service = new FirebaseSyncService();
$doc = $service->getDocument('afiliados', '001-1016963-8');

echo "--- DOCUMENT INSPECTION (001-1016963-8) ---\n";
print_r($doc);
