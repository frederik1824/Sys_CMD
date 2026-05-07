<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CallCenterEstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Pendiente de gestión', 'color' => '#6B7280', 'icono' => 'clock', 'orden' => 1, 'finalizador' => false],
            ['nombre' => 'En gestión', 'color' => '#3B82F6', 'icono' => 'phone', 'orden' => 2, 'finalizador' => false],
            ['nombre' => 'No contactado', 'color' => '#EF4444', 'icono' => 'phone-off', 'orden' => 3, 'finalizador' => false],
            ['nombre' => 'Contactado', 'color' => '#10B981', 'icono' => 'user-check', 'orden' => 4, 'finalizador' => false],
            ['nombre' => 'Contactado vía empresa', 'color' => '#8B5CF6', 'icono' => 'briefcase', 'orden' => 5, 'finalizador' => false],
            ['nombre' => 'Pendiente de documentación', 'color' => '#F59E0B', 'icono' => 'file-text', 'orden' => 6, 'finalizador' => false],
            ['nombre' => 'Documentación recibida', 'color' => '#059669', 'icono' => 'file-check', 'orden' => 7, 'finalizador' => false],
            ['nombre' => 'Datos actualizados', 'color' => '#6366F1', 'icono' => 'refresh', 'orden' => 8, 'finalizador' => false],
            ['nombre' => 'Listo para despacho', 'color' => '#10B981', 'icono' => 'truck', 'orden' => 9, 'finalizador' => false],
            ['nombre' => 'Enviado a carnetización', 'color' => '#4F46E5', 'icono' => 'external-link', 'orden' => 10, 'finalizador' => false],
            ['nombre' => 'Despachado con mensajero', 'color' => '#D97706', 'icono' => 'send', 'orden' => 11, 'finalizador' => false],
            ['nombre' => 'Entregado al afiliado', 'color' => '#047857', 'icono' => 'user-check', 'orden' => 12, 'finalizador' => false],
            ['nombre' => 'Formulario recibido', 'color' => '#065F46', 'icono' => 'file-badge', 'orden' => 13, 'finalizador' => false],
            ['nombre' => 'Cerrado', 'color' => '#111827', 'icono' => 'check-circle', 'orden' => 14, 'finalizador' => true],
            ['nombre' => 'Rechazado', 'color' => '#991B1B', 'icono' => 'x-circle', 'orden' => 15, 'finalizador' => true],
            ['nombre' => 'No localizable', 'color' => '#4B5563', 'icono' => 'map-pin-off', 'orden' => 16, 'finalizador' => true],
            ['nombre' => 'Datos incorrectos', 'color' => '#B91C1C', 'icono' => 'alert-triangle', 'orden' => 17, 'finalizador' => true],
            ['nombre' => 'Duplicado', 'color' => '#1F2937', 'icono' => 'copy', 'orden' => 18, 'finalizador' => true],
        ];

        foreach ($estados as $estado) {
            \App\Models\CallCenterEstado::updateOrCreate(['nombre' => $estado['nombre']], $estado);
        }
    }
}
