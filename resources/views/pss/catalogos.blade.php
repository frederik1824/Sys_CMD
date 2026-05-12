@extends('layouts.portal')

@section('content')
<div class="p-8 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                <a href="{{ route('pss.dashboard') }}" class="hover:text-primary transition-colors">PSS Dashboard</a>
                <i class="ph-bold ph-caret-right"></i>
                <span class="text-slate-600">Gestión de Catálogos</span>
            </nav>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter">Configuración de Red PSS</h1>
            <p class="text-slate-500 font-medium text-sm">Administra las ciudades, especialidades, grupos y clínicas del sistema.</p>
        </div>
    </div>

    <!-- Livewire Component -->
    @livewire('pss.catalog-manager')
</div>
@endsection
