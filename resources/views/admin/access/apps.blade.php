@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ph ph-squares-four text-indigo-600"></i>
                Inventario de Módulos
            </h1>
            <p class="text-slate-500 mt-1 font-medium italic">Gestión del ecosistema de aplicaciones y servicios del ERP</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.access.apps.create') }}" class="flex items-center gap-2 px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:shadow-indigo-500/30 transition-all active:scale-95">
                <i class="ph ph-plus-square text-lg"></i>
                Registrar Aplicación
            </a>
        </div>
    </div>

    <!-- Apps Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($applications as $app)
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden group hover:border-indigo-200 transition-all duration-500">
            <div class="p-8">
                <div class="flex items-start justify-between mb-6">
                    <div class="w-16 h-16 bg-slate-50 rounded-3xl flex items-center justify-center text-3xl group-hover:bg-indigo-50 transition-colors duration-500">
                        <i class="{{ $app->icon ?: 'ph ph-app-window' }} text-slate-400 group-hover:text-indigo-600"></i>
                    </div>
                    <form action="{{ route('admin.access.apps.toggle', $app) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest transition-all {{ $app->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $app->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                            {{ $app->is_active ? 'Activo' : 'Inactivo' }}
                        </button>
                    </form>
                </div>

                <h3 class="text-xl font-black text-slate-900 mb-1 group-hover:text-indigo-600 transition-colors">{{ $app->name }}</h3>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Slug: {{ $app->slug }}</p>
                
                <div class="space-y-2 mb-8">
                    <div class="flex items-center gap-2 text-xs font-bold text-slate-500">
                        <i class="ph ph-link text-lg"></i>
                        {{ $app->url ?: 'URL no configurada' }}
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.access.apps.edit', $app) }}" class="w-10 h-10 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all">
                            <i class="ph ph-pencil-simple text-lg"></i>
                        </a>
                    </div>
                    <div class="text-[10px] font-black text-slate-300 uppercase tracking-tighter">
                        Orden: {{ $app->order_weight }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
