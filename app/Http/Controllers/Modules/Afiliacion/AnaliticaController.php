<?php

namespace App\Http\Controllers\Modules\Afiliacion;

use App\Http\Controllers\Controller;
use App\Models\SolicitudAfiliacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnaliticaController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->periodo ?: 'month'; // 'month', 'quarter', 'year'
        $startDate = match($periodo) {
            'year' => now()->startOfYear(),
            'quarter' => now()->subMonths(3),
            default => now()->startOfMonth(),
        };

        // 1. KPI: Tiempo Promedio de Afiliación (Turnaround Time - TAT)
        // Solo para solicitudes completadas
        $tatPromedio = SolicitudAfiliacion::where('estado', 'Completada')
            ->where('fecha_cierre', '>=', $startDate)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, fecha_cierre)) as hours'))
            ->first()->hours ?: 0;

        // 2. KPI: Tasa de Rechazo
        $totalFinalizadas = SolicitudAfiliacion::whereIn('estado', ['Completada', 'Rechazada'])
            ->where('fecha_cierre', '>=', $startDate)
            ->count();
        $totalRechazadas = SolicitudAfiliacion::where('estado', 'Rechazada')
            ->where('fecha_cierre', '>=', $startDate)
            ->count();
        $tasaRechazo = $totalFinalizadas > 0 ? ($totalRechazadas / $totalFinalizadas) * 100 : 0;

        // 3. KPI: Resolución en Primer Intento (FCR)
        $completadasEnPeriodo = SolicitudAfiliacion::where('estado', 'Completada')
            ->where('fecha_cierre', '>=', $startDate)
            ->count();
        $completadasFCR = SolicitudAfiliacion::where('estado', 'Completada')
            ->where('es_primera_resolucion', true)
            ->where('fecha_cierre', '>=', $startDate)
            ->count();
        $tasaFCR = $completadasEnPeriodo > 0 ? ($completadasFCR / $completadasEnPeriodo) * 100 : 0;

        // 4. KPI: Nivel de Satisfacción (CSAT)
        $csatPromedio = SolicitudAfiliacion::whereNotNull('satisfaccion_nivel')
            ->where('fecha_cierre', '>=', $startDate)
            ->avg('satisfaccion_nivel') ?: 0;

        // 5. KPI: Tiempo Promedio de Validación TSS (Pensionados) - Mandato SISALRIL
        $tatPensionadosTSS = SolicitudAfiliacion::whereNotNull('pago_confirmado_at')
            ->where('created_at', '>=', $startDate)
            ->where(function($q) {
                $q->whereNotNull('tipo_pension')
                  ->orWhereNotNull('institucion_pension');
            })
            ->select(DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, pago_confirmado_at)) as days'))
            ->first()->days ?: 0;

        // 5. Métricas por Tipo de Solicitud (Especialmente Pensionados)
        $metricasPorTipo = SolicitudAfiliacion::where('fecha_cierre', '>=', $startDate)
            ->join('tipos_solicitud_afiliacion', 'solicitudes_afiliacion.tipo_solicitud_id', '=', 'tipos_solicitud_afiliacion.id')
            ->select(
                'tipos_solicitud_afiliacion.nombre',
                DB::raw('count(*) as total'),
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, solicitudes_afiliacion.created_at, solicitudes_afiliacion.fecha_cierre)) as tat')
            )
            ->whereIn('solicitudes_afiliacion.estado', ['Completada'])
            ->groupBy('tipos_solicitud_afiliacion.nombre')
            ->get();

        // 6. Dashboard de Envejecimiento de Solicitudes (Sisalril Compliance)
        $pensionadosAging = SolicitudAfiliacion::where('estado', 'Pendiente')
            ->where(function($q) {
                $q->whereNotNull('tipo_pension')
                  ->orWhereNotNull('institucion_pension');
            })
            ->select(
                DB::raw('SUM(CASE WHEN DATEDIFF(NOW(), created_at) <= 30 THEN 1 ELSE 0 END) as range_30'),
                DB::raw('SUM(CASE WHEN DATEDIFF(NOW(), created_at) > 30 AND DATEDIFF(NOW(), created_at) <= 60 THEN 1 ELSE 0 END) as range_60'),
                DB::raw('SUM(CASE WHEN DATEDIFF(NOW(), created_at) > 60 THEN 1 ELSE 0 END) as range_90'),
                DB::raw('count(*) as total')
            )
            ->first();

        // 7. Auditoría de No Coincidencias (Excepciones Críticas)
        $excepcionesPago = SolicitudAfiliacion::where('estado', 'Pendiente')
            ->whereNull('pago_confirmado_at')
            ->where(function($q) {
                $q->whereNotNull('tipo_pension')
                  ->orWhereNotNull('institucion_pension');
            })
            ->where('created_at', '<=', now()->subDays(30)) // Solo las que tienen más de 30 días sin pago
            ->orderBy('created_at', 'asc')
            ->limit(10)
            ->get();

        // 8. Tendencia Mensual (Volumen)
        $tendencia = SolicitudAfiliacion::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                DB::raw('count(*) as total'),
                DB::raw('SUM(CASE WHEN estado = "Completada" THEN 1 ELSE 0 END) as completadas')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return view('modules.afiliacion.analytics', compact(
            'tatPromedio', 'tasaRechazo', 'tasaFCR', 'csatPromedio', 
            'tatPensionadosTSS', 'metricasPorTipo', 'tendencia', 'periodo',
            'pensionadosAging', 'excepcionesPago'
        ));
    }

    public function export(Request $request)
    {
        $periodo = $request->periodo ?: 'month';
        $startDate = match($periodo) {
            'year' => now()->startOfYear(),
            'quarter' => now()->subMonths(3),
            default => now()->startOfMonth(),
        };

        // Recolectar datos (mismos que index pero para el PDF)
        $tatPromedio = SolicitudAfiliacion::where('estado', 'Completada')
            ->where('fecha_cierre', '>=', $startDate)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, fecha_cierre)) as hours'))
            ->first()->hours ?: 0;

        $tatPensionadosTSS = SolicitudAfiliacion::whereNotNull('pago_confirmado_at')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, pago_confirmado_at)) as days'))
            ->first()->days ?: 0;

        $pensionadosAging = SolicitudAfiliacion::where('estado', 'Pendiente')
            ->where(function($q) { $q->whereNotNull('tipo_pension')->orWhereNotNull('institucion_pension'); })
            ->select(
                DB::raw('SUM(CASE WHEN DATEDIFF(NOW(), created_at) <= 30 THEN 1 ELSE 0 END) as range_30'),
                DB::raw('SUM(CASE WHEN DATEDIFF(NOW(), created_at) > 30 AND DATEDIFF(NOW(), created_at) <= 60 THEN 1 ELSE 0 END) as range_60'),
                DB::raw('SUM(CASE WHEN DATEDIFF(NOW(), created_at) > 60 THEN 1 ELSE 0 END) as range_90'),
                DB::raw('count(*) as total')
            )->first();

        $excepcionesPago = SolicitudAfiliacion::where('estado', 'Pendiente')
            ->whereNull('pago_confirmado_at')
            ->where(function($q) { $q->whereNotNull('tipo_pension')->orWhereNotNull('institucion_pension'); })
            ->where('created_at', '<=', now()->subDays(30))
            ->orderBy('created_at', 'asc')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('modules.afiliacion.reports.sisalril_compliance', compact(
            'tatPromedio', 'tatPensionadosTSS', 'pensionadosAging', 'excepcionesPago', 'periodo'
        ));

        return $pdf->download('Reporte_Cumplimiento_Sisalril_'.now()->format('Ymd').'.pdf');
    }
}
