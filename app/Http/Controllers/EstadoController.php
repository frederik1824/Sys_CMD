<?php

namespace App\Http\Controllers;

use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::orderBy('id', 'asc')->paginate(10);
        return view('estados.index', compact('estados'));
    }

    public function create()
    {
        return view('estados.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Estado::create($validated);
        return redirect()->route('estados.index')->with('success', 'Estado creado exitosamente.');
    }

    public function edit(Estado $estado)
    {
        return view('estados.form', compact('estado'));
    }

    public function update(Request $request, Estado $estado)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $estado->update($validated);
        return redirect()->route('estados.index')->with('success', 'Estado actualizado exitosamente.');
    }

    public function destroy(Estado $estado)
    {
        $estado->delete();
        return redirect()->route('estados.index')->with('success', 'Estado eliminado.');
    }
}
