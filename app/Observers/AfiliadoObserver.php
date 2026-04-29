<?php

namespace App\Observers;

use App\Models\Afiliado;
use App\Services\FirebaseSyncService;

class AfiliadoObserver
{
    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function saved(Afiliado $afiliado): void
    {
        if ($afiliado->cedula) {
            $documentId = $afiliado->cedula;
            
            // En lugar de sincronizar en tiempo real (bloqueando el proceso), 
            // lo enviamos a una cola de baja prioridad para que no bloquee la carga local.
            \App\Jobs\SyncModelToFirebase::dispatch($afiliado, 'afiliados', $documentId, auth()->id())
                ->onQueue('low'); 
        }
    }

    /**
     * Handle the Afiliado "deleted" event.
     */
    public function deleted(Afiliado $afiliado): void
    {
        if ($afiliado->cedula) {
            $documentId = $afiliado->cedula;
            // También las eliminaciones las pasamos a segundo plano
            // Nota: Aquí podrías crear un Job específico para borrar si prefieres
            $this->syncService->deleteDocument('afiliados', $documentId);
        }
    }
}
