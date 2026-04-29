<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$countNull = \App\Models\Empresa::whereNull('firebase_synced_at')->count();
$countNotNull = \App\Models\Empresa::whereNotNull('firebase_synced_at')->count();

var_dump(["null" => $countNull, "not_null" => $countNotNull]);

$sample = \App\Models\Empresa::whereNull('firebase_synced_at')->first();
var_dump($sample->toArray());
