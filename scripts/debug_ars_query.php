<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;

echo "--- QUERY DEBUGGING ---\n";

$query = Afiliado::query()->ars();
echo "SQL Query for ars(): " . $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";

$results = $query->limit(20)->get();

echo "Results Found: " . $results->count() . "\n";
foreach ($results as $r) {
    $resp = $r->responsable->nombre ?? 'N/A';
    echo " - [{$r->id}] {$r->nombre_completo} | Responsable: {$resp} (ID: {$r->responsable_id})\n";
    if (str_contains($r->nombre_completo, 'Jonathan Alexander')) {
        echo "   [!] FOUND ILLEGAL ENTRY!\n";
    }
}
