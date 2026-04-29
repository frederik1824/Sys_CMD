<?php

namespace App\Http\Controllers\Modules\Afiliacion;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Departamento;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $moduleCodes = ['AFIL', 'SC'];
    protected $moduleName = 'Gestión de Personal (Global)';
    protected $routePrefix = 'solicitudes-afiliacion.usuarios';

    public function index()
    {
        $users = User::with(['roles', 'departamento'])->orderBy('name')->get();

        return view('modules.afiliacion.users.index', [
            'users' => $users,
            'moduleName' => $this->moduleName,
            'routePrefix' => $this->routePrefix
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('modules.afiliacion.users.create', [
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
            'departamento_id' => $request->departamento_id ?? Departamento::where('codigo', 'AFIL')->first()->id,
        ]);

        $user->assignRole($request->role_name);

        return redirect()->route($this->routePrefix . '.index')->with('success', "Personal de {$this->moduleName} registrado.");
    }

    public function edit(User $usuario)
    {
        $roles = Role::all();
        $departamentos = Departamento::orderBy('nombre')->get();
        return view('modules.afiliacion.users.edit', [
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
            'departamento_id' => 'required|exists:departamentos,id',
        ]);

        $data = $request->only(['name', 'email', 'departamento_id']);
        
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
