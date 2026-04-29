<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\FirebaseSyncJob;
use App\Models\FirebaseSyncLog;

echo "Iniciando prueba de sincronización ligera...\n";

$log = FirebaseSyncLog::create([
    'type' => 'Pull',
    'status' => 'running',
    'performed_by' => 'Sistema (Prueba de Diagnóstico)',
    'summary' => [
        'mode' => 'Light Test',
        'intensity' => 10,
        'snapshot' => false
    ],
    'started_at' => now()
]);

FirebaseSyncJob::dispatch(['--intensity' => 10, '--snapshot' => false], $log->id)->onQueue('default');

echo "Job despachado a la cola 'default' con Log ID: {$log->id}\n";
echo "Por favor, observa el dashboard para ver el avance.\n";
