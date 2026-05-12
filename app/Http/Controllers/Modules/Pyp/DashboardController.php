<?php

namespace App\Http\Controllers\Modules\Pyp;

use App\Http\Controllers\Controller;
use App\Models\PypExpediente;
use App\Models\PypPrograma;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs Generales
        $stats = [
            'total' => PypExpediente::count(),
            'alto' => PypExpediente::where('riesgo_nivel', 'Alto')->count(),
            'moderado' => PypExpediente::where('riesgo_nivel', 'Moderado')->count(),
            'bajo' => PypExpediente::where('riesgo_nivel', 'Bajo')->count(),
            'descompensados' => PypExpediente::where('estado_clinico', 'Descompensado')->count(),
        ];

        // Distribución por Género (Simulado desde relación Afiliado)
        $generoDist = PypExpediente::join('afiliados', 'pyp_expedientes.afiliado_id', '=', 'afiliados.id')
            ->select('afiliados.sexo', DB::raw('count(*) as total'))
            ->groupBy('afiliados.sexo')
            ->get();

        // Alertas: Pacientes Críticos sin seguimiento reciente (> 7 días)
        $alertas = PypExpediente::with('afiliado')
            ->where('riesgo_nivel', 'Alto')
            ->where(function($q) {
                $q->whereNull('ultimo_seguimiento_at')
                  ->orWhere('ultimo_seguimiento_at', '<', now()->subDays(7));
            })
            ->take(5)
            ->get();

        $programas = PypPrograma::withCount('expedientes')->get();

        return view('modules.pyp.dashboard', compact('stats', 'generoDist', 'alertas', 'programas'));
    }
}
