<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asistencia\Departamento;
use App\Models\Asistencia\Cargo;
use App\Models\Asistencia\Turno;
use App\Models\Asistencia\Empleado;
use App\Models\Asistencia\TipoPermiso;
use App\Models\User;

class AsistenciaSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Departamentos
        $dep = Departamento::create(['nombre' => 'Servicio al Cliente', 'codigo' => 'SAC']);
        
        // 2. Cargos
        $cargo = Cargo::create(['nombre' => 'Representante de Servicio', 'departamento_id' => $dep->id]);
        
        // 3. Turnos
        $turnoMañana = Turno::create([
            'nombre' => 'Turno Mañana (8-5)',
            'entrada_esperada' => '08:00:00',
            'salida_esperada' => '17:00:00',
            'tolerancia_minutos' => 15,
            'minutos_almuerzo' => 60
        ]);

        // 4. Tipos de Permiso
        TipoPermiso::create(['nombre' => 'Permiso Médico', 'slug' => 'medico', 'requiere_evidencia' => true]);
        TipoPermiso::create(['nombre' => 'Diligencia Personal', 'slug' => 'personal', 'requiere_evidencia' => false]);
        TipoPermiso::create(['nombre' => 'Fallecimiento Familiar', 'slug' => 'duelo', 'requiere_evidencia' => true]);

        // 5. Vincular usuario actual como empleado de prueba
        $admin = User::first();
        if ($admin) {
            Empleado::updateOrCreate(
                ['user_id' => $admin->id],
                [
                    'codigo_empleado' => 'EMP-001',
                    'cedula' => '000-0000000-0',
                    'nombre_completo' => $admin->name,
                    'cargo_id' => $cargo->id,
                    'turno_id' => $turnoMañana->id,
                    'fecha_ingreso' => now()->subYear(),
                    'estado' => 'activo'
                ]
            );
        }
    }
}
