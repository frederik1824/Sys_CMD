<?php

namespace App\Services;

use App\Models\Afiliado;
use App\Models\SolicitudAfiliacion;
use App\Models\Traspaso;
use App\Models\DispersionPeriod;
use App\Models\PssCentro;
use App\Models\PssMedico;
use App\Models\CallCenterRegistro;
use App\Models\Asistencia\Registro as AsistenciaRegistro;
use App\Models\Asistencia\Empleado as AsistenciaEmpleado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ExecutiveDashboardService
{
    /**
     * Devuelve todas las métricas consolidadas o desde la caché.
     */
    public function getAllMetrics(string $period = 'month')
    {
        $cacheKey = "executive_dashboard_full_{$period}";
        
        // Use a 15 min cache for the request if not already warmed up
        return Cache::remember($cacheKey, 900, function() use ($period) {
            $dates = $this->getDateRange($period);
            
            return [
                'resumen' => $this->getResumenMetrics($dates),
                'carnetizacion' => $this->getCarnetizacionMetrics($dates),
                'afiliacion' => $this->getAfiliacionMetrics($dates),
                'traspasos' => $this->getTraspasosMetrics($dates),
                'dispersion' => $this->getDispersionMetrics($dates),
                'pss' => $this->getPssMetrics($dates),
                'callcenter' => $this->getCallCenterMetrics($dates),
                'asistencia' => $this->getAsistenciaMetrics($dates),
                'alertas' => $this->generateAlerts($dates),
                'tendencia' => $this->getGlobalTrend()
            ];
        });
    }

    private function getDateRange(string $period): array
    {
        $now = Carbon::now();
        switch ($period) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay(), $now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()];
            case 'week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), $now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()];
            case 'year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear(), $now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()];
            case 'month':
            default:
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), $now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()];
        }
    }

    private function calculateVariation($current, $previous)
    {
        if ($previous == 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function getResumenMetrics($dates)
    {
        list($start, $end, $prevStart, $prevEnd) = $dates;

        $totalAfiliados = Afiliado::whereBetween('created_at', [$start, $end])->count();
        $prevAfiliados = Afiliado::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $solicitudes = SolicitudAfiliacion::whereBetween('created_at', [$start, $end])->count();
        $prevSolicitudes = SolicitudAfiliacion::whereBetween('created_at', [$prevStart, $prevEnd])->count();

        $traspasos = Traspaso::whereBetween('fecha_solicitud', [$start, $end])->count();
        $prevTraspasos = Traspaso::whereBetween('fecha_solicitud', [$prevStart, $prevEnd])->count();

        $asistencia = AsistenciaRegistro::whereDate('fecha', Carbon::today())->count();
        $empleados = AsistenciaEmpleado::where('estado', 'Activo')->count();

        return [
            'afiliados' => [
                'value' => $totalAfiliados,
                'variation' => $this->calculateVariation($totalAfiliados, $prevAfiliados),
                'label' => 'Total Afiliados'
            ],
            'solicitudes' => [
                'value' => $solicitudes,
                'variation' => $this->calculateVariation($solicitudes, $prevSolicitudes),
                'label' => 'Solicitudes de Afiliación'
            ],
            'traspasos' => [
                'value' => $traspasos,
                'variation' => $this->calculateVariation($traspasos, $prevTraspasos),
                'label' => 'Traspasos Gestionados'
            ],
            'asistencia' => [
                'value' => $asistencia,
                'total' => $empleados,
                'label' => 'Personal Presente Hoy'
            ]
        ];
    }

    public function getCarnetizacionMetrics($dates)
    {
        list($start, $end) = $dates;
        $q = Afiliado::whereBetween('created_at', [$start, $end]);
        
        $total = (clone $q)->count();
        $completados = (clone $q)->whereHas('estado', function($e) { $e->whereIn('nombre', ['Completado', 'Cierre parcial', 'Entregado']); })->count();
        $pendientesRecepcion = (clone $q)->where('estado_id', 7)->count();
        
        $estados = DB::table('afiliados')
            ->join('estados', 'afiliados.estado_id', '=', 'estados.id')
            ->whereBetween('afiliados.created_at', [$start, $end])
            ->select('estados.nombre as label', DB::raw('count(*) as value'))
            ->groupBy('estados.nombre')
            ->get()
            ->toArray();

        return [
            'total' => $total,
            'completados' => $completados,
            'pendientes_recepcion' => $pendientesRecepcion,
            'tasa_entrega' => $total > 0 ? round(($completados / $total) * 100, 1) : 0,
            'criticos_sla' => Afiliado::whereDoesntHave('estado', function($e) { $e->where('nombre', 'COMPLETADO'); })
                                    ->whereNotNull('fecha_entrega_proveedor')
                                    ->whereRaw('DATEDIFF(NOW(), fecha_entrega_proveedor) >= 20')
                                    ->count(),
            'estados_distribucion' => $estados
        ];
    }

    public function getAfiliacionMetrics($dates)
    {
        list($start, $end) = $dates;
        $q = SolicitudAfiliacion::whereBetween('created_at', [$start, $end]);

        $recibidas = (clone $q)->count();
        $aprobadas = (clone $q)->where('estado', 'Aprobada')->count();
        $devueltas = (clone $q)->where('estado', 'Devuelta')->count();

        // Tiempo de respuesta aproximado (horas) de las completadas en este periodo
        $tiempoResp = DB::table('solicitudes_afiliacion')
            ->whereBetween('created_at', [$start, $end])
            ->where('estado', 'Aprobada')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
            ->first()->avg_hours ?? 0;

        $porEstado = (clone $q)->select('estado', DB::raw('count(*) as value'))
            ->groupBy('estado')
            ->get()
            ->pluck('value', 'estado')
            ->toArray();

        return [
            'recibidas' => $recibidas,
            'aprobadas' => $aprobadas,
            'devueltas' => $devueltas,
            'tasa_aprobacion' => $recibidas > 0 ? round(($aprobadas / $recibidas) * 100, 1) : 0,
            'sla_horas' => round($tiempoResp, 1),
            'distribucion_estados' => $porEstado
        ];
    }

    public function getTraspasosMetrics($dates)
    {
        list($start, $end) = $dates;
        $q = Traspaso::whereBetween('fecha_solicitud', [$start, $end]);

        $registrados = (clone $q)->count();
        $efectivos = Traspaso::whereBetween('fecha_efectivo', [$start, $end])->count();
        $rechazados = Traspaso::whereBetween('fecha_rechazo', [$start, $end])->count();

        $tendencia = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $tendencia['labels'][] = $d->translatedFormat('M');
            $tendencia['efectivos'][] = Traspaso::whereMonth('fecha_efectivo', $d->month)->whereYear('fecha_efectivo', $d->year)->count();
            $tendencia['rechazados'][] = Traspaso::whereMonth('fecha_rechazo', $d->month)->whereYear('fecha_rechazo', $d->year)->count();
        }

        return [
            'registrados' => $registrados,
            'efectivos' => $efectivos,
            'rechazados' => $rechazados,
            'hit_rate' => $registrados > 0 ? round(($efectivos / $registrados) * 100, 1) : 0,
            'tendencia' => $tendencia
        ];
    }

    public function getDispersionMetrics($dates)
    {
        list($start, $end) = $dates;
        // In dispersion we usually want the current year, but we'll fetch all periods that intersect
        // Or simply fetch all periods for the year of the $end date to match the user request.
        $year = $end->year;
        
        $periodos = DispersionPeriod::with(['cortes.values.indicator'])
            ->where('year', $year)
            ->orderBy('month', 'desc')
            ->get();

        $desglose = [];
        $granMontoTotal = 0;
        $titularesPromedio = 0;

        foreach($periodos as $p) {
            $corte1 = $p->cortes->where('corte_number', 1)->first();
            $corte2 = $p->cortes->where('corte_number', 2)->first();

            $val1 = \App\Models\DispersionValue::where('corte_id', $corte1?->id)
                ->whereHas('indicator', fn($q) => $q->where('code', 'TOTAL_GENERAL_PDSS'))
                ->first()?->quantity ?? 0;

            $val2 = \App\Models\DispersionValue::where('corte_id', $corte2?->id)
                ->whereHas('indicator', fn($q) => $q->where('code', 'TOTAL_GENERAL_PDSS'))
                ->first()?->quantity ?? 0;

            $montoCorte1 = \App\Models\DispersionValue::where('corte_id', $corte1?->id)
                ->whereHas('indicator', fn($q) => $q->where('category', 'Montos')->where('is_total', false))
                ->sum('amount');

            $montoCorte2 = \App\Models\DispersionValue::where('corte_id', $corte2?->id)
                ->whereHas('indicator', fn($q) => $q->where('category', 'Montos')->where('is_total', false))
                ->sum('amount');

            $totalAfiliados = $val2 > 0 ? $val2 : $val1;
            $montoTotalMes = $montoCorte1 + $montoCorte2;

            $granMontoTotal += $montoTotalMes;

            $desglose[] = [
                'mes' => $p->month_name,
                'anio' => $p->year,
                'corte_1_afiliados' => $val1,
                'corte_1_monto' => $montoCorte1,
                'corte_2_afiliados' => $val2,
                'corte_2_monto' => $montoCorte2,
                'total_afiliados' => $totalAfiliados,
                'total_monto' => $montoTotalMes,
            ];
        }

        // For summary card we can show the latest month's affiliates
        if (count($desglose) > 0) {
            $titularesPromedio = $desglose[0]['total_afiliados'];
        }

        return [
            'periodos_cargados' => $periodos->count(),
            'monto_total' => $granMontoTotal,
            'titulares' => $titularesPromedio, // Current active affiliates
            'desglose' => $desglose
        ];
    }

    public function getPssMetrics($dates)
    {
        $especialidades = DB::table('pss_medicos')
            ->join('pss_especialidades', 'pss_medicos.especialidad_id', '=', 'pss_especialidades.id')
            ->select('pss_especialidades.nombre as label', DB::raw('count(*) as value'))
            ->groupBy('pss_especialidades.nombre')
            ->orderByDesc('value')
            ->limit(10)
            ->get();

        return [
            'centros' => PssCentro::count(),
            'centros_activos' => PssCentro::where('estado', 'Activo')->orWhere('estado', 'ACTIVO')->count(),
            'medicos' => PssMedico::count(),
            'medicos_activos' => PssMedico::where('estado', 'Activo')->orWhere('estado', 'ACTIVO')->count(),
            'especialidades' => $especialidades
        ];
    }

    public function getCallCenterMetrics($dates)
    {
        list($start, $end) = $dates;
        $q = CallCenterRegistro::whereBetween('created_at', [$start, $end]);

        $total = (clone $q)->count();
        $gestionados = (clone $q)->whereHas('gestiones')->count();
        $efectivos = (clone $q)->where('estado_id', 4)->count(); // 4 = Promovido/Efectivo

        return [
            'total' => $total,
            'gestionados' => $gestionados,
            'efectivos' => $efectivos,
            'tasa_conversion' => $gestionados > 0 ? round(($efectivos / $gestionados) * 100, 1) : 0
        ];
    }

    public function getAsistenciaMetrics($dates)
    {
        $hoy = Carbon::today();
        
        $personalActivo = AsistenciaEmpleado::where('estado', 'Activo')->count();
        $presentes = AsistenciaRegistro::whereDate('fecha', $hoy)->count();
        
        // Tardanzas de hoy
        $tardanzas = AsistenciaRegistro::whereDate('fecha', $hoy)
                        ->where('minutos_tardanza', '>', 0)
                        ->count();

        // Tendencia últimos 7 días
        $tendencia = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $tendencia['labels'][] = $date->translatedFormat('D d');
            $pres = AsistenciaRegistro::whereDate('fecha', $date)->count();
            $tendencia['valores'][] = $personalActivo > 0 ? round(($pres / $personalActivo) * 100, 1) : 0;
        }

        return [
            'personal_activo' => $personalActivo,
            'presentes' => $presentes,
            'ausentes' => max(0, $personalActivo - $presentes),
            'tardanzas' => $tardanzas,
            'porcentaje_asistencia' => $personalActivo > 0 ? round(($presentes / $personalActivo) * 100, 1) : 0,
            'tendencia_semanal' => $tendencia
        ];
    }

    public function getGlobalTrend()
    {
        $labels = [];
        $cmd = [];
        $afil = [];
        $tra = [];

        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $labels[] = $d->translatedFormat('M Y');
            
            $cmd[] = Afiliado::whereMonth('created_at', $d->month)->whereYear('created_at', $d->year)->count();
            $afil[] = SolicitudAfiliacion::whereMonth('created_at', $d->month)->whereYear('created_at', $d->year)->count();
            $tra[] = Traspaso::whereMonth('fecha_solicitud', $d->month)->whereYear('fecha_solicitud', $d->year)->count();
        }

        return compact('labels', 'cmd', 'afil', 'tra');
    }

    public function generateAlerts($dates)
    {
        $alertas = [];
        list($start, $end) = $dates;

        // 1. Críticos SLA Carnetización
        $criticosSla = Afiliado::whereDoesntHave('estado', function($e) { $e->where('nombre', 'COMPLETADO'); })
                                ->whereNotNull('fecha_entrega_proveedor')
                                ->whereRaw('DATEDIFF(NOW(), fecha_entrega_proveedor) >= 20')
                                ->count();
        if ($criticosSla > 0) {
            $alertas[] = [
                'type' => 'danger',
                'title' => 'Retrasos Críticos SLA',
                'message' => "Existen {$criticosSla} expedientes con más de 20 días sin entrega."
            ];
        }

        // 2. Alto volumen de solicitudes devueltas (hoy)
        $devueltasHoy = SolicitudAfiliacion::whereDate('created_at', Carbon::today())->where('estado', 'Devuelta')->count();
        if ($devueltasHoy > 10) {
            $alertas[] = [
                'type' => 'warning',
                'title' => 'Pico de Solicitudes Devueltas',
                'message' => "Hoy se han devuelto {$devueltasHoy} solicitudes de afiliación."
            ];
        }
        
        // 3. Baja asistencia hoy
        $empleados = AsistenciaEmpleado::where('estado', 'Activo')->count();
        $presentes = AsistenciaRegistro::whereDate('fecha', Carbon::today())->count();
        if ($empleados > 0 && ($presentes / $empleados) < 0.7) {
            $alertas[] = [
                'type' => 'warning',
                'title' => 'Alta tasa de ausentismo',
                'message' => "Solo el " . round(($presentes / $empleados) * 100) . "% del personal está presente hoy."
            ];
        }

        return $alertas;
    }
}
