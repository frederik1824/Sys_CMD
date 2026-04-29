<?php

namespace App\Http\Controllers\Modules\Afiliacion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SolicitudAfiliacion;
use App\Models\TipoSolicitudAfiliacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasAnyRole([
            'Admin', 
            'Supervisor de Afiliación', 
            'Supervisor de Autorizaciones', 
            'Supervisor de Cuentas Médicas',
            'Supervisor de Servicio al Cliente'
        ]);
        
        // Base query para métricas generales
        $baseQuery = SolicitudAfiliacion::query();

        // 0. Segregación inteligente por departamento
        if ($isAdmin && !$user->hasRole('Admin') && $user->departamento_id) {
            $esSupervisorCSR = str_contains($user->departamento->nombre, 'Servicio al Cliente');
            
            if ($esSupervisorCSR) {
                // Supervisores de CSR ven lo que crea su equipo
                $baseQuery->whereHas('solicitante', function($q) use ($user) {
                    $q->where('departamento_id', $user->departamento_id);
                });
            } else {
                // Supervisores operativos ven lo que les llega a su área
                $baseQuery->where('departamento_id', $user->departamento_id);
            }
        }

        // Título dinámico basado en el departamento
        $departamentoNombre = $user->departamento ? $user->departamento->nombre : 'Afiliación';
        $tituloPagina = "Analítica de " . ($user->hasRole('Admin') ? 'Gestión Global' : $departamentoNombre);

        // Aplicar restricciones de seguridad adicionales para colaboradores
        if (!$isAdmin) {
            // Colaboradores solo ven su propio volumen
            $baseQuery->where('solicitante_user_id', $user->id);
        } else {
            // Admin/Supervisores pueden filtrar por un usuario específico
            if ($request->filled('user_id')) {
                $baseQuery->where('solicitante_user_id', $request->user_id);
            }
        }

        // 1. Solicitudes por Tipo
        $porTipo = (clone $baseQuery)->select('tipo_solicitud_id', DB::raw('count(*) as total'))
            ->with('tipoSolicitud')
            ->groupBy('tipo_solicitud_id')
            ->get();

        // 2. Solicitudes por Estado
        $porEstado = (clone $baseQuery)->select('estado', DB::raw('count(*) as total'))
            ->groupBy('estado')
            ->get();

        // 3. Cumplimiento SLA
        $dentroSla = (clone $baseQuery)->where('estado', 'Aprobada')
            ->whereColumn('fecha_cierre', '<=', 'sla_fecha_limite')
            ->count();
        
        $fueraSla = (clone $baseQuery)->where('estado', 'Aprobada')
            ->whereColumn('fecha_cierre', '>', 'sla_fecha_limite')
            ->count();

        // 4. Productividad (Top Asignados) - Siempre Global para Admin, restringida para usuario
        $productividadQuery = SolicitudAfiliacion::where('estado', 'Aprobada')
            ->whereNotNull('asignado_user_id');
        
        if (!$isAdmin) {
            $productividadQuery->where('solicitante_user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $productividadQuery->where('solicitante_user_id', $request->user_id);
        }

        $productividad = $productividadQuery->select('asignado_user_id', DB::raw('count(*) as total'))
            ->with('asignado')
            ->groupBy('asignado_user_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // 5. Volumen por prioridad
        $porPrioridad = (clone $baseQuery)->select('prioridad', DB::raw('count(*) as total'))
            ->groupBy('prioridad')
            ->get();

        // 6. Tiempo promedio de resolución (en horas)
        $promedioResolucion = (clone $baseQuery)->where('estado', 'Aprobada')
            ->whereNotNull('fecha_cierre')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, fecha_cierre)) as horas'))
            ->first()->horas ?? 0;

        // 7. Solicitudes hoy
        $hoy = (clone $baseQuery)->whereDate('created_at', now())->count();

        // 8. Métricas de Devolución (Calidad)
        $totalGestionadas = array_sum(array_column($porEstado->toArray(), 'total'));
        $totalDevueltas = (clone $baseQuery)->where('estado', 'Devuelta')->count();
        $tasaDevolucion = $totalGestionadas > 0 ? round(($totalDevueltas / $totalGestionadas) * 100, 1) : 0;

        // 9. Lista de usuarios para filtro (Segregada por departamento para supervisores)
        $usuariosQuery = User::role(['Colaborador', 'Representante', 'Operador', 'Analista de Afiliación', 'Servicio al Cliente (CSR)'])->orderBy('name');
        
        if (!$user->hasRole('Admin') && $user->departamento_id) {
            $usuariosQuery->where('departamento_id', $user->departamento_id);
        }
        
        $usuarios = $isAdmin ? $usuariosQuery->get() : collect();

        // 4. Productividad Diaria (Calendario)
        $productividadDiaria = (clone $baseQuery)
            ->whereIn('estado', ['Aprobada', 'Rechazada', 'Cerrada'])
            ->whereMonth('fecha_cierre', now()->month)
            ->whereYear('fecha_cierre', now()->year)
            ->select(DB::raw('DAY(fecha_cierre) as dia'), DB::raw('count(*) as total'))
            ->groupBy('dia')
            ->orderBy('dia', 'asc')
            ->pluck('total', 'dia');

        return view('modules.afiliacion.reports', compact(
            'porTipo', 
            'porEstado', 
            'dentroSla', 
            'fueraSla', 
            'productividad', 
            'porPrioridad', 
            'promedioResolucion', 
            'hoy',
            'usuarios',
            'isAdmin',
            'tituloPagina',
            'totalDevueltas',
            'tasaDevolucion'
        ));
    }

    public function workload()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $userRoles = $user->getRoleNames();

        if (!$isAdmin && !str_contains($userRoles->first() ?? '', 'Supervisor')) {
            abort(403, 'No tienes permisos para acceder a esta herramienta.');
        }

        $departamentoId = $user->departamento_id;
        $isCSRSupervisor = str_contains($userRoles->first() ?? '', 'Servicio al Cliente');
        
        // Estados que definen "Carga de Trabajo" activa
        $estadosCarga = $isCSRSupervisor ? ['Borrador', 'Devuelta'] : ['Asignada', 'En revisión', 'Escalada'];
        $relation = $isCSRSupervisor ? 'solicitudesCreadas' : 'solicitudesAsignadas';

        $analistasQuery = User::query();
        
        if (!$isAdmin) {
            $analistasQuery->where('departamento_id', $departamentoId)
                          ->where('id', '!=', $user->id);
        } else {
            // Admin ve a todos los que tengan roles operativos
            $analistasQuery->role([
                'Analista de Afiliación', 'Analista de Autorizaciones', 'Analista de Cuentas Médicas',
                'Servicio al Cliente (CSR)', 'Representante'
            ]);
        }

        $analistas = $analistasQuery->withCount([
            // Carga Activa (Pendientes de acción)
            $relation . ' as solicitudes_count' => function($q) use ($estadosCarga) {
                $q->whereIn('estado', $estadosCarga);
            },
            // Completados (Aprobados / Procesados)
            $relation . ' as completados_count' => function($q) {
                $q->whereIn('estado', ['Aprobada', 'Cerrada']);
            },
            // Rechazados
            $relation . ' as rechazados_count' => function($q) {
                $q->where('estado', 'Rechazada');
            },
            // Resueltos Hoy (Productividad diaria)
            $relation . ' as hoy_count' => function($q) {
                $q->whereIn('estado', ['Aprobada', 'Rechazada', 'Cerrada'])
                  ->whereDate('updated_at', now());
            }
        ])->get();

        // Solicitudes en espera de ser tomadas por el departamento
        $pendientes = 0;
        if (!$isCSRSupervisor) {
            $pendientesQuery = SolicitudAfiliacion::whereIn('estado', ['Pendiente', 'Corregida']);
            if (!$isAdmin) {
                $pendientesQuery->where('departamento_id', $departamentoId);
            }
            $pendientes = $pendientesQuery->count();
        }

        return view('modules.afiliacion.workload', compact('analistas', 'pendientes', 'isAdmin', 'isCSRSupervisor'), [
            'estadoToWatch' => $isCSRSupervisor ? 'Devuelta' : 'En revisión'
        ]);
    }
}
