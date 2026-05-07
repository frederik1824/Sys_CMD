@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ph ph-shield-star text-amber-500"></i>
                Diccionario de Roles
            </h1>
            <p class="text-slate-500 mt-1 font-medium italic">Definición de perfiles y capacidades granulares del sistema</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.access.roles.create') }}" class="flex items-center gap-2 px-6 py-2.5 bg-amber-500 text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-amber-600 hover:shadow-lg hover:shadow-amber-500/30 transition-all active:scale-95">
                <i class="ph ph-plus-circle text-lg"></i>
                Nuevo Rol
            </a>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nombre del Rol</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Permisos Asociados</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($roles as $role)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 font-black">
                                    <i class="ph ph-shield-check text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 leading-none mb-1">{{ $role->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Guard: {{ $role->guard_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-1.5 max-w-xl">
                                @forelse($role->permissions->take(10) as $permission)
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[9px] font-black uppercase tracking-tight">{{ $permission->name }}</span>
                                @empty
                                    <span class="text-[10px] font-bold text-slate-300 italic uppercase">Sin permisos asignados</span>
                                @endforelse
                                @if($role->permissions->count() > 10)
                                    <span class="px-2 py-0.5 bg-slate-50 text-slate-400 rounded-md text-[9px] font-black italic">+{{ $role->permissions->count() - 10 }} más</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="#" class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center">
                                    <i class="ph ph-pencil-simple text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
