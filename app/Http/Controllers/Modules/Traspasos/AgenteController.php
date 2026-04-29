<?php

namespace App\Http\Controllers\Modules\Traspasos;

use App\Http\Controllers\Controller;
use App\Models\AgenteTraspaso;
use App\Models\SupervisorTraspaso;
use Illuminate\Http\Request;

class AgenteController extends Controller
{
    public function index()
    {
        $supervisores = SupervisorTraspaso::with('agentes')->get();
        $agentes = AgenteTraspaso::with('supervisor')->get();
        
        return view('modules.traspasos.config.agentes', compact('supervisores', 'agentes'));
    }

    public function storeSupervisor(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        SupervisorTraspaso::create($request->only('nombre'));
        return back()->with('success', 'Supervisor creado correctamente');
    }

    public function storeAgente(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'supervisor_id' => 'required|exists:supervisor_traspasos,id'
        ]);
        AgenteTraspaso::create($request->only(['nombre', 'supervisor_id']));
        return back()->with('success', 'Agente creado correctamente');
    }

    public function toggleSupervisor(SupervisorTraspaso $supervisor)
    {
        $supervisor->update(['activo' => !$supervisor->activo]);
        return back();
    }

    public function toggleAgente(AgenteTraspaso $agente)
    {
        $agente->update(['activo' => !$agente->activo]);
        return back();
    }

    public function storeMeta(Request $request)
    {
        $request->validate([
            'agente_id' => 'required|exists:agente_traspasos,id',
            'periodo' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'meta_cantidad' => 'required|integer|min:1'
        ]);

        \App\Models\MetaTraspaso::updateOrCreate(
            ['agente_id' => $request->agente_id, 'periodo' => $request->periodo],
            ['meta_cantidad' => $request->meta_cantidad]
        );

        return back()->with('success', 'Meta asignada correctamente');
    }
}
