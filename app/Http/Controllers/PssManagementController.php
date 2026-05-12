<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PssMedico;
use App\Models\PssCentro;
use App\Models\PssCiudad;
use App\Models\PssEspecialidad;
use App\Models\PssGrupo;
use App\Models\PssImportacion;
use Illuminate\Support\Facades\DB;

class PssManagementController extends Controller
{
    public function index()
    {
        $stats = [
            'total_medicos' => PssMedico::count(),
            'total_centros' => PssCentro::count(),
            'total_ciudades' => PssCiudad::count(),
            'total_especialidades' => PssEspecialidad::count(),
            'total_grupos' => PssGrupo::count(),
            'registros_sin_telefono' => PssMedico::whereNull('telefono_1')->count() + PssCentro::whereNull('telefono_1')->count(),
            'ultimas_importaciones' => PssImportacion::with('user')->latest()->limit(5)->get(),
        ];

        // Médicos por Especialidad (Top 5)
        $medicosPorEspecialidad = PssMedico::select('pss_especialidades.nombre', DB::raw('count(*) as total'))
            ->join('pss_especialidades', 'pss_medicos.especialidad_id', '=', 'pss_especialidades.id')
            ->groupBy('pss_especialidades.nombre')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        return view('pss.dashboard', compact('stats', 'medicosPorEspecialidad'));
    }

    public function import()
    {
        return view('pss.import');
    }

    public function medicos()
    {
        return view('pss.medicos');
    }

    public function centros()
    {
        return view('pss.centros');
    }

    public function catalogos()
    {
        return view('pss.catalogos');
    }
}
