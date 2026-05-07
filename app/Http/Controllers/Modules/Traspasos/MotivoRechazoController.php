<?php

namespace App\Http\Controllers\Modules\Traspasos;

use App\Http\Controllers\Controller;
use App\Models\MotivoRechazoTraspaso;
use Illuminate\Http\Request;

class MotivoRechazoController extends Controller
{
    public function index()
    {
        $motivos = MotivoRechazoTraspaso::orderBy('codigo_sisalril', 'asc')->get();
        return view('modules.traspasos.motivos.index', compact('motivos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'descripcion' => 'required',
            'codigo_sisalril' => 'nullable',
            'codigo_unsigima' => 'nullable',
        ]);

        MotivoRechazoTraspaso::create($data);

        return redirect()->back()->with('success', 'Motivo creado correctamente');
    }

    public function update(Request $request, MotivoRechazoTraspaso $motivo)
    {
        $data = $request->validate([
            'descripcion' => 'required',
            'codigo_sisalril' => 'nullable',
            'codigo_unsigima' => 'nullable',
        ]);

        $motivo->update($data);

        return redirect()->back()->with('success', 'Motivo actualizado correctamente');
    }

    public function toggle(MotivoRechazoTraspaso $motivo)
    {
        $motivo->update(['activo' => !$motivo->activo]);
        return redirect()->back()->with('success', 'Estado actualizado correctamente');
    }

    public function destroy(MotivoRechazoTraspaso $motivo)
    {
        $motivo->delete();
        return redirect()->back()->with('success', 'Motivo eliminado correctamente');
    }
}
