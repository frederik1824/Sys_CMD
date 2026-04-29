<?php

namespace App\Http\Controllers;

use App\Models\Mensajero;
use Illuminate\Http\Request;

class MensajeroController extends Controller
{
    public function index()
    {
        $mensajeros = Mensajero::paginate(10);
        return view('mensajeros.index', compact('mensajeros'));
    }

    public function create()
    {
        return view('mensajeros.form', ['mensajero' => new Mensajero()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|unique:mensajeros,cedula',
            'telefono' => 'nullable|string|max:20',
            'vehiculo_placa' => 'nullable|string|max:20',
            'vehiculo_tipo' => 'required|in:Motor,Carro,Camioneta',
            'color' => 'nullable|string|max:7',
            'activo' => 'boolean'
        ]);

        Mensajero::create($data);

        return redirect()->route('mensajeros.index')->with('success', 'Mensajero registrado exitosamente.');
    }

    public function edit(Mensajero $mensajero)
    {
        return view('mensajeros.form', compact('mensajero'));
    }

    public function update(Request $request, Mensajero $mensajero)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|unique:mensajeros,cedula,' . $mensajero->id,
            'telefono' => 'nullable|string|max:20',
            'vehiculo_placa' => 'nullable|string|max:20',
            'vehiculo_tipo' => 'required|in:Motor,Carro,Camioneta',
            'color' => 'nullable|string|max:7',
            'activo' => 'boolean'
        ]);

        $mensajero->update($data);

        return redirect()->route('mensajeros.index')->with('success', 'Datos del mensajero actualizados.');
    }

    public function destroy(Mensajero $mensajero)
    {
        $mensajero->delete();
        return redirect()->route('mensajeros.index')->with('success', 'Mensajero eliminado.');
    }
}
