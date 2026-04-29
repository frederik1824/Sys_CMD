<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FirebasePushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $options;
    protected $logId;

    /**
     * Create a new job instance.
     *
     * @param array $options Options for the firebase:sync-all command
     * @param int|null $logId
     */
    public function __construct(array $options = [], $logId = null, $intensity = 50)
    {
        $this->options = $options;
        $this->logId = $logId;
        $this->options['--intensity'] = $intensity;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting background Firebase PUSH with options: " . json_encode($this->options));
        
        try {
            if ($this->logId) {
                $this->options['--log-id'] = $this->logId;
            }
            Artisan::call('firebase:sync-all', $this->options);
            Log::info("Background Firebase PUSH completed successfully.");
        } catch (\Exception $e) {
            Log::error("Background Firebase PUSH failed: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $e): void
    {
        Log::error("Firebase Push Job CRITICAL FAILURE: " . $e->getMessage());
        
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

        // Clear cache to allow new attempts
        \Illuminate\Support\Facades\Cache::put('firebase_sync_active', false);
        \Illuminate\Support\Facades\Cache::put('firebase_sync_control', 'stopped');
    }
}
