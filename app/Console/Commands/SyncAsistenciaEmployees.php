<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Asistencia\Empleado;
use App\Models\Asistencia\Cargo;
use App\Models\Asistencia\Turno;
use App\Models\Asistencia\Departamento;

class SyncAsistenciaEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asistencia:sync-employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza todos los usuarios con roles de Servicio al Cliente al sistema de asistencia';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de personal SAC...');

        // 1. Asegurar Estructura Base
        $depto = Departamento::firstOrCreate(['codigo' => 'SAC'], ['nombre' => 'Servicio al Cliente']);
        $cargo = Cargo::firstOrCreate(['nombre' => 'Representante de Servicio'], ['departamento_id' => $depto->id]);
        $turno = Turno::first() ?: Turno::create([
            'nombre' => 'Turno General (8-5)',
            'entrada_esperada' => '08:00:00',
            'salida_esperada' => '17:00:00',
            'tolerancia_minutos' => 15,
            'minutos_almuerzo' => 60
        ]);

        // 2. Obtener usuarios con roles relevantes
        $users = User::role(['Servicio al Cliente (CSR)', 'Supervisor de Servicio al Cliente'])->get();
        
        $count = 0;
        foreach ($users as $user) {
            $empleado = Empleado::where('user_id', $user->id)->first();

            if (!$empleado) {
                Empleado::create([
                    'user_id' => $user->id,
                    'codigo_empleado' => 'SAC-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'cedula' => $user->cedula ?? ('TMP-' . str_pad($user->id, 8, '0', STR_PAD_LEFT)),
                    'nombre_completo' => $user->name,
                    'cargo_id' => $cargo->id,
                    'turno_id' => $turno->id,
                    'fecha_ingreso' => now(),
                    'estado' => 'activo'
                ]);
                $this->line("Perfil creado para: {$user->name}");
                $count++;
            }
        }

        $this->info("Sincronización completada. Se crearon {$count} nuevos perfiles de asistencia.");
    }
}
