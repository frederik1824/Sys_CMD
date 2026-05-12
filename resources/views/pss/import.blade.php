@extends('layouts.portal')

@section('content')
<div class="p-8 max-w-5xl mx-auto">
    <!-- Header Section -->
    <div class="flex items-center justify-between mb-10">
        <div>
            <nav class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                <a href="{{ route('pss.dashboard') }}" class="hover:text-primary transition-colors">PSS Dashboard</a>
                <i class="ph-bold ph-caret-right"></i>
                <span class="text-slate-600">Importación</span>
            </nav>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter">Carga Masiva de Prestadores</h1>
        </div>
        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100">
            <i class="ph-bold ph-database text-primary text-xl"></i>
        </div>
    </div>

    <!-- Wizard Component -->
    @livewire('pss.import-wizard')

    <!-- Guidelines -->
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="flex gap-4">
            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="ph-bold ph-check text-lg"></i>
            </div>
            <div>
                <h5 class="text-xs font-black text-slate-800 uppercase tracking-tight">Normalización Automática</h5>
                <p class="text-[11px] text-slate-400 font-medium mt-1">Nombres y teléfonos se corrigen al formato institucional automáticamente.</p>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="ph-bold ph-copy text-lg"></i>
            </div>
            <div>
                <h5 class="text-xs font-black text-slate-800 uppercase tracking-tight">Prevención de Duplicados</h5>
                <p class="text-[11px] text-slate-400 font-medium mt-1">El sistema detecta si el médico ya existe en la misma ciudad antes de crear uno nuevo.</p>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <i class="ph-bold ph-map-pin text-lg"></i>
            </div>
            <div>
                <h5 class="text-xs font-black text-slate-800 uppercase tracking-tight">Ciudades Inteligentes</h5>
                <p class="text-[11px] text-slate-400 font-medium mt-1">Se crean automáticamente las nuevas ciudades encontradas en el archivo.</p>
            </div>
        </div>
    </div>
</div>
@endsection
