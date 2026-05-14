<?php

namespace App\Services;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class AppContextService
{
    const APP_CMD = 'cmd';
    const APP_TRASPASOS = 'traspasos';
    const APP_AFILIACION = 'afiliacion';
    const APP_CALL_CENTER = 'call_center';
    const APP_EXECUTIVE = 'executive';
    const APP_ACCESS_CONTROL = 'access_control';
    const APP_UPDATE_MANAGER = 'update_manager';
    const APP_DISPERSION = 'dispersion';
    const APP_PYP = 'pyp';
    const APP_ASISTENCIA = 'asistencia';

    /**
     * Identifica la aplicación activa basada en la ruta actual.
     */
    public function getCurrentApp(): string
    {
        $route = Route::currentRouteName();
        $path = Request::path();

        if (str_starts_with($path, 'traspasos') || str_contains($route, 'traspasos')) {
            return self::APP_TRASPASOS;
        }

        if (str_starts_with($path, 'solicitudes-afiliacion') || str_contains($route, 'afiliacion')) {
            return self::APP_AFILIACION;
        }

        if (str_starts_with($path, 'call-center') || str_contains($route, 'call-center')) {
            return self::APP_CALL_CENTER;
        }

        if (str_contains($route, 'executive')) {
            return self::APP_EXECUTIVE;
        }

        if (str_starts_with($path, 'admin/control-accesos') || str_contains($route, 'admin.access')) {
            return self::APP_ACCESS_CONTROL;
        }

        if (str_starts_with($path, 'admin/updates') || str_contains($route, 'admin.updates')) {
            return self::APP_UPDATE_MANAGER;
        }

        if (str_starts_with($path, 'admin/dispersion') || str_starts_with($path, 'dispersion') || str_contains($route, 'dispersion')) {
            return self::APP_DISPERSION;
        }

        if (str_starts_with($path, 'pyp') || str_contains($route, 'pyp')) {
            return self::APP_PYP;
        }

        if (str_starts_with($path, 'asistencia') || str_contains($route, 'asistencia')) {
            return self::APP_ASISTENCIA;
        }

        return self::APP_CMD;
    }

    /**
     * Obtiene metadatos de la aplicación activa.
     */
    public function getAppMeta(): array
    {
        $app = $this->getCurrentApp();
        
        return match($app) {
            self::APP_TRASPASOS => [
                'name' => 'Traspasos',
                'type' => 'Operativo',
                'color' => 'amber',
                'icon' => 'ph-lightning'
            ],
            self::APP_AFILIACION => [
                'name' => 'Afiliación',
                'type' => 'Interno',
                'color' => 'indigo',
                'icon' => 'ph-list-checks'
            ],
            self::APP_CALL_CENTER => [
                'name' => 'Call Center',
                'type' => 'Gestión CRM',
                'color' => 'emerald',
                'icon' => 'ph-headset'
            ],
            self::APP_ACCESS_CONTROL => [
                'name' => 'Seguridad',
                'type' => 'Administración',
                'color' => 'slate',
                'icon' => 'ph-shield-checkered'
            ],
            self::APP_DISPERSION => [
                'name' => 'Control de Dispersión',
                'type' => 'Administración',
                'color' => 'slate',
                'icon' => 'ph-chart-pie-slice'
            ],
            self::APP_CMD => [
                'name' => 'Servicios',
                'type' => 'CMD',
                'color' => 'blue',
                'icon' => 'ph-buildings'
            ],
            default => [
                'name' => 'Sistema',
                'type' => 'Corporativo',
                'color' => 'slate',
                'icon' => 'ph-circles-four'
            ]
        };
    }
}
