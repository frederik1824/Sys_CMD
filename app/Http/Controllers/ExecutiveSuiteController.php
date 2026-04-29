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

class ExecutiveSuiteController extends Controller
{
    /**
     * Dashboard Principal de la Suite Ejecutiva
     * Construido desde cero para aislamiento total.
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        // --- 1. CONFIGURACIÓN DE PERMISOS (HARD-ISOLATION) ---
        // Solo ven CMD los departamentos operativos o administradores
        $canSeeCmd = $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION', 'PROD']));
        
        // Solo ven Afiliación los dptos de oficina o admin
        $canSeeAfiliacion = $isAdmin || $user->can('solicitudes_afiliacion.index') || ($dept && in_array($dept->codigo, ['AFIL', 'AUTOR', 'AUDIT', 'SERV', 'COM']));
        
        // Solo ven Traspasos ventas o admin
        $canSeeTraspasos = $isAdmin || $user->can('access_traspasos') || ($dept && in_array($dept->codigo, ['TRA', 'VENTAS', 'MARK']));

        $ttl = 600; // 10 minutos de cache
        $cacheKey = "exec_suite_v1_" . ($isAdmin ? 'global' : ($dept->id ?? $user->id));

        // --- 2. RECOPILACIÓN DE DATOS (MÓDULOS INDEPENDIENTES) ---
        
        // Módulo CMD (Carnets)
        $cmdData = $canSeeCmd ? Cache::remember($cacheKey . '_cmd', $ttl, function() {
            return [
                'total' => Afiliado::count(),
                'completados' => Afiliado::whereHas('estado', function($q) { 
                    $q->whereIn('nombre', ['Completado', 'Entregado']); 
                })->count(),
                'criticos' => Afiliado::with('estado')->get()->filter(fn($a) => $a->sla_status === 'critico')->count(),
                'monto' => Afiliado::whereHas('estado', function($q) { $q->where('nombre', 'Completado'); })
                                    ->where('liquidado', false)->sum('costo_entrega')
            ];
        }) : null;

        // Módulo Afiliación (Interno)
        $afiliacionData = $canSeeAfiliacion ? Cache::remember($cacheKey . '_afil', $ttl, function() use ($user, $isAdmin, $dept) {
            $query = SolicitudAfiliacion::query();
            if (!$isAdmin) {
                if ($dept && in_array($dept->codigo, ['AFIL', 'AUTOR', 'AUDIT'])) {
                    $query->where('departamento_id', $dept->id);
                } else {
                    $query->where('solicitante_user_id', $user->id);
                }
            }
            return [
                'total' => (clone $query)->count(),
                'aprobadas' => (clone $query)->where('estado', 'Aprobada')->count(),
                'pendientes' => (clone $query)->where('estado', 'Pendiente')->count(),
                'hoy' => (clone $query)->whereDate('created_at', now())->count()
            ];
        }) : null;

        // Módulo Traspasos (Mercado)
        $traspasosData = $canSeeTraspasos ? Cache::remember($cacheKey . '_tra', $ttl, function() {
            $periodoActual = now()->format('Y-m');
            return [
                'total' => Traspaso::count(),
                'efectivos' => Traspaso::whereNotNull('fecha_efectivo')->count(),
                'mes' => Traspaso::where('periodo_efectivo', $periodoActual)->count(),
                'meta' => \App\Models\MetaTraspaso::where('periodo', $periodoActual)->sum('meta_cantidad')
            ];
        }) : null;

        // Módulo Call Center (Productividad)
        $callCenterData = Cache::remember($cacheKey . '_cc', $ttl, function() {
            $totalAsignaciones = \App\Models\AsignacionLlamada::count();
            $efectivas = \App\Models\AsignacionLlamada::where('activa', false)->count(); // Las cerradas se consideran procesadas
            return [
                'total' => $totalAsignaciones,
                'efectivas' => $efectivas,
                'porcentaje' => $totalAsignaciones > 0 ? round(($efectivas / $totalAsignaciones) * 100, 1) : 0,
                'pendientes' => \App\Models\AsignacionLlamada::where('activa', true)->count()
            ];
        });

        // Tendencia Unificada (Para el Gráfico Central)
        $tendencia = Cache::remember($cacheKey . '_trend', $ttl, function() use ($canSeeCmd, $canSeeAfiliacion, $canSeeTraspasos, $user, $isAdmin, $dept) {
            $labels = []; $cmd = []; $afil = []; $tra = [];
            for ($i = 5; $i >= 0; $i--) {
                $d = now()->subMonths($i);
                $labels[] = $d->translatedFormat('M');
                
                $cmd[] = $canSeeCmd ? Afiliado::whereMonth('created_at', $d->month)->whereYear('created_at', $d->year)->count() : 0;
                
                if ($canSeeAfiliacion) {
                    $q = SolicitudAfiliacion::whereMonth('created_at', $d->month)->whereYear('created_at', $d->year);
                    if (!$isAdmin) $q->where(fn($sq) => $sq->where('departamento_id', $dept->id ?? 0)->orWhere('solicitante_user_id', $user->id));
                    $afil[] = $q->count();
                } else { $afil[] = 0; }

                $tra[] = $canSeeTraspasos ? Traspaso::whereMonth('fecha_solicitud', $d->month)->whereYear('fecha_solicitud', $d->year)->count() : 0;
            }
            return compact('labels', 'cmd', 'afil', 'tra');
        });

        return view('reportes.suite', compact(
            'cmdData', 'afiliacionData', 'traspasosData', 'callCenterData', 'tendencia', 
            'canSeeCmd', 'canSeeAfiliacion', 'canSeeTraspasos'
        ));
    }
}
