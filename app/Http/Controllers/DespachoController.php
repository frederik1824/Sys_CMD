<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Despacho;
use App\Models\DespachoItem;
use App\Models\Mensajero;
use App\Models\Ruta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoController extends Controller
{
    public function index()
    {
        $despachos = Despacho::with(['mensajero', 'ruta'])->withCount('items')->latest()->paginate(10);
        return view('despachos.index', compact('despachos'));
    }

    public function createBatch(Request $request)
    {
        $mensajeros = Mensajero::where('activo', true)->get();
        $rutas = Ruta::all();
        
        $query = Afiliado::where('estado_id', 1)
            ->whereDoesntHave('despachoItems', function($q) {
                $q->whereIn('status', ['pendiente']);
            })
            ->with(['empresaModel', 'corte']);

        // Filtros del Servidor (Para optimizar carga)
        if ($request->filled('searchTerm')) {
            $term = $request->searchTerm;
            $query->where(function($q) use ($term) {
                $q->where('nombre_completo', 'like', "%{$term}%")
                  ->orWhere('cedula', 'like', "%{$term}%")
                  ->orWhereHas('empresaModel', function($qe) use ($term) {
                      $qe->where('nombre', 'like', "%{$term}%");
                  });
            });
        }

        if ($request->filled('filterProvince')) {
            $query->where('provincia', $request->filterProvince);
        }

        $afiliados = $query->paginate(30);

        if ($request->ajax()) {
            return view('despachos.partials.selection_table', compact('afiliados'))->render();
        }

        return view('despachos.create_batch', compact('mensajeros', 'rutas', 'afiliados'));
    }

    public function processBatch(Request $request)
    {
        $request->validate([
            'mensajero_id' => 'required|exists:mensajeros,id',
            'ruta_id' => 'nullable|exists:rutas,id',
            'afiliado_ids' => 'required|array|min:1',
            'observaciones' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $despacho = Despacho::create([
                'mensajero_id' => $request->mensajero_id,
                'ruta_id' => $request->ruta_id,
                'status' => 'en_transito',
                'fecha_salida' => now(),
                'observaciones' => $request->observaciones
            ]);

            foreach ($request->afiliado_ids as $id) {
                DespachoItem::create([
                    'despacho_id' => $despacho->id,
                    'afiliado_id' => $id,
                    'status' => 'pendiente'
                ]);

                // Actualizar estado del afiliado a "En ruta" (ID=3)
                $afiliado = Afiliado::findOrFail($id);
                $afiliado->update(['estado_id' => 3]);
                
                // Historial
                \App\Models\HistorialEstado::create([
                    'afiliado_id' => $id,
                    'estado_anterior_id' => 1,
                    'estado_nuevo_id' => 3,
                    'user_id' => auth()->id(),
                    'observacion' => "Despachado en lote #" . $despacho->id
                ]);
            }

            DB::commit();
            return redirect()->route('despachos.index')->with('success', 'Despacho iniciado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar despacho: ' . $e->getMessage());
        }
    }

    public function show(Despacho $despacho)
    {
        $despacho->load(['mensajero', 'ruta', 'items.afiliado.estado', 'items.afiliado.empresaModel']);
        return view('despachos.show', compact('despacho'));
    }

    public function print(Despacho $despacho)
    {
        $despacho->load(['mensajero', 'ruta', 'items.afiliado.empresaModel', 'items.afiliado.provinciaRel', 'items.afiliado.municipioRel']);
        return view('despachos.print', compact('despacho'));
    }

    public function updateItemStatus(Request $request, $id)
    {
        $item = DespachoItem::findOrFail($id);
        $request->validate([
            'status' => 'required|in:entregado,fallido',
            'motivo_fallo' => 'required_if:status,fallido'
        ]);

        try {
            DB::beginTransaction();
            $item->update([
                'status' => $request->status,
                'motivo_fallo' => $request->motivo_fallo,
                'fecha_evento' => now()
            ]);

            $afiliado = $item->afiliado;
            $nuevoEstado = $request->status == 'entregado' ? 6 : 4; // 6: Entregado, 4: No localizado

            $afiliado->update([
                'estado_id' => $nuevoEstado,
                'fecha_entrega_safesure' => $request->status == 'entregado' ? now() : null
            ]);

            // Historial
            \App\Models\HistorialEstado::create([
                'afiliado_id' => $afiliado->id,
                'estado_anterior_id' => 3,
                'estado_nuevo_id' => $nuevoEstado,
                'user_id' => auth()->id(),
                'observacion' => "Resultado despacho #{$item->despacho_id}: " . ($request->status == 'entregado' ? 'EXITOSO' : 'FALLIDO - ' . $request->motivo_fallo)
            ]);

            DB::commit();
            return back()->with('success', 'Estatus del ítem actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }
}
