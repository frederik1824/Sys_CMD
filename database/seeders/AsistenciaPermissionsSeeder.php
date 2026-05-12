<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AsistenciaPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Permisos Específicos
        $permissions = [
            'asistencia.marcar',
            'asistencia.ver_historial',
            'asistencia.solicitar_permisos',
            'asistencia.ver_dashboard',
            'asistencia.aprobar_permisos',
            'asistencia.ver_reportes',
            'asistencia.gestionar_configuracion'
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Asignar a Roles Existentes o Nuevos
        
        // --- Representante SAC ---
        $roleRepresentante = Role::findOrCreate('Servicio al Cliente (CSR)');
        $roleRepresentante->givePermissionTo([
            'asistencia.marcar',
            'asistencia.ver_historial',
            'asistencia.solicitar_permisos'
        ]);

        // --- Supervisor SAC ---
        $roleSupervisor = Role::findOrCreate('Supervisor de Servicio al Cliente');
        $roleSupervisor->givePermissionTo([
            'asistencia.marcar',
            'asistencia.ver_historial',
            'asistencia.solicitar_permisos',
            'asistencia.ver_dashboard',
            'asistencia.aprobar_permisos',
            'asistencia.ver_reportes'
        ]);

        // --- Admin (Acceso Total) ---
        $roleAdmin = Role::findOrCreate('Admin');
        $roleAdmin->givePermissionTo($permissions);
    }
}
