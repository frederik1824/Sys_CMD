<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'firebase/webhook'
        ]);
        
        $middleware->append(\App\Http\Middleware\MaintenanceLockMiddleware::class);
        $middleware->append(\App\Http\Middleware\SanitizeInput::class);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'access_module' => \App\Http\Middleware\CheckApplicationAccess::class,
            'app_access' => \App\Http\Middleware\CheckApplicationAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e, \Illuminate\Http\Request $request) {
            if (str_contains($e->getMessage(), '2002') || str_contains($e->getMessage(), 'Connection refused')) {
                return response()->view('errors.db-connection', ['exception' => $e], 500);
            }
        });
    })->create();
