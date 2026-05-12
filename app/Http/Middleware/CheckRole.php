<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        $user = auth()->user();

        // Bypass total para administradores y roles operativos core
        if ($user->hasAnyRole(['Admin', 'Super-Admin', 'Analista de Afiliación', 'Supervisor de Afiliación'])) {
            return $next($request);
        }

        // Verificación de roles requeridos mediante Spatie
        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');

        return $next($request);
    }
}
