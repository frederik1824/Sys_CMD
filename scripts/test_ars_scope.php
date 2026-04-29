<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

$count = Afiliado::withoutGlobalScopes()->ars()->count();
echo "Total with scope ars(): {$count}\n";

$exists = Afiliado::withoutGlobalScopes()->ars()
    ->where('nombre_completo', 'like', '%Jonathan Alexander%')
    ->exists();
    
echo "Jonathan Alexander exists in ars() query: " . ($exists ? 'YES' : 'NO') . "\n";

$raw = Afiliado::withoutGlobalScopes()->ars()
    ->where('nombre_completo', 'like', '%Jonathan Alexander%')
    ->first();

if ($raw) {
    echo "Detail: Resp ID: {$raw->responsable_id}\n";
}
