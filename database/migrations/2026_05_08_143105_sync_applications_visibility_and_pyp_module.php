<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Application;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Asegurar Módulo PyP
        Application::updateOrCreate(
            ['slug' => 'pyp'],
            [
                'name' => 'Programa PyP',
                'description' => 'Módulo de Promoción y Prevención / Riesgo Clínico',
                'route' => 'pyp.dashboard',
                'icon' => 'ph ph-heartbeat',
                'color' => 'indigo',
                'is_active' => true,
                'is_visible' => true,
                'order_weight' => 100
            ]
        );

        // 2. Desactivar Módulos Obsoletos (Configuración y Roles)
        Application::whereIn('slug', ['admin', 'configuracion'])->update([
            'is_active' => false,
            'is_visible' => false
        ]);

        // 3. Asegurar consistencia en otros módulos críticos
        Application::where('slug', 'call_center')->update(['name' => 'Call Center & CRM']);
        Application::where('slug', 'afiliacion')->update(['name' => 'Solicitudes de Afiliación']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Opcional: Revertir estados si es necesario
    }
};
