@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700" x-data="{ activeCorte: 1 }">
    {{-- Period Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden relative">
        <div class="absolute right-0 top-0 opacity-[0.03]">
            <i class="ph ph-calendar-blank text-[12rem] -mr-10 -mt-10"></i>
        </div>
        
        <div class="flex items-center gap-6 relative z-10">
            <a href="{{ route('dispersion.index') }}" class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 transition-all">
                <i class="ph ph-arrow-left text-2xl"></i>
            </a>
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <h2 class="text-3xl font-black tracking-tight text-slate-900">{{ $period->month_name }} {{ $period->year }}</h2>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                        {{ $period->status === 'closed' ? 'Histórico' : 'Periodo Abierto' }}
                    </span>
                </div>
                <div class="flex items-center gap-4 text-slate-400 font-bold text-[10px] uppercase tracking-widest">
                    <span class="flex items-center gap-1"><i class="ph ph-user"></i> Creado por: {{ $period->createdBy->name }}</span>
                    <span class="h-1 w-1 bg-slate-200 rounded-full"></span>
                    <span class="flex items-center gap-1"><i class="ph ph-clock"></i> {{ $period->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 relative z-10">
            <a href="{{ route('dispersion.reports.show', $period->id) }}" 
               class="flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-widest hover:border-emerald-500 hover:text-emerald-600 hover:scale-105 transition-all shadow-sm">
                <i class="ph ph-eye text-lg text-slate-900"></i>
                Ver Vista Previa Reporte
            </a>
            <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-2xl">
            <button @click="activeCorte = 1" 
                    :class="activeCorte === 1 ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                    class="px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all">
                1er Corte
            </button>
            <button @click="activeCorte = 2" 
                    :class="activeCorte === 2 ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                    class="px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all">
                2do Corte
            </button>
        </div>
    </div>
    </div>

    {{-- Capture Area --}}
    <div x-show="activeCorte === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        @php $corte1 = $period->cortes->where('corte_number', 1)->first(); @endphp
        @if($corte1)
            @livewire('modules.admin.dispersion.corte-capture', ['corteId' => $corte1->id, 'corteNumber' => 1], key('corte-1-'.$corte1->id))
        @endif
    </div>

    <div x-show="activeCorte === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
        @php $corte2 = $period->cortes->where('corte_number', 2)->first(); @endphp
        @if($corte2)
            @livewire('modules.admin.dispersion.corte-capture', ['corteId' => $corte2->id, 'corteNumber' => 2], key('corte-2-'.$corte2->id))
        @else
            <div class="bg-white p-20 rounded-[2.5rem] border border-slate-200 text-center">
                <p class="text-slate-400 font-black text-xs uppercase tracking-widest">Corte no inicializado</p>
            </div>
        @endif
    </div>
</div>
@endsection
