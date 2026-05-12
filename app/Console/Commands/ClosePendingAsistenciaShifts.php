<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asistencia\Registro;
use Carbon\Carbon;

class ClosePendingAsistenciaShifts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asistencia:close-pending-shifts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra los turnos que no marcaron salida el día anterior y solicita justificación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando turnos incompletos...');

        // Buscamos registros de días pasados que tengan entrada pero no salida
        $incompletos = Registro::whereNull('hora_salida')
            ->whereNotNull('hora_entrada')
            ->where('fecha', '<', Carbon::today())
            ->where('requiere_justificacion', false)
            ->get();

        foreach ($incompletos as $reg) {
            $reg->update([
                'requiere_justificacion' => true,
                'observaciones' => ($reg->observaciones ? $reg->observaciones . "\n" : "") . "Turno cerrado automáticamente por el sistema por falta de salida."
            ]);
            
            $this->warn("Registro ID {$reg->id} marcado para justificación (Empleado: {$reg->empleado->nombre_completo})");
        }

        $this->info('Proceso completado.');
    }
}
