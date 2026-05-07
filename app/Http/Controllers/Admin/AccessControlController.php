<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use App\Models\UserApplicationRole;
use App\Models\Departamento;
use App\Models\Responsable;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessControlController extends Controller
{
    /**
     * Lista maestra de usuarios y sus accesos
     */
    public function index(Request $request)
    {
        $usersQuery = User::with(['roles', 'applicationAccess.application', 'applicationAccess.role']);

        if ($request->filled('search')) {
            $usersQuery->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $usersQuery->paginate(10);
        $applications = Application::where('is_active', true)->get();
        $roles = Role::all();

        return view('admin.access.index', compact('users', 'applications', 'roles'));
    }

    /**
     * Gestión Centralizada de Usuarios
     */
    public function users(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->paginate(15);
        $departamentos = Departamento::all();
        $responsables = Responsable::all();
        return view('admin.access.users', compact('users', 'departamentos', 'responsables'));
    }

    public function createUser()
    {
        $departamentos = Departamento::all();
        $responsables = Responsable::all();
        return view('admin.access.users-create', compact('departamentos', 'responsables'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'departamento_id' => $request->departamento_id,
            'responsable_id' => $request->responsable_id,
        ]);

        return redirect()->route('admin.access.users')->with('success', 'Usuario creado exitosamente.');
    }

    public function editUser(User $user)
    {
        $departamentos = Departamento::all();
        $responsables = Responsable::all();
        return view('admin.access.users-edit', compact('user', 'departamentos', 'responsables'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($request->only(['name', 'email', 'departamento_id', 'responsable_id']));

        return redirect()->route('admin.access.users')->with('success', 'Usuario actualizado.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();
        return redirect()->route('admin.access.users')->with('success', 'Usuario eliminado.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return back()->with('success', 'Contraseña reseteada exitosamente.');
    }

    /**
     * Gestión de Aplicaciones (Módulos)
     */
    public function applications()
    {
        $applications = Application::orderBy('order_weight')->get();
        return view('admin.access.apps', compact('applications'));
    }

    public function createApplication()
    {
        return view('admin.access.apps-create');
    }

    public function storeApplication(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:applications',
            'icon' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
        ]);

        Application::create($request->all());

        return redirect()->route('admin.access.apps')->with('success', 'Aplicación registrada.');
    }

    public function editApplication(Application $application)
    {
        return view('admin.access.apps-edit', compact('application'));
    }

    public function updateApplication(Request $request, Application $application)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:applications,slug,' . $application->id,
        ]);

        $application->update($request->all());

        return redirect()->route('admin.access.apps')->with('success', 'Aplicación actualizada.');
    }

    public function toggleApplication(Application $application)
    {
        $application->update(['is_active' => !$application->is_active]);
        return back()->with('success', 'Estado de la aplicación actualizado.');
    }

    /**
     * Gestión de Roles (Spatie)
     */
    public function roles()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.access.roles', compact('roles'));
    }

    public function createRole()
    {
        $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function($p) {
            return explode('.', $p->name)[0]; // Agrupar por prefijo de módulo
        });
        return view('admin.access.roles-create', compact('permissions'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles',
            'permissions' => 'nullable|array'
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.access.roles')->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Gestión de Permisos
     */
    public function permissions()
    {
        $permissions = \Spatie\Permission\Models\Permission::all()->groupBy(function($p) {
            return explode('.', $p->name)[0];
        });
        return view('admin.access.permissions', compact('permissions'));
    }

    /**
     * Asignar o actualizar acceso a una aplicación
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'application_id' => 'required|exists:applications,id',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        try {
            DB::transaction(function() use ($request) {
        UserApplicationRole::updateOrCreate(
                    [
                        'user_id' => $request->user_id, 
                        'application_id' => $request->application_id
                    ],
                    [
                        'role_id' => $request->role_id,
                        'is_active' => true,
                        'assigned_by' => auth()->id(),
                        'assigned_at' => now()
                    ]
                );

                // Auditoría de Seguridad
                DB::table('access_audit_logs')->insert([
                    'performer_id' => auth()->id(),
                    'target_user_id' => $request->user_id,
                    'action' => 'grant_access',
                    'application_key' => Application::find($request->application_id)->key,
                    'new_values' => json_encode(['role_id' => $request->role_id]),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now()
                ]);
            });

            return back()->with('success', 'Acceso y rol configurados correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar el acceso: ' . $e->getMessage());
        }
    }

    /**
     * Alternar estado de acceso (Activo/Inactivo)
     */
    public function toggleAccess($id)
    {
        $access = UserApplicationRole::findOrFail($id);
        $newStatus = !$access->is_active;
        
        $access->update(['is_active' => $newStatus]);

        // Auditoría
        DB::table('access_audit_logs')->insert([
            'performer_id' => auth()->id(),
            'target_user_id' => $access->user_id,
            'action' => $newStatus ? 'enable_access' : 'disable_access',
            'application_key' => $access->application->key,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);

        return back()->with('success', 'Estado de acceso actualizado.');
    }

    /**
     * Revocar acceso totalmente
     */
    public function revokeAccess($id)
    {
        $access = UserApplicationRole::findOrFail($id);
        
        DB::table('access_audit_logs')->insert([
            'performer_id' => auth()->id(),
            'target_user_id' => $access->user_id,
            'action' => 'revoke_access',
            'application_key' => $access->application->key,
            'old_values' => json_encode($access->toArray()),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);

        $access->delete();

        return back()->with('success', 'Acceso revocado definitivamente.');
    }

    /**
     * Entrar como este usuario (Impersonación)
     */
    public function impersonate(User $user)
    {
        $adminId = auth()->id();
        $fromRoute = route('admin.access.index');

        // Auditoría
        DB::table('access_audit_logs')->insert([
            'performer_id' => $adminId,
            'target_user_id' => $user->id,
            'action' => 'impersonate_start',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now()
        ]);

        // Iniciar sesión como el usuario
        auth()->login($user);

        // Guardar rastro del admin en la NUEVA sesión
        session()->put('impersonate_original_id', $adminId);
        session()->put('impersonate_from_route', $fromRoute);
        session()->save();

        return redirect()->route('portal')->with('success', "Navegando como: {$user->name}");
    }

    /**
     * Detener Impersonación y volver al Admin
     */
    public function stopImpersonating()
    {
        $originalId = session()->get('impersonate_original_id');
        $fromRoute = session()->get('impersonate_from_route', route('admin.access.index'));

        if ($originalId) {
            $originalUser = User::find($originalId);
            if ($originalUser) {
                auth()->login($originalUser);
                
                // Limpiar rastros una vez vuelto al admin
                session()->forget(['impersonate_original_id', 'impersonate_from_route']);
                session()->save();

                return redirect($fromRoute)->with('success', 'Has vuelto a tu sesión administrativa.');
            }
        }

        auth()->logout();
        return redirect()->route('login');
    }
}
