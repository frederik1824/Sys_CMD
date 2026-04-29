<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$service = new FirebaseSyncService();

$query = [
    'structuredQuery' => [
        'from' => [['collectionId' => 'afiliados']],
        'where' => [
            'fieldFilter' => [
                'field' => ['fieldPath' => 'estado_id'],
                'op' => 'EQUAL',
                'value' => ['integerValue' => 9]
            ]
        ]
    ]
];

echo "Querying Firebase for Completados (ID 9)...\n";
$fbCompletados = $service->runQuery('afiliados', $query);

echo "Firebase Completados found: " . count($fbCompletados) . "\n";

if (count($fbCompletados) > 0) {
    echo "First 5 examples:\n";
    foreach (array_slice($fbCompletados, 0, 5) as $doc) {
        echo " - [{$doc['cedula']}] {$doc['nombre_completo']}\n";
    }
}
