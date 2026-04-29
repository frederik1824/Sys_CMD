<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Responsable;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->hasRole('Gestor de Llamadas') && !auth()->user()->hasAnyRole(['Admin'])) {
            return redirect()->route('callcenter.dashboard');
        }

        $ttl = 300; // 5 minutos de Caché Térmica
        $cachePrefix = (auth()->check() && auth()->user()->responsable_id && !auth()->user()->hasRole(['Admin'])) ? "rep_" . auth()->user()->responsable_id . "_" : "global_";


        // Métricas Generales
        $totalAfiliados = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_totalAfiliados', $ttl, fn() => Afiliado::count());
        $totalAsignados = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_totalAsignados', $ttl, fn() => Afiliado::whereNotNull('responsable_id')->count());
        
        $totalEntregados = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_totalEntregados', $ttl, function() {
            return Afiliado::whereHas('estado', function($q) {
                $q->whereIn('nombre', ['Carnet entregado', 'Cierre parcial', 'Completado', 'Pendiente de recepción']);
            })->count();
        });

        $totalPendienteValidacion = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_totalPendienteValidacion', $ttl, function() {
            return Afiliado::whereIn('estado_id', [7, 8])->count();
        });
        
        $totalCompletados = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_totalCompletados', $ttl, function() {
            return Afiliado::whereHas('estado', function($q) {
                $q->whereIn('nombre', ['Completado', 'Cierre parcial']);
            })->count();
        });

        // Conteo por Empresas FILIAL
        $totalFilial = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_totalFilial', $ttl, fn() => Afiliado::enEmpresaFilial()->count());
        
        $confirmadosFilial = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_confirmadosFilial', $ttl, function() {
            return Afiliado::enEmpresaFilial()->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            })->count();
        });

        $totalOtras = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_totalOtras', $ttl, function() {
            return Afiliado::whereDoesntHave('empresaModel', function($q) {
                $q->where('es_filial', true);
            })->count();
        });

        $terminadosOtras = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_terminadosOtras', $ttl, function() {
            return Afiliado::whereDoesntHave('empresaModel', function($q) {
                $q->where('es_filial', true);
            })->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            })->count();
        });

        // Métricas SAFESURE / SLA
        $fueraSlaCount = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_fueraSlaCount', $ttl, function() {
            return Afiliado::whereNotNull('fecha_entrega_proveedor')
                ->where('liquidado', false)
                ->get() 
                ->filter(fn($a) => $a->sla_status === 'critico')
                ->count();
        });

        $montoArs = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_montoArs', $ttl, function() {
            return Afiliado::ars()
                ->whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                ->where('liquidado', false)
                ->sum('costo_entrega');
        });

        $montoNoArs = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_montoNoArs', $ttl, function() {
            return Afiliado::noArs()
                ->whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                ->where('liquidado', false)
                ->sum('costo_entrega');
        });

        // Métricas de EMPRESAS VERIFICADAS (Ex-SAFE)
        $totalVerificadas = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_totalVerificadas', $ttl, fn() => Afiliado::enEmpresaReal()->count());
        
        $confirmadosVerificadas = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_confirmadosVerificadas', $ttl, function() {
            return Afiliado::enEmpresaReal()->whereHas('estado', function($q) { 
                $q->where('nombre', 'Completado'); 
            })->count();
        });

        // Calcular porcentaje global
        $porcentajeCompletado = $totalAfiliados > 0 ? round(($totalCompletados / $totalAfiliados) * 100) : 0;

        // Breakdown por Estado (para gráficos)
        $afiliadosPorEstado = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_afiliadosPorEstado', $ttl, function() {
            return Afiliado::select('estado_id', DB::raw('count(*) as total'))
                ->groupBy('estado_id')
                ->with('estado')
                ->get();
        });

        // Breakdown por Corte
        $afiliadosPorCorte = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_afiliadosPorCorte', $ttl, function() {
            return Afiliado::select('corte_id', DB::raw('count(*) as total'))
                ->groupBy('corte_id')
                ->with('corte')
                ->orderBy('corte_id', 'desc')
                ->take(5)
                ->get();
        });

        // Breakdown por Responsable
        $productividadResponsables = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_productividadResponsables', $ttl, function() {
            return Afiliado::select('responsable_id', DB::raw('count(*) as total_asignados'))
                ->whereNotNull('responsable_id')
                ->groupBy('responsable_id')
                ->with('responsable')
                ->get()->map(function($item) {
                    $entregados = Afiliado::where('responsable_id', $item->responsable_id)
                        ->whereHas('estado', function($q) {
                            $q->whereIn('nombre', ['Carnet entregado', 'Cierre parcial', 'Completado', 'Pendiente de recepción']);
                        })->count();
                    $item->entregados = $entregados;
                    $item->porcentaje = $item->total_asignados > 0 ? round(($entregados / $item->total_asignados) * 100) : 0;
                    return $item;
                });
        });

        // Estadísticas mensuales (Tendencia últimos 6 meses)
        $statsPorMes = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_statsPorMes', $ttl, function() {
            $trendQuery = DB::table('afiliados')
                ->select(DB::raw("DATE_FORMAT(created_at, '%M') as mes"), DB::raw('count(*) as total'))
                ->where('created_at', '>=', now()->subMonths(6));

            if (auth()->check() && auth()->user()->responsable_id && !auth()->user()->hasRole(['Admin'])) {
                $trendQuery->where('responsable_id', auth()->user()->responsable_id);
            }

            return $trendQuery->groupBy('mes')
                ->orderBy(DB::raw("MIN(created_at)"))
                ->get();
        });

        // Actividad Reciente (Este reduciremos el TTL a 1 minuto porque debe ser algo realista)
        $actividadReciente = \Illuminate\Support\Facades\Cache::remember($cachePrefix . 'dashboard_actividadReciente', 60, function() {
            return \App\Models\HistorialEstado::with(['afiliado', 'estadoNuevo', 'user'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
        });

        return view('dashboard', compact(
            'totalAfiliados', 
            'totalAsignados', 
            'totalEntregados',
            'totalPendienteValidacion',
            'totalCompletados',
            'totalFilial',
            'confirmadosFilial',
            'totalOtras',
            'terminadosOtras',
            'porcentajeCompletado',
            'afiliadosPorEstado',
            'afiliadosPorCorte',
            'productividadResponsables',
            'actividadReciente',
            'fueraSlaCount',
            'montoArs',
            'montoNoArs',
            'totalVerificadas',
            'confirmadosVerificadas',
            'statsPorMes'
        ));
    }
}
