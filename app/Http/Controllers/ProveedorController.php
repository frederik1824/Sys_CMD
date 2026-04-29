<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::all();
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio_base' => 'required|numeric|min:0',
            'activo' => 'boolean'
        ]);

        Proveedor::create([
            'nombre' => $request->nombre,
            'precio_base' => $request->precio_base,
            'activo' => $request->has('activo')
        ]);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado exitosamente.');
    }

    public function edit(Proveedor $proveedore)
    {
        return view('proveedores.edit', ['proveedor' => $proveedore]);
    }

    public function update(Request $request, Proveedor $proveedore)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio_base' => 'required|numeric|min:0',
            'activo' => 'boolean'
        ]);

        $proveedore->update([
            'nombre' => $request->nombre,
            'precio_base' => $request->precio_base,
            'activo' => $request->has('activo')
        ]);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedore)
    {
        try {
            $proveedore->delete();
            return back()->with('success', 'Proveedor eliminado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar el proveedor. Probablemente tiene afiliados asignados.');
        }
    }
}

