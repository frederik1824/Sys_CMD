<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new App\Services\FirebaseSyncService();
$doc = $service->getDocument('afiliados', '402-1003251-8');

if ($doc) {
    echo "Firebase Data:\n";
    print_r($doc);
} else {
    echo "Document not found in Firebase.\n";
}
