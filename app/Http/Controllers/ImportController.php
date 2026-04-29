<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Corte;
use App\Models\Responsable;
use App\Imports\AfiliadosImport;
use App\Exports\AfiliadosTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

class ImportController extends Controller
{
    public function index()
    {
        $cortes = Corte::where('activo', true)->orderBy('id', 'asc')->get();
        $responsables = Responsable::orderBy('nombre')->get();
        return view('import.index', compact('cortes', 'responsables'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'corte_id'       => 'required|exists:cortes,id',
            'empresa_tipo'   => 'required|in:CMD,OTRAS',
            'responsable_id' => 'nullable|exists:responsables,id',
            'file'           => 'required|mimes:xlsx,csv,xls',
        ]);

        try {
            // Regla 5.10: Generar Lote para trazabilidad
            $lote = \App\Models\Lote::create([
                'nombre' => 'Carga ' . $request->empresa_tipo . ' - ' . now()->format('d/m/Y H:i'),
                'corte_id' => $request->corte_id,
                'empresa_tipo' => $request->empresa_tipo,
                'user_id' => auth()->id() ?? 1,
                'total_registros' => 0, 
            ]);

            $import = new AfiliadosImport($request->corte_id, $request->empresa_tipo, $lote->id, $request->responsable_id);
            
            // Proceso asíncrono para mejor experiencia de usuario
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('imports', $fileName, 'public');
            
            Excel::queueImport($import, $filePath, 'public');
            
            // IMPORTANTE: No flasheamos éxito aquí porque el proceso es asíncrono.
            // El éxito será flasheado por getProgress cuando el worker termine.

            return response()->json([
                'success' => true,
                'lote_id' => $lote->id,
                'status'  => 'pending',
                'message' => 'Archivo recibido. Iniciando procesamiento en segundo plano...'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function getProgress($lote_id)
    {
        $total    = Cache::get("import_total_{$lote_id}", 0);
        $progress = Cache::get("import_progress_{$lote_id}", 0);
        $status   = Cache::get("import_status_{$lote_id}", 'pending');
        $duplicados = Cache::get("import_duplicados_{$lote_id}", []);

        $percentage = ($total > 0) ? round(($progress / $total) * 100) : 0;

        if ($status === 'completed') {
            // Pasamos los duplicados a la sesión para que la vista los muestre al recargar
            session()->flash('import_duplicated', $duplicados);
            session()->flash('success', "Importación del Lote #{$lote_id} finalizada: {$progress} filas de {$total} procesadas y " . count($duplicados) . " actualizadas.");
        }

        return response()->json([
            'percentage' => min($percentage, 100),
            'status'     => $status,
            'processed'  => $progress,
            'total'      => $total,
            'duplicados_count' => count($duplicados),
            'logs'       => Cache::get("import_logs_{$lote_id}", [])
        ]);
    }

    public function downloadTemplate()
    {
        return Excel::download(new AfiliadosTemplateExport, 'plantilla_afiliados.xlsx');
    }
}
