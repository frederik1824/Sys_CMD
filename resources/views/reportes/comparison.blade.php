@extends('layouts.app')

@section('content')
<div class="space-y-8 p-4 md:p-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <span class="material-symbols-outlined text-4xl text-primary">analytics</span>
                Torre de Control Corporativa
            </h1>
            <p class="text-slate-500 font-medium mt-1">Comparativa de Rendimiento: ARS CMD vs SAFESURE</p>
        </div>
        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-xs font-black text-slate-600 tracking-widest uppercase">Datos en Tiempo Real</span>
        </div>
    </div>

    <!-- Comparison Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @foreach($comparisonData as $name => $data)
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden group hover:border-primary/20 transition-all duration-500">
            <!-- Card Header -->
            <div class="{{ $name === 'ARS CMD' ? 'bg-indigo-600' : 'bg-slate-800' }} p-8 text-white relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <span class="text-[0.6rem] font-black uppercase tracking-[0.3em] opacity-70">Responsable</span>
                        <h2 class="text-2xl font-black tracking-tight">{{ $name }}</h2>
                    </div>
                    <div class="bg-white/10 p-4 rounded-2xl backdrop-blur-md">
                        <span class="material-symbols-outlined text-3xl">
                            {{ $name === 'ARS CMD' ? 'medical_services' : 'security' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-8 space-y-8">
                <!-- Main KPI -->
                <div class="flex items-end justify-between">
                    <div class="space-y-1">
                        <p class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($data['total']) }}</p>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Afiliados Totales</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-black {{ $data['porcentaje'] > 70 ? 'text-emerald-500' : 'text-amber-500' }} tracking-tighter">
                            {{ $data['porcentaje'] }}%
                        </p>
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Tasa de Entrega</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="space-y-2">
                    <div class="flex justify-between text-[0.65rem] font-black uppercase tracking-widest text-slate-500">
                        <span>Progreso de Gestión</span>
                        <span>{{ number_format($data['completados']) }} / {{ number_format($data['total']) }}</span>
                    </div>
                    <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $name === 'ARS CMD' ? 'bg-indigo-500' : 'bg-slate-700' }} rounded-full transition-all duration-1000 shadow-lg" 
                             style="width: {{ $data['porcentaje'] }}%"></div>
                    </div>
                </div>

                <!-- Detailed Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-slate-50 p-4 rounded-3xl border border-slate-100 text-center">
                        <p class="text-xl font-black text-rose-500">{{ $data['criticos'] }}</p>
                        <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-tighter">SLA Crítico</p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-3xl border border-slate-100 text-center">
                        <p class="text-xl font-black text-amber-500">{{ $data['alertas'] }}</p>
                        <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-tighter">SLA Alerta</p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-3xl border border-slate-100 text-center">
                        <p class="text-sm font-black text-slate-700 mt-1">RD${{ number_format($data['por_liquidar'] / 1000, 1) }}k</p>
                        <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-tighter">Por Liquidar</p>
                    </div>
                </div>

                <!-- Control Button -->
                <a href="{{ route('reportes.supervision', ['responsable_id' => $data['id']]) }}" 
                   class="w-full py-4 bg-slate-50 hover:bg-white hover:shadow-lg border border-slate-100 rounded-2xl flex items-center justify-center gap-3 text-sm font-black text-slate-600 transition-all hover:-translate-y-1">
                    <span class="material-symbols-outlined text-xl">visibility</span>
                    Ver Auditoría Detallada
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Global Insights -->
    <div class="bg-white p-8 rounded-[2.5rem] shadow-xl border border-slate-100">
        <h3 class="text-sm font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Balance de Operación</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-400">Total Universo</p>
                <p class="text-2xl font-black text-slate-800">{{ number_format(collect($comparisonData)->sum('total')) }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-400">Liquidación Pendiente</p>
                <p class="text-2xl font-black text-slate-800">RD${{ number_format(collect($comparisonData)->sum('por_liquidar')) }}</p>
            </div>
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-400">Eficiencia Global</p>
                @php
                    $total_u = collect($comparisonData)->sum('total');
                    $comp_u = collect($comparisonData)->sum('completados');
                    $ef_global = $total_u > 0 ? round(($comp_u / $total_u) * 100, 1) : 0;
                @endphp
                <p class="text-2xl font-black {{ $ef_global > 75 ? 'text-emerald-500' : 'text-amber-500' }}">{{ $ef_global }}%</p>
            </div>
            <div class="space-y-1">
                <p class="text-xs font-bold text-slate-400">Riesgo SLA</p>
                <p class="text-2xl font-black text-rose-500">{{ collect($comparisonData)->sum('criticos') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
