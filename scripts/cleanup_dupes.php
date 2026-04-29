<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting optimized cleanup of duplicate affiliates...\n";

$sql = "DELETE a1 FROM afiliados a1
        INNER JOIN afiliados a2 
        WHERE a1.id > a2.id AND a1.cedula = a2.cedula";

$deleted = DB::delete($sql);

echo "Cleanup finished. Total duplicate records removed: {$deleted}\n";
