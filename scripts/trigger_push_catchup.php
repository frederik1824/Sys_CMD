<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Jobs\FirebaseSyncJob;
use Illuminate\Support\Facades\Log;

echo "🚀 Despachando Trabajo de Sincronización Masiva (PUSH) para subir los cambios locales...\n";

// Creamos un log para seguimiento
$log = \App\Models\FirebaseSyncLog::create([
    'type' => 'Push',
    'status' => 'pending',
    'started_at' => now(),
    'performed_by' => 'Sistema (Auto-Catchup)',
    'summary' => ['mode' => 'Push Masivo', 'reason' => 'Sincronizar Lotes 2, 3 y 4']
]);

// Despachamos el job con intensidad alta (200) para que sea rápido pero seguro
// Usamos el comando 'firebase:sync-all' que es el de PUSH
$job = new FirebaseSyncJob([
    '--force-mass' => true // Saltamos chequeos de seguridad ya que sabemos que hay muchos cambios
], $log->id, 200, 'firebase:sync-all');

dispatch($job);

echo "✅ Trabajo encolado con ID de Log: {$log->id}\n";
echo "📌 NOTA: Asegúrate de que 'php artisan queue:work' ESTE CORRIENDO en tu terminal.\n";
echo "   Si ya estaba corriendo de antes, por favor deténlo con CTRL+C y vuélvelo a iniciar para que tome los cambios.\n";
