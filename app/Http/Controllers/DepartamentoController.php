<?php

namespace App\Http\Controllers;

use App\Models\Departamento;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::withCount('users')->get();
        return view('departamentos.index', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|unique:departamentos,codigo',
        ]);

        Departamento::create($request->all());

        return back()->with('success', 'Departamento creado correctamente.');
    }

    public function update(Request $request, Departamento $departamento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|unique:departamentos,codigo,' . $departamento->id,
        ]);

        $departamento->update($request->all());

        return back()->with('success', 'Departamento actualizado.');
    }

    public function destroy(Departamento $departamento)
    {
        if ($departamento->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un departamento con usuarios asociados.');
        }

        $departamento->delete();
        return back()->with('success', 'Departamento eliminado.');
    }
}
