<?php

use App\Services\FirebaseSyncService;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$firebase = new FirebaseSyncService();
$cedula = '402-3149765-8';

echo "🔍 Inspeccionando cédula $cedula en Firestore...\n";

// Probamos obtener por ID directo (asumiendo que el ID es la cédula con guiones)
$doc = $firebase->getDocument("afiliados", $cedula);

if ($doc) {
    echo "✅ Encontrado por ID directo!\n";
    echo json_encode($doc, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "⚠️ No encontrado por ID directo. Buscando por campo...\n";
    $query = [
        'structuredQuery' => [
            'from' => [['collectionId' => 'afiliados']],
            'where' => [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'cedula'],
                    'op' => 'EQUAL',
                    'value' => ['stringValue' => $cedula]
                ]
            ]
        ]
    ];
    $results = $firebase->runQuery('afiliados', $query);
    if (count($results) > 0) {
        echo "✅ Encontrado vía Query!\n";
        echo json_encode($results[0], JSON_PRETTY_PRINT) . "\n";
    } else {
        echo "❌ No se encontró el registro en la colección 'afiliados'.\n";
    }
}
