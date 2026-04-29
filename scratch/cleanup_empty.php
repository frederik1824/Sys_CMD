<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$count = Afiliado::where(function($q) {
    $q->whereNull('cedula')->orWhere('cedula', '');
})->where(function($q) {
    $q->whereNull('nombre_completo')->orWhere('nombre_completo', '');
})->forceDelete();

echo "\n>>> LIMPIEZA COMPLETADA: Se han borrado $count registros vacíos.\n\n";
