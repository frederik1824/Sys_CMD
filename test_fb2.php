<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$empresa = \App\Models\Empresa::where('nombre', 'INVERSIONES NUEVA SALIDA S A')->first();
if (!$empresa) {
    echo "Empresa not found\n";
    exit;
}
$srv = new \App\Services\FirebaseSyncService();
try {
    $res = $srv->push("empresas", $empresa->getFirebaseDocumentId(), $empresa->toArray());
    var_dump($res);
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
