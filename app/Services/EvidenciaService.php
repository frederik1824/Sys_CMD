<?php

namespace App\Services;

use App\Models\EvidenciaAfiliado;
use App\Models\Afiliado;
use Illuminate\Support\Facades\Storage;
use Exception;

class EvidenciaService
{
    protected $afiliadoService;

    public function __construct(AfiliadoService $afiliadoService)
    {
        $this->afiliadoService = $afiliadoService;
    }

    public function upload(Afiliado $afiliado, string $tipo, $file, $userId, $observaciones = null)
    {
        // Rule 5.3: Inmutability check
        if (strtolower($afiliado->estado?->nombre) === 'completado') {
            throw new Exception("No se pueden subir evidencias a un expediente COMPLETADO.");
        }

        $path = $file->store('evidencias/' . $afiliado->cedula, 'public');

        $evidencia = EvidenciaAfiliado::updateOrCreate(
            ['afiliado_id' => $afiliado->id, 'tipo_documento' => $tipo],
            [
                'status' => 'recibido',
                'file_path' => $path,
                'user_id' => $userId,
                'observaciones' => $observaciones
            ]
        );

        // Trigger automatic state recalculation
        $this->afiliadoService->updateStatus(
            $afiliado, 
            $afiliado->estado_id, 
            "Actualización automática por carga de " . str_replace('_', ' ', $tipo),
            $userId
        );

        return $evidencia;
    }

    public function validate(EvidenciaAfiliado $evidencia, string $status, $validatorId, $motivo = null)
    {
        // Rule 5.7 override for physical validation? No, we use validatePhysical instead for clarity
        if (!$evidencia->file_path && $status === 'validado') {
            throw new Exception("Regla 5.7: No se puede validar un documento sin archivo físico.");
        }

        $evidencia->status = $status;
        $evidencia->validated_by = $validatorId;
        if ($motivo) $evidencia->observaciones = $motivo;
        $evidencia->save();

        // Recalculate affiliate state
        $this->afiliadoService->updateStatus(
            $evidencia->afiliado,
            $evidencia->afiliado->estado_id,
            "Documento {$evidencia->tipo_documento} marcado como {$status}",
            $validatorId
        );

        return $evidencia;
    }

    /**
     * Valida un documento que se recibió físicamente en oficina (sin upload)
     */
    public function validatePhysical(Afiliado $afiliado, string $tipo, $userId, $observaciones = null)
    {
        $evidencia = EvidenciaAfiliado::updateOrCreate(
            ['afiliado_id' => $afiliado->id, 'tipo_documento' => $tipo],
            [
                'status' => 'validado',
                'file_path' => null, // No hay archivo digital
                'user_id' => $userId,
                'validated_by' => $userId,
                'observaciones' => $observaciones ?? "Verificación física en oficina/mensajería."
            ]
        );

        // Obtener el ID del estado COMPLETADO para forzar el cambio
        $estadoCompletado = \App\Models\Estado::where('nombre', 'Completado')->first();

        // Disparar recálculo de estado del afiliado
        $this->afiliadoService->updateStatus(
            $afiliado,
            $estadoCompletado->id ?? $afiliado->estado_id,
            "VALIDACIÓN FÍSICA de " . str_replace('_', ' ', $tipo) . " realizada manualmente.",
            $userId
        );

        return $evidencia;
    }
}
