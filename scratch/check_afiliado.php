<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a = App\Models\Afiliado::withoutGlobalScopes()->where('cedula', '047-0214677-2')->first();
if ($a) {
    echo "ID: " . $a->id . "\n";
    echo "Name: " . $a->nombre . "\n";
    echo "Corte ID: " . $a->corte_id . "\n";
    echo "Empresa ID: " . ($a->empresa_id ?? 'NULL') . "\n";
    echo "User ID: " . ($a->usuario_id ?? 'NULL') . "\n";
    echo "Responsable ID: " . ($a->responsable_id ?? 'NULL') . "\n";
    echo "Lote ID: " . ($a->lote_id ?? 'NULL') . "\n";
    echo "Created At: " . $a->created_at . "\n";
} else {
    echo "NOT FOUND\n";
}
