<?php

namespace App\Http\Controllers\Modules\CMD;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Departamento;
use App\Models\Responsable;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $excludedCodes = ['TRAS', 'AFIL', 'SC'];
    protected $moduleName = 'Carnetización (ID System)';
    protected $routePrefix = 'usuarios'; // Mantener el nombre original o cambiarlo a uno específico

    public function index()
    {
        $users = User::whereHas('departamento', function($q) {
            $q->whereNotIn('codigo', $this->excludedCodes);
        })->orWhereDoesntHave('departamento')
        ->with(['roles', 'departamento', 'responsable'])->get();

        return view('modules.cmd.users.index', [
            'users' => $users,
            'moduleName' => $this->moduleName,
            'routePrefix' => $this->routePrefix
        ]);
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Admin', 'Asistente de Logística', 'Auditor', 'Gestor de Llamadas', 'Operador', 'Supervisor de Llamadas'])->get();
        $departamentos = Departamento::whereNotIn('codigo', $this->excludedCodes)->get();
        $responsables = Responsable::all();
        return view('modules.cmd.users.create', [
            'roles' => $roles,
            'departamentos' => $departamentos,
            'responsables' => $responsables,
            'moduleName' => $this->moduleName,
            'routePrefix' => $this->routePrefix
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_name' => 'required|exists:roles,name',
            'departamento_id' => 'nullable|exists:departamentos,id',
            'responsable_id' => 'nullable|exists:responsables,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'departamento_id' => $request->departamento_id,
            'responsable_id' => $request->responsable_id,
        ]);

        $user->assignRole($request->role_name);

        return redirect()->route($this->routePrefix . '.index')->with('success', 'Usuario de ID System creado.');
    }

    public function edit(User $usuario)
    {
        $roles = Role::whereIn('name', ['Admin', 'Asistente de Logística', 'Auditor', 'Gestor de Llamadas', 'Operador', 'Supervisor de Llamadas'])->get();
        $departamentos = Departamento::whereNotIn('codigo', $this->excludedCodes)->get();
        $responsables = Responsable::all();
        return view('modules.cmd.users.edit', [
            'usuario' => $usuario,
            'roles' => $roles,
            'departamentos' => $departamentos,
            'responsables' => $responsables,
            'moduleName' => $this->moduleName,
            'routePrefix' => $this->routePrefix
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$usuario->id,
            'role_name' => 'required|exists:roles,name',
        ]);

        $data = $request->only(['name', 'email', 'departamento_id', 'responsable_id']);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'confirmed|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);
        $usuario->syncRoles([$request->role_name]);

        return redirect()->route($this->routePrefix . '.index')->with('success', 'Usuario actualizado.');
    }

    public function impersonate(User $user)
    {
        session()->put('impersonate_original_id', auth()->id());
        session()->put('impersonate_from_route', route($this->routePrefix . '.index'));
        auth()->login($user);

        return redirect()->route('portal')->with('success', "Navegando como: {$user->name}");
    }
}
