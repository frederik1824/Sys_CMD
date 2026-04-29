<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \App\Models\Empresa::whereColumn('updated_at', '>', 'firebase_synced_at')->count();
var_dump(["updated_gt_synced" => $count]);
