<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'access_admin_panel',
            'manage_users',
            'manage_companies',
            'manage_affiliates',
            'edit_completed_affiliates',
            'delete_records',
            'view_reports',
            'physical_closing',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $superAdmin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $admin = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $operador = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Operador']);
        $auditor = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Auditor']);

        // Assign permissions to roles
        $admin->givePermissionTo(['access_admin_panel', 'manage_companies', 'manage_affiliates', 'view_reports', 'physical_closing']);
        $operador->givePermissionTo(['manage_affiliates', 'view_reports']);
        $auditor->givePermissionTo(['view_reports']);

        // Migrate current users from 'rol_id' to Spatie Roles
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $rolLegacy = \App\Models\Rol::find($user->rol_id);
            if ($rolLegacy) {
                $nombreRol = $rolLegacy->nombre;
                // Mapear roles antiguos a nuevos si es necesario
                if ($nombreRol === 'Administrador') {
                    $user->assignRole($admin);
                } elseif ($nombreRol === 'Supervisor') {
                    $user->assignRole($admin);
                } else {
                    $user->assignRole($operador);
                }
            } else {
                // Si no tiene rol legacy, asignar Operador por defecto
                $user->assignRole($operador);
            }
        }

        // Si existe un usuario "Admin" por defecto (típicamente id=1)
        $firstUser = \App\Models\User::find(1);
        if ($firstUser) {
            $firstUser->assignRole($superAdmin);
        }
    }
}
