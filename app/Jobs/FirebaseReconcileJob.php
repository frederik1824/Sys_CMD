<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class FirebaseReconcileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $type;
    protected $logId;

    /**
     * Create a new job instance.
     */
    public function __construct($type = 'afiliados', $logId = null)
    {
        $this->type = $type;
        $this->logId = $logId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting targeted RECONCILIATION for: {$this->type}");
        
        $options = [
            '--log-id' => $this->logId,
            '--intensity' => 100
        ];

        if ($this->type === 'afiliados') {
            $options['--affiliates'] = true;
        } else {
            $options['--companies'] = true;
        }

        try {
            // Usamos pull-all porque el gap es Cloud > Local
            Artisan::call('firebase:pull-all', $options);
            Log::info("Targeted RECONCILIATION completed for: {$this->type}");
        } catch (\Exception $e) {
            Log::error("Targeted RECONCILIATION failed: " . $e->getMessage());
            
            // Actualizar el log si falló
            if ($this->logId) {
                $log = \App\Models\FirebaseSyncLog::find($this->logId);
                if ($log) {
                    $log->update([
                        'status' => 'failed',
                        'summary' => array_merge($log->summary ?? [], ['error' => $e->getMessage()]),
                        'finished_at' => now()
                    ]);
                }
            }
            throw $e;
        }
    }
}
