<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckSla extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-sla';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica el SLA de los afiliados y notifica a los supervisores.';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando verificación de SLA...');

        // Buscamos afiliados que:
        // 1. Tengan fecha de entrega a proveedor
        // 2. NO estén completados
        // 3. Tengan 20 días o más de gestión
        $afiliadosCriticos = \App\Models\Afiliado::whereNotNull('fecha_entrega_proveedor')
            ->whereHas('estado', function($q) {
                $q->where('nombre', '!=', 'Completado');
            })
            ->get()
            ->filter(fn($a) => $a->dias_transcurridos >= 20);

        if ($afiliadosCriticos->isEmpty()) {
            $this->info('No se encontraron nuevos casos críticos de SLA.');
            return;
        }

        $supervisores = \App\Models\User::role(['Admin'])->get();

        $agrupados = $afiliadosCriticos->groupBy('corte_id');

        foreach ($agrupados as $corteId => $items) {
            $corte = \App\Models\Corte::find($corteId);
            $count = $items->count();

            foreach ($supervisores as $user) {
                $user->notify(new \App\Notifications\SlaNotification($count, $corte->nombre ?? 'N/A'));
            }
        }

        $this->info('Notificaciones de SLA enviadas exitosamente.');
    }
}
