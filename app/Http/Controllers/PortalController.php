<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\SolicitudAfiliacion;

class PortalController extends Controller
{
    /**
     * Display the application portal.
     */
    public function index()
    {
        $user = auth()->user();
        
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;

        $stats = [
            'total_solicitudes' => $isAdmin ? SolicitudAfiliacion::count() : 
                                   ($dept ? SolicitudAfiliacion::where('departamento_id', $dept->id)->count() : 
                                   SolicitudAfiliacion::where('solicitante_user_id', $user->id)->count()),
            
            'urgentes' => SolicitudAfiliacion::where('prioridad', 'Urgente')
                            ->whereNotIn('estado', ['Aprobada', 'Rechazada', 'Cerrada', 'Cancelada'])
                            ->when(!$isAdmin, function($q) use ($user, $dept) {
                                if ($dept) return $q->where('departamento_id', $dept->id);
                                return $q->where('solicitante_user_id', $user->id);
                            })->count(),
            
            'pendientes_depto' => SolicitudAfiliacion::where('departamento_id', $user->departamento_id)
                                    ->whereIn('estado', ['Pendiente', 'Corregida'])
                                    ->count(),
            'eficiencia' => 98
        ];

        $modules = config('modules.list');

        // Sort by order
        uasort($modules, function($a, $b) {
            return ($a['order'] ?? 100) <=> ($b['order'] ?? 100);
        });
        // Add additional metadata for display
        foreach ($modules as $key => &$module) {
            $hasPermission = isset($module['permission']) ? $user->can($module['permission']) : true;
            $module['has_access'] = $hasPermission;
            
            // Determine if the route is valid/exists
            $module['url'] = '#';
            if ($hasPermission && $module['status'] === 'active') {
                if (Route::has($module['route'])) {
                    $module['url'] = route($module['route']);
                } else {
                    $module['url'] = url($module['route']);
                }
            }
        }

        return view('portal.index', compact('modules', 'user', 'stats'));
    }
}
