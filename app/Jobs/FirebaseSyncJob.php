<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FirebaseSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $timeout = 3600;
    public $tries = 1;

    protected $options;
    protected $logId;
    protected $command;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @param array $options Options for the command
     * @param int|null $logId The ID of the log record to update
     * @param int $intensity Speed intensity
     * @param string $command The Artisan command to run
     * @param int|null $userId The user to notify
     */
    public function __construct(array $options = [], $logId = null, $intensity = 50, $command = 'firebase:pull-all', $userId = null)
    {
        $this->options = $options;
        $this->logId = $logId;
        $this->options['--intensity'] = $intensity;
        $this->command = $command;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting background Firebase Sync with options: " . json_encode($this->options));
        
        try {
            if ($this->logId) {
                $this->options['--log-id'] = $this->logId;
            }
            
            Artisan::call($this->command, $this->options);
            Log::info("Background Firebase Sync ({$this->command}) completed successfully.");

            if ($this->userId) {
                $user = \App\Models\User::find($this->userId);
                if ($user) {
                    $typeStr = str_contains($this->command, 'push') ? 'Subida' : 'Descarga';
                    $user->notify(new \App\Notifications\FirebaseSyncNotification(
                        "Sincronización Finalizada",
                        "El proceso de {$typeStr} se ha completado correctamente en segundo plano.",
                        "task_alt"
                    ));
                }
            }
        } catch (\Exception $e) {
            Log::error("Background Firebase Sync failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $e): void
    {
        Log::error("Firebase Sync Job CRITICAL FAILURE: " . $e->getMessage());
        
        // Update DB log if possible, but only if it's not already SUCCESS
        if ($this->logId) {
            $log = \App\Models\FirebaseSyncLog::find($this->logId);
            if ($log && $log->status !== 'success') {
                $log->update([
                    'status' => 'failed',
                    'summary' => array_merge($log->summary ?? [], ['error' => 'Error crítico en el proceso de fondo: ' . $e->getMessage()]),
                    'finished_at' => now()
                ]);
            }
        }

        if ($this->userId) {
            $user = \App\Models\User::find($this->userId);
            if ($user) {
                $user->notify(new \App\Notifications\FirebaseSyncNotification(
                    "Error de Sincronización",
                    "El proceso en segundo plano ha fallado críticamente. Revise los logs para más detalles.",
                    "error"
                ));
            }
        }

        // Clear cache to allow new attempts
        \Illuminate\Support\Facades\Cache::put('firebase_sync_active', false);
        \Illuminate\Support\Facades\Cache::put('firebase_sync_control', 'stopped');
    }
}
