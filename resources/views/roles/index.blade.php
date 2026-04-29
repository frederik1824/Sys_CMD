@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Usuarios & Roles</h2>
            <p class="text-slate-500 text-sm mt-1">Configura los permisos granularmente para cada nivel de acceso.</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-slate-100">
        <a href="{{ route('usuarios.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 border-b-2 border-transparent transition-all">
            Usuarios
        </a>
        <a href="{{ route('roles.index') }}" class="px-6 py-3 text-sm font-bold border-b-2 border-primary text-primary transition-all">
            Roles & Permisos
        </a>
        <a href="{{ route('admin.audit.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-all border-b-2 border-transparent">
            Auditoría
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em] border-r border-slate-100 bg-white sticky left-0 z-10">Módulo / Permiso</th>
                        @foreach($roles as $role)
                        <th class="px-6 py-4 text-center border-r border-slate-100 last:border-0 min-w-[150px]">
                            <div class="flex flex-col items-center gap-1">
                                <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">{{ $role->name }}</span>
                                <a href="{{ route('roles.edit', $role->id) }}" class="text-[0.6rem] font-bold text-primary hover:underline">Editar Rol</a>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        // Agrupar permisos por prefijo (ej: manage_afiliados -> Afiliados)
                        $groupedPermissions = $permissions->groupBy(function($perm) {
                            $parts = explode('_', $perm->name);
                            return count($parts) > 1 ? ucwords($parts[1]) : 'Sistema';
                        });
                    @endphp

                    @foreach($groupedPermissions as $group => $groupPerms)
                        <tr class="bg-slate-50/30">
                            <td colspan="{{ $roles->count() + 1 }}" class="px-6 py-2 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                                Sección: {{ $group }}
                            </td>
                        </tr>
                        @foreach($groupPerms as $permission)
                        <tr class="hover:bg-slate-50/30 transition-colors group">
                            <td class="px-6 py-4 border-r border-slate-100 bg-white sticky left-0 z-10">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-700 leading-none">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                                    <span class="text-[0.65rem] text-slate-400 font-medium mt-1">{{ $permission->name }}</span>
                                </div>
                            </td>
                            @foreach($roles as $role)
                            <td class="px-6 py-4 text-center border-r border-slate-100 last:border-0">
                                @php $hasPermission = $role->hasPermissionTo($permission->name); @endphp
                                @if($role->name === 'Admin')
                                    <span class="material-symbols-outlined text-emerald-500 text-lg opacity-40">check_circle</span>
                                @else
                                    <div class="flex justify-center">
                                        @if($hasPermission)
                                            <span class="w-2.5 h-2.5 rounded-full bg-primary shadow-lg shadow-primary/40" title="Activo"></span>
                                        @else
                                            <span class="w-2.5 h-2.5 rounded-full bg-slate-200" title="Inactivo"></span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
