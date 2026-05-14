@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500" x-data="{ 
    showModal: false, 
    selectedUser: null, 
    selectedUserName: '',
    openAssign(id, name) {
        this.selectedUser = id;
        this.selectedUserName = name;
        this.showModal = true;
    }
}">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Control de Accesos Maestro</h1>
            <p class="text-slate-500 mt-1 font-medium">Gestiona la matriz de permisos y roles de la suite empresarial.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative group">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-600 transition-colors"></i>
                <form action="{{ route('admin.access.index') }}" method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="pl-12 pr-6 py-3 bg-white border border-slate-200 rounded-2xl w-80 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all font-bold text-slate-700" 
                           placeholder="Buscar usuario por nombre o email...">
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Total Usuarios</p>
            <p class="text-3xl font-black text-slate-900">{{ $users->total() }}</p>
        </div>
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black uppercase tracking-widest text-blue-500 mb-2">Aplicaciones Activas</p>
            <p class="text-3xl font-black text-slate-900">{{ $applications->count() }}</p>
        </div>
        <div class="bg-indigo-600 p-6 rounded-[32px] shadow-xl shadow-indigo-500/20">
            <p class="text-[10px] font-black uppercase tracking-widest text-indigo-100 mb-2">Accesos Configurados</p>
            <p class="text-3xl font-black text-white">{{ \App\Models\UserApplicationRole::count() }}</p>
        </div>
        <div class="bg-slate-900 p-6 rounded-[32px] shadow-xl">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Estado del Sistema</p>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                <p class="text-xl font-black text-white">Sincronizado</p>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Usuario</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Matriz de Aplicaciones</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($users as $user)
                    <tr class="hover:bg-slate-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <img src="{{ $user->avatar_url }}" class="w-12 h-12 rounded-2xl object-cover border-2 border-white shadow-md">
                                <div>
                                    <p class="font-black text-slate-900 leading-none mb-1">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-500 font-medium">{{ $user->email }}</p>
                                    <div class="flex gap-1 mt-2">
                                        @foreach($user->roles as $role)
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 bg-slate-200 text-slate-600 rounded-md">{{ $role->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-3">
                                @forelse($user->applicationAccess as $access)
                                    @if($access->application)
                                    <div class="flex items-center gap-2 p-2 pr-4 {{ $access->is_active ? 'bg-white border-slate-200 shadow-sm' : 'bg-slate-100 opacity-50' }} border rounded-2xl group/app relative">
                                        <div class="w-8 h-8 rounded-xl bg-{{ $access->application->color }}-50 flex items-center justify-center">
                                            <i class="{{ $access->application->icon }} text-[18px] text-{{ $access->application->color }}-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-slate-900 leading-none">{{ $access->application->name }}</p>
                                            <p class="text-[9px] font-bold text-{{ $access->application->color }}-600 uppercase mt-0.5">{{ $access->role?->name ?? 'Sin Rol' }}</p>
                                        </div>
                                        
                                        <!-- Quick Controls for Access -->
                                        <div class="absolute -top-2 -right-2 flex gap-1 opacity-0 group-hover/app:opacity-100 transition-opacity">
                                            <form action="{{ route('admin.access.toggle', $access->id) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" title="{{ $access->is_active ? 'Desactivar' : 'Activar' }}" class="w-6 h-6 rounded-full {{ $access->is_active ? 'bg-amber-500' : 'bg-emerald-500' }} text-white flex items-center justify-center shadow-lg">
                                                    <i class="ph-bold ph-power text-[10px]"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.access.revoke', $access->id) }}" method="POST" onsubmit="return confirm('¿Revocar acceso total?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Revocar Todo" class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center shadow-lg">
                                                    <i class="ph-bold ph-trash text-[10px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <span class="text-xs text-slate-400 italic">Sin aplicaciones asignadas</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right flex items-center justify-end gap-2">
                            <form action="{{ route('admin.access.impersonate', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" title="Entrar como este usuario" 
                                        class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center group/imp">
                                    <i class="ph ph-mask-happy text-xl group-hover/imp:scale-110 transition-transform"></i>
                                </button>
                            </form>

                            <button @click="openAssign({{ $user->id }}, '{{ $user->name }}')" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all active:scale-95">
                                <i class="ph ph-plus-circle text-lg"></i>
                                Gestionar Accesos
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Assignment Modal (Alpine.js) -->
    <div x-show="showModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-cloak>
        
        <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl overflow-hidden"
             @click.outside="showModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-12 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600 mb-1">Configuración de Suite</p>
                    <h2 class="text-2xl font-black text-slate-900" x-text="'Accesos: ' + selectedUserName"></h2>
                </div>
                <button @click="showModal = false" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-rose-500 transition-colors shadow-sm">
                    <i class="ph ph-x text-xl"></i>
                </button>
            </div>

            <form action="{{ route('admin.access.store') }}" method="POST" class="p-8 space-y-6" x-data="{ selectedApp: null }">
                @csrf
                <input type="hidden" name="user_id" :value="selectedUser">

                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-500 ml-1">Seleccionar Aplicación</label>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($applications as $app)
                        <label class="relative flex flex-col p-4 bg-slate-50 border-2 border-transparent rounded-3xl cursor-pointer hover:bg-slate-100 transition-all has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50 group">
                            <input type="radio" name="application_id" value="{{ $app->id }}" @change="selectedApp = $el.value" class="sr-only" required>
                            <div class="w-10 h-10 rounded-2xl bg-white shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                <i class="{{ $app->icon }} text-2xl text-{{ $app->color }}-600"></i>
                            </div>
                            <p class="text-[11px] font-black text-slate-900 leading-tight">{{ $app->name }}</p>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-2" x-show="selectedApp">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-500 ml-1">Rol en esta Aplicación</label>
                    <div class="relative">
                        <select name="role_id" class="w-full px-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold text-slate-700 appearance-none">
                            <option value="">Acceso Estándar (Sin Rol Crítico)</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                        x-show="selectedApp == '{{ $role->app_id }}' || '{{ $role->app_id }}' == '0'">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="ph ph-caret-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-slate-500 ml-1">Expiración (Opcional)</label>
                    <div class="relative">
                        <i class="ph ph-calendar-blank absolute left-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" name="expires_at" 
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 font-bold text-slate-700">
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium ml-1">Deja vacío para acceso permanente.</p>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showModal = false" class="flex-1 px-8 py-4 bg-slate-100 text-slate-600 font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-slate-200 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-2 px-12 py-4 bg-slate-900 text-white font-black uppercase tracking-widest text-xs rounded-2xl hover:bg-black transition-all shadow-xl shadow-slate-900/20 active:scale-95">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Soporte para colores dinámicos de Tailwind que pueden no estar compilados */
    .bg-blue-50 { background-color: #eff6ff; }
    .text-blue-600 { color: #2563eb; }
    .bg-indigo-50 { background-color: #eef2ff; }
    .text-indigo-600 { color: #4f46e5; }
    .bg-emerald-50 { background-color: #ecfdf5; }
    .text-emerald-600 { color: #059669; }
    .bg-amber-50 { background-color: #fffbeb; }
    .text-amber-600 { color: #d97706; }
    .bg-rose-50 { background-color: #fff1f2; }
    .text-rose-600 { color: #e11d48; }
    .bg-violet-50 { background-color: #f5f3ff; }
    .text-violet-600 { color: #7c3aed; }
    .bg-cyan-50 { background-color: #ecfeff; }
    .text-cyan-600 { color: #0891b2; }
    .bg-slate-50 { background-color: #f8fafc; }
    .text-slate-600 { color: #475569; }
</style>
@endsection
