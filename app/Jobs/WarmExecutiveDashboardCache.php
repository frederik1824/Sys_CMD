<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ExecutiveDashboardService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WarmExecutiveDashboardCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ExecutiveDashboardService $service): void
    {
        $periods = ['today', 'week', 'month', 'year'];
        
        Log::info('Inicio de precarga (Warm up) de la caché del Dashboard Ejecutivo.');

        foreach ($periods as $period) {
            $cacheKey = "executive_dashboard_full_{$period}";
            
            // Calculamos todas las métricas en crudo
            $dates = $this->getDateRange($period);
            
            $data = [
                'resumen' => $service->getResumenMetrics($dates),
                'carnetizacion' => $service->getCarnetizacionMetrics($dates),
                'afiliacion' => $service->getAfiliacionMetrics($dates),
                'traspasos' => $service->getTraspasosMetrics($dates),
                'dispersion' => $service->getDispersionMetrics($dates),
                'pss' => $service->getPssMetrics($dates),
                'callcenter' => $service->getCallCenterMetrics($dates),
                'asistencia' => $service->getAsistenciaMetrics($dates),
                'alertas' => $service->generateAlerts($dates),
                'tendencia' => $service->getGlobalTrend()
            ];

            // Guardamos con TTL de 20 minutos (1200 segundos), asumiendo que el Job corre cada 15 min.
            Cache::put($cacheKey, $data, 1200);
        }

        Log::info('Fin de precarga de la caché del Dashboard Ejecutivo.');
    }

    private function getDateRange(string $period): array
    {
        $now = \Carbon\Carbon::now();
        switch ($period) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay(), $now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay()];
            case 'week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek(), $now->copy()->subWeek()->startOfWeek(), $now->copy()->subWeek()->endOfWeek()];
            case 'year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear(), $now->copy()->subYear()->startOfYear(), $now->copy()->subYear()->endOfYear()];
            case 'month':
            default:
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth(), $now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()];
        }
    }
}
