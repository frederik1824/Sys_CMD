<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Afiliado;
use App\Models\SolicitudAfiliacion;
use App\Models\Traspaso;
use App\Models\Corte;
use App\Models\Estado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ExecutiveDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        // Determinar permisos por módulo
        $canSeeCmd = $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION']));
        $canSeeAfiliacion = $isAdmin || $user->can('solicitudes_afiliacion.index') || ($dept && in_array($dept->codigo, ['AFIL', 'AUTOR', 'AUDIT', 'SERV']));
        $canSeeTraspasos = $isAdmin || $user->can('access_traspasos') || ($dept && in_array($dept->codigo, ['TRA', 'VENTAS']));

        $ttl = 600; // 10 minutos de cache
        $cachePrefix = "exec_v2_" . ($isAdmin ? 'global' : ($dept->id ?? $user->id)) . "_";
        
        // --- 1. MÉTRICAS CMD (Carnetización) ---
        $cmdData = $canSeeCmd ? Cache::remember($cachePrefix . 'cmd_data', $ttl, function() {
            $total = Afiliado::count();
            $completados = Afiliado::whereHas('estado', function($q) { 
                $q->whereIn('nombre', ['Completado', 'Cierre parcial', 'Entregado']); 
            })->count();
            
            $criticosSla = Afiliado::whereDoesntHave('estado', function($q) { $q->where('nombre', 'COMPLETADO'); })
                ->whereNotNull('fecha_entrega_proveedor')
                ->whereRaw('DATEDIFF(NOW(), fecha_entrega_proveedor) >= 20')
                ->count();

            return [
                'total' => $total,
                'completados' => $completados,
                'porcentaje' => $total > 0 ? round(($completados / $total) * 100, 1) : 0,
                'criticos' => $criticosSla,
                'monto_pendiente' => Afiliado::whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                    ->where('liquidado', false)
                    ->sum('costo_entrega')
            ];
        }) : null;

        // --- 2. MÉTRICAS AFILIACIÓN (Solicitudes Internas) ---
        $afiliacionData = $canSeeAfiliacion ? Cache::remember($cachePrefix . 'afiliacion_data', $ttl, function() use ($user, $isAdmin, $dept) {
            $query = SolicitudAfiliacion::query();
            
            // Filtro contextual si no es admin
            if (!$isAdmin) {
                if ($dept && in_array($dept->codigo, ['AFIL', 'AUTOR', 'AUDIT'])) {
                    $query->where('departamento_id', $dept->id);
                } else {
                    $query->where('solicitante_user_id', $user->id);
                }
            }

            $total = (clone $query)->count();
            $aprobadas = (clone $query)->where('estado', 'Aprobada')->count();
            $hoy = (clone $query)->whereDate('created_at', now())->count();
            $devueltas = (clone $query)->where('estado', 'Devuelta')->count();

            return [
                'total' => $total,
                'aprobadas' => $aprobadas,
                'porcentaje' => $total > 0 ? round(($aprobadas / $total) * 100, 1) : 0,
                'hoy' => $hoy,
                'tasa_devolucion' => $total > 0 ? round(($devueltas / $total) * 100, 1) : 0
            ];
        }) : null;

        // --- 3. MÉTRICAS TRASPASOS (Efectividad Externa) ---
        $traspasosData = $canSeeTraspasos ? Cache::remember($cachePrefix . 'traspasos_data', $ttl, function() {
            $total = Traspaso::count();
            $efectivos = Traspaso::whereNotNull('fecha_efectivo')->count();
            $currentMonth = now()->format('Y-m');
            $efectivosMes = Traspaso::where('periodo_efectivo', $currentMonth)->count();
            
            $metaMes = \App\Models\MetaTraspaso::where('periodo', $currentMonth)->sum('meta_cantidad');

            return [
                'total' => $total,
                'efectivos' => $efectivos,
                'porcentaje' => $total > 0 ? round(($efectivos / $total) * 100, 1) : 0,
                'mes_actual' => $efectivosMes,
                'cumplimiento_meta' => $metaMes > 0 ? round(($efectivosMes / $metaMes) * 100, 1) : 0
            ];
        }) : null;

        // --- 4. TENDENCIA GLOBAL ---
        $tendencia = Cache::remember($cachePrefix . 'global_trend', $ttl, function() use ($canSeeCmd, $canSeeAfiliacion, $canSeeTraspasos, $user, $isAdmin, $dept) {
            $labels = [];
            $cmd = [];
            $afiliacion = [];
            $traspasos = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->translatedFormat('M');
                $month = $date->month;
                $year = $date->year;

                $cmd[] = $canSeeCmd ? Afiliado::whereMonth('created_at', $month)->whereYear('created_at', $year)->count() : 0;
                
                if ($canSeeAfiliacion) {
                    $afiQuery = SolicitudAfiliacion::whereMonth('created_at', $month)->whereYear('created_at', $year);
                    if (!$isAdmin) {
                        if ($dept && in_array($dept->codigo, ['AFIL', 'AUTOR', 'AUDIT'])) {
                            $afiQuery->where('departamento_id', $dept->id);
                        } else {
                            $afiQuery->where('solicitante_user_id', $user->id);
                        }
                    }
                    $afiliacion[] = $afiQuery->count();
                } else {
                    $afiliacion[] = 0;
                }

                $traspasos[] = $canSeeTraspasos ? Traspaso::whereMonth('fecha_solicitud', $month)->whereYear('fecha_solicitud', $year)->count() : 0;
            }

            return [
                'labels' => $labels,
                'cmd' => $cmd,
                'afiliacion' => $afiliacion,
                'traspasos' => $traspasos
            ];
        });

        // --- 5. ÍNDICE DE SALUD OPERATIVA (Calculado solo con lo visible) ---
        $healthSum = 0;
        $div = 0;
        if ($canSeeCmd) { $healthSum += $cmdData['porcentaje'] * 0.4; $div += 0.4; }
        if ($canSeeAfiliacion) { $healthSum += $afiliacionData['porcentaje'] * 0.3; $div += 0.3; }
        if ($canSeeTraspasos) { $healthSum += $traspasosData['porcentaje'] * 0.3; $div += 0.3; }
        
        $healthIndex = $div > 0 ? round($healthSum / $div) : 0;

        return view('reportes.executive', compact(
            'cmdData', 
            'afiliacionData', 
            'traspasosData', 
            'tendencia', 
            'healthIndex',
            'canSeeCmd',
            'canSeeAfiliacion',
            'canSeeTraspasos'
        ));
    }
}
