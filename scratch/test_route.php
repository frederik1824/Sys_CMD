<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$afiliado = Afiliado::first();
echo "ID: " . $afiliado->id . "\n";
echo "UUID: " . $afiliado->uuid . "\n";
echo "Route Key Name: " . $afiliado->getRouteKeyName() . "\n";
echo "Route Key: " . $afiliado->getRouteKey() . "\n";
echo "Show URL: " . route('afiliados.show', $afiliado) . "\n";
