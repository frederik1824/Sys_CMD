<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$service = new FirebaseSyncService();
echo "--- SAFESURE CENSUS BY ID (responsable_id = 2) ---\n";

$query = [
    'structuredQuery' => [
        'from' => [['collectionId' => 'afiliados']],
        'where' => [
            'compositeFilter' => [
                'op' => 'AND',
                'filters' => [
                    [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => 'responsable_id'],
                            'op' => 'EQUAL',
                            'value' => ['integerValue' => 2]
                        ]
                    ],
                    [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => 'estado_id'],
                            'op' => 'EQUAL',
                            'value' => ['integerValue' => 9]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$docs = $service->runQuery('afiliados', $query);
echo "Safesure Completados (ID 9) in Firebase: " . count($docs) . "\n";

$query7 = [
    'structuredQuery' => [
        'from' => [['collectionId' => 'afiliados']],
        'where' => [
            'compositeFilter' => [
                'op' => 'AND',
                'filters' => [
                    [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => 'responsable_id'],
                            'op' => 'EQUAL',
                            'value' => ['integerValue' => 2]
                        ]
                    ],
                    [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => 'estado_id'],
                            'op' => 'EQUAL',
                            'value' => ['integerValue' => 7]
                        ]
                    ]
                ]
            ]
        ]
    ]
];

$docs7 = $service->runQuery('afiliados', $query7);
echo "Safesure Acuses (ID 7) in Firebase: " . count($docs7) . "\n";
