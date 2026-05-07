<?php

namespace App\Http\Controllers\Modules\Traspasos;

use App\Http\Controllers\Controller;
use App\Models\Traspaso;
use App\Models\AgenteTraspaso;
use App\Models\SupervisorTraspaso;
use App\Models\EstadoTraspaso;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TraspasoController extends Controller
{
    /**
     * Display the transfer inbox (Bandeja).
     */
    public function index(Request $request)
    {
        $query = Traspaso::query();

        // Basic Filtering
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_afiliado', 'like', "%{$search}%")
                  ->orWhere('cedula_afiliado', 'like', "%{$search}%")
                  ->orWhere('numero_solicitud_epbd', 'like', "%{$search}%");
            });
        }

        if ($request->has('estado') && $request->estado != 'all') {
            $query->where('estado_id', $request->estado);
        }

        if ($request->has('agente') && $request->agente != 'all') {
            $query->where('agente_id', $request->agente);
        }

        if ($request->has('supervisor') && $request->supervisor != 'all') {
            $query->whereHas('agenteRel', function($q) use ($request) {
                $q->where('supervisor_id', $request->supervisor);
            });
        }

        if ($request->has('efectividad') && $request->efectividad != 'all') {
            if ($request->efectividad == 'efectivas') {
                $query->whereNotNull('fecha_efectivo');
            } elseif ($request->efectividad == 'no_efectivas') {
                $query->whereNull('fecha_efectivo');
            }
        }

        $traspasos = $query->with(['agenteRel.supervisor', 'estadoRel', 'motivoRechazoRel'])->orderBy('fecha_solicitud', 'desc')->paginate(30);
        $agentes = AgenteTraspaso::where('activo', true)->get();
        $estados = EstadoTraspaso::all();
        $supervisores = SupervisorTraspaso::where('activo', true)->get();
        $motivosRechazo = \App\Models\MotivoRechazoTraspaso::where('activo', true)->get();

        return view('modules.traspasos.index', compact('traspasos', 'agentes', 'estados', 'supervisores', 'motivosRechazo'));
    }

    /**
     * Display the transfer dashboard.
     */
    public function dashboard(Request $request)
    {
        $now = now();
        if ($request->has('month') && $request->has('year')) {
            $now = \Carbon\Carbon::create($request->year, $request->month, 1);
        }

        $currentMonth = $now->month;
        $currentYear = $now->year;

        $currentMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        
        // Meta del Mes
        $metaMesTotal = \App\Models\MetaTraspaso::whereYear('periodo', $now->year)
                                        ->whereMonth('periodo', $now->month)
                                        ->sum('meta_cantidad');

        // Stats Generales (Acumulado Histórico)
        $globalTotalTitulares = Traspaso::count();
        $globalTotalDependientes = Traspaso::sum('cantidad_dependientes');
        $globalEfectivos = Traspaso::whereHas('estadoRel', fn($q) => $q->where('slug', 'efectivo'))->count();
        
        $statsGlobal = [
            'total_vidas' => $globalTotalTitulares + $globalTotalDependientes,
            'total_titulares' => $globalTotalTitulares,
            'total_dependientes' => $globalTotalDependientes,
            'efectivos' => $globalEfectivos,
            'hit_rate' => $globalTotalTitulares > 0 ? round(($globalEfectivos / $globalTotalTitulares) * 100, 1) : 0,
        ];

        // Stats del Mes (Filtro Actual)
        $stats = [
            'total' => Traspaso::count(),
            'efectivos' => Traspaso::whereHas('estadoRel', fn($q) => $q->where('slug', 'efectivo'))->count(),
            'rechazados' => Traspaso::whereHas('estadoRel', fn($q) => $q->where('slug', 'rechazado'))->count(),
            'generados_mes' => Traspaso::whereYear('fecha_solicitud', $now->year)
                                        ->whereMonth('fecha_solicitud', $now->month)
                                        ->count(),
            'total_dependientes' => Traspaso::whereYear('fecha_solicitud', $now->year)
                                        ->whereMonth('fecha_solicitud', $now->month)
                                        ->sum('cantidad_dependientes'),
            'meta_mes' => $metaMesTotal,
            'efectivos_reales_mes' => Traspaso::whereYear('fecha_solicitud', $now->year)
                                        ->whereMonth('fecha_solicitud', $now->month)
                                        ->whereHas('estadoRel', fn($q) => $q->where('slug', 'efectivo'))
                                        ->count(),
        ];

        // Stats Mes Anterior (Para comparación - Basado en SOLICITUD)
        $solicitudesActual = $stats['generados_mes'] + $stats['total_dependientes'];
        
        $solicitudesPrev = Traspaso::whereYear('fecha_solicitud', $lastMonthStart->year)
                                    ->whereMonth('fecha_solicitud', $lastMonthStart->month)
                                    ->count();
        $dependientesPrev = Traspaso::whereYear('fecha_solicitud', $lastMonthStart->year)
                                    ->whereMonth('fecha_solicitud', $lastMonthStart->month)
                                    ->sum('cantidad_dependientes');
        
        $vidasPrev = $solicitudesPrev + $dependientesPrev;
        $crecimiento = $vidasPrev > 0 ? (($solicitudesActual - $vidasPrev) / $vidasPrev) * 100 : 0;

        // 1. Efectividad (Hit Rate) con LAG de 2 meses (Justicia operativa)
        $monthLag = $now->copy()->subMonths(2);
        $solicitudesLag = Traspaso::whereYear('fecha_solicitud', $monthLag->year)
                                    ->whereMonth('fecha_solicitud', $monthLag->month)
                                    ->count();
        $efectivosLag = Traspaso::whereYear('fecha_solicitud', $monthLag->year)
                                    ->whereMonth('fecha_solicitud', $monthLag->month)
                                    ->whereHas('estadoRel', fn($q) => $q->where('slug', 'efectivo'))
                                    ->count();
        
        $hitRateHistorico = $solicitudesLag > 0 ? ($efectivosLag / $solicitudesLag) * 100 : 0;

        // 2. SLA de Maduración (Basado en lo que se hizo efectivo ESTE mes)
        $maduracionPromedio = Traspaso::whereNotNull('fecha_solicitud')
            ->whereNotNull('fecha_efectivo')
            ->whereYear('fecha_efectivo', $now->year)
            ->whereMonth('fecha_efectivo', $now->month)
            ->selectRaw('AVG(DATEDIFF(fecha_efectivo, fecha_solicitud)) as avg_days')
            ->first()->avg_days ?? 0;

        // 3. Casos Estancados (Pendientes con más de 60 días)
        $casosEstancados = Traspaso::whereHas('estadoRel', fn($q) => $q->where('slug', 'proceso'))
            ->where('fecha_solicitud', '<=', $now->copy()->subDays(60))
            ->count();

        // KPI: Ratio Familiar
        $ratioFamiliar = $stats['generados_mes'] > 0 ? ($stats['total_dependientes'] / $stats['generados_mes']) : 0;

        // Ranking por Agente (Top 10) - Basado en SOLICITUD
        $rankingAgentes = Traspaso::select('agente_id')
            ->with('agenteRel.supervisor')
            ->selectRaw('count(*) as total_titulares')
            ->selectRaw('sum(cantidad_dependientes) as total_dep')
            ->whereYear('fecha_solicitud', $now->year)
            ->whereMonth('fecha_solicitud', $now->month)
            ->groupBy('agente_id')
            ->orderByDesc(\DB::raw('count(*) + sum(cantidad_dependientes)'))
            ->limit(10)
            ->get();

        // Ranking por Equipo (Supervisor) - Basado en SOLICITUD
        $rankingEquipos = SupervisorTraspaso::select('supervisor_traspasos.id', 'supervisor_traspasos.nombre')
            ->leftJoin('agente_traspasos', 'supervisor_traspasos.id', '=', 'agente_traspasos.supervisor_id')
            ->leftJoin('traspasos', 'agente_traspasos.id', '=', 'traspasos.agente_id')
            ->whereYear('traspasos.fecha_solicitud', $now->year)
            ->whereMonth('traspasos.fecha_solicitud', $now->month)
            ->selectRaw('count(traspasos.id) as total_titulares')
            ->selectRaw('sum(traspasos.cantidad_dependientes) as total_dep')
            ->groupBy('supervisor_traspasos.id', 'supervisor_traspasos.nombre')
            ->orderByDesc(\DB::raw('count(traspasos.id) + sum(traspasos.cantidad_dependientes)'))
            ->get();

        // Motivos de Rechazo (Unificado: Texto + Relación)
        $rechazosRaw = Traspaso::whereHas('estadoRel', fn($q) => $q->where('slug', 'rechazado'))
            ->whereYear('fecha_solicitud', $now->year)
            ->whereMonth('fecha_solicitud', $now->month)
            ->with('motivoRechazoRel')
            ->get();

        $rankingMotivos = [];
        foreach ($rechazosRaw as $r) {
            $label = $r->motivoRechazoRel->descripcion ?? ($r->motivos_estado ?: 'Sin motivo especificado');
            if (!isset($rankingMotivos[$label])) {
                $rankingMotivos[$label] = 0;
            }
            $rankingMotivos[$label]++;
        }

        arsort($rankingMotivos);
        $motivosRechazo = collect($rankingMotivos)->take(5)->map(function($total, $label) {
            return (object)[
                'motivos_estado' => $label,
                'total' => $total
            ];
        });

        // Datos para el Gráfico de Tendencia (Últimos 6 meses)
        $labels = [];
        $dataGenerados = [];
        $dataEfectivos = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->copy()->subMonths($i);
            $labels[] = $month->translatedFormat('F');
            $dataGenerados[] = Traspaso::whereYear('fecha_solicitud', $month->year)
                                        ->whereMonth('fecha_solicitud', $month->month)
                                        ->count();
            $dataEfectivos[] = Traspaso::whereYear('fecha_solicitud', $month->year)
                                        ->whereMonth('fecha_solicitud', $month->month)
                                        ->whereHas('estadoRel', fn($q) => $q->where('slug', 'efectivo'))
                                        ->count();
        }

        $chartData = ['labels' => $labels, 'generados' => $dataGenerados, 'efectivos' => $dataEfectivos];

        return view('modules.traspasos.dashboard', compact(
            'stats', 'statsGlobal', 'rankingAgentes', 'rankingEquipos', 'motivosRechazo', 
            'chartData', 'hitRateHistorico', 'maduracionPromedio', 'ratioFamiliar', 'crecimiento', 'casosEstancados',
            'currentMonth', 'currentYear'
        ));
    }

    /**
     * Show the Unipago sync view.
     */
    public function syncUnipagoView()
    {
        return view('modules.traspasos.sync_unipago');
    }

    /**
     * Process the Unipago detailed report sync.
     */
    public function processSyncUnipago(Request $request)
    {
        $request->validate([
            'data' => 'required|string'
        ]);

        $lines = explode("\n", $request->data);
        $created = 0;
        $updated = 0;
        $unchanged = 0;

        // IDs de estados críticos (Búsqueda directa para seguridad)
        $idProceso = EstadoTraspaso::where('slug', 'proceso')->first()->id ?? 1;
        $idRechazado = EstadoTraspaso::where('slug', 'rechazado')->first()->id ?? 2;
        $idEfectivo = EstadoTraspaso::where('slug', 'efectivo')->first()->id ?? 3;

        $motivosMap = \App\Models\MotivoRechazoTraspaso::pluck('id', 'codigo_unsigima')->toArray();

        foreach ($lines as $index => $line) {
            $rawLine = trim($line);
            if ($index === 0 || empty($rawLine)) continue;
            
            // Regex para dividir por TAB o por 2+ espacios (muy común en copy-paste)
            $columns = preg_split('/\t|\s{2,}/', $rawLine);
            if (count($columns) < 10) continue;

            $numeroSolicitud = trim($columns[0]);
            $cedula = preg_replace('/[^0-9]/', '', $columns[3] ?? '');
            
            // Buscar la columna de estado (buscamos RE, OK, PE o sus nombres largos)
            $estadoIdx = -1;
            foreach ($columns as $idx => $val) {
                $val = strtoupper(trim($val));
                if (in_array($val, ['RE', 'RECHAZADO', 'RECHAZADA', 'OK', 'EFECTIVO', 'EF', 'PE', 'PROCESO', 'EN PROCESO'])) {
                    $estadoIdx = $idx;
                    break;
                }
            }

            if ($estadoIdx === -1) continue;

            $estadoTexto = strtoupper(trim($columns[$estadoIdx]));
            $motivoCodigo = trim($columns[$estadoIdx + 1] ?? '');

            $estadoId = $idProceso;
            if (str_contains($estadoTexto, 'RE')) $estadoId = $idRechazado;
            elseif (str_contains($estadoTexto, 'OK') || str_contains($estadoTexto, 'EF')) $estadoId = $idEfectivo;

            // Homologación Inteligente de Motivos
            $motivoId = null;
            if (!empty($motivoCodigo)) {
                $motivoId = $motivosMap[$motivoCodigo] ?? null;
                
                if (!$motivoId) {
                    // Buscar por Sisalril si falla Unsigima
                    $motivoObj = \App\Models\MotivoRechazoTraspaso::where('codigo_sisalril', $motivoCodigo)
                        ->orWhere('codigo_unsigima', $motivoCodigo)
                        ->first();
                    
                    if (!$motivoObj) {
                        // AUTO-CREACIÓN: Si no existe, lo creamos para que tenga "lectura"
                        $motivoObj = \App\Models\MotivoRechazoTraspaso::create([
                            'codigo_unsigima' => $motivoCodigo,
                            'codigo_sisalril' => $motivoCodigo,
                            'descripcion' => "RECHAZO UNIPAGO ($motivoCodigo)",
                            'activo' => true
                        ]);
                        // Actualizar mapa para la siguiente línea
                        $motivosMap[$motivoCodigo] = $motivoObj->id;
                    }
                    $motivoId = $motivoObj->id;
                }
            }

            $traspaso = Traspaso::where('numero_solicitud_epbd', $numeroSolicitud)->first();

            $updateData = [
                'estado_id' => $estadoId,
                'motivo_rechazo_id' => $motivoId,
                'motivos_estado' => (!$motivoId && !empty($motivoCodigo)) ? "PENDIENTE_HOMOLOGACIÓN: $motivoCodigo" : ($traspaso->motivos_estado ?? null),
            ];

            if ($traspaso) {
                // Actualizar siempre
                if ($traspaso->estado_id != $updateData['estado_id'] || $traspaso->motivo_rechazo_id != $updateData['motivo_rechazo_id']) {
                    
                    if ($estadoId == $idEfectivo) {
                        $updateData['fecha_efectivo'] = now();
                        // El periodo se calcula automáticamente si el modelo tiene observadores, 
                        // sino lo forzamos aquí si es necesario.
                    } elseif ($estadoId == $idRechazado) {
                        // REVERSIÓN: Si se rechaza algo que era efectivo, limpiamos las fechas
                        $updateData['fecha_efectivo'] = null;
                        $updateData['periodo_efectivo'] = null;
                    }

                    $traspaso->update($updateData);
                    $updated++;
                } else {
                    $unchanged++;
                }
            } else {
                // Si es nuevo, lo creamos
                $newData = array_merge($updateData, [
                    'numero_solicitud_epbd' => $numeroSolicitud,
                    'cedula_afiliado' => $cedula,
                    'fecha_solicitud' => $fechaRecepcion, // USAR FECHA DEL REPORTE COMO FALLBACK
                    'fecha_envio_epbd' => $fechaRecepcion,
                    'nombre_afiliado' => 'SINC_NUEVO_' . $cedula,
                ]);
                Traspaso::create($newData);
                $created++;
            }
        }

        return redirect()->route('traspasos.index')->with('success', "Sincronización Unipago Finalizada. Actualizados: $updated, Nuevos: $created, Sin cambios: $unchanged");
    }

    /**
     * Get history of a transfer.
     */
    public function history(Traspaso $traspaso)
    {
        $logs = \App\Models\AuditLog::where('model_type', get_class($traspaso))
            ->where('model_id', $traspaso->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($log) {
                $description = 'Acción realizada';
                
                if ($log->event === 'created') {
                    $description = 'Solicitud creada en el sistema';
                } elseif ($log->event === 'updated') {
                    $changes = [];
                    foreach ($log->new_values ?? [] as $key => $value) {
                        $old = $log->old_values[$key] ?? 'N/A';
                        
                        // Traducción de campos técnicos
                        $label = str_replace('_', ' ', $key);
                        if ($key === 'estado_id') {
                            $oldName = \App\Models\EstadoTraspaso::find($old)->nombre ?? $old;
                            $newName = \App\Models\EstadoTraspaso::find($value)->nombre ?? $value;
                            $changes[] = "Estado: <b>{$oldName}</b> → <b>{$newName}</b>";
                        } elseif ($key === 'agente_id') {
                            $oldName = \App\Models\AgenteTraspaso::find($old)->nombre ?? $old;
                            $newName = \App\Models\AgenteTraspaso::find($value)->nombre ?? $value;
                            $changes[] = "Agente: <b>{$oldName}</b> → <b>{$newName}</b>";
                        } else {
                            $changes[] = "{$label}: <b>{$old}</b> → <b>{$value}</b>";
                        }
                    }
                    $description = implode('<br>', $changes);
                }

                return [
                    'id' => $log->id,
                    'user' => $log->user->name ?? 'Sistema',
                    'event' => $log->event,
                    'description' => $description,
                    'date' => $log->created_at->format('d/m/Y h:i A'),
                    'relative_date' => $log->created_at->diffForHumans(),
                ];
            });

        return response()->json($logs);
    }

    /**
     * Export filtered transfers to CSV.
     */
    public function export(Request $request)
    {
        $query = Traspaso::query();

        // Replicamos filtros de index (Corregidos para esquema normalizado)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre_afiliado', 'like', "%{$search}%")
                  ->orWhere('cedula_afiliado', 'like', "%{$search}%")
                  ->orWhere('numero_solicitud_epbd', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('estado') && $request->estado != 'all') {
            $query->where('estado_id', $request->estado);
        }

        if ($request->has('agente') && $request->agente != 'all') {
            $query->where('agente_id', $request->agente);
        }
        
        if ($request->has('supervisor') && $request->supervisor != 'all') {
            $agenteIds = AgenteTraspaso::where('supervisor_id', $request->supervisor)->pluck('id');
            $query->whereIn('agente_id', $agenteIds);
        }

        if ($request->has('efectividad') && $request->efectividad != 'all') {
            if ($request->efectividad == 'efectivas') {
                $query->whereNotNull('fecha_efectivo');
            } elseif ($request->efectividad == 'no_efectivas') {
                $query->whereNull('fecha_efectivo');
            }
        }

        $traspasos = $query->with(['agenteRel', 'estadoRel'])->orderBy('fecha_solicitud', 'desc')->get();

        $filename = "reporte_traspasos_" . now()->format('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Nombre Afiliado', 'Cedula', 'Solicitud EPBD', 'Fecha Solicitud', 
            'Agente', 'Estado', 'Motivo', 'Fecha Efectiva', 'Periodo', 'Dependientes'
        ];

        $callback = function() use($traspasos, $columns) {
            $file = fopen('php://output', 'w');
            // Bom para Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $columns, ';');

            foreach ($traspasos as $t) {
                fputcsv($file, [
                    $t->nombre_afiliado,
                    $t->cedula_afiliado,
                    $t->numero_solicitud_epbd,
                    $t->fecha_solicitud ? $t->fecha_solicitud->format('d/m/Y') : 'N/A',
                    $t->agenteRel->nombre ?? 'N/A',
                    $t->estadoRel->nombre ?? 'N/A',
                    $t->motivos_estado,
                    $t->fecha_efectivo ? $t->fecha_efectivo->format('d/m/Y') : '',
                    $t->periodo_efectivo,
                    $t->cantidad_dependientes,
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for importing transfers.
     */
    public function importView()
    {
        return view('modules.traspasos.import');
    }

    /**
     * Handle the data import (CSV/TSV/Paste).
     */
    public function import(Request $request)
    {
        $request->validate([
            'data' => 'required|string'
        ]);

        $lines = explode("\n", $request->data);
        $count = 0;
        $updated = 0;

        // Cachear agentes, estados y motivos para optimizar
        $agentesMap = [];
        foreach (\App\Models\AgenteTraspaso::all() as $ag) {
            $agentesMap[Traspaso::normalizeName($ag->nombre)] = $ag->id;
        }
        
        $estadosMap = EstadoTraspaso::pluck('id', 'slug')->toArray();
        $motivosMap = \App\Models\MotivoRechazoTraspaso::pluck('id', 'codigo_unsigima')->toArray();
        $defaultEstadoId = $estadosMap['proceso'] ?? null;

        foreach ($lines as $index => $line) {
            $rawLine = trim($line);
            if ($index === 0 || empty($rawLine)) continue;
            
            $columns = explode("\t", $rawLine);
            if (count($columns) < 5) continue;

            // DETECCIÓN DE FORMATO
            // Si tiene >= 13 columnas, es el reporte detallado de Unipago
            $isDetailedReport = count($columns) >= 13;
            
            if ($isDetailedReport) {
                // Mapeo Reporte Unipago
                $numeroSolicitud = trim($columns[0]);
                $cedula = preg_replace('/[^0-9]/', '', $columns[3] ?? '');
                $estadoLetra = strtoupper(trim($columns[12] ?? ''));
                $motivoCodigo = trim($columns[13] ?? '10140');
                
                $estadoId = $defaultEstadoId;
                if ($estadoLetra === 'RE') $estadoId = $estadosMap['rechazado'] ?? $estadoId;
                elseif ($estadoLetra === 'PE') $estadoId = $estadosMap['proceso'] ?? $estadoId;
                elseif ($estadoLetra === 'OK' || $estadoLetra === 'EF') $estadoId = $estadosMap['efectivo'] ?? $estadoId;

                $data = [
                    'numero_solicitud_epbd' => $numeroSolicitud,
                    'cedula_afiliado' => $cedula,
                    'estado_id' => $estadoId,
                    'motivo_rechazo_id' => $motivosMap[$motivoCodigo] ?? null,
                    'nombre_afiliado' => $columns[0] ?? 'N/A', // Temporal si es nuevo
                ];
            } else {
                // Formato Simple Original
                $agenteNombre = Traspaso::normalizeName($columns[9] ?? 'Desconocido');
                $agenteId = $agentesMap[$agenteNombre] ?? null;

                $estadoTexto = strtoupper($columns[10] ?? '');
                $estadoId = $defaultEstadoId;
                if (str_contains($estadoTexto, 'RE')) $estadoId = $estadosMap['rechazado'] ?? $estadoId;
                elseif (str_contains($estadoTexto, 'EN')) $estadoId = $estadosMap['proceso'] ?? $estadoId;
                
                $data = [
                    'nombre_afiliado' => $columns[0] ?? '',
                    'cedula_afiliado' => $columns[1] ?? '',
                    'nombre_solicitante' => $columns[2] ?? '',
                    'cedula_solicitante' => $columns[3] ?? '',
                    'fecha_solicitud' => $this->parseDate($columns[4] ?? null),
                    'fecha_envio_epbd' => $this->parseDate($columns[5] ?? null),
                    'numero_solicitud_epbd' => $columns[6] ?? null,
                    'pendiente_carga_documento' => strtolower($columns[7] ?? '') === 'si',
                    'pendiente_aprobar_consentimiento' => strtolower($columns[8] ?? '') === 'si',
                    'agente_id' => $agenteId,
                    'estado_id' => $estadoId,
                    'motivos_estado' => $columns[11] ?? '',
                ];
            }

            // SINCRONIZACIÓN POR NÚMERO DE SOLICITUD
            if ($data['numero_solicitud_epbd']) {
                $traspaso = Traspaso::where('numero_solicitud_epbd', $data['numero_solicitud_epbd'])->first();

                if ($traspaso) {
                    $updateFields = [
                        'estado_id' => $data['estado_id'],
                    ];
                    if (isset($data['motivo_rechazo_id'])) $updateFields['motivo_rechazo_id'] = $data['motivo_rechazo_id'];
                    if (isset($data['motivos_estado'])) $updateFields['motivos_estado'] = $data['motivos_estado'];
                    
                    $traspaso->update($updateFields);
                    $updated++;
                } else {
                    // Si es nuevo desde el reporte detallado, creamos lo básico
                    Traspaso::create($data);
                    $count++;
                }
            }
        }

        return redirect()->route('traspasos.index')->with('success', "Sincronización completada. Nuevos: $count, Actualizados: $updated");
    }

    /**
     * Update enrichment data (effectiveness and dependents).
     */
    /**
     * Show the bulk effective marking view.
     */
    public function bulkEffectiveView()
    {
        return view('modules.traspasos.bulk_effective');
    }

    /**
     * Process the bulk effective marking.
     */
    public function processBulkEffective(Request $request)
    {
        $request->validate([
            'cedulas' => 'required|string',
            'fecha_efectivo' => 'required|date',
            'periodo_efectivo' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        // Extraer cédulas y sanitizar (solo números)
        $rawCedulas = preg_split('/[\n\r\t,]+/', $request->cedulas, -1, PREG_SPLIT_NO_EMPTY);
        $cedulas = array_unique(array_map(function($c) {
            return preg_replace('/[^0-9]/', '', $c);
        }, $rawCedulas));

        // 1. Obtener todos los traspasos potenciales para estas cédulas
        // Buscamos comparando cédulas sanitizadas (sin guiones)
        $traspasosPotenciales = Traspaso::whereIn(\DB::raw("REPLACE(cedula_afiliado, '-', '')"), $cedulas)->get();
        $agrupados = $traspasosPotenciales->groupBy(function($item) {
            return preg_replace('/[^0-9]/', '', $item->cedula_afiliado);
        });

        $idsParaActualizar = [];
        $duplicadosConflictivos = [];

        foreach ($agrupados as $cedula => $registros) {
            if ($registros->count() === 1) {
                // Caso simple: Solo hay uno
                $idsParaActualizar[] = $registros->first()->id;
            } else {
                // Caso duplicado: Aplicar regla de negocio (Priorizar EN sobre RE)
                $filtradosEN = $registros->filter(fn($r) => str_contains($r->numero_solicitud_epbd, 'EN'));
                
                if ($filtradosEN->count() === 1) {
                    // La regla de negocio resolvió el conflicto
                    $idsParaActualizar[] = $filtradosEN->first()->id;
                } else {
                    // Sigue habiendo ambigüedad (o no hay ninguno EN, o hay varios EN)
                    $duplicadosConflictivos[] = $cedula;
                }
            }
        }

        // 2. Ejecutar la actualización masiva para los IDs resueltos
        if (!empty($idsParaActualizar)) {
            Traspaso::whereIn('id', $idsParaActualizar)
                ->update([
                    'fecha_efectivo' => $request->fecha_efectivo,
                    'periodo_efectivo' => $request->periodo_efectivo,
                ]);
        }

        $totalProcesados = count($idsParaActualizar);

        if (count($duplicadosConflictivos) > 0) {
            return back()->with('success', "Se han procesado {$totalProcesados} traspasos correctamente.")
                ->withErrors([
                    'cedulas' => "Las siguientes cédulas tienen múltiples solicitudes (Sin patrón EN/RE claro) y deben revisarse: " . implode(', ', $duplicadosConflictivos)
                ])->withInput();
        }

        return back()->with('success', "Se han marcado {$totalProcesados} traspasos como efectivos correctamente.");
    }

    public function updateEnrichment(Request $request, Traspaso $traspaso)
    {
        $request->validate([
            'fecha_efectivo' => 'nullable|date',
            'periodo_efectivo' => 'nullable|string|regex:/^\d{4}-\d{2}$/',
            'cantidad_dependientes' => 'nullable|integer|min:0',
        ]);

        $data = $request->only(['fecha_efectivo', 'periodo_efectivo', 'cantidad_dependientes']);

        // Autocalcular periodo si se envía fecha
        if (!empty($data['fecha_efectivo'])) {
            $data['periodo_efectivo'] = \Carbon\Carbon::parse($data['fecha_efectivo'])->format('Y-m');
        }

        $traspaso->update($data);

        return response()->json(['success' => true, 'message' => 'Datos actualizados correctamente']);
    }

    public function rechazar(Request $request, Traspaso $traspaso)
    {
        $request->validate([
            'motivo_id' => 'nullable|exists:motivo_rechazo_traspasos,id',
            'motivos_estado' => 'required|string'
        ]);

        $estadoRechazado = EstadoTraspaso::where('slug', 'rechazado')->first();

        $traspaso->update([
            'estado_id' => $estadoRechazado->id,
            'motivo_rechazo_id' => $request->motivo_id,
            'motivos_estado' => $request->motivos_estado,
            'fecha_efectivo' => null,
            'periodo_efectivo' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Traspaso marcado como rechazado.']);
    }

    /**
     * Emitir carnet (crear afiliado) desde un traspaso efectivo.
     */
    public function emitirCarnet(Traspaso $traspaso)
    {
        // Verificar si ya existe un afiliado con esa cédula
        $exists = \App\Models\Afiliado::where('cedula', $traspaso->cedula_afiliado)->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Ya existe un afiliado registrado con esta cédula.']);
        }

        // Obtener el corte más reciente (es obligatorio para crear un afiliado)
        $ultimoCorte = \App\Models\Corte::latest()->first();

        if (!$ultimoCorte) {
            return response()->json(['success' => false, 'message' => 'No hay periodos de corte creados en el sistema para asignar el afiliado.']);
        }

        // Crear el afiliado
        $afiliado = \App\Models\Afiliado::create([
            'nombre_completo' => $traspaso->nombre_afiliado,
            'cedula' => $traspaso->cedula_afiliado,
            'estado_id' => 1, // Pendiente
            'corte_id' => $ultimoCorte->id,
            // Podríamos añadir más lógica de asignación aquí
        ]);

        // Marcar como emitido
        $estadoEmitido = EstadoTraspaso::where('slug', 'emitido')->first();
        $traspaso->update([
            'es_emitido' => true,
            'estado_id' => $estadoEmitido->id ?? $traspaso->estado_id
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Afiliado creado correctamente en el módulo de carnetización.',
            'uuid' => $afiliado->uuid
        ]);
    }

    private function parseDate($dateString)
    {
        if (!$dateString || trim($dateString) == '') return null;
        try {
            // Unipago dates are often DD/MM/YYYY
            return Carbon::createFromFormat('d/m/Y', $dateString)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($dateString)->format('Y-m-d');
            } catch (\Exception $e2) {
                return null;
            }
        }
    }
}
