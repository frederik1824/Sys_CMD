<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CallCenterRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos
        $permissions = [
            'access_callcenter',
            'manage_own_calls',
            'assign_calls',
            'view_callcenter_dashboard'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Rol Gestor de Llamadas
        $gestorRole = Role::firstOrCreate(['name' => 'Gestor de Llamadas']);
        $gestorRole->syncPermissions(['access_callcenter', 'manage_own_calls']);

        // Rol Supervisor de Llamadas
        $supervisorRole = Role::firstOrCreate(['name' => 'Supervisor de Llamadas']);
        $supervisorRole->syncPermissions(['access_callcenter', 'manage_own_calls', 'assign_calls', 'view_callcenter_dashboard']);

        // Dar permisos al Admin
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Dar permisos al Admin
        $superAdminRole = Role::where('name', 'Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permissions);
        }
    }
}
