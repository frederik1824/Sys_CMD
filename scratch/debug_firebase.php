<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$sync = app(App\Services\FirebaseSyncService::class);
$cedula = "001-0053470-0";
$raw = preg_replace('/[^0-9]/', '', $cedula);

echo "Checking: $cedula\n";
$doc = $sync->getDocument('afiliados', $cedula);
var_dump($doc);

echo "\nChecking: $raw\n";
$doc2 = $sync->getDocument('afiliados', $raw);
var_dump($doc2);
