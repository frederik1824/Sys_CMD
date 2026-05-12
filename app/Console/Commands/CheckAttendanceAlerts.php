<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asistencia\Empleado;
use App\Models\Asistencia\Registro;
use Carbon\Carbon;

class CheckAttendanceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asistencia:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detecta ausencias y tardanzas críticas en tiempo real para generar alertas operativas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificación de asistencia...');
        
        $ahora = Carbon::now();
        $hoy = Carbon::today();

        // 1. Detectar Representantes que debieron entrar y no lo han hecho
        $empleados = Empleado::with('turno')
            ->where('estado', 'activo')
            ->whereDoesntHave('registros', function($q) use ($hoy) {
                $q->where('fecha', $hoy);
            })
            ->get();

        foreach ($empleados as $emp) {
            $entradaEsperada = Carbon::parse($hoy->format('Y-m-d') . ' ' . $emp->turno->entrada_esperada);
            $limiteTolerancia = $entradaEsperada->copy()->addMinutes($emp->turno->tolerancia_minutos);

            if ($ahora->greaterThan($limiteTolerancia)) {
                $this->warn("Alerta: {$emp->nombre_completo} no ha registrado entrada (Esperada: {$emp->turno->entrada_esperada})");
                // Aquí se podría disparar una notificación real (Email, WhatsApp, Notificación DB)
                // Por ahora lo logueamos o preparamos para un panel de alertas live
            }
        }

        $this->info('Verificación completada.');
    }
}
