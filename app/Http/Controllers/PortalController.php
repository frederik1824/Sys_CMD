<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\SolicitudAfiliacion;
use App\Models\Application;

class PortalController extends Controller
{
    /**
     * Display the application portal.
     */
    public function index()
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole(['Admin', 'Super-Admin']);

        // Obtener la versión actual del sistema
        $latestUpdate = \App\Models\SystemUpdate::latest()->first();
        $systemVersion = $latestUpdate ? $latestUpdate->version : '3.0.0';

        // 1. Obtener aplicaciones autorizadas para el usuario
        // Si es Admin, ve todas las aplicaciones activas
        // Si es usuario regular, ve las que tiene asignadas y están activas
        if ($isAdmin) {
            $apps = Application::where('is_active', true)
                        ->orderBy('order_weight')
                        ->get();
            \Illuminate\Support\Facades\Log::info("Portal Apps for Admin: " . $apps->pluck('slug')->implode(', '));
        } else {
            $apps = Application::where('is_active', true)
                        ->whereHas('userAccess', function($q) use ($user) {
                            $q->where('user_id', $user->id)->where('is_active', true);
                        })
                        ->orderBy('order_weight')
                        ->get();
        }

        $modules = [];
        foreach ($apps as $app) {
            // Determinar URL final
            $url = '#';
            if ($app->route) {
                if (Route::has($app->route)) {
                    $url = route($app->route);
                } else {
                    // Fallback para rutas de texto o absolutas
                    $url = str_starts_with($app->route, 'http') ? $app->route : url($app->route);
                }
            }
            
            $modules[$app->slug] = [
                'name' => $app->name,
                'description' => $app->description ?? 'Acceso al módulo ' . $app->name,
                'icon' => $app->icon ?? 'ph ph-app-window',
                'color' => $app->color ?? 'blue',
                'status' => $app->is_active ? 'active' : 'inactive',
                'has_access' => true, // Ya filtrado arriba
                'url' => $url,
                'order' => $app->order_weight
            ];
        }

        // Estadísticas básicas (CMD context)
        $stats = [
            'total_solicitudes' => SolicitudAfiliacion::count(),
            'urgentes' => SolicitudAfiliacion::where('prioridad', 'Urgente')->whereNotIn('estado', ['Aprobada', 'Cerrada'])->count(),
            'eficiencia' => 98
        ];

        return view('portal.index', compact('modules', 'user', 'stats', 'systemVersion'));
    }
}
