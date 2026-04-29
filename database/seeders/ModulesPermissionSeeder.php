<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModulesPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Extraer permisos requeridos desde config/modules.php
        $modules = config('modules.list');
        $permissions = [];
        
        foreach ($modules as $module) {
            if (isset($module['permission'])) {
                $permissions[] = $module['permission'];
            }
        }

        // Crear permisos que no existan
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar los permisos básicos de módulos al rol Super Admin (si existe)
        $superAdmin = Role::where('name', 'Super Admin')->orWhere('name', 'Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }
    }
}
