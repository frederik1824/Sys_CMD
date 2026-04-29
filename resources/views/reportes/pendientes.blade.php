@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-fade-in pb-20">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <span class="text-primary/60">Monitoreo Directivo</span>
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span class="text-primary text-bold">Inventario Pendiente</span>
            </nav>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none">Inventario de Servicios Pendientes</h2>
            <p class="text-slate-500 text-sm font-medium">Control de casos en sistema no finalizados, agrupados por corte mensual.</p>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('reportes.pendientes.export') }}" target="_blank" 
               class="bg-white text-slate-700 px-6 py-3 rounded-2xl border border-slate-200 shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2 group">
                <span class="material-symbols-outlined text-[20px] text-primary group-hover:scale-110 transition-transform">picture_as_pdf</span>
                <span class="text-[0.65rem] font-black uppercase tracking-widest">Reporte Ejecutivo</span>
            </a>
            
            <div class="bg-amber-500/10 px-6 py-3 rounded-2xl border border-amber-500/20 shadow-sm">
                <p class="text-[0.6rem] font-black text-amber-600 uppercase tracking-widest leading-none mb-1">Total Pendiente Global</p>
                <p class="text-2xl font-black text-amber-700">{{ number_format($reporteCortes->sum('pendientes_count')) }}</p>
            </div>
        </div>
    </div>

    {{-- Grid de Cortes --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($reporteCortes as $c)
        <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden group hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-500 relative">
            {{-- Status Indicator --}}
            @php
                $porcentaje = $c->total > 0 ? ($c->entregados_count / $c->total) * 100 : 0;
                $isCritical = $c->pendientes_count > 0 && $loop->index > 2; // Cortes viejos con pendientes
            @endphp
            
            <div class="p-8 space-y-6">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <h4 class="text-xl font-black text-slate-800 tracking-tight group-hover:text-primary transition-colors">{{ $c->nombre }}</h4>
                        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">calendar_month</span>
                            Creado el {{ $c->created_at->format('d M, Y') }}
                        </p>
                    </div>
                    @if($isCritical)
                    <span class="bg-rose-500 text-white text-[0.6rem] font-black px-3 py-1 rounded-full animate-pulse">REZAGO CRÍTICO</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50/50 p-4 rounded-3xl border border-slate-100/50">
                    <div>
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase leading-none mb-1">Ingresados</p>
                        <p class="text-lg font-black text-slate-700">{{ number_format($c->total) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase leading-none mb-1">Pendientes</p>
                        <p class="text-lg font-black text-rose-500">{{ number_format($c->pendientes_count) }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <p class="text-[0.6rem] font-black text-slate-500 uppercase tracking-widest">Progreso de Entrega</p>
                        <p class="text-sm font-black text-slate-800">{{ round($porcentaje) }}%</p>
                    </div>
                    <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden p-0.5 border border-slate-200/50">
                        <div class="h-full rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(34,197,94,0.3)]
                            {{ $porcentaje == 100 ? 'bg-emerald-500' : ($porcentaje > 50 ? 'bg-primary' : 'bg-amber-500') }}" 
                            style="width: {{ $porcentaje }}%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 border-t border-slate-100 p-4 px-8 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-[0.65rem] font-bold text-slate-500 uppercase">{{ number_format($c->entregados_count) }} Finalizados</span>
                </div>
                <a href="{{ route('afiliados.index', ['corte_id' => $c->id]) }}" class="text-[0.65rem] font-black text-primary uppercase tracking-widest hover:underline flex items-center gap-1">
                    Ver Detalles <span class="material-symbols-outlined text-[12px]">arrow_forward</span>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Empty State --}}
    @if($reporteCortes->isEmpty())
    <div class="flex flex-col items-center justify-center p-20 bg-white rounded-[3rem] border border-dashed border-slate-300">
        <span class="material-symbols-outlined text-6xl text-slate-200 mb-4">inventory_2</span>
        <p class="text-lg font-black text-slate-400 uppercase tracking-widest">No hay cortes registrados</p>
    </div>
    @endif
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }
    .no-scrollbar::-webkit-scrollbar { display: none; }
</style>
@endsection
