<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use App\Services\FirebaseSyncService;

$service = new FirebaseSyncService();
$advancedStates = [6, 7, 8, 9];
$totalUpdated = 0;

echo "--- STARTING FORCED SYNC FOR ADVANCED STATES (6, 7, 8, 9) ---\n";

foreach ($advancedStates as $stateId) {
    echo "Querying Firebase for State ID {$stateId}...\n";
    
    $query = [
        'structuredQuery' => [
            'from' => [['collectionId' => 'afiliados']],
            'where' => [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'estado_id'],
                    'op' => 'EQUAL',
                    'value' => ['integerValue' => $stateId]
                ]
            ]
        ]
    ];
    
    $docs = $service->runQuery('afiliados', $query);
    echo "   Found " . count($docs) . " records in Firebase.\n";
    
    foreach ($docs as $doc) {
        $cedula = $doc['cedula'] ?? null;
        if (!$cedula) continue;
        
        $af = Afiliado::withoutGlobalScopes()->where('cedula', $cedula)->first();
        
        if ($af) {
            if ($af->estado_id != $doc['estado_id']) {
                echo "   [UPDATE] {$cedula}: Local ({$af->estado_id}) -> Firebase ({$doc['estado_id']})\n";
                $af->estado_id = $doc['estado_id'];
                
                // If syncing from Firebase, we should probably update other metadata too
                if (isset($doc['fecha_entrega_safesure'])) $af->fecha_entrega_safesure = $doc['fecha_entrega_safesure'];
                
                $af->firebase_synced_at = now();
                $af->save();
                $totalUpdated++;
            }
        } else {
            // If it doesn't exist locally but is in Firebase, we should create it
            // but for now let's just focus on updating existing ones as requested
            // echo "   [NOT FOUND LOCAL] {$cedula}\n";
        }
    }
}

echo "\n--- SYNC FINISHED ---\n";
echo "Total records updated locally: {$totalUpdated}\n";
