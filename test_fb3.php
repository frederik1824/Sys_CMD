<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$e = \App\Models\Empresa::first(); 
var_dump([
    'updated_at' => $e->updated_at ? $e->updated_at->toDateTimeString() : null, 
    'firebase_synced_at' => $e->firebase_synced_at ? $e->firebase_synced_at->toDateTimeString() : null,
    'dirty' => $e->isDirty()
]);
