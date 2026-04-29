<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Despacho;
use App\Models\DespachoItem;
use App\Models\Mensajero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogisticaDashboardController extends Controller
{
    public function index()
    {
        // 1. Métricas de Estado de Flota y Carga
        $totalPendientes = Afiliado::where('estado_id', 1)->count();
        $totalEnRuta = Afiliado::where('estado_id', 3)->count();
        $despachosActivos = Despacho::where('status', 'en_transito')->count();
        $mensajerosActivos = Mensajero::where('activo', true)->count();

        // 2. Rendimiento Hoy
        $hoy = now()->startOfDay();
        $entregasHoy = DespachoItem::where('status', 'entregado')->where('fecha_evento', '>=', $hoy)->count();
        $fallosHoy = DespachoItem::where('status', 'fallido')->where('fecha_evento', '>=', $hoy)->count();

        // 3. Distribución Geográfica (Top 5 Provincias Pendientes)
        $distribucionProvincias = Afiliado::where('estado_id', 1)
            ->select('provincia', DB::raw('count(*) as total'))
            ->whereNotNull('provincia')
            ->groupBy('provincia')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        // 4. Últimos Despachos Iniciados
        $ultimosDespachos = Despacho::with(['mensajero', 'ruta'])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 5. Productividad Mensajeros (Total de entregas exitosas históricas)
        $productividadMensajeros = Mensajero::withCount(['despachos' => function($q) {
            $q->whereHas('items', function($qi) {
                $qi->where('status', 'entregado');
            });
        }])->orderBy('despachos_count', 'desc')->take(5)->get();

        return view('logistica.dashboard', compact(
            'totalPendientes',
            'totalEnRuta',
            'despachosActivos',
            'mensajerosActivos',
            'entregasHoy',
            'fallosHoy',
            'distribucionProvincias',
            'ultimosDespachos',
            'productividadMensajeros'
        ));
    }
}
