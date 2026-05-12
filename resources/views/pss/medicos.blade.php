@extends('layouts.portal')

@section('content')
<div class="p-8 space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                <a href="{{ route('pss.dashboard') }}" class="hover:text-primary transition-colors">PSS Dashboard</a>
                <i class="ph-bold ph-caret-right"></i>
                <span class="text-slate-600">Médicos y Especialistas</span>
            </nav>
            <h1 class="text-3xl font-black text-slate-900 tracking-tighter">Directorio de Médicos</h1>
            <p class="text-slate-500 font-medium text-sm">Administra la base de datos de profesionales de la salud.</p>
        </div>
    </div>

    <!-- Livewire Table Component -->
    @livewire('pss.record-table', ['type' => 'medicos'])
</div>
@endsection
