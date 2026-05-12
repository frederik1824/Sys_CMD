<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Afiliado;
use App\Models\SolicitudAfiliacion;
use App\Models\Traspaso;
use App\Models\Corte;
use App\Models\Estado;
use Spatie\Permission\Models\Role;
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
        $canSeeCallCenter = $isAdmin || $user->can('callcenter.access');
        $canSeeSecurity = $isAdmin || $user->can('access_admin_panel');

        $ttl = 600; // 10 minutos de cache
        $cachePrefix = "exec_v3_" . ($isAdmin ? 'global' : ($dept->id ?? $user->id)) . "_";
        
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

            return [
                'total' => $total,
                'aprobadas' => $aprobadas,
                'porcentaje' => $total > 0 ? round(($aprobadas / $total) * 100, 1) : 0,
                'hoy' => $hoy
            ];
        }) : null;

        // --- 3. MÉTRICAS CALL CENTER (CRM & Prospección) ---
        $callCenterData = $canSeeCallCenter ? Cache::remember($cachePrefix . 'callcenter_data', $ttl, function() {
            $total = \App\Models\CallCenterRegistro::count();
            $gestionados = \App\Models\CallCenterRegistro::whereHas('gestiones')->count();
            $convertidos = \App\Models\CallCenterRegistro::where('estado_id', 4)->count(); // Asumiendo 4 = Promovido/Cerrado

            return [
                'total' => $total,
                'gestionados' => $gestionados,
                'convertidos' => $convertidos,
                'porcentaje_gestion' => $total > 0 ? round(($gestionados / $total) * 100, 1) : 0,
                'tasa_conversion' => $gestionados > 0 ? round(($convertidos / $gestionados) * 100, 1) : 0
            ];
        }) : null;

        // --- 4. MÉTRICAS DE SEGURIDAD (Compliance) ---
        $securityData = $canSeeSecurity ? Cache::remember($cachePrefix . 'security_data', $ttl, function() {
            $roles = Role::with('permissions')->get();
            $conflictsCount = 0;
            
            foreach ($roles as $role) {
                $pNames = $role->permissions->pluck('name');
                // Lógica de detección de SoD (Segregation of Duties)
                if ($pNames->contains('afiliacion.store') && $pNames->contains('afiliacion.approve')) $conflictsCount++;
                if ($pNames->contains('call-center.import.store') && $pNames->contains('call-center.document.update')) $conflictsCount++;
            }

            return [
                'roles_criticos' => Role::count(),
                'conflictos_activos' => $conflictsCount,
                'auditorias_hoy' => DB::table('access_audit_logs')->whereDate('created_at', now())->count()
            ];
        }) : null;

        // --- 5. TENDENCIA GLOBAL (Actualizada) ---
        $tendencia = Cache::remember($cachePrefix . 'global_trend_v3', $ttl, function() use ($canSeeCmd, $canSeeCallCenter, $canSeeTraspasos) {
            $labels = [];
            $cmd = [];
            $crm = [];
            $traspasos = [];

            for ($i = 5; $i >= 0; $i--) {
                $date = now()->subMonths($i);
                $labels[] = $date->translatedFormat('M');
                $month = $date->month;
                $year = $date->year;

                $cmd[] = $canSeeCmd ? Afiliado::whereMonth('created_at', $month)->whereYear('created_at', $year)->count() : 0;
                $crm[] = $canSeeCallCenter ? \App\Models\CallCenterRegistro::whereMonth('created_at', $month)->whereYear('created_at', $year)->count() : 0;
                $traspasos[] = $canSeeTraspasos ? Traspaso::whereMonth('fecha_solicitud', $month)->whereYear('fecha_solicitud', $year)->count() : 0;
            }

            return [
                'labels' => $labels,
                'cmd' => $cmd,
                'crm' => $crm,
                'traspasos' => $traspasos
            ];
        });

        // --- 6. ÍNDICE DE SALUD OPERATIVA ---
        $healthSum = 0;
        $div = 0;
        if ($canSeeCmd) { $healthSum += $cmdData['porcentaje'] * 0.3; $div += 0.3; }
        if ($canSeeAfiliacion) { $healthSum += $afiliacionData['porcentaje'] * 0.2; $div += 0.2; }
        if ($canSeeCallCenter) { $healthSum += $callCenterData['tasa_conversion'] * 2.5 * 0.3; $div += 0.3; } // Pesamos más la conversión
        if ($canSeeSecurity) { $healthSum += (100 - ($securityData['conflictos_activos'] * 10)) * 0.2; $div += 0.2; }
        
        $healthIndex = $div > 0 ? round($healthSum / $div) : 0;

        return view('reportes.executive', compact(
            'cmdData', 'afiliacionData', 'callCenterData', 'securityData',
            'tendencia', 'healthIndex',
            'canSeeCmd', 'canSeeAfiliacion', 'canSeeCallCenter', 'canSeeSecurity'
        ));
    }
}
