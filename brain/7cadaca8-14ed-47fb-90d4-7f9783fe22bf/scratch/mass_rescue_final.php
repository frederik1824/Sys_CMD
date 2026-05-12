<?php

use App\Services\FirebaseSyncService;
use App\Models\Afiliado;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$firebase = new FirebaseSyncService();
echo "⚡ Iniciando descarga masiva de estados 7 (Acuse) y 9 (Completado)...\n";

$targetStates = [7, 9];
$results = ['updated' => 0, 'created' => 0, 'errors' => 0];

foreach ($targetStates as $stateId) {
    echo "🔍 Consultando Firebase para estado_id: $stateId...\n";
    
    $query = [
        'structuredQuery' => [
            'from' => [['collectionId' => 'afiliados']],
            'where' => [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'estado_id'],
                    'op' => 'EQUAL',
                    'value' => ['integerValue' => (string)$stateId]
                ]
            ]
        ]
    ];

    $docs = $firebase->runQuery('afiliados', $query);
    echo "✅ Encontrados " . count($docs) . " documentos.\n";

    foreach ($docs as $data) {
        $cedula = $data['cedula'] ?? null;
        if (!$cedula) continue;

        $cleanCedula = preg_replace('/[^0-9]/', '', $cedula);
        $afiliado = Afiliado::withoutGlobalScopes()->whereRaw("REPLACE(cedula, '-', '') = ?", [$cleanCedula])->first();
        
        $localState = ($stateId == 9) ? 9 : 10; // 9 = Completado, 10 = Acuse Recibido (según mapeo local)
        
        // Probamos con el ID 9 para completado en lugar de 20 para ver si la UI lo toma mejor
        if ($stateId == 9) $localState = 9; 

        if (!$afiliado) {
            $afiliado = new Afiliado();
            $afiliado->cedula = $cedula;
            $results['created']++;
        } else {
            $results['updated']++;
        }

        $afiliado->nombre_completo = $data['nombre_completo'] ?? $afiliado->nombre_completo;
        $afiliado->estado_id = $localState;
        
        if (isset($data['fecha_entrega_safesure'])) {
            $afiliado->fecha_entrega_safesure = $data['fecha_entrega_safesure'];
        }

        Afiliado::withoutEvents(function() use ($afiliado) {
            $afiliado->save();
        });
    }
}

echo "\n--- RESULTADOS FINALES ---\n";
echo "Creados: {$results['created']}\n";
echo "Actualizados: {$results['updated']}\n";
echo "Total: " . ($results['created'] + $results['updated']) . "\n";
