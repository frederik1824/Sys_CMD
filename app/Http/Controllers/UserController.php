<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::with(['roles', 'responsable', 'departamento'])->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $responsables = \App\Models\Responsable::all();
        $departamentos = \App\Models\Departamento::where('activo', true)->get();
        return view('users.create', compact('roles', 'responsables', 'departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_name' => 'required|exists:roles,name',
            'responsable_id' => 'nullable|exists:responsables,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'responsable_id' => $request->responsable_id,
            'departamento_id' => $request->departamento_id,
        ]);

        $user->assignRole($request->role_name);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(\App\Models\User $usuario)
    {
        $roles = \Spatie\Permission\Models\Role::all();
        $responsables = \App\Models\Responsable::all();
        $departamentos = \App\Models\Departamento::where('activo', true)->get();
        return view('users.edit', compact('usuario', 'roles', 'responsables', 'departamentos'));
    }

    public function update(Request $request, \App\Models\User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$usuario->id,
            'role_name' => 'required|exists:roles,name',
            'responsable_id' => 'nullable|exists:responsables,id',
            'departamento_id' => 'nullable|exists:departamentos,id',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'responsable_id' => $request->responsable_id,
            'departamento_id' => $request->departamento_id,
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed|min:8']);
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $usuario->update($data);
        $usuario->syncRoles([$request->role_name]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(\App\Models\User $usuario)
    {
        if (auth()->id() === $usuario->id) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }
        $usuario->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }

    /**
     * Entrar al sistema como otro usuario (Impersonación)
     */
    public function impersonate(\App\Models\User $user)
    {
        if (!auth()->user()->hasRole(['Admin'])) {
            abort(403, 'No tienes permiso para impersonar usuarios.');
        }

        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes impersonarte a ti mismo.');
        }

        session()->put('impersonate_original_id', auth()->id());
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', "Ahora estás navegando como: {$user->name}");
    }

    /**
     * Volver al usuario original
     */
    public function stopImpersonating()
    {
        if (!session()->has('impersonate_original_id')) {
            return redirect()->route('dashboard');
        }

        $fromRoute = session()->pull('impersonate_from_route', route('usuarios.index'));
        $originalId = session()->pull('impersonate_original_id');
        $user = \App\Models\User::find($originalId);

        if ($user) {
            auth()->login($user);
            return redirect($fromRoute)->with('success', 'Has vuelto a tu sesión original.');
        }

        return redirect()->route('login');
    }
}
