@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12">
    <form action="{{ route('admin.access.roles.store') }}" method="POST">
        @csrf
        
        <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('admin.access.roles') }}" class="w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center hover:bg-slate-200 transition-all">
                        <i class="ph ph-caret-left text-xl"></i>
                    </a>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">Nuevo Rol de Sistema</h1>
                </div>
                <div class="max-w-xl">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Nombre Único del Rol</label>
                    <input type="text" name="name" required
                           class="w-full px-6 py-4 bg-white border-2 border-slate-100 rounded-2xl text-lg font-black focus:border-amber-500/20 focus:ring-4 focus:ring-amber-500/10 transition-all"
                           placeholder="Ej. Auditor de Procesos">
                </div>
            </div>
            
            <button type="submit" class="px-10 py-5 bg-amber-500 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-amber-600 hover:shadow-2xl hover:shadow-amber-500/40 transition-all active:scale-95 shadow-xl shadow-amber-500/20">
                Guardar Nuevo Rol
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($permissions as $module => $items)
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/30 overflow-hidden flex flex-col h-full">
                <div class="px-8 py-6 bg-slate-50/50 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-widest text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-2 bg-amber-400 rounded-full"></span>
                        Módulo: {{ strtoupper($module) }}
                    </h3>
                    <span class="px-2 py-1 bg-white border border-slate-100 rounded-lg text-[9px] font-black text-slate-400">{{ count($items) }} permisos</span>
                </div>
                
                <div class="p-8 space-y-4 flex-1">
                    @foreach($items as $permission)
                    <label class="flex items-start gap-3 p-4 bg-slate-50/50 rounded-2xl border border-transparent hover:border-amber-200 hover:bg-amber-50/30 transition-all cursor-pointer group">
                        <div class="relative flex items-center mt-0.5">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                   class="w-5 h-5 rounded-lg border-2 border-slate-200 text-amber-500 focus:ring-amber-500/20 transition-all">
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-700 mb-0.5 group-hover:text-amber-600 transition-colors">{{ $permission->name }}</p>
                            <p class="text-[9px] font-medium text-slate-400 leading-tight">Privilegio granular para el entorno {{ $module }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </form>
</div>
@endsection
