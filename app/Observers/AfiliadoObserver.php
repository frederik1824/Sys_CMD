<?php

namespace App\Observers;

use App\Models\Afiliado;
use App\Services\FirebaseSyncService;

class AfiliadoObserver
{
    use \App\Traits\NormalizesData;

    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Handle the Afiliado "creating" event.
     */
    public function creating(Afiliado $afiliado): void
    {
        // 1. Auto-asignación de responsable si es nulo (Evita que el registro sea invisible por Scopes)
        if (!$afiliado->responsable_id && auth()->check()) {
            $afiliado->responsable_id = auth()->user()->responsable_id;
        }

        // 2. Auto-asignación geográfica general
        if (!$afiliado->responsable_id && $afiliado->provincia_id) {
            $responsableGeo = \Illuminate\Support\Facades\DB::table('provincia_responsable')
                ->where('provincia_id', $afiliado->provincia_id)
                ->inRandomOrder() 
                ->first();
            
            if ($responsableGeo) {
                $afiliado->responsable_id = $responsableGeo->responsable_id;
            }
        }
    }

    /**
     * Handle the Afiliado "saving" event.
     */
    public function saving(Afiliado $afiliado): void
    {
        // 1. Regla Estricta: Asegurar costo base al guardar si está completado
        if ($afiliado->estado_id) {
            $estado = \App\Models\Estado::find($afiliado->estado_id);
            if ($estado && strtolower($estado->nombre) === 'completado') {
                if (is_null($afiliado->costo_entrega) || $afiliado->costo_entrega == 0) {
                    if ($afiliado->proveedor_id && $afiliado->proveedor?->precio_base > 0) {
                        $afiliado->costo_entrega = $afiliado->proveedor->precio_base;
                    } elseif ($afiliado->responsable_id && $afiliado->responsable?->precio_entrega > 0) {
                        $afiliado->costo_entrega = $afiliado->responsable->precio_entrega;
                    }
                }
            }
        }

        // 2. Calidad de Datos & Normalización
        if ($afiliado->nombre_completo) {
            $afiliado->nombre_completo = $this->toTitleCase($afiliado->nombre_completo);
        }

        if ($afiliado->cedula) {
            $afiliado->cedula = $this->formatCedula($afiliado->cedula);
        }

        // 3. Herencia de datos de Empresa
        if ($afiliado->empresa_id && $afiliado->isDirty('empresa_id')) {
            $empresa = $afiliado->empresaModel;
            if ($empresa) {
                $afiliado->telefono = $afiliado->telefono ?: $empresa->telefono;
                $afiliado->direccion = $afiliado->direccion ?: $empresa->direccion;
                $afiliado->provincia_id = $afiliado->provincia_id ?: $empresa->provincia_id;
                $afiliado->municipio_id = $afiliado->municipio_id ?: $empresa->municipio_id;
            }
        }

        // 4. Normalizar Dirección
        if ($afiliado->direccion) {
            $afiliado->direccion = $this->normalizeAddressField($afiliado->direccion);
        }
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
