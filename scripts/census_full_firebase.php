<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$service = new FirebaseSyncService();
echo "--- COMPLETE FIREBASE CENSUS (STATES 1-9) ---\n";

for ($id = 1; $id <= 9; $id++) {
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
    echo "State {$id}: " . count($docs) . " records.\n";
}
