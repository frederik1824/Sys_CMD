<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UpdateManagerApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Aplicación
        $app = Application::updateOrCreate(
            ['slug' => 'update_manager'],
            [
                'name' => 'Update Manager',
                'description' => 'Centro de actualizaciones on-premise, backups y monitoreo de salud del servidor.',
                'route' => 'admin.updates.index',
                'icon' => 'ph-duotone ph-rocket-launch',
                'color' => 'blue',
                'is_active' => true,
                'is_visible' => true,
                'order_weight' => 100, // Al final
            ]
        );

        // 2. Permiso específico
        Permission::firstOrCreate(['name' => 'access_update_manager', 'guard_name' => 'web']);

        // 3. Asignar al Admin
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo('access_update_manager');
        }

        $this->command->info('Aplicación Update Manager registrada correctamente.');
    }
}
