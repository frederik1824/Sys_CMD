<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;
use App\Models\Afiliado;
use App\Models\Estado;

echo "🚀 Iniciando recuperación de afiliados completados...\n";

$firebase = new FirebaseSyncService();
$syncCount = 0;

// Desactivar chequeo de llaves foráneas para permitir la carga masiva
\DB::statement('SET FOREIGN_KEY_CHECKS=0');

// IDs de estados en Firebase (según lo verificado)
// 9: Completado, 10: Acuse recibido
$targetStatuses = [
    9 => 'Completado', 
    10 => 'Acuse recibido'
];

foreach ($targetStatuses as $statusId => $statusName) {
    echo "🔍 Buscando afiliados con estado_id: $statusId ($statusName)...\n";
    
    $query = [
        'structuredQuery' => [
            'from' => [['collectionId' => 'afiliados']],
            'where' => [
                'fieldFilter' => [
                    'field' => ['fieldPath' => 'estado_id'],
                    'op' => 'EQUAL',
                    'value' => ['integerValue' => (string)$statusId]
                ]
            ]
        ]
    ];

    $results = $firebase->runQuery('afiliados', $query);
    echo "✅ Encontrados: " . count($results) . "\n";

    foreach ($results as $mapped) {
        if (!isset($mapped['cedula'])) continue;

        Afiliado::withoutEvents(function() use ($mapped, &$syncCount, $statusId) {
            // Saltamos la regla de gating (applyGatingRule) para esta restauración forzada
            // para recuperar el trabajo marcado como Completado directamente.
            
            Afiliado::updateOrCreate(['cedula' => $mapped['cedula']], [
                'nombre_completo' => $mapped['nombre_completo'] ?? null,
                'telefono' => $mapped['telefono'] ?? null,
                'direccion' => $mapped['direccion'] ?? null,
                'poliza' => $mapped['poliza'] ?? null,
                'contrato' => $mapped['contrato'] ?? null,
                'empresa' => $mapped['empresa'] ?? null,
                'rnc_empresa' => $mapped['rnc_empresa'] ?? null,
                'estado_id' => $statusId, // Forzamos el estado real de Firebase (9 o 10)
                'lote_id' => $mapped['lote_id'] ?? null,
                'firebase_synced_at' => now(),
            ]);
            $syncCount++;
        });
    }
}

\DB::statement('SET FOREIGN_KEY_CHECKS=1');
echo "🎉 Proceso finalizado. Registros recuperados: $syncCount\n";
