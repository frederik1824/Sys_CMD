<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Estado;
use App\Models\Corte;
use App\Services\EvidenciaService;
use Illuminate\Http\Request;
use Exception;

class CierreController extends Controller
{
    protected $evidenciaService;

    public function __construct(EvidenciaService $evidenciaService)
    {
        $this->evidenciaService = $evidenciaService;
    }

    public function index(Request $request)
    {
        $query = Afiliado::with(['estado', 'responsable', 'evidenciasAfiliado'])
            ->whereHas('estado', function($q) {
                // Solo mostramos los que están en procesos activos (no completados o cancelados)
                // O quizás todos los que NO estén completados
                $q->where('nombre', '!=', 'Completado');
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%")
                  ->orWhere('cedula', 'like', "%{$search}%")
                  ->orWhere('poliza', 'like', "%{$search}%");
            });
        }

        if ($request->filled('corte_id')) {
            $query->where('corte_id', $request->corte_id);
        }

        if ($request->filled('responsable_id')) {
            $query->where('responsable_id', $request->responsable_id);
        }

        $afiliados = $query->orderBy('updated_at', 'desc')->paginate(30)->withQueryString();
        $cortes = Corte::all();
        $responsables = \App\Models\Responsable::all();

        return view('cierre.index', compact('afiliados', 'cortes', 'responsables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'afiliado_id' => 'required|exists:afiliados,id',
            'documentos' => 'required|array',
            'documentos.*' => 'in:acuse_recibo,formulario_firmado'
        ]);

        try {
            $afiliado = Afiliado::findOrFail($request->afiliado_id);
            $userId = auth()->id() ?? 1;

            foreach ($request->documentos as $tipo) {
                $this->evidenciaService->validatePhysical($afiliado, $tipo, $userId, $request->observaciones);
            }

            return response()->json([
                'success' => true,
                'message' => 'Documentos cerrados correctamente.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
