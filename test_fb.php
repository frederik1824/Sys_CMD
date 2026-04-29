<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$srv = new \App\Services\FirebaseSyncService();
$res = $srv->push("empresas", "test-123", ["nombre" => "TEST EMPRESA"]);
var_dump($res);
