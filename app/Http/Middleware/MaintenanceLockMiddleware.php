<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceLockMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Si el sistema está en modo actualización y el usuario no es un Admin
        // permitimos el acceso a las rutas de actualización para que el admin pueda terminar el proceso
        if (Cache::has('system_update_lock')) {
            if (!$request->is('admin/updates*') && !request()->routeIs('admin.updates.*')) {
                return response()->view('errors.maintenance', [], 503);
            }
        }

        return $next($request);
    }
}
