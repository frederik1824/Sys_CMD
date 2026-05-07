<?php

namespace App\Observers;

use App\Models\Empresa;
use App\Services\FirebaseSyncService;

class EmpresaObserver
{
    use \App\Traits\NormalizesData;

    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Handle the Empresa "saving" event.
     */
    public function saving(Empresa $empresa): void
    {
        if ($empresa->direccion) {
            $empresa->direccion = $this->normalizeAddressField($empresa->direccion);
        }
    }

    /**
     * Handle the Empresa "saved" event (covers created and updated)
     */
    public function saved(Empresa $empresa): void
    {
        $documentId = $empresa->getFirebaseDocumentId();
        
        if ($documentId) {
            // Sincronización en segundo plano (Baja prioridad)
            \App\Jobs\SyncModelToFirebase::dispatch($empresa, 'empresas', $documentId, auth()->id())
                ->onQueue('low');
        }
    }

    /**
     * Handle the Empresa "deleted" event.
     */
    public function deleted(Empresa $empresa): void
    {
        $documentId = $empresa->getFirebaseDocumentId();
        
        if ($documentId) {
            $this->syncService->deleteDocument('empresas', $documentId);
        }
    }
}
