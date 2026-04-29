<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FirebaseSyncService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SyncModelToFirebase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $tries = 3;
    public $backoff = 2; // Wait 2 seconds between retries

    protected $model;
    protected $collection;
    protected $documentId;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(Model $model, string $collection, string $documentId, $userId = null)
    {
        $this->model = $model;
        $this->collection = $collection;
        $this->documentId = $documentId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseSyncService $syncService): void
    {
        try {
            $syncService->syncModel($this->model, $this->collection, $this->documentId);
            
            if ($this->userId) {
                $user = \App\Models\User::find($this->userId);
                if ($user) {
                    $name = $this->model->nombre_completo ?? $this->model->nombre ?? 'Registro';
                    $user->notify(new \App\Notifications\FirebaseSyncNotification(
                        "Respaldo en Nube OK",
                        "El registro [{$name}] ha sido sincronizado automáticamente.",
                        "task_alt",
                        null
                    ));
                }
            }
        } catch (\Exception $e) {
            Log::error("Async Firebase Sync Error [{$this->collection}/{$this->documentId}]: " . $e->getMessage());
            
            if ($this->userId) {
                $user = \App\Models\User::find($this->userId);
                if ($user) {
                    $user->notify(new \App\Notifications\FirebaseSyncNotification(
                        "Fallo de Auto-Sync",
                        "No se pudo respaldar un cambio reciente en Firebase: " . $e->getMessage(),
                        "warning"
                    ));
                }
            }

            throw $e; 
        }
    }
}
