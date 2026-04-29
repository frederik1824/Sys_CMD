<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empresa;

$empresa = Empresa::where('nombre', 'like', '%ZERTIDAL%')->first();
if ($empresa) {
    if (!$empresa->es_verificada) {
        $empresa->es_verificada = true;
        $empresa->save();
        echo "ZERTIDAL INVESTMENTS SRL marcada como verificada.\n";
    } else {
        echo "ZERTIDAL already verified.\n";
    }
}
