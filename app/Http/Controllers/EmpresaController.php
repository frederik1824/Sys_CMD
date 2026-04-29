<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Empresa;
use App\Http\Requests\StoreEmpresaRequest;
use App\Http\Requests\UpdateEmpresaRequest;
use Illuminate\Support\Facades\DB;
class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Empresa::query();

        if (auth()->check() && auth()->user()->responsable_id && !auth()->user()->hasRole(['Admin'])) {
            $assignedProvincias = \Illuminate\Support\Facades\DB::table('provincia_responsable')
                ->where('responsable_id', auth()->user()->responsable_id)
                ->pluck('provincia_id');
            $query->whereIn('provincia_id', $assignedProvincias);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'reales' => (clone $query)->where('es_verificada', true)->count(),
            'filiales' => (clone $query)->where('es_filial', true)->count(),
        ];

        // Distribution data for chart
        $stats['distribution'] = [
            'reales' => $stats['reales'],
            'filiales' => $stats['filiales'],
            'otros' => $stats['total'] - ($stats['reales'] + $stats['filiales'])
        ];

        return view('empresas.index', compact('stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empresa = new Empresa();
        $empresa->estado_contacto = 'Nuevo'; // Default value for form
        $provincias = \App\Models\Provincia::orderBy('nombre')->get();
        $municipios = collect(); // Se cargará por AJAX
        $promotores = \App\Models\User::orderBy('name')->get();
        return view('empresas.create', compact('empresa', 'provincias', 'municipios', 'promotores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmpresaRequest $request)
    {
        $validated = $request->validated();

        $empresa = DB::transaction(function () use ($validated) {
            return Empresa::create($validated);
        });
        
        $empresa->refresh();
        
        if ($empresa->firebase_synced_at) {
            return redirect()->route('empresas.index')->with('cloud_success', 'Empresa creada y sincronizada con la nube exitosamente.');
        }

        return redirect()->route('empresas.index')->with('success', 'Empresa creada localmente (Sincronización pendiente).');
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        $empresa->pullFromFirebase();

        $afiliados = $empresa->afiliados()
            ->with(['estado', 'responsable'])
            ->latest()
            ->paginate(15);

        $statusBreakdown = $empresa->afiliados()
            ->select('estado_id')
            ->selectRaw('count(*) as total')
            ->groupBy('estado_id')
            ->with('estado')
            ->get()
            ->map(function($item) {
                return [
                    'label' => $item->estado->nombre ?? 'Pendiente',
                    'value' => $item->total,
                ];
            });

        $interacciones = $empresa->interacciones()->with('user')->get();
        
        $auditorias = \App\Models\AuditLog::where('model_type', Empresa::class)
            ->where('model_id', $empresa->id)
            ->with('user')
            ->latest('created_at')
            ->get();

        return view('empresas.show', compact('empresa', 'afiliados', 'statusBreakdown', 'interacciones', 'auditorias'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        $empresa->pullFromFirebase();

        $provincias = \App\Models\Provincia::orderBy('nombre')->get();
        $municipios = $empresa->provincia_id ? \App\Models\Municipio::where('provincia_id', $empresa->provincia_id)->orderBy('nombre')->get() : collect();
        $promotores = \App\Models\User::orderBy('name')->get();

        return view('empresas.edit', compact('empresa', 'provincias', 'municipios', 'promotores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmpresaRequest $request, Empresa $empresa)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($empresa, $validated) {
            $empresa->update($validated);
        });

        $empresa->refresh(); // Asegurar que capturamos el firebase_synced_at del observer

        $success = $empresa->firebase_synced_at && $empresa->firebase_synced_at->isAfter(now()->subSeconds(10));

        if ($success) {
            return redirect()->route('empresas.show', $empresa)->with('cloud_success', 'Empresa actualizada y respaldada en la nube.');
        }

        return redirect()->route('empresas.show', $empresa)->with('success', 'Empresa actualizada localmente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        if ($empresa->afiliados()->count() > 0) {
            return redirect()->route('empresas.index')->with('error', 'No se puede eliminar la empresa porque tiene afiliados asociados.');
        }

        $empresa->delete();
        return redirect()->route('empresas.index')->with('success', 'Empresa eliminada exitosamente.');
    }

    public function enrich()
    {
        $incomplete = Empresa::whereNull('provincia_id')->count();
        return view('empresas.enrich', compact('incomplete'));
    }

    public function processEnrich()
    {
        $empresas = Empresa::whereNull('provincia_id')->get();
        $updated = 0;

        foreach($empresas as $empresa) {
            /** @var Empresa $empresa */
            // Buscamos el primer afiliado de esta empresa que SI tenga provincia/municipio
            $afiliado = $empresa->afiliados()
                ->whereNotNull('provincia')
                ->where('provincia', '!=', '')
                ->first();

            if ($afiliado) {
                // Intentar mapear el texto de provincia a un ID
                $provincia = \App\Models\Provincia::where('nombre', 'like', $afiliado->provincia)->first();
                if ($provincia) {
                    $empresa->provincia_id = $provincia->id;
                    
                    // Intentar municipio
                    if ($afiliado->municipio) {
                        $municipio = \App\Models\Municipio::where('provincia_id', $provincia->id)
                            ->where('nombre', 'like', $afiliado->municipio)
                            ->first();
                        if ($municipio) {
                            $empresa->municipio_id = $municipio->id;
                        }
                    }

                    $empresa->save();
                    $updated++;
                }
            }
        }

        return back()->with('success', "Se han enriquecido exitosamente {$updated} empresas basándose en sus afiliados.");
    }

    public function getMunicipios($provincia_id)
    {
        $municipios = \App\Models\Municipio::where('provincia_id', $provincia_id)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
            
        return response()->json($municipios);
    }

    /**
     * Store a new interaction for the company.
     */
    public function storeInteraction(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|in:Llamada,Servicio,Reunión,General',
            'descripcion' => 'required|string',
        ]);

        $empresa->interacciones()->create([
            'user_id' => auth()->id(),
            'tipo' => $validated['tipo'],
            'descripcion' => $validated['descripcion'],
            'fecha_contacto' => now(),
        ]);

        return redirect()->route('empresas.show', $empresa)->with('success', 'Interacción registrada exitosamente.');
    }

    /**
     * Sincroniza la colección de empresas desde Firebase (Triggered from Index)
     */
    public function syncFirebase()
    {
        try {
            \App\Jobs\FirebaseSyncJob::dispatch(['--full' => true]);
            return back()->with('success', 'Sincronización de empresas y catálogos iniciada en segundo plano. Los datos aparecerán gradualmente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al iniciar sincronización: ' . $e->getMessage());
        }
    }
}
