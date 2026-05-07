<?php

$replacements = [
    // Carnetizacion / CMD
    "route('carnetizacion.afiliados." => "route('carnetizacion.afiliados.",
    "route(\"afiliados." => "route(\"carnetizacion.afiliados.",
    "route('carnetizacion.import.index')" => "route('carnetizacion.import.index')",
    "route(\"import.index\")" => "route(\"carnetizacion.import.index\")",
    "route('carnetizacion.import.store')" => "route('carnetizacion.import.store')",
    "route('carnetizacion.import.progress'" => "route('carnetizacion.import.progress'",
    "route('carnetizacion.import.template')" => "route('carnetizacion.import.template')",
    "route('carnetizacion.sync_center." => "route('carnetizacion.sync_center.",
    "route(\"sync_center." => "route(\"carnetizacion.sync_center.",
    "route('carnetizacion.callcenter." => "route('carnetizacion.callcenter.",
    "route('carnetizacion.logistica." => "route('carnetizacion.logistica.",
    "route('carnetizacion.mensajeros." => "route('carnetizacion.mensajeros.",
    "route('carnetizacion.rutas." => "route('carnetizacion.rutas.",
    "route('carnetizacion.despachos." => "route('carnetizacion.despachos.",
    "route('carnetizacion.lotes." => "route('carnetizacion.lotes.",
    "route('carnetizacion.evidencias." => "route('carnetizacion.evidencias.",
    "route('carnetizacion.cierre." => "route('carnetizacion.cierre.",
    "route('carnetizacion.liquidacion." => "route('carnetizacion.liquidacion.",
    "route('carnetizacion.notas.store')" => "route('carnetizacion.notas.store')",

    // Sistema
    "route('sistema.empresas." => "route('sistema.empresas.",
    "route(\"empresas." => "route(\"sistema.empresas.",
    "route('sistema.usuarios." => "route('sistema.usuarios.",
    "route(\"usuarios." => "route(\"sistema.usuarios.",
    "route('sistema.departamentos." => "route('sistema.departamentos.",
    "route('sistema.roles." => "route('sistema.roles.",
    "route('sistema.audit.index')" => "route('sistema.audit.index')",
    "route(\"admin.audit.index\")" => "route(\"sistema.audit.index\")",
    "route('sistema.cortes." => "route('sistema.cortes.",
    "route('sistema.responsables." => "route('sistema.responsables.",
    "route('sistema.estados." => "route('sistema.estados.",
    "route('sistema.proveedores." => "route('sistema.proveedores.",

    // Reportes
    "route('reportes.index')" => "route('reportes.index')", // No change needed if name was reportes.index
    "route('reportes.executive.suite')" => "route('reportes.executive.suite')",
    "route('reportes.executive')" => "route('reportes.executive')",
    "route('reportes.export_center')" => "route('reportes.export_center')",
    "route('reportes.resumen')" => "route('reportes.resumen')",
    "route('reportes.supervision')" => "route('reportes.supervision')",
    "route(\"reportes.supervision\")" => "route(\"reportes.supervision\")",
    "route('reportes.export')" => "route('reportes.export')",
    "route('reportes.heatmap')" => "route('reportes.heatmap')",
    "route('reportes.sla_alerts')" => "route('reportes.sla_alerts')",
    "route('reportes.comparativa')" => "route('reportes.comparativa')",
    "route('reportes.pendientes')" => "route('reportes.pendientes')",

    // Solicitudes Afiliacion (already mapped to afiliacion.)
    "route('afiliacion." => "route('afiliacion.",
    "route(\"solicitudes-afiliacion." => "route(\"afiliacion.",

    // Active Checks (Blade request()->routeIs)
    "request()->routeIs('carnetizacion.afiliados.*')" => "request()->routeIs('carnetizacion.afiliados.*')",
    "request()->routeIs('sistema.empresas.*')" => "request()->routeIs('sistema.empresas.*')",
    "request()->routeIs('sistema.usuarios.*')" => "request()->routeIs('sistema.usuarios.*')",
    "request()->routeIs('sistema.departamentos.*')" => "request()->routeIs('sistema.departamentos.*')",
    "request()->routeIs('afiliacion.*')" => "request()->routeIs('afiliacion.*')",
];

// Re-scanning all files in project
exec("grep -r \"route('\" c:\\Users\\flopez\\Videos\\sys_carnet", $output1);
exec("grep -r \"route(\\\"\" c:\\Users\\flopez\\Videos\\sys_carnet", $output2);

$foundFiles = [];
foreach(array_merge($output1, $output2) as $line) {
    if (preg_match('/^(.+?):/', $line, $matches)) {
        $file = $matches[1];
        if (strpos($file, 'vendor') === false && strpos($file, 'storage') === false && strpos($file, '.git') === false && strpos($file, 'node_modules') === false) {
            $foundFiles[$file] = true;
        }
    }
}

foreach (array_keys($foundFiles) as $path) {
    if (file_exists($path) && is_file($path)) {
        $content = file_get_contents($path);
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($content !== $newContent) {
            file_put_contents($path, $newContent);
            echo "Updated: $path\n";
        }
    }
}
