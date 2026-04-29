@extends('layouts.app')

@section('content')
@php
    $color = str_contains($routePrefix, 'traspasos') ? 'amber' : (str_contains($routePrefix, 'afiliacion') ? 'indigo' : 'blue');
    $icon = str_contains($routePrefix, 'traspasos') ? 'ph-swap' : (str_contains($routePrefix, 'afiliacion') ? 'ph-user-plus' : 'ph-shield-check');
@endphp

<div class="p-8 max-w-[1400px] mx-auto">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-{{ $color }}-600 rounded-[24px] flex items-center justify-center text-white shadow-2xl shadow-{{ $color }}-500/20">
                <i class="ph-fill {{ $icon }} text-3xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black tracking-tighter text-slate-900">Personal: {{ $moduleName }}</h1>
                <p class="text-slate-500 font-medium mt-1">Gestiona los accesos y roles específicos para esta unidad operativa.</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.create') }}" 
           class="bg-slate-900 text-white rounded-2xl px-8 py-4 text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 flex items-center gap-3 group">
            <i class="ph-bold ph-user-plus text-lg group-hover:scale-110 transition-transform"></i> Nuevo Miembro
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="py-6 px-8 text-[10px] font-black uppercase text-slate-400 tracking-widest">Colaborador</th>
                    <th class="py-6 px-8 text-[10px] font-black uppercase text-slate-400 tracking-widest">Rol & Permisos</th>
                    <th class="py-6 px-8 text-[10px] font-black uppercase text-slate-400 tracking-widest">Departamento</th>
                    <th class="py-6 px-8 text-[10px] font-black uppercase text-slate-400 tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50/30 transition-all group">
                    <td class="py-6 px-8">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-{{ $color }}-50 flex items-center justify-center text-{{ $color }}-600 font-black text-lg border border-{{ $color }}-100 group-hover:bg-{{ $color }}-600 group-hover:text-white transition-all">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900">{{ $user->name }}</span>
                                <span class="text-[11px] text-slate-400 font-bold">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-6 px-8">
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->getRoleNames() as $role)
                            <span class="px-3 py-1 bg-{{ $color }}-50 text-{{ $color }}-700 rounded-lg text-[9px] font-black uppercase tracking-widest border border-{{ $color }}-100/50">
                                {{ $role }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-6 px-8">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-{{ $color }}-500"></div>
                            <span class="text-xs font-bold text-slate-600">{{ $user->departamento->nombre ?? 'Sin Asignar' }}</span>
                        </div>
                    </td>
                    <td class="py-6 px-8 text-right">
                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all translate-x-4 group-hover:translate-x-0">
                            @if(auth()->id() !== $user->id && !session()->has('impersonate_original_id'))
                            <form action="{{ route($routePrefix . '.impersonate', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-600 hover:text-white transition-all" title="Entrar como este usuario">
                                    <i class="ph-bold ph-detective text-lg"></i>
                                </button>
                            </form>
                            @endif
                            
                            <a href="{{ route($routePrefix . '.edit', $user) }}" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all">
                                <i class="ph-bold ph-pencil-simple text-lg"></i>
                            </a>

                            @if(auth()->id() !== $user->id)
                            <form action="{{ route($routePrefix . '.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Eliminar acceso?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center hover:bg-rose-600 hover:text-white transition-all">
                                    <i class="ph-bold ph-trash text-lg"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
