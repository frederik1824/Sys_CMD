<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$afiliado = App\Models\Afiliado::find(12238);
if ($afiliado) {
    if ($afiliado->estado_id != 1) {
        $afiliado->estado_id = 1;
        $afiliado->save();
        echo "Afiliado 12238 reseteado a Pendiente.\n";
    } else {
        echo "Afiliado ya estaba en Pendiente.\n";
    }
}
