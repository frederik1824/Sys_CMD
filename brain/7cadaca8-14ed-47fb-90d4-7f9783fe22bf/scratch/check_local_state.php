<?php

use App\Models\Afiliado;
use App\Models\Estado;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- MAPEO DE ESTADOS LOCALES ---\n";
Estado::all()->each(function($e) {
    echo "ID: {$e->id} - Nombre: {$e->nombre}\n";
});

echo "\n--- BUSCANDO REGISTRO 402-3149765-8 ---\n";
$a = Afiliado::withoutGlobalScopes()->where('cedula', 'like', '%402%3149765%8%')->first();
if ($a) {
    echo "ID Local: {$a->id}\n";
    echo "Cédula Local: {$a->cedula}\n";
    echo "Nombre Local: {$a->nombre_completo}\n";
    echo "Estado ID Local: {$a->estado_id}\n";
} else {
    echo "❌ Registro NO ENCONTRADO en la base de datos local.\n";
}
