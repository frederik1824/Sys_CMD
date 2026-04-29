<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$service = new FirebaseSyncService();
$states = [
    1 => 'Pendiente', 
    6 => 'Carnet entregado', 
    7 => 'Pendiente de recepción', 
    8 => 'Cierre parcial', 
    9 => 'Completado'
];

echo "--- FIREBASE STATE CENSUS ---\n";

foreach ($states as $id => $name) {
    $query = [
        'structuredQuery' => [
            'from' => [['collectionId' => 'afiliados']],
            'where' => [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'estado_id'],
                    'op' => 'EQUAL',
                    'value' => ['integerValue' => $id]
                ]
            ]
        ]
    ];
    
    $docs = $service->runQuery('afiliados', $query);
    echo "State {$id} ({$name}): " . count($docs) . "\n";
}
