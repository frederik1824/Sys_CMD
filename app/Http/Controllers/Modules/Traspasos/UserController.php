<?php

namespace App\Http\Controllers\Modules\Traspasos;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Departamento;
use App\Models\Responsable;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $moduleCode = 'TRAS';
    protected $moduleName = 'Traspasos';
    protected $routePrefix = 'traspasos.usuarios';

    public function index()
    {
        $users = User::whereHas('departamento', function($q) {
            $q->where('codigo', $this->moduleCode);
        })->with(['roles', 'departamento'])->get();

        return view('modules.traspasos.users.index', [
            'users' => $users,
            'moduleName' => $this->moduleName,
            'routePrefix' => $this->routePrefix
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('modules.traspasos.users.create', [
            'roles' => $roles,
            'departamentos' => $departamentos,
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
            'departamento_id' => 'required|exists:departamentos,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'departamento_id' => $request->departamento_id ?? Departamento::where('codigo', $this->moduleCode)->first()->id,
        ]);

        $user->assignRole($request->role_name);

        return redirect()->route($this->routePrefix . '.index')->with('success', "Colaborador de {$this->moduleName} creado con éxito.");
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('modules.traspasos.users.edit', [
            'usuario' => $usuario,
            'roles' => $roles,
            'departamentos' => $departamentos,
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

        $data = $request->only(['name', 'email']);
        
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
