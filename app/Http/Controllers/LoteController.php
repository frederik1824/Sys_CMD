<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Estado;
use App\Models\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoteController extends Controller
{
    public function index(Request $request)
    {
        $cortes = Corte::all();
        $responsables = Responsable::all();
        $estados = Estado::all();
        $proveedores = \App\Models\Proveedor::where('activo', true)->get();

        $query = Afiliado::with(['corte', 'estado', 'responsable', 'proveedor']);

        if ($request->filled('corte_id')) {
            $query->where('corte_id', $request->corte_id);
        }

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        if ($request->filled('responsable_id')) {
            $query->where('responsable_id', $request->responsable_id);
        }

        // Filtro especial: No entregados a Proveedor
        if ($request->has('sin_fecha_entrega')) {
            $query->whereNull('fecha_entrega_proveedor');
        }

        $afiliados = $query->paginate(50)->withQueryString();

        return view('lotes.index', compact('afiliados', 'cortes', 'responsables', 'estados', 'proveedores'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'action' => 'required|in:entrega_proveedor,cambio_estado,asignar_responsable',
            'fecha_entrega' => 'nullable|required_if:action,entrega_proveedor|date',
            'proveedor_id' => 'nullable|required_if:action,entrega_proveedor|exists:proveedors,id',
            'costo' => 'nullable|numeric|min:0',
            'estado_id' => 'nullable|required_if:action,cambio_estado|exists:estados,id',
            'responsable_id' => 'nullable|required_if:action,asignar_responsable|exists:responsables,id',
        ]);

        try {
            DB::beginTransaction();

            $updateData = [];

            if ($request->action === 'entrega_proveedor') {
                $updateData = [
                    'fecha_entrega_proveedor' => $request->fecha_entrega,
                    'proveedor_id' => $request->proveedor_id,
                    'costo_entrega' => $request->costo ?? 0,
                ];
            } elseif ($request->action === 'cambio_estado') {
                $updateData = ['estado_id' => $request->estado_id];
                // Nota: Idealmente esto debería pasar por AfiliadoService para auditoría, pero por eficiencia en lotes:
                $afiliadosBulk = \App\Models\Afiliado::whereIn('uuid', $request->selected)->get();
                foreach($afiliadosBulk as $aBulk) {
                    \App\Models\HistorialEstado::create([
                        'afiliado_id' => $aBulk->id,
                        'estado_nuevo_id' => $request->estado_id,
                        'user_id' => auth()->id() ?? 1,
                        'observacion' => 'Cambio masivo vía Módulo de Lotes.'
                    ]);
                }
            } elseif ($request->action === 'asignar_responsable') {
                $updateData = ['responsable_id' => $request->responsable_id];
            }

            Afiliado::whereIn('uuid', $request->selected)->update($updateData);

            DB::commit();
            return back()->with('success', 'Procesamiento de lote exitoso para ' . count($request->selected) . ' registros.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar lote: ' . $e->getMessage());
        }
    }
}
