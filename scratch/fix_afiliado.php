<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a = App\Models\Afiliado::withoutGlobalScopes()->where('cedula', '047-0214677-2')->first();
if ($a) {
    $a->responsable_id = 1;
    $a->save();
    echo "FIXED: " . $a->nombre_completo . " assigned to Responsable 1\n";
} else {
    echo "NOT FOUND\n";
}
