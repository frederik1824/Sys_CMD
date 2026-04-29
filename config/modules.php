<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Suite Empresarial Modules
    |--------------------------------------------------------------------------
    |
    | Define here all the modules available in the application portal.
    | The 'status' can be: 'active', 'development', 'coming_soon', 'maintenance'.
    | The 'permission' corresponds to the Spatie permission required to access it.
    |
    */

    'list' => [
        'cmd' => [
            'name' => 'Carnetización',
            'description' => 'Control de afiliados, carnetización, llamadas y formularios.',
            'icon' => 'badge',
            'color' => 'blue',
            'route' => 'dashboard', // Ruta o nombre de ruta existente
            'status' => 'active',
            'permission' => 'access_cmd',
            'order' => 10,
        ],
        'afiliacion' => [
            'name' => 'Solicitudes de Afiliación',
            'description' => 'Gestión operativa interna: afiliaciones, novedades y validación documental.',
            'icon' => 'person_add',
            'color' => 'indigo',
            'route' => 'solicitudes-afiliacion.index',
            'status' => 'active',
            'permission' => 'solicitudes_afiliacion.index',
            'order' => 20,
        ],
        'rrhh' => [
            'name' => 'Recursos Humanos',
            'description' => 'Gestión de empleados, permisos, vacaciones y expedientes.',
            'icon' => 'groups',
            'color' => 'emerald',
            'route' => 'rrhh.index',
            'status' => 'coming_soon',
            'permission' => 'access_rrhh',
            'order' => 30,
        ],
        'traspasos' => [
            'name' => 'Traspasos',
            'description' => 'Administración de traspasos y validaciones externas.',
            'icon' => 'swap_horiz',
            'color' => 'amber',
            'route' => 'traspasos.index',
            'status' => 'active',
            'permission' => 'access_traspasos',
            'order' => 40,
        ],
        'intranet' => [
            'name' => 'Intranet Documental',
            'description' => 'Políticas, formularios, manuales y documentos internos.',
            'icon' => 'folder_open',
            'color' => 'violet',
            'route' => 'intranet.index',
            'status' => 'development',
            'permission' => 'access_intranet',
            'order' => 50,
        ],
        'reportes' => [
            'name' => 'Dashboard Ejecutivo',
            'description' => 'Reportes gerenciales, KPIs y mapas de calor institucionales.',
            'icon' => 'query_stats',
            'color' => 'rose',
            'route' => 'reportes.executive',
            'status' => 'active',
            'permission' => 'view_reports',
            'order' => 60,
        ],
        'admin' => [
            'name' => 'Configuración y Roles',
            'description' => 'Administración del sistema, roles, permisos y logs de auditoría.',
            'icon' => 'admin_panel_settings',
            'color' => 'slate',
            'route' => 'usuarios.index',
            'status' => 'active',
            'permission' => 'manage_users',
            'order' => 100,
        ],
    ]
];
