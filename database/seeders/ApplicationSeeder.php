<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // El config tiene la estructura ['list' => [...]]
        $modules = config('modules.list', []);

        foreach ($modules as $key => $module) {
            Application::updateOrCreate(
                ['key' => $key],
                [
                    'name' => $module['name'],
                    'description' => $module['description'],
                    'route' => $module['route'] ?? null,
                    'icon' => $module['icon'],
                    'color' => $module['color'] ?? 'blue',
                    'status' => ($module['status'] === 'coming_soon' || $module['status'] === 'development') ? 'development' : 'active',
                    'is_visible' => true,
                    'order_weight' => $module['order'] ?? 0
                ]
            );
        }

        // Aplicación Maestra de Accesos (Nueva)
        Application::updateOrCreate(['key' => 'access_control'], [
            'name' => 'Control de Accesos',
            'description' => 'Administración centralizada de usuarios, aplicaciones y roles contextuales.',
            'route' => 'admin.access.index',
            'icon' => 'ph-duotone ph-shield-checkered',
            'color' => 'indigo',
            'status' => 'active',
            'is_visible' => true,
            'order_weight' => 999
        ]);
    }
}
