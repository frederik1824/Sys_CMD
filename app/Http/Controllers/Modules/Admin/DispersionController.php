<?php

namespace App\Http\Controllers\Modules\Admin;

use App\Http\Controllers\Controller;
use App\Models\DispersionPeriod;
use App\Models\DispersionCorte;
use App\Models\DispersionIndicator;
use App\Models\DispersionBajaType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DispersionController extends Controller
{
    public function showReport(DispersionPeriod $period)
    {
        $period->load(['cortes.values.indicator', 'cortes.bajaValues.bajaType']);
        $indicators = DispersionIndicator::orderBy('order_weight')->get();
        $bajaTypes = DispersionBajaType::orderBy('order_weight')->get();
        $stats = self::getStats($period);

        return view('modules.admin.dispersion.report_preview', compact('period', 'indicators', 'bajaTypes', 'stats'));
    }

    public function downloadReport(DispersionPeriod $period)
    {
        $period->load(['cortes.values.indicator', 'cortes.bajaValues.bajaType']);
        $indicators = DispersionIndicator::orderBy('order_weight')->get();
        $bajaTypes = DispersionBajaType::orderBy('order_weight')->get();
        $stats = self::getStats($period);

        $pdf = Pdf::loadView('modules.admin.dispersion.pdf_report', compact('period', 'indicators', 'bajaTypes', 'stats'));
        
        $filename = 'Reporte_Dispersion_' . $period->month_name . '_' . $period->year . '.pdf';
        
        return $pdf->download($filename);
    }

    public function index(Request $request)
    {
        $view = $request->query('view');

        if ($view === 'history') {
            return $this->history();
        }

        if ($view === 'reports') {
            return $this->reports();
        }

        if ($view === 'config') {
            return $this->config();
        }

        if ($view === 'cartera') {
            return $this->cartera($request);
        }

        $periods = DispersionPeriod::with(['createdBy', 'cortes'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12)
            ->withQueryString();

        // Datos para el dashboard (Último mes consolidado)
        $latestPeriod = DispersionPeriod::orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->first();

        $stats = $this->getStats($latestPeriod);

        // Datos para gráficos (Últimos 6 meses)
        $history = DispersionPeriod::with(['cortes.values'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get()
            ->reverse();

        $chartData = [
            'labels' => $history->map(fn($p) => $p->month_name . ' ' . $p->year),
            'afiliados' => $history->map(fn($p) => self::getStats($p)['total_afiliados']),
            'montos' => $history->map(fn($p) => self::getStats($p)['monto_total']),
            'bajas' => $history->map(fn($p) => self::getStats($p)['total_bajas']),
        ];

        return view('modules.admin.dispersion.index', compact('periods', 'stats', 'latestPeriod', 'chartData'));
    }

    /**
     * Vista de histórico con filtros avanzados.
     */
    public function history()
    {
        $periods = DispersionPeriod::with(['createdBy', 'cortes'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('modules.admin.dispersion.history', compact('periods'));
    }

    /**
     * Central de reportes ejecutivos.
     */
    public function reports()
    {
        $periods = DispersionPeriod::with(['cortes'])
            ->whereHas('cortes', fn($q) => $q->where('status', '!=', 'pending'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
                ->get();

        return view('modules.admin.dispersion.reports', compact('periods'));
    }

    /**
     * Gestión de la Cartera Homologada de Pensionados.
     */
    public function cartera(Request $request)
    {
        $search = $request->query('search');

        $query = \App\Models\Modules\Dispersion\PensionadoMaster::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre_completo', 'like', "%{$search}%");
                
                $cleanSearch = preg_replace('/[^0-9]/', '', $search);
                if (!empty($cleanSearch)) {
                    $q->orWhereRaw("REPLACE(cedula, '-', '') LIKE ?", ["%{$cleanSearch}%"])
                      ->orWhereRaw("REPLACE(nss, '-', '') LIKE ?", ["%{$cleanSearch}%"]);
                }
            });
        }

        // Estadísticas sobre el total de la consulta (antes de paginar)
        $totalCartera = (clone $query)->count();
        $totalActivos = (clone $query)->where('estado_sistema', 'ACTIVO')->count();
        $totalEnProceso = (clone $query)->where('estado_sistema', 'EN PROCESO')->count();
        $totalNuevos = (clone $query)->whereNull('notificado_at')->whereNotNull('ultimo_pago_confirmado_at')->count();

        $pensionados = $query->orderBy('ultimo_pago_confirmado_at', 'desc')
            ->orderBy('nombre_completo')
            ->paginate(25)
            ->withQueryString();

        return view('modules.admin.dispersion.pensionados.cartera', compact(
            'pensionados', 'search', 'totalCartera', 'totalActivos', 'totalEnProceso', 'totalNuevos'
        ));
    }

    /**
     * Configuración de catálogos e indicadores.
     */
    public function config()
    {
        $indicators = DispersionIndicator::orderBy('category')->orderBy('order_weight')->get();
        $bajaTypes = DispersionBajaType::orderBy('order_weight')->get();

        return view('modules.admin.dispersion.config', compact('indicators', 'bajaTypes'));
    }

    public function storePeriod(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        // Evitar duplicados
        $exists = DispersionPeriod::where('year', $request->year)
            ->where('month', $request->month)
            ->exists();

        if ($exists) {
            return back()->with('error', 'El periodo ya existe.');
        }

        $period = DispersionPeriod::create([
            'year' => $request->year,
            'month' => $request->month,
            'status' => 'pending',
            'created_by' => Auth::id()
        ]);

        // Crear automáticamente los dos cortes básicos
        DispersionCorte::create([
            'period_id' => $period->id,
            'corte_number' => 1,
            'status' => 'pending',
            'user_id' => Auth::id()
        ]);

        DispersionCorte::create([
            'period_id' => $period->id,
            'corte_number' => 2,
            'status' => 'pending',
            'user_id' => Auth::id()
        ]);

        return redirect()->route('dispersion.show', $period->id)
            ->with('success', 'Periodo creado exitosamente.');
    }

    public function show(DispersionPeriod $period)
    {
        $period->load(['cortes.user', 'cortes.values.indicator', 'cortes.bajaValues.bajaType']);
        
        $indicators = DispersionIndicator::orderBy('order_weight')->get();
        $bajaTypes = DispersionBajaType::orderBy('order_weight')->get();

        return view('modules.admin.dispersion.show', compact('period', 'indicators', 'bajaTypes'));
    }

    public static function getStats($period)
    {
        if (!$period) return [
            'total_afiliados' => 0,
            'total_dispersados' => 0,
            'monto_total' => 0,
            'total_bajas' => 0,
        ];

        $corteIds = $period->cortes->pluck('id');
        
        // Lógica de "Snapshot": El total del mes es el del 2do corte si existe, sino el del 1ro
        $totalAfiliados = 0;
        $corte2 = $period->cortes->where('corte_number', 2)->first();
        $corte1 = $period->cortes->where('corte_number', 1)->first();

        $val2 = \App\Models\DispersionValue::where('corte_id', $corte2?->id)
            ->whereHas('indicator', fn($q) => $q->where('code', 'TOTAL_GENERAL_PDSS'))
            ->first()?->quantity ?? 0;

        if ($val2 > 0) {
            $totalAfiliados = $val2;
        } else {
            $totalAfiliados = \App\Models\DispersionValue::where('corte_id', $corte1?->id)
                ->whereHas('indicator', fn($q) => $q->where('code', 'TOTAL_GENERAL_PDSS'))
                ->first()?->quantity ?? 0;
        }

        // Lógica "Acumulativa": Los montos y bajas se suman de ambos cortes
        $totalDispersados = \App\Models\DispersionValue::whereIn('corte_id', $corteIds)
            ->whereHas('indicator', fn($q) => $q->where('code', 'TOTAL_DISPERSADOS'))
            ->sum('quantity');

        $montoTotal = \App\Models\DispersionValue::whereIn('corte_id', $corteIds)
            ->whereHas('indicator', fn($q) => $q->where('category', 'Montos')->where('is_total', false))
            ->sum('amount');

        $totalBajas = \App\Models\DispersionBajaValue::whereIn('corte_id', $corteIds)
            ->sum('quantity');
            
        return [
            'total_afiliados' => $totalAfiliados,
            'total_dispersados' => $totalDispersados,
            'monto_total' => $montoTotal,
            'total_bajas' => $totalBajas,
        ];
    }
}
