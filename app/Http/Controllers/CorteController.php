<?php

namespace App\Http\Controllers;

use App\Models\Corte;
use Illuminate\Http\Request;

class CorteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cortes = Corte::withCount([
            'afiliados',
            'afiliados as entregados_count' => function($q) {
                $q->whereNotNull('fecha_entrega_proveedor');
            },
            'afiliados as completados_count' => function($q) {
                $q->whereHas('estado', function($e) { $e->where('nombre', 'COMPLETADO'); });
            },
            'afiliados as liquidados_count' => function($q) {
                $q->where('liquidado', true);
            }
        ])->orderBy('id', 'desc')->paginate(10);

        // Calcular montos manualmente o vía selectRaw if needed
        foreach($cortes as $corte) {
            $corte->monto_ars = \App\Models\Afiliado::where('corte_id', $corte->id)
                ->ars()
                ->whereHas('estado', function($e) { $e->where('nombre', 'Completado'); })
                ->where('liquidado', false)
                ->sum('costo_entrega');

            $corte->monto_no_ars = \App\Models\Afiliado::where('corte_id', $corte->id)
                ->noArs()
                ->whereHas('estado', function($e) { $e->where('nombre', 'Completado'); })
                ->where('liquidado', false)
                ->sum('costo_entrega');
                
            $corte->monto_pendiente = $corte->monto_ars + $corte->monto_no_ars;
        }

        return view('cortes.index', compact('cortes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cortes.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);
        $validated['activo'] = $request->has('activo');

        Corte::create($validated);
        return redirect()->route('sistema.cortes.index')->with('success', 'Corte creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $corte = Corte::findOrFail($id);
        return view('cortes.show', compact('corte'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $corte = Corte::findOrFail($id);
        return view('cortes.form', compact('corte'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $corte = Corte::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);
        $validated['activo'] = $request->has('activo');

        $corte->update($validated);
        return redirect()->route('sistema.cortes.index')->with('success', 'Corte actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $corte = Corte::findOrFail($id);
        $corte->delete();
        return redirect()->route('sistema.cortes.index')->with('success', 'Corte eliminado exitosamente.');
    }
}
