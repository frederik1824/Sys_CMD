<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Application;

class CallCenterAccessControlSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Registrar la Aplicación para el Launchpad
        $app = Application::updateOrCreate(
            ['slug' => 'call_center'],
            [
                'name' => 'Call Center & CRM',
                'description' => 'Gestión inteligente de prospectos, validación documental e integración logística.',
                'route' => 'call-center.worklist',
                'icon' => 'ph-duotone ph-headset',
                'color' => 'emerald',
                'is_active' => true,
                'is_visible' => true,
                'order_weight' => 10,
            ]
        );

        // 2. Definir Permisos Granulares
        $permissions = [
            'callcenter.access'  => 'Acceso general al módulo de Call Center',
            'callcenter.import'  => 'Capacidad de importar data masiva por lotes',
            'callcenter.manage'  => 'Gestionar y registrar llamadas de prospectos',
            'callcenter.promote' => 'Promover prospectos al flujo de carnetización',
            'callcenter.stats'   => 'Ver estadísticas y dashboards operativos',
            'callcenter.admin'   => 'Configuración avanzada del módulo',
        ];

        foreach ($permissions as $name => $desc) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // 3. Configurar Rol: Operador Call Center
        $operador = Role::firstOrCreate(['name' => 'Operador Call Center', 'guard_name' => 'web']);
        $operador->syncPermissions([
            'callcenter.access',
            'callcenter.manage',
        ]);

        // 4. Configurar Rol: Supervisor Call Center
        $supervisor = Role::firstOrCreate(['name' => 'Supervisor Call Center', 'guard_name' => 'web']);
        $supervisor->syncPermissions([
            'callcenter.access',
            'callcenter.import',
            'callcenter.manage',
            'callcenter.promote',
            'callcenter.stats',
        ]);

        // 5. Asegurar que el Admin tenga acceso total
        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo(array_keys($permissions));
        }

        $this->command->info('Configuración de Control de Accesos para Call Center completada.');
    }
}
