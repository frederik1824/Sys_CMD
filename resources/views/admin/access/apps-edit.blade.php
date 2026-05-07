@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Editar Aplicación</h1>
            <p class="text-slate-500 font-medium">Modifica los parámetros de {{ $application->name }}</p>
        </div>
        <a href="{{ route('admin.access.apps') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
            <i class="ph ph-x-circle text-4xl"></i>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <form action="{{ route('admin.access.apps.update', $application) }}" method="POST" class="p-10 md:p-16">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Nombre del Módulo</label>
                    <div class="relative">
                        <i class="ph ph-app-window absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="text" name="name" value="{{ old('name', $application->name) }}" required
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/20 transition-all"
                               placeholder="Ej. Control de Inventario">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Identificador (Slug)</label>
                    <div class="relative">
                        <i class="ph ph-fingerprint absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="text" name="slug" value="{{ old('slug', $application->slug) }}" required
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/20 transition-all"
                               placeholder="ej-modulo-inventario">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Icono (Phosphor Icon)</label>
                    <div class="relative">
                        <i class="ph ph-image absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="text" name="icon" value="{{ old('icon', $application->icon) }}"
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/20 transition-all"
                               placeholder="ph ph-package">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">URL de Acceso</label>
                    <div class="relative">
                        <i class="ph ph-link absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="text" name="url" value="{{ old('url', $application->url) }}"
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/20 transition-all"
                               placeholder="/inventario">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Peso en Orden (Sorting)</label>
                    <div class="relative">
                        <i class="ph ph-sort-ascending absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="number" name="order_weight" value="{{ old('order_weight', $application->order_weight) }}"
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500/20 transition-all"
                               placeholder="0">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.access.apps') }}" class="px-8 py-4 text-sm font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                    Descartar
                </a>
                <button type="submit" class="px-10 py-4 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-indigo-700 hover:shadow-xl hover:shadow-indigo-500/40 transition-all active:scale-95">
                    Actualizar Módulo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
