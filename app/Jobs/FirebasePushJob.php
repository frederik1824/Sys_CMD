<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class FirebasePushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;

    /**
     * Create a new job instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Re-check if model still exists
        if (!$this->model->exists) {
            return;
        }

        try {
            // We use a flag to prevent re-triggering the job if pushToFirebase is called
            // although pushToFirebase uses saveQuietly internally.
            if (method_exists($this->model, 'pushToFirebase')) {
                $this->model->pushToFirebase();
            }
        } catch (\Exception $e) {
            Log::error("FirebasePushJob failed for " . get_class($this->model) . " ID: " . $this->model->id . " - " . $e->getMessage());
            throw $e;
        }
    }
}
