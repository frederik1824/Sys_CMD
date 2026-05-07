<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Jobs\FirebaseSyncJob;
use App\Models\User;

class FirebaseAutoMidnightSync extends Command
{
    protected $signature   = 'firebase:auto-midnight-sync';
    protected $description = 'Lanza automáticamente la sincronización full de Firebase a las 12:05 AM (reset de cuota Google).';

    public function handle(): int
    {
        // Guard: no lanzar si ya hay una sync activa
        if (Cache::get('firebase_sync_active')) {
            $this->warn('[AUTO-SYNC] Sincronización en progreso, se omite el disparo automático.');
            Log::info('firebase:auto-midnight-sync skipped — sync already active.');
            return self::SUCCESS;
        }

        $this->info('[AUTO-SYNC] Cuota Firebase reiniciada. Lanzando sincronización completa...');
        Log::info('firebase:auto-midnight-sync dispatched — quota reset window triggered at ' . now()->toDateTimeString());

        // Notificar al primer admin disponible al finalizar
        $adminUser = User::role('Admin')->first();

        FirebaseSyncJob::dispatch(
            options : ['--affiliates' => true, '--companies' => true, '--snapshot' => true],
            logId   : null,
            intensity: 100,
            command : 'firebase:pull-all',
            userId  : $adminUser?->id
        );

        $this->info('[AUTO-SYNC] Job despachado correctamente. Revisa el Sync Center para el progreso.');
        return self::SUCCESS;
    }
}
