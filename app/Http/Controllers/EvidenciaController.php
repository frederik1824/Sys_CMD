<?php

namespace App\Http\Controllers;

use App\Services\EvidenciaService;
use App\Models\EvidenciaAfiliado;
use App\Models\Afiliado;
use Illuminate\Http\Request;
use Exception;

class EvidenciaController extends Controller
{
    protected $evidenciaService;

    public function __construct(EvidenciaService $evidenciaService)
    {
        $this->evidenciaService = $evidenciaService;
    }

    public function index(Request $request)
    {
        $query = EvidenciaAfiliado::with(['afiliado', 'user', 'validador']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipo_documento')) {
            $query->where('tipo_documento', $request->tipo_documento);
        }

        $evidencias = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('evidencias.index', compact('evidencias'));
    }

    public function updateStatus(Request $request, EvidenciaAfiliado $evidencia)
    {
        $request->validate([
            'status' => 'required|in:valido,invalido,recibido',
            'observaciones' => 'nullable|string'
        ]);

        try {
            // Mapping UI 'valido' to 'validado' used in service to match docs
            $serviceStatus = $request->status === 'valido' ? 'validado' : ($request->status === 'invalido' ? 'rechazado' : $request->status);

            $this->evidenciaService->validate(
                $evidencia,
                $serviceStatus,
                auth()->id() ?? 1,
                $request->observaciones
            );

            return redirect()->back()->with('success', 'El estado del documento ha sido actualizado.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function validatePhysical(Request $request)
    {
        // Soporta tanto un ID único como un array de IDs (para masivo)
        $afiliadoIds = is_array($request->afiliado_id) ? $request->afiliado_id : [$request->afiliado_id];
        $tipoDocumentos = is_array($request->tipo_documento) ? $request->tipo_documento : [$request->tipo_documento];
        
        try {
            $userId = auth()->id() ?? 1;
            $count = 0;

            foreach ($afiliadoIds as $index => $id) {
                $afiliado = Afiliado::findOrFail($id);
                
                // Si viene masivo, validamos ambos docs para cada ID
                if (count($afiliadoIds) > 1 && count($tipoDocumentos) > 1) {
                    $this->evidenciaService->validatePhysical($afiliado, 'carnet', $userId);
                    $this->evidenciaService->validatePhysical($afiliado, 'formulario', $userId);
                } else {
                    // Si viene específico (ej. desde un botón individual)
                    $tipo = $tipoDocumentos[$index] ?? $tipoDocumentos[0];
                    $this->evidenciaService->validatePhysical($afiliado, $tipo, $userId, $request->observaciones);
                }
                $count++;
            }

            return redirect()->back()->with('success', "Se validaron físicamente los documentos de {$count} afiliado(s).");
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
