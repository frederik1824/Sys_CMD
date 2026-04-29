<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ModuleRolesPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Traspasos
        $permTraspasos = ['access_traspasos'];
        foreach ($permTraspasos as $p) Permission::firstOrCreate(['name' => $p]);
        
        $roleAgenteTras = Role::where('name', 'Agente de Traspasos')->first();
        if ($roleAgenteTras) $roleAgenteTras->givePermissionTo($permTraspasos);
        
        $roleSupTras = Role::where('name', 'Supervisor de Traspasos')->first();
        if ($roleSupTras) $roleSupTras->givePermissionTo($permTraspasos);

        // 2. Afiliación
        $permAfilIndex = ['solicitudes_afiliacion.index', 'solicitudes_afiliacion.show'];
        $permAfilFull = [
            'solicitudes_afiliacion.index',
            'solicitudes_afiliacion.create',
            'solicitudes_afiliacion.show',
            'solicitudes_afiliacion.asignarse',
            'solicitudes_afiliacion.procesar',
            'solicitudes_afiliacion.configurar',
            'solicitudes_afiliacion.ver_todas'
        ];

        foreach ($permAfilFull as $p) Permission::firstOrCreate(['name' => $p]);

        $roleCSR = Role::where('name', 'Servicio al Cliente (CSR)')->first();
        if ($roleCSR) $roleCSR->givePermissionTo(['solicitudes_afiliacion.index', 'solicitudes_afiliacion.create', 'solicitudes_afiliacion.show']);

        $roleAnalista = Role::where('name', 'Analista de Afiliación')->first();
        if ($roleAnalista) $roleAnalista->givePermissionTo(['solicitudes_afiliacion.index', 'solicitudes_afiliacion.show', 'solicitudes_afiliacion.asignarse', 'solicitudes_afiliacion.procesar']);

        $roleSupAfil = Role::where('name', 'Supervisor de Afiliación')->first();
        if ($roleSupAfil) $roleSupAfil->givePermissionTo($permAfilFull);

        // 3. Carnetización (CMD)
        $permCMD = ['access_cmd', 'manage_affiliates'];
        foreach ($permCMD as $p) Permission::firstOrCreate(['name' => $p]);
        
        // Asignar permisos básicos a roles de ID System
        $rolesCMD = ['Asistente de Logística', 'Auditor', 'Gestor de Llamadas', 'Operador', 'Supervisor de Llamadas'];
        foreach ($rolesCMD as $r) {
            $role = Role::where('name', $r)->first();
            if ($role) $role->givePermissionTo($permCMD);
        }
    }
}
