<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExecutiveDashboardService;
use Illuminate\Support\Facades\Gate;

class ExecutiveDashboardController extends Controller
{
    protected $executiveService;

    public function __construct(ExecutiveDashboardService $executiveService)
    {
        $this->executiveService = $executiveService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Admin');
        $dept = $user->departamento;
        
        // Determinar permisos por módulo (Para la UI de navegación)
        $permissions = [
            'cmd' => $isAdmin || $user->can('access_cmd') || ($dept && in_array($dept->codigo, ['LOG', 'OPER', 'ADMISION'])),
            'afiliacion' => $isAdmin || $user->can('solicitudes_afiliacion.index') || ($dept && in_array($dept->codigo, ['AFIL', 'AUTOR', 'AUDIT', 'SERV'])),
            'traspasos' => $isAdmin || $user->can('access_traspasos') || ($dept && in_array($dept->codigo, ['TRA', 'VENTAS'])),
            'dispersion' => $isAdmin || $user->can('dispersion.access'),
            'pss' => $isAdmin || $user->can('pss.access'),
            'callcenter' => $isAdmin || $user->can('callcenter.access'),
            'asistencia' => $isAdmin || $user->can('asistencia.access'),
            'cloud' => $isAdmin
        ];

        // Rango de fechas por defecto
        $period = $request->get('period', 'month');
        $validPeriods = ['today', 'week', 'month', 'year'];
        if (!in_array($period, $validPeriods)) {
            $period = 'month';
        }

        // Obtener datos desde el servicio (cacheados)
        $data = $this->executiveService->getAllMetrics($period);

        return view('reportes.executive_v4.index', compact('data', 'period', 'permissions'));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'month');
        $data = $this->executiveService->getAllMetrics($period);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reportes.executive_v4.pdf', compact('data', 'period'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Executive_Dashboard_' . ucfirst($period) . '_' . date('Ymd_His') . '.pdf');
    }
}
