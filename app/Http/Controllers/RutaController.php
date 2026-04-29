<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    public function index()
    {
        $rutas = Ruta::paginate(10);
        return view('rutas.index', compact('rutas'));
    }

    public function create()
    {
        return view('rutas.form', ['ruta' => new Ruta()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'zona' => 'required|string|max:100',
            'es_frecuente' => 'boolean'
        ]);

        Ruta::create($data);

        return redirect()->route('rutas.index')->with('success', 'Ruta creada exitosamente.');
    }

    public function edit(Ruta $ruta)
    {
        return view('rutas.form', compact('ruta'));
    }

    public function update(Request $request, Ruta $ruta)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'zona' => 'required|string|max:100',
            'es_frecuente' => 'boolean'
        ]);

        $ruta->update($data);

        return redirect()->route('rutas.index')->with('success', 'Ruta actualizada.');
    }

    public function destroy(Ruta $ruta)
    {
        $ruta->delete();
        return redirect()->route('rutas.index')->with('success', 'Ruta eliminada.');
    }
}
