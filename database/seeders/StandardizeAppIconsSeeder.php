<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;

class StandardizeAppIconsSeeder extends Seeder
{
    public function run(): void
    {
        $updates = [
            'cmd' => ['icon' => 'ph-duotone ph-identification-badge', 'color' => 'blue'],
            'afiliacion' => ['icon' => 'ph-duotone ph-user-plus', 'color' => 'indigo'],
            'rrhh' => ['icon' => 'ph-duotone ph-users-three', 'color' => 'emerald'],
            'traspasos' => ['icon' => 'ph-duotone ph-arrows-left-right', 'color' => 'amber'],
            'intranet' => ['icon' => 'ph-duotone ph-folder-notched-open', 'color' => 'violet'],
            'reportes' => ['icon' => 'ph-duotone ph-chart-line-up', 'color' => 'rose'],
            'admin' => ['icon' => 'ph-duotone ph-gear-six', 'color' => 'slate'],
            'sync_center' => ['icon' => 'ph-duotone ph-arrows-clockwise', 'color' => 'cyan'],
            'access_control' => ['icon' => 'ph-duotone ph-shield-checkered', 'color' => 'indigo'],
            'call_center' => ['icon' => 'ph-duotone ph-headset', 'color' => 'emerald'],
        ];

        foreach ($updates as $slug => $data) {
            Application::where('slug', $slug)->update($data);
        }
    }
}
