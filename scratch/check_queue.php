<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$jobs = DB::table('jobs')->get();
echo "\n--- TRABAJOS EN COLA ---\n";
foreach($jobs as $j) {
    $data = json_decode($j->payload);
    echo "ID: " . $j->id . " | Tarea: " . ($data->displayName ?? 'Desconocida') . " | Intentos: " . $j->attempts . "\n";
}
echo "------------------------\n\n";
