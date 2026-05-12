@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header Premium -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <i class="ph-fill ph-shield-star text-white text-2xl"></i>
                </div>
                Matriz de Accesos
            </h1>
            <p class="text-slate-500 mt-2 font-medium">Define y gestiona las capacidades operativas de cada perfil en el ecosistema.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="hidden sm:block px-6 py-3 bg-white border border-slate-100 rounded-2xl shadow-sm">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Total Perfiles</span>
                <span class="text-xl font-black text-slate-900">{{ count($roles) }} Roles</span>
            </div>
            <a href="{{ route('admin.access.roles.create') }}" 
               class="flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-amber-500 hover:shadow-2xl hover:shadow-amber-500/40 transition-all active:scale-95 shadow-xl shadow-slate-900/10">
                <i class="ph-bold ph-plus text-lg"></i>
                Nuevo Perfil
            </a>
        </div>
    </div>

    <!-- Grid de Roles -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($roles as $role)
        <div class="group bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500 flex flex-col overflow-hidden relative">
            
            <!-- Acciones Flotantes -->
            <div class="absolute top-6 right-6 flex gap-2">
                <form action="{{ route('admin.access.roles.duplicate', $role->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" title="Clonar como Plantilla" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                        <i class="ph-bold ph-copy text-lg"></i>
                    </button>
                </form>
                <a href="{{ route('admin.access.roles.edit', $role->id) }}" title="Editar Perfil" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                    <i class="ph-bold ph-pencil-simple text-lg"></i>
                </a>
            </div>

            <!-- Header de Tarjeta -->
            <div class="p-8 pb-4">
                <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 mb-6 group-hover:bg-amber-50 group-hover:text-amber-600 transition-all duration-500">
                    <i class="ph-fill ph-identification-badge text-3xl"></i>
                </div>
                
                <h3 class="text-xl font-black text-slate-900 leading-tight mb-2 group-hover:text-amber-600 transition-colors">{{ $role->name }}</h3>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[9px] font-black uppercase tracking-widest">{{ $role->guard_name }}</span>
                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                    <span class="text-[10px] font-bold text-slate-400 italic">Protección Nivel 2</span>
                </div>
            </div>

            <!-- Resumen de Permisos -->
            <div class="px-8 py-4 flex-1">
                <div class="flex flex-wrap gap-1.5">
                    @forelse($role->permissions->take(6) as $permission)
                        <span class="px-2.5 py-1 bg-slate-50 text-slate-400 border border-slate-100 rounded-lg text-[9px] font-bold group-hover:border-amber-100 group-hover:bg-amber-50/50 group-hover:text-amber-700 transition-all">
                            {{ $permission->name }}
                        </span>
                    @empty
                        <div class="w-full p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-center">
                            <p class="text-[10px] font-bold text-slate-300 uppercase italic">Sin permisos asignados</p>
                        </div>
                    @endforelse
                    
                    @if($role->permissions->count() > 6)
                        <span class="px-2.5 py-1 bg-slate-900 text-white rounded-lg text-[9px] font-black">
                            +{{ $role->permissions->count() - 6 }} MÁS
                        </span>
                    @endif
                </div>
            </div>

            <!-- Footer de Tarjeta -->
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50 mt-auto flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex -space-x-2">
                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-200"></div>
                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-300"></div>
                    </div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Active Users</span>
                </div>
                
                <a href="{{ route('admin.access.roles.edit', $role->id) }}" class="flex items-center gap-1.5 text-amber-600 hover:text-amber-700 transition-colors">
                    <span class="text-[10px] font-black uppercase tracking-widest">Ver Matriz</span>
                    <i class="ph-bold ph-arrow-right"></i>
                </a>
            </div>
        </div>
        @endforeach

        <!-- Card para Nuevo Rol -->
        <a href="{{ route('admin.access.roles.create') }}" 
           class="group border-4 border-dashed border-slate-100 rounded-[2.5rem] flex flex-col items-center justify-center p-12 hover:border-amber-200 hover:bg-amber-50/30 transition-all duration-500">
            <div class="w-16 h-16 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-300 mb-6 group-hover:bg-amber-500 group-hover:text-white group-hover:rotate-90 transition-all duration-500">
                <i class="ph-bold ph-plus text-3xl"></i>
            </div>
            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.3em] group-hover:text-amber-600 transition-colors">Crear Nuevo Perfil</p>
        </a>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }
</style>
@endsection
