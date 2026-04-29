<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiquidacionController extends Controller
{
    public function __invoke(Request $request)
    {
        $responsableId = $request->get('responsable_id');
        $proveedorId = $request->get('proveedor_id');
        $search = $request->get('search');

        $query = Afiliado::with(['estado', 'responsable', 'proveedor'])
            ->whereHas('estado', function($q) {
                $q->where('nombre', 'like', 'completado');
            });

        // Solo mostrar los que NO están liquidados por defecto
        if (!$request->has('show_all')) {
            $query->where('liquidado', false);
        }

        // Filtros dinámicos
        $query->when($responsableId, fn($q) => $q->where('responsable_id', $responsableId))
              ->when($proveedorId, fn($q) => $q->where('proveedor_id', $proveedorId))
              ->when($search, function($q) use ($search) {
                  $q->where(function($sq) use ($search) {
                      $sq->where('nombre_completo', 'like', '%' . $search . '%')
                        ->orWhere('cedula', 'like', '%' . $search . '%');
                  });
              });

        // Totales dinámicos basados en los filtros aplicados (excepto búsqueda de texto para el resumen general si se desea, pero usualmente mejor que coincidan)
        $totalsQuery = Afiliado::whereHas('estado', function($q) { $q->where('nombre', 'like', 'completado'); })
                        ->when($responsableId, fn($q) => $q->where('responsable_id', $responsableId))
                        ->when($proveedorId, fn($q) => $q->where('proveedor_id', $proveedorId));

        $totales = [
            'pendiente_monto' => (clone $totalsQuery)->where('liquidado', false)->sum('costo_entrega'),
            'pendiente_conteo' => (clone $totalsQuery)->where('liquidado', false)->count(),
            'liquidado_monto' => (clone $totalsQuery)->where('liquidado', true)->sum('costo_entrega'),
            'fuera_sla' => (clone $totalsQuery)->where('liquidado', false)->whereRaw("DATEDIFF(now(), created_at) > 20")->count()
        ];

        // Eficiencia por Responsable (Punto 2)
        $eficiencia = \App\Models\Responsable::withCount([
            'afiliados as total' => fn($q) => $q->whereHas('estado', fn($e) => $e->where('nombre', 'like', 'completado')),
            'afiliados as fuera_sla' => fn($q) => $q->whereHas('estado', fn($e) => $e->where('nombre', 'like', 'completado'))
                                                 ->whereRaw("DATEDIFF(updated_at, created_at) > 20")
        ])->get()->map(function($r) {
            $r->porcentaje_alerta = $r->total > 0 ? ($r->fuera_sla / $r->total) * 100 : 0;
            return $r;
        });

        $afiliados = $query->paginate(20)->withQueryString();
        $responsables = \App\Models\Responsable::all();
        $proveedores = \App\Models\Proveedor::all();

        return view('liquidacion.index', compact('afiliados', 'totales', 'responsables', 'proveedores', 'eficiencia'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'selected' => 'required|array',
            'recibo' => 'required|string|max:100',
            'fecha' => 'required|date',
            'evidencia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        try {
            DB::beginTransaction();

            $afiliados = Afiliado::whereIn('uuid', $request->selected)->get();
            $montoTotal = $afiliados->sum('costo_entrega');
            $conteo = $afiliados->count();
            
            // Tomamos el responsable/proveedor del primer registro para el lote
            $primer = $afiliados->first();

            $evidenciaPath = null;
            if ($request->hasFile('evidencia')) {
                $evidenciaPath = $request->file('evidencia')->store('liquidaciones', 'public');
            }

            $lote = \App\Models\LoteLiquidacion::create([
                'recibo' => $request->recibo,
                'fecha' => $request->fecha,
                'monto_total' => $montoTotal,
                'conteo_registros' => $conteo,
                'responsable_id' => $primer->responsable_id,
                'proveedor_id' => $primer->proveedor_id,
                'evidencia_path' => $evidenciaPath
            ]);

            Afiliado::whereIn('uuid', $request->selected)->update([
                'liquidado' => true,
                'fecha_liquidacion' => $request->fecha,
                'recibo_liquidacion' => $request->recibo,
                'lote_liquidacion_id' => $lote->id
            ]);

            DB::commit();

            return redirect()->route('liquidacion.print', ['recibo' => $lote->id])
                ->with('success', 'Liquidación procesada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la liquidación: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        // Ahora permitimos imprimir por ID de Lote (más seguro) o por Recibo (compatibilidad)
        $lote = \App\Models\LoteLiquidacion::with(['responsable', 'proveedor'])->find($id);
        
        if ($lote) {
            $afiliados = Afiliado::where('lote_liquidacion_id', $lote->id)->get();
            $recibo = $lote->recibo;
            $totalAfiliados = $lote->conteo_registros;
            $totalMonto = $lote->monto_total;
            $fecha = $lote->fecha;
            $responsable = $lote->responsable->nombre ?? ($lote->proveedor->nombre ?? 'Varios');
        } else {
            // Fallback para recibos antiguos por texto
            $afiliados = Afiliado::with(['responsable', 'proveedor'])
                ->where('recibo_liquidacion', $id)
                ->where('liquidado', true)
                ->get();

            if ($afiliados->isEmpty()) {
                return redirect()->route('liquidacion.index')->with('error', 'No se encontraron registros.');
            }

            $recibo = $id;
            $totalAfiliados = $afiliados->count();
            $totalMonto = $afiliados->sum('costo_entrega');
            $fecha = $afiliados->first()->fecha_liquidacion;
            $responsable = $afiliados->first()->responsable->nombre ?? 'Varios';
        }

        return view('liquidacion.print', compact('afiliados', 'recibo', 'totalAfiliados', 'totalMonto', 'fecha', 'responsable'));
    }

    public function history()
    {
        $lotes = \App\Models\LoteLiquidacion::with(['responsable', 'proveedor'])
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('liquidacion.history', compact('lotes'));
    }
}
