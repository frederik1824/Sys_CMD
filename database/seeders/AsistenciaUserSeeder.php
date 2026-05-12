<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asistencia\Empleado;
use App\Models\Asistencia\Cargo;
use App\Models\Asistencia\Turno;
use Illuminate\Support\Facades\Hash;

class AsistenciaUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Usuario Supervisor
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor.sac@ars.cmd'],
            [
                'name' => 'Supervisor de Servicio al Cliente',
                'password' => Hash::make('Asistencia2026*'),
                'email_verified_at' => now()
            ]
        );

        $supervisor->assignRole('Supervisor de Servicio al Cliente');

        // 2. Vincular como Empleado
        $cargo = Cargo::where('nombre', 'Representante de Servicio')->first();
        $turno = Turno::first(); // Asignar el primer turno disponible
        
        Empleado::updateOrCreate(
            ['user_id' => $supervisor->id],
            [
                'codigo_empleado' => 'SUP-001',
                'cedula' => '000-0000000-1',
                'nombre_completo' => $supervisor->name,
                'cargo_id' => $cargo->id ?? null,
                'turno_id' => $turno->id ?? null,
                'fecha_ingreso' => now(),
                'estado' => 'activo'
            ]
        );
    }
}
