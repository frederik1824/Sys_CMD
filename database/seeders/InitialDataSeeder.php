<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Roles
        $roles = ['Administrador', 'Supervisor', 'Operador', 'Consulta'];
        foreach ($roles as $rol) {
            \App\Models\Rol::firstOrCreate(['nombre' => $rol]);
        }

        // Responsables
        $responsables = ['ARS CMD', 'SAFESURE'];
        foreach ($responsables as $resp) {
            \App\Models\Responsable::firstOrCreate(['nombre' => $resp]);
        }

        // Estados
        $estados = [
            ['nombre' => 'Pendiente', 'es_final' => false],
            ['nombre' => 'Contactado', 'es_final' => false],
            ['nombre' => 'En ruta', 'es_final' => false],
            ['nombre' => 'No localizado', 'es_final' => false],
            ['nombre' => 'Reprogramado', 'es_final' => false],
            ['nombre' => 'Carnet entregado', 'es_final' => false],
            ['nombre' => 'Pendiente de recepción', 'es_final' => false],
            ['nombre' => 'Cierre parcial', 'es_final' => false],
            ['nombre' => 'Completado', 'es_final' => true],
        ];
        foreach ($estados as $est) {
            \App\Models\Estado::firstOrCreate(['nombre' => $est['nombre']], ['es_final' => $est['es_final']]);
        }

        // Admin User
        $adminRol = \App\Models\Rol::where('nombre', 'Administrador')->first();
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@arscmd.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'), // password
                'rol_id' => $adminRol->id ?? null,
            ]
        );
    }
}
