<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Application;

class CheckApplicationAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $applicationKey): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // 1. Master Bypass para Administradores
        if ($user->hasRole(['Admin', 'Super-Admin'])) {
            return $next($request);
        }

        // 2. Verificar acceso específico a la aplicación
        $hasAccess = $user->applicationAccess()
            ->whereHas('application', function($q) use ($applicationKey) {
                $q->where('slug', $applicationKey)->where('is_active', true);
            })
            ->where('is_active', true)
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('dashboard')->with('error', 'No tienes acceso autorizado a este módulo.');
        }

        return $next($request);
    }
}
