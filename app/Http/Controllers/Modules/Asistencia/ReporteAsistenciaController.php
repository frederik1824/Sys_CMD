<?php

namespace App\Http\Controllers\Modules\Asistencia;

use App\Http\Controllers\Controller;
use App\Models\Asistencia\Empleado;
use App\Models\Asistencia\Registro;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReporteAsistenciaController extends Controller
{
    /**
     * Vista principal de reportes de asistencia
     * Auditoría: Eliminación de N+1 mediante Eager Loading filtrado
     */
    public function index(Request $request)
    {
        $fecha_desde = $request->input('fecha_desde', Carbon::today()->startOfMonth()->format('Y-m-d'));
        $fecha_hasta = $request->input('fecha_hasta', Carbon::today()->format('Y-m-d'));

        // Obtener resumen por empleado en el rango (Optimizado)
        $empleados = Empleado::with(['cargo.departamento', 'turno', 'registros' => function($q) use ($fecha_desde, $fecha_hasta) {
            $q->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
        }])
        ->where('estado', 'activo')
        ->get();

        $reporte = $empleados->map(function($emp) {
            $registros = $emp->registros; // Ya filtrados por el eager loading

            return (object) [
                'id' => $emp->id,
                'nombre' => $emp->nombre_completo,
                'codigo' => $emp->codigo_empleado,
                'cargo' => $emp->cargo->nombre ?? 'N/A',
                'departamento' => $emp->cargo->departamento->nombre ?? 'N/A',
                'total_dias' => $registros->count(),
                'tardanzas' => $registros->where('minutos_tardanza', '>', 0)->count(),
                'salidas_tempranas' => $registros->where('minutos_salida_temprana', '>', 0)->count(),
                'horas_totales' => round($registros->sum('minutos_trabajados_neto') / 60, 1),
                'cumplimiento' => $registros->count() > 0 
                    ? round(($registros->where('cumplio_jornada', true)->count() / $registros->count()) * 100, 1) 
                    : 0
            ];
        });

        return view('modules.asistencia.reportes.index', compact('reporte', 'fecha_desde', 'fecha_hasta'));
    }

    /**
     * Exportar reporte a CSV (Excel)
     * Auditoría: Optimización de memoria mediante chunking y eager loading
     */
    public function export(Request $request)
    {
        $fecha_desde = $request->input('fecha_desde');
        $fecha_hasta = $request->input('fecha_hasta');

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=reporte_asistencia_{$fecha_desde}_{$fecha_hasta}.csv",
        ];

        $callback = function() use ($fecha_desde, $fecha_hasta) {
            $file = fopen('php://output', 'w');
            // Añadir BOM para Excel en español
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['ID Empleado', 'Nombre', 'Cargo', 'Departamento', 'Días Laborados', 'Tardanzas', 'Horas Totales', 'Cumplimiento %']);

            Empleado::with(['cargo.departamento', 'registros' => function($q) use ($fecha_desde, $fecha_hasta) {
                $q->whereBetween('fecha', [$fecha_desde, $fecha_hasta]);
            }])
            ->where('estado', 'activo')
            ->chunk(100, function($empleados) use ($file) {
                foreach ($empleados as $emp) {
                    $registros = $emp->registros;

                    fputcsv($file, [
                        $emp->codigo_empleado,
                        $emp->nombre_completo,
                        $emp->cargo->nombre ?? 'N/A',
                        $emp->cargo->departamento->nombre ?? 'N/A',
                        $registros->count(),
                        $registros->where('minutos_tardanza', '>', 0)->count(),
                        round($registros->sum('minutos_trabajados_neto') / 60, 1),
                        $registros->count() > 0 ? round(($registros->where('cumplio_jornada', true)->count() / $registros->count()) * 100, 1) : 0
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
