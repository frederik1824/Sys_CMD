<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\Request;

class ResponsableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $responsables = Responsable::with(['user', 'provincias'])->orderBy('id', 'desc')->paginate(10);
        return view('responsables.index', compact('responsables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        $provincias = \App\Models\Provincia::orderBy('nombre')->get();
        return view('responsables.form', compact('users', 'provincias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_entrega' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
            'provincias' => 'nullable|array',
            'provincias.*' => 'exists:provincias,id',
        ]);
        $validated['activo'] = $request->has('activo');

        $responsable = Responsable::create(\Illuminate\Support\Arr::except($validated, ['provincias']));
        
        if ($request->has('provincias')) {
            $responsable->provincias()->sync($request->provincias);
        }

        if (!empty($validated['user_id'])) {
            \App\Models\User::where('id', $validated['user_id'])->update(['responsable_id' => $responsable->id]);
            $user = \App\Models\User::find($validated['user_id']);
            if ($user) {
                $user->notify(new \App\Notifications\ResponsabilidadAsignada($responsable, count($request->provincias ?? [])));
            }
        }

        return redirect()->route('responsables.index')->with('success', 'Responsable creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $responsable = Responsable::findOrFail($id);
        return view('responsables.show', compact('responsable'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $responsable = Responsable::with('provincias')->findOrFail($id);
        $users = \App\Models\User::orderBy('name')->get();
        $provincias = \App\Models\Provincia::orderBy('nombre')->get();
        return view('responsables.form', compact('responsable', 'users', 'provincias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $responsable = Responsable::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio_entrega' => 'nullable|numeric|min:0',
            'user_id' => 'nullable|exists:users,id',
            'provincias' => 'nullable|array',
            'provincias.*' => 'exists:provincias,id',
        ]);
        $validated['activo'] = $request->has('activo');

        $responsable->update(\Illuminate\Support\Arr::except($validated, ['provincias']));
        
        if ($request->has('provincias')) {
            $responsable->provincias()->sync($request->provincias);
        } else {
            $responsable->provincias()->detach();
        }

        // Si se ligó a un usuario, limpiar el responsable anterior de su tabla y setear en el actual
        if (!empty($validated['user_id'])) {
            $previousUser = \App\Models\User::where('responsable_id', $responsable->id)->first();
            if ($previousUser && $previousUser->id != $validated['user_id']) {
                $previousUser->update(['responsable_id' => null]);
            }
            
            \App\Models\User::where('id', $validated['user_id'])->update(['responsable_id' => $responsable->id]);
            
            // Si hubo cambio de provincias o nuevo usuario atado, notificar al actual
            $user = \App\Models\User::find($validated['user_id']);
            if ($user && ($request->has('provincias') || ($previousUser && $previousUser->id != $validated['user_id']))) {
                $user->notify(new \App\Notifications\ResponsabilidadAsignada($responsable, count($request->provincias ?? [])));
            }
        } else {
            // Si lo desvinculó de un usuario, limpiarlo del usuario que lo tenía asociado.
            \App\Models\User::where('responsable_id', $responsable->id)->update(['responsable_id' => null]);
        }

        return redirect()->route('responsables.index')->with('success', 'Responsable actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $responsable = Responsable::findOrFail($id);
        $responsable->delete();
        return redirect()->route('responsables.index')->with('success', 'Responsable eliminado exitosamente.');
    }
}
