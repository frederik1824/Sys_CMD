<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());

        // Implicitly grant "Admin" and "Super-Admin" roles all permissions
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if ($user->email === 'admin@arscmd.com' || $user->hasAnyRole(['Admin', 'Super-Admin'])) {
                return true;
            }
            return null;
        });

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\UpdateLastLogin::class
        );

        // Registro de Observadores para Sincronización con Firebase (Safesure Integration)
        \App\Models\Afiliado::observe(\App\Observers\AfiliadoObserver::class);
        \App\Models\Empresa::observe(\App\Observers\EmpresaObserver::class);
    }
}
