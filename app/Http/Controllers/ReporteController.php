<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Estado;
use App\Models\Responsable;
use App\Models\Empresa;
use App\Models\Traspaso;
use App\Models\AgenteTraspaso;
use App\Models\SupervisorTraspaso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * Original General Dashboard (Restored)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        // Determinar si puede ver Carnetización (CMD)
        $canSeeCmd = $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION']));

        // Estadísticas Críticas
        $stats = [
            'total_afiliados' => $canSeeCmd ? Afiliado::count() : 0,
            'completados' => $canSeeCmd ? Afiliado::whereHas('estado', function($q) { $q->whereIn('nombre', ['Completado', 'Cierre parcial']); })->count() : 0,
            'pendiente_recepcion' => $canSeeCmd ? Afiliado::where('estado_id', 7)->count() : 0,
            'critico_sla' => $canSeeCmd ? Afiliado::whereDoesntHave('estado', function($q) { $q->where('nombre', 'COMPLETADO'); })
                                ->whereNotNull('fecha_entrega_proveedor')
                                ->whereRaw('DATEDIFF(NOW(), fecha_entrega_proveedor) >= 20')
                                ->count() : 0,
            'por_liquidar' => $canSeeCmd ? Afiliado::whereHas('estado', function($q) { $q->where('nombre', 'COMPLETADO'); })
                            ->where('liquidado', false)
                            ->sum('costo_entrega') : 0,
        ];

        // Progreso por Corte (Solo si puede ver CMD)
        $cortes_progreso = $canSeeCmd ? Corte::withCount(['afiliados', 'afiliados as completados_count' => function($q) {
            $q->whereHas('estado', function($st) { $st->where('nombre', 'COMPLETADO'); });
        }])->get() : collect();

        // Distribución por Estado (Solo si puede ver CMD)
        $estados_labels = $canSeeCmd ? Estado::pluck('nombre') : collect();
        $estados_counts = $canSeeCmd ? Estado::withCount('afiliados')->pluck('afiliados_count') : collect();

        return view('reportes.index', compact('stats', 'cortes_progreso', 'estados_labels', 'estados_counts', 'canSeeCmd'));
    }

    /**
     * New Executive Supervision Dashboard
     */
    public function supervision(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        // Determinar si puede ver Carnetización (CMD)
        $canSeeCmd = $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION']));

        if (!$canSeeCmd) {
            return redirect()->route('reportes.index')->with('error', 'No tiene permisos para acceder a supervisión de carnetización.');
        }

        $fecha_desde = $request->input('fecha_desde', now()->startOfMonth()->format('Y-m-d'));
        $fecha_hasta = $request->input('fecha_hasta', now()->format('Y-m-d'));
        $corte_id = $request->input('corte_id');
        $responsable_id = $request->input('responsable_id');
        $empresa_id = $request->input('empresa_id');

        // Query base con filtros aplicados
        $query = Afiliado::query()
            ->when($fecha_desde, fn($q) => $q->whereDate('created_at', '>=', $fecha_desde))
            ->when($fecha_hasta, fn($q) => $q->whereDate('created_at', '<=', $fecha_hasta))
            ->when($corte_id, fn($q) => $q->where('corte_id', $corte_id))
            ->when($responsable_id, fn($q) => $q->where('responsable_id', $responsable_id))
            ->when($empresa_id, fn($q) => $q->where('empresa_id', $empresa_id));

        // 1. Estadísticas KPI
        $ingresos_count = (clone $query)->count();
        
        $salidas_query = \App\Models\HistorialEstado::whereHas('estadoNuevo', function($q) {
                $q->whereIn('nombre', ['COMPLETADO', 'LIQUIDADO', 'ENTREGADO']);
            })
            ->whereDate('created_at', '>=', $fecha_desde)
            ->whereDate('created_at', '<=', $fecha_hasta)
            ->whereHas('afiliado', function($q) use ($corte_id, $responsable_id, $empresa_id) {
                $q->when($corte_id, fn($sq) => $sq->where('corte_id', $corte_id))
                  ->when($responsable_id, fn($sq) => $sq->where('responsable_id', $responsable_id))
                  ->when($empresa_id, fn($sq) => $sq->where('empresa_id', $empresa_id));
            });

        $salidas_count = $salidas_query->count();

        $stats = [
            'ingresos' => $ingresos_count,
            'salidas' => $salidas_count,
            'critico_sla' => (clone $query)->whereDoesntHave('estado', function($q) { $q->where('nombre', 'COMPLETADO'); })
                            ->whereNotNull('fecha_entrega_proveedor')
                            ->whereRaw('DATEDIFF(NOW(), fecha_entrega_proveedor) >= 20')
                            ->count(),
            'por_liquidar' => (clone $query)->whereHas('estado', function($q) { $q->where('nombre', 'COMPLETADO'); })
                            ->where('liquidado', false)
                            ->sum('costo_entrega'),
        ];
        
        $stats['tasa_entrega'] = $stats['ingresos'] > 0 ? ($stats['salidas'] / $stats['ingresos']) * 100 : 0;

        // 2. Datos para Gráfico de Tendencia
        $tendencia = Afiliado::select(
                DB::raw('DATE(created_at) as fecha'),
                DB::raw('COUNT(*) as total_ingreso')
            )
            ->whereDate('created_at', '>=', $fecha_desde)
            ->whereDate('created_at', '<=', $fecha_hasta)
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // 3. Distribución por Estado
        $estados = Estado::withCount(['afiliados' => function($q) use ($fecha_desde, $fecha_hasta, $corte_id, $responsable_id, $empresa_id) {
            $q->when($fecha_desde, fn($sq) => $sq->whereDate('created_at', '>=', $fecha_desde))
              ->when($fecha_hasta, fn($sq) => $sq->whereDate('created_at', '<=', $fecha_hasta))
              ->when($corte_id, fn($sq) => $sq->where('corte_id', $corte_id))
              ->when($responsable_id, fn($sq) => $sq->where('responsable_id', $responsable_id))
              ->when($empresa_id, fn($sq) => $sq->where('empresa_id', $empresa_id));
        }])->get();

        // 4. Datos por Corte
        $cortes_data = Corte::withCount(['afiliados' => function($q) use ($fecha_desde, $fecha_hasta, $responsable_id, $empresa_id) {
            $q->when($fecha_desde, fn($sq) => $sq->whereDate('created_at', '>=', $fecha_desde))
              ->when($fecha_hasta, fn($sq) => $sq->whereDate('created_at', '<=', $fecha_hasta))
              ->when($responsable_id, fn($sq) => $sq->where('responsable_id', $responsable_id))
              ->when($empresa_id, fn($sq) => $sq->where('empresa_id', $empresa_id));
        }])->get();

        // 5. Productividad por Responsable
        $responsables_data = Responsable::withCount(['afiliados' => function($q) use ($fecha_desde, $fecha_hasta, $corte_id, $empresa_id) {
            $q->when($fecha_desde, fn($sq) => $sq->whereDate('created_at', '>=', $fecha_desde))
              ->when($fecha_hasta, fn($sq) => $sq->whereDate('created_at', '<=', $fecha_hasta))
              ->when($corte_id, fn($sq) => $sq->where('corte_id', $corte_id))
              ->when($empresa_id, fn($sq) => $sq->where('empresa_id', $empresa_id));
        }])
        ->orderBy('afiliados_count', 'desc')
        ->take(10)
        ->get();

        // Data for view
        $cortes = Corte::all();
        $responsables = Responsable::all();
        $empresas = Empresa::where('es_real', true)->get();

        return view('reportes.supervision', compact(
            'stats', 'estados', 'cortes_data', 'responsables_data', 
            'tendencia', 'cortes', 'responsables', 'empresas',
            'fecha_desde', 'fecha_hasta', 'corte_id', 'responsable_id', 'empresa_id',
            'canSeeCmd'
        ));
    }

    /**
     * Executive Export Center View
     */
    public function exportCenter(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        $canSeeCmd = $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION']));

        $cortes = Corte::orderBy('id', 'desc')->get();
        $estados = Estado::all();
        
        return view('reportes.export_center', compact('cortes', 'estados', 'canSeeCmd'));
    }

    public function export(Request $request)
    {
        // Si el tipo es resumen, delegamos a exportSummary
        if ($request->type === 'summary') {
            return $this->exportSummary($request);
        }

        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        
        $query = Afiliado::with(['corte', 'estado', 'responsable', 'empresaModel'])
            ->when($request->fecha_desde, fn($q) => $q->whereDate('created_at', '>=', $request->fecha_desde))
            ->when($request->fecha_hasta, fn($q) => $q->whereDate('created_at', '<=', $request->fecha_hasta))
            ->when($request->corte_id, fn($q) => $q->where('corte_id', $request->corte_id))
            ->when($request->responsable_id, fn($q) => $q->where('responsable_id', $request->responsable_id))
            ->when($request->empresa_id, fn($q) => $q->where('empresa_id', $request->empresa_id));

        // Aplicar filtros de aislamiento si no es admin
        if (!$isAdmin) {
            $query->where('responsable_id', $user->responsable_id ?? 0);
            if ($user->hasRole('Operador')) {
                $query->whereHas('lote', function($q) {
                    $q->where('empresa_tipo', 'CMD');
                });
            }
        }

        $afiliados = $query->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reporte_detallado_'.date('Y-m-d').'.csv"',
        ];

        $callback = function() use ($afiliados) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID', 'Nombre Completo', 'Cedula', 'Contrato', 'Empresa', 'RNC Empresa', 
                'Corte', 'Estado', 'Responsable', 'Fecha Ingreso', 'Fecha Entrega Prov', 
                'SLA Status', 'Costo Entrega', 'Liquidado'
            ]);

            foreach ($afiliados as $a) {
                fputcsv($file, [
                    $a->id,
                    $a->nombre_completo,
                    $a->cedula,
                    $a->contrato,
                    $a->empresaModel->nombre ?? $a->empresa,
                    $a->rnc_empresa,
                    $a->corte->nombre ?? 'N/A',
                    $a->estado->nombre ?? 'N/A',
                    $a->responsable->nombre ?? 'N/A',
                    $a->created_at->format('Y-m-d'),
                    $a->fecha_entrega_proveedor?->format('Y-m-d') ?? '',
                    $a->sla_status,
                    $a->costo_entrega,
                    $a->liquidado ? 'SI' : 'NO'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Genera un reporte de resumen estadístico agrupado por Responsable y Corte
     */
    public function exportSummary(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');

        $query = DB::table('afiliados')
            ->leftJoin('responsables', 'afiliados.responsable_id', '=', 'responsables.id')
            ->leftJoin('cortes', 'afiliados.corte_id', '=', 'cortes.id')
            ->select([
                'responsables.nombre as responsable_nombre',
                'cortes.nombre as corte_nombre',
                DB::raw('count(*) as total'),
                DB::raw('sum(case when estado_id = 9 then 1 else 0 end) as entregados'),
                DB::raw('sum(case when estado_id = 7 then 1 else 0 end) as con_acuse')
            ]);

        // Aplicar filtros de aislamiento si no es admin
        if (!$isAdmin) {
            $query->where('afiliados.responsable_id', $user->responsable_id ?? 0);
            if ($user->hasRole('Operador')) {
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('lotes')
                        ->whereColumn('lotes.id', 'afiliados.lote_id')
                        ->where('empresa_tipo', 'CMD');
                });
            }
        }

        $data = $query->when($request->responsable_id, fn($q) => $q->where('afiliados.responsable_id', $request->responsable_id))
            ->when($request->corte_id, fn($q) => $q->where('afiliados.corte_id', $request->corte_id))
            ->groupBy('responsables.nombre', 'cortes.nombre')
            ->orderBy('cortes.nombre', 'desc')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reporte_resumen_'.date('Y-m-d').'.csv"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Responsable', 'Corte', 'Total Afiliados', 'Entregados (Completados)', 'Con Acuse (Pendiente Recepcion)', 'Pendientes Otros'
            ]);

            foreach ($data as $row) {
                $otros = $row->total - ($row->entregados + $row->con_acuse);
                fputcsv($file, [
                    $row->responsable_nombre ?? 'SIN ASIGNAR',
                    $row->corte_nombre ?? 'SIN CORTE',
                    $row->total,
                    $row->entregados,
                    $row->con_acuse,
                    $otros
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Muestra una tabla visual de resumen en la plataforma
     */
    public function resumenTable(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        $canSeeCmd = $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION']));

        if (!$canSeeCmd) {
            return redirect()->route('reportes.index')->with('error', 'No tiene permisos para acceder al resumen de carnetización.');
        }

        $query = DB::table('afiliados')
            ->leftJoin('responsables', 'afiliados.responsable_id', '=', 'responsables.id')
            ->leftJoin('cortes', 'afiliados.corte_id', '=', 'cortes.id')
            ->select([
                'afiliados.responsable_id',
                'responsables.nombre as responsable_nombre',
                'cortes.nombre as corte_nombre',
                DB::raw('count(*) as total'),
                DB::raw('sum(case when estado_id = 9 then 1 else 0 end) as entregados'),
                DB::raw('sum(case when estado_id = 7 then 1 else 0 end) as con_acuse')
            ]);

        // Aplicar filtros de Scopes manualmente si no es admin (ya que es DB::table)
        if (!$isAdmin) {
            $query->where('afiliados.responsable_id', $user->responsable_id ?? 0);
            if ($user->hasRole('Operador')) {
                $query->whereExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('lotes')
                        ->whereColumn('lotes.id', 'afiliados.lote_id')
                        ->where('empresa_tipo', 'CMD');
                });
            }
        }

        if ($request->filled('responsable_id')) {
            $query->where('afiliados.responsable_id', $request->responsable_id);
        }

        $data = $query->groupBy('afiliados.responsable_id', 'responsables.nombre', 'cortes.nombre')
            ->orderBy('cortes.nombre', 'desc')
            ->get();

        $responsables = \App\Models\Responsable::orderBy('nombre')->get();

        return view('reportes.resumen', compact('data', 'responsables', 'canSeeCmd'));
    }

    public function heatmap()
    {
        $densidadProvincia = Afiliado::select('provincia_id')
            ->selectRaw('count(*) as total')
            ->whereNotNull('provincia_id')
            ->groupBy('provincia_id')
            ->with('provinciaRel')
            ->orderBy('total', 'desc')
            ->get();

        $densidadMunicipio = Afiliado::select('provincia_id', 'municipio_id')
            ->selectRaw('count(*) as total')
            ->whereNotNull('municipio_id')
            ->groupBy('provincia_id', 'municipio_id')
            ->with(['provinciaRel', 'municipioRel'])
            ->orderBy('total', 'desc')
            ->take(20)
            ->get();

        $puntosMapa = Empresa::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->withCount('afiliados')
            ->get();

        return view('reportes.heatmap', compact('densidadProvincia', 'densidadMunicipio', 'puntosMapa'));
    }

    public function comparison()
    {
        $responsables = Responsable::whereIn('nombre', ['ARS CMD', 'SAFESURE'])->get();
        
        $comparisonData = [];
        
        foreach ($responsables as $resp) {
            $query = Afiliado::where('responsable_id', $resp->id);
            
            $total = (clone $query)->count();
            $completados = (clone $query)->whereHas('estado', function($q) {
                $q->whereIn('nombre', ['COMPLETADO', 'LIQUIDADO']);
            })->count();
            
            $criticos = (clone $query)
                ->whereDoesntHave('estado', function($q) { $q->where('nombre', 'COMPLETADO'); })
                ->whereNotNull('fecha_entrega_proveedor')
                ->whereRaw('DATEDIFF(NOW(), fecha_entrega_proveedor) >= 20')
                ->count();
                
            $alertas = (clone $query)
                ->whereDoesntHave('estado', function($q) { $q->where('nombre', 'COMPLETADO'); })
                ->whereNotNull('fecha_entrega_proveedor')
                ->whereRaw('DATEDIFF(NOW(), fecha_entrega_proveedor) >= 15')
                ->whereRaw('DATEDIFF(NOW(), fecha_entrega_proveedor) < 20')
                ->count();
            
            $comparisonData[$resp->nombre] = [
                'id' => $resp->id,
                'total' => $total,
                'completados' => $completados,
                'porcentaje' => $total > 0 ? round(($completados / $total) * 100, 1) : 0,
                'criticos' => $criticos,
                'alertas' => $alertas,
                'por_liquidar' => (clone $query)->whereHas('estado', function($q) {
                    $q->where('nombre', 'COMPLETADO');
                })->where('liquidado', false)->sum('costo_entrega')
            ];
        }
        
        return view('reportes.comparison', compact('comparisonData'));
    }

    public function slaAlerts()
    {
        return view('reportes.sla_alerts');
    }

    public function pendientes(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        $canSeeCmd = $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION']));

        if (!$canSeeCmd) {
            return redirect()->route('reportes.index')->with('error', 'No tiene permisos para acceder al reporte de pendientes de carnetización.');
        }

        // Obtener ID de estados finales o avanzados
        $finalStatesIds = Estado::where('es_final', true)
            ->orWhereIn('nombre', ['Completado', 'Carnet entregado', 'Pendiente de recepción', 'Cierre parcial'])
            ->pluck('id');

        // Agrupación por Corte
        $reporteCortes = Corte::withCount([
            'afiliados as total',
            'afiliados as pendientes_count' => function($q) use ($finalStatesIds) {
                $q->whereNotIn('estado_id', $finalStatesIds);
            },
            'afiliados as entregados_count' => function($q) use ($finalStatesIds) {
                $q->whereIn('estado_id', $finalStatesIds);
            }
        ])
        ->get();

        $mesesMap = [
            'Enero' => 1, 'Febrero' => 2, 'Marzo' => 3, 'Abril' => 4,
            'Mayo' => 5, 'Junio' => 6, 'Julio' => 7, 'Agosto' => 8,
            'Septiembre' => 9, 'Octubre' => 10, 'Noviembre' => 11, 'Diciembre' => 12
        ];

        $reporteCortes = $reporteCortes->sort(function($a, $b) use ($mesesMap) {
            preg_match('/\d{4}/', $a->nombre, $yearA);
            preg_match('/\d{4}/', $b->nombre, $yearB);
            $yA = $yearA[0] ?? 0;
            $yB = $yearB[0] ?? 0;
            if ($yA != $yB) return $yA <=> $yB;

            $mA = 0; $mB = 0;
            foreach ($mesesMap as $nombre => $num) {
                if (str_contains(strtolower($a->nombre), strtolower($nombre))) $mA = $num;
                if (str_contains(strtolower($b->nombre), strtolower($nombre))) $mB = $num;
            }
            if ($mA != $mB) return $mA <=> $mB;

            $nA = (str_contains(strtolower($a->nombre), '1er') || str_contains(strtolower($a->nombre), '1era')) ? 1 : 2;
            $nB = (str_contains(strtolower($b->nombre), '1er') || str_contains(strtolower($b->nombre), '1era')) ? 1 : 2;
            return $nA <=> $nB;
        });

        return view('reportes.pendientes', compact('reporteCortes', 'canSeeCmd'));
    }

    public function exportPendientes()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        $canSeeCmd = $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION']));

        if (!$canSeeCmd) {
            return redirect()->route('reportes.index')->with('error', 'No tiene permisos para exportar pendientes de carnetización.');
        }

        $finalStatesIds = Estado::where('es_final', true)
            ->orWhereIn('nombre', ['Completado', 'Carnet entregado', 'Pendiente de recepción', 'Cierre parcial'])
            ->pluck('id');

        $reporteCortes = Corte::withCount([
            'afiliados as total',
            'afiliados as pendientes_count' => function($q) use ($finalStatesIds) {
                $q->whereNotIn('estado_id', $finalStatesIds);
            },
            'afiliados as entregados_count' => function($q) use ($finalStatesIds) {
                $q->whereIn('estado_id', $finalStatesIds);
            }
        ])
        ->orderBy('id', 'asc')
        ->get()
        ->filter(function($c) {
            return $c->pendientes_count > 0;
        });

        $mesesMap = [
            'Enero' => 1, 'Febrero' => 2, 'Marzo' => 3, 'Abril' => 4,
            'Mayo' => 5, 'Junio' => 6, 'Julio' => 7, 'Agosto' => 8,
            'Septiembre' => 9, 'Octubre' => 10, 'Noviembre' => 11, 'Diciembre' => 12
        ];

        $reporteCortes = $reporteCortes->sort(function($a, $b) use ($mesesMap) {
            // Extraer Año (ej: 2026)
            preg_match('/\d{4}/', $a->nombre, $yearA);
            preg_match('/\d{4}/', $b->nombre, $yearB);
            $yA = $yearA[0] ?? 0;
            $yB = $yearB[0] ?? 0;

            if ($yA != $yB) return $yA <=> $yB;

            // Extraer Mes
            $mA = 0; $mB = 0;
            foreach ($mesesMap as $nombre => $num) {
                if (str_contains($a->nombre, $nombre)) $mA = $num;
                if (str_contains($b->nombre, $nombre)) $mB = $num;
            }

            if ($mA != $mB) return $mA <=> $mB;

            // Extraer Número de Corte (1er o 2do)
            $nA = str_contains($a->nombre, '1er') ? 1 : (str_contains($a->nombre, '2do') ? 2 : 3);
            $nB = str_contains($b->nombre, '1er') ? 1 : (str_contains($b->nombre, '2do') ? 2 : 3);

            return $nA <=> $nB;
        });

        return view('reportes.pendientes_print', compact('reporteCortes'));
    }

    /**
     * Reporte Avanzado de Producción de Traspasos
     */
    public function produccionTraspasos(Request $request)
    {
        $fecha_desde = $request->input('fecha_desde', now()->subMonths(3)->startOfMonth()->format('Y-m-d'));
        $fecha_hasta = $request->input('fecha_hasta', now()->format('Y-m-d'));
        $supervisor_id = $request->input('supervisor_id');

        // 1. Efectividad y Producción (Transaccional)
        $rankingAgentes = Traspaso::select('agente_id')
            ->selectRaw('sum(case when fecha_solicitud >= ? and fecha_solicitud <= ? then 1 else 0 end) as total_solicitudes', [$fecha_desde, $fecha_hasta])
            ->selectRaw('sum(case when fecha_efectivo >= ? and fecha_efectivo <= ? then 1 else 0 end) as efectivos', [$fecha_desde, $fecha_hasta])
            ->selectRaw('sum(case when fecha_efectivo >= ? and fecha_efectivo <= ? then cantidad_dependientes else 0 end) as dependientes_efectivos', [$fecha_desde, $fecha_hasta])
            ->selectRaw('sum(case when estado_id = (select id from estado_traspasos where slug = "rechazado" limit 1) and updated_at >= ? and updated_at <= ? then 1 else 0 end) as rechazados', [$fecha_desde, $fecha_hasta])
            ->selectRaw('sum(case when fecha_efectivo is null and estado_id != (select id from estado_traspasos where slug = "rechazado" limit 1) then 1 else 0 end) as pendientes')
            ->selectRaw('sum(case when fecha_solicitud >= ? and fecha_solicitud <= ? then cantidad_dependientes else 0 end) as total_dependientes_sol', [$fecha_desde, $fecha_hasta])
            ->where(function($q) use ($fecha_desde, $fecha_hasta) {
                $q->whereBetween('fecha_solicitud', [$fecha_desde, $fecha_hasta])
                  ->orWhereBetween('fecha_efectivo', [$fecha_desde, $fecha_hasta]);
            })
            ->when($supervisor_id, function($q) use ($supervisor_id) {
                $q->whereHas('agenteRel', fn($sq) => $sq->where('supervisor_id', $supervisor_id));
            })
            ->groupBy('agente_id')
            ->with('agenteRel.supervisor')
            ->get()
            ->map(function($item) {
                $item->hit_rate = $item->total_solicitudes > 0 ? round(($item->efectivos / $item->total_solicitudes) * 100, 1) : 0;
                $item->total_vidas_efectivas = $item->efectivos + $item->dependientes_efectivos;
                return $item;
            })
            ->sortByDesc('efectivos');

        // 3. Resumen Ejecutivo (Los 4 Números Clave)
        $stats = [
            'total_efectivos' => $rankingAgentes->sum('efectivos'),
            'total_dependientes_efectivos' => $rankingAgentes->sum('dependientes_efectivos'),
            'total_pendientes' => $rankingAgentes->sum('pendientes'),
            'total_rechazados' => $rankingAgentes->sum('rechazados'),
            'total_solicitudes' => $rankingAgentes->sum('total_solicitudes'),
            'hit_rate_promedio' => $rankingAgentes->count() > 0 ? round($rankingAgentes->avg('hit_rate'), 1) : 0
        ];

        // 2. Tendencia de Producción (Últimos 6 Meses)
        $tendencia = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;
            
            $queryBase = Traspaso::query();
            if ($supervisor_id) {
                $queryBase->whereHas('agenteRel', fn($sq) => $sq->where('supervisor_id', $supervisor_id));
            }

            $total = (clone $queryBase)->whereMonth('fecha_solicitud', $month)->whereYear('fecha_solicitud', $year)->count();
            $efectivos = (clone $queryBase)->whereMonth('fecha_efectivo', $month)->whereYear('fecha_efectivo', $year)->count();

            $tendencia[] = [
                'label' => $date->translatedFormat('M Y'),
                'total' => $total,
                'efectivos' => $efectivos,
                'hit_rate' => $total > 0 ? round(($efectivos / $total) * 100, 1) : 0
            ];
        }

        $supervisores = SupervisorTraspaso::where('activo', true)->get();

        return view('reportes.produccion_traspasos', compact(
            'rankingAgentes', 'tendencia', 'stats', 'supervisores',
            'fecha_desde', 'fecha_hasta', 'supervisor_id'
        ));
    }
}
