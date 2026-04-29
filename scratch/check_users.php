<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = App\Models\User::role('Operador')->get();
foreach ($users as $u) {
    echo "User: " . $u->name . ", Responsable ID: " . ($u->responsable_id ?? 'NULL') . "\n";
}
