<?php

$replacements = [
    "route('sistema.empresas." => "route('sistema.empresas.",
    "route('sistema.usuarios." => "route('sistema.usuarios.",
    "route('sistema.departamentos." => "route('sistema.departamentos.",
    "route('admin.audit." => "route('sistema.audit.",
    "route('afiliacion." => "route('afiliacion.",
    "request()->routeIs('sistema.empresas.*')" => "request()->routeIs('sistema.empresas.*')",
    "request()->routeIs('sistema.usuarios.*')" => "request()->routeIs('sistema.usuarios.*')",
    "request()->routeIs('sistema.departamentos.*')" => "request()->routeIs('sistema.departamentos.*')",
    "request()->routeIs('afiliacion.*')" => "request()->routeIs('afiliacion.*')",
    "request()->routeIs('admin.audit.*')" => "request()->routeIs('sistema.audit.*')",
    "'/departamentos/'" => "'/sistema/departamentos/'", // Fixed hardcoded path
    "'/solicitudes-afiliacion/configuracion/tipos/" => "'/solicitudes-afiliacion/configuracion/tipos/", // URL stays same
];

$files = [
    'resources/views/livewire/empresa/index.blade.php',
    'resources/views/livewire/dashboard/sla-alerts.blade.php',
    'resources/views/layouts/navigation.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/empresas/show.blade.php',
    'resources/views/empresas/index.blade.php',
    'resources/views/empresas/enrich.blade.php',
    'resources/views/empresas/create.blade.php',
    'resources/views/empresas/edit.blade.php',
    'resources/views/catalogo/index.blade.php',
    'resources/views/afiliados/index.blade.php',
    'resources/views/afiliados/show.blade.php',
    'app/Http/Controllers/EmpresaController.php',
    'resources/views/users/index.blade.php',
    'resources/views/users/edit.blade.php',
    'resources/views/users/create.blade.php',
    'resources/views/roles/index.blade.php',
    'resources/views/admin/audit/index.blade.php',
    'app/Http/Controllers/UserController.php',
    'resources/views/departamentos/index.blade.php',
    'resources/views/modules/afiliacion/workload.blade.php',
    'resources/views/modules/afiliacion/show.blade.php',
    'resources/views/modules/afiliacion/reports.blade.php',
    'resources/views/modules/afiliacion/index.blade.php',
    'resources/views/modules/afiliacion/edit.blade.php',
    'resources/views/modules/afiliacion/create.blade.php',
    'resources/views/modules/afiliacion/config.blade.php',
    'app/Notifications/Modules/Afiliacion/SolicitudCreada.php',
    'app/Http/Controllers/Modules/Afiliacion/SolicitudController.php',
    'app/Http/Controllers/Modules/Afiliacion/ReporteController.php',
    'app/Http/Controllers/Modules/Afiliacion/ConfiguracionController.php',
    'app/Notifications/ResponsabilidadAsignada.php',
    'app/Notifications/SlaNotification.php',
    'resources/views/dashboard.blade.php',
];

foreach ($files as $file) {
    $path = "c:\\Users\\flopez\\Videos\\sys_carnet\\" . str_replace('/', '\\', $file);
    if (file_exists($path)) {
        $content = file_get_contents($path);
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($content !== $newContent) {
            file_put_contents($path, $newContent);
            echo "Updated: $file\n";
        }
    } else {
        echo "Not found: $file\n";
    }
}
