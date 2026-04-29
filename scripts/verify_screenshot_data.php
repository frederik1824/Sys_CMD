<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$af1 = Afiliado::withoutGlobalScopes()->where('nombre_completo', 'like', '%Jonathan Alexander%')->first();
$af2 = Afiliado::withoutGlobalScopes()->where('nombre_completo', 'like', '%Polinis Badio%')->first();

echo "--- DATA VERIFICATION FROM SCREENSHOT ---\n";
if ($af1) {
    echo "1. {$af1->nombre_completo} -> Resp ID: {$af1->responsable_id} (Lote ID: {$af1->lote_id})\n";
} else {
    echo "1. Jonathan Alexander NOT FOUND.\n";
}

if ($af2) {
    echo "2. {$af2->nombre_completo} -> Resp ID: {$af2->responsable_id} (Lote ID: {$af2->lote_id})\n";
} else {
    echo "2. Rosa Angelica (Polinis) NOT FOUND.\n";
}
