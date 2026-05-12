<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Bypass administrativo y operativo core
        if ($user->hasRole(['Admin', 'Super-Admin', 'Analista de Afiliación', 'Supervisor de Afiliación'])) {
            return $next($request);
        }

        // Verificar el permiso granular
        if (!$user->can($permission)) {
            abort(403, "No tienes el permiso necesario: [{$permission}] para realizar esta acción.");
        }

        return $next($request);
    }
}
