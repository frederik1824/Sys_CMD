<?php

namespace App\Http\Controllers\Modules\Pyp;

use App\Http\Controllers\Controller;
use App\Models\Afiliado;
use App\Models\PypExpediente;
use App\Models\PypPrograma;
use Illuminate\Http\Request;

class AfiliadoController extends Controller
{
    /**
     * Display the search and index of clinical affiliates.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $afiliados = Afiliado::query()
            ->with(['pypExpediente.programas'])
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('nombre_completo', 'like', "%{$search}%")
                      ->orWhere('cedula', 'like', "%{$search}%")
                      ->orWhere('poliza', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(12);

        $programasCount = PypPrograma::count();
        
        return view('modules.pyp.afiliados.index', compact('afiliados', 'programasCount'));
    }

    /**
     * Display the individual clinical record (Ficha Clínica).
     */
    public function show($uuid)
    {
        $afiliado = Afiliado::where('uuid', $uuid)
            ->with(['pypExpediente.seguimientos.user', 'pypExpediente.evaluaciones.medico'])
            ->firstOrFail();

        // Si no tiene expediente PyP, lo creamos bajo demanda (Lazy Initialization)
        if (!$afiliado->pypExpediente) {
            $afiliado->pypExpediente()->create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'riesgo_nivel' => 'Sin Evaluar',
                'estado_clinico' => 'Pendiente'
            ]);
            $afiliado->load('pypExpediente');
        }

        return view('modules.pyp.afiliados.show', compact('afiliado'));
    }

    public function create()
    {
        return view('modules.pyp.afiliados.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'cedula' => 'required|string|unique:afiliados,cedula',
            'nombre_completo' => 'required|string',
            'sexo' => 'required|string',
            'telefono' => 'nullable|string',
            'direccion' => 'nullable|string',
        ]);

        \DB::transaction(function() use ($request, &$afiliado) {
            $afiliado = Afiliado::create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'nombre_completo' => $request->nombre_completo,
                'cedula' => $request->cedula,
                'sexo' => $request->sexo,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'estado_id' => 1, // Por defecto activo/pendiente
            ]);

            // Crear expediente PyP inmediatamente
            $afiliado->pypExpediente()->create([
                'uuid' => (string) \Illuminate\Support\Str::uuid(),
                'riesgo_nivel' => 'Sin Evaluar',
                'estado_clinico' => 'Pendiente'
            ]);
        });

        return redirect()->route('pyp.afiliados.show', $afiliado->uuid)
                         ->with('success', 'Afiliado matriculado exitosamente en el Programa PyP.');
    }
}
