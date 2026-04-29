<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$service = new FirebaseSyncService();
echo "--- TOTAL SAFESURE UNIVERSE IN FIREBASE ---\n";

$query = [
    'structuredQuery' => [
        'from' => [['collectionId' => 'afiliados']],
        'where' => [
            'fieldFilter' => [
                'field' => ['fieldPath' => 'responsable_id'],
                'op' => 'EQUAL',
                'value' => ['integerValue' => 2]
            ]
        ]
    ]
];

$docs = $service->runQuery('afiliados', $query);
echo "Total Safesure documents in Firebase: " . count($docs) . "\n";
