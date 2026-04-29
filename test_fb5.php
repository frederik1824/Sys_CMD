<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$e = \App\Models\Empresa::where('nombre', 'INVERSIONES NUEVA SALIDA S A')->first(); 
var_dump($e->id);
