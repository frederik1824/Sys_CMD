<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationCallCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Application::updateOrCreate(
            ['slug' => 'call_center'],
            [
                'name' => 'Call Center & CRM',
                'description' => 'Gestión de prospectos, validación documental e integración logística.',
                'route' => 'call-center.index',
                'icon' => 'ph-duotone ph-headset',
                'color' => 'emerald',
                'is_active' => true,
                'is_visible' => true,
                'order_weight' => 50,
            ]
        );
    }
}
