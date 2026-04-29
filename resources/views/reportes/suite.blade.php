@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-fade-in pb-20">
    {{-- Header Suite --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <span class="text-primary/60">Business Intelligence</span>
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span class="text-primary text-bold">Suite Ejecutiva v3</span>
            </nav>
            <h2 class="text-5xl font-black text-slate-900 tracking-tight leading-none">Intelligence Hub</h2>
            <p class="text-slate-500 text-sm font-medium">Panel unificado de métricas operativas transversales.</p>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-white/50 backdrop-blur-md px-6 py-4 rounded-3xl border border-slate-200/60 shadow-sm flex items-center gap-4">
                <div class="w-10 h-10 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-600">monitoring</span>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Última Actualización</p>
                    <p class="text-xs font-bold text-slate-700">{{ now()->format('d M, Y - h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Bento Grid --}}
    <div class="grid grid-cols-12 gap-6">
        
        {{-- 1. Unified Trend Chart (Centro de Inteligencia) --}}
        <div class="col-span-12 lg:col-span-8 bg-white p-8 rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 relative overflow-hidden group min-h-[500px]">
            <div class="absolute top-0 right-0 p-8">
                <div class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-full border border-slate-100">
                    <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase">Live Intelligence</span>
                </div>
            </div>
            
            <div class="mb-10">
                <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Tendencia Consolidada</h3>
                <p class="text-xs text-slate-400 font-medium">Rendimiento comparativo de los últimos 6 meses</p>
            </div>

            <div class="h-80 w-full">
                <canvas id="mainTrendChart"></canvas>
            </div>

            <div class="mt-8 flex flex-wrap gap-8">
                @if($canSeeCmd)
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Carnetización</p>
                        <p class="text-sm font-black text-slate-800">{{ number_format($cmdData['total'] ?? 0) }}</p>
                    </div>
                </div>
                @endif
                @if($canSeeAfiliacion)
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-indigo-500"></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Afiliación</p>
                        <p class="text-sm font-black text-slate-800">{{ number_format($afiliacionData['total'] ?? 0) }}</p>
                    </div>
                </div>
                @endif
                @if($canSeeTraspasos)
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Traspasos</p>
                        <p class="text-sm font-black text-slate-800">{{ number_format($traspasosData['total'] ?? 0) }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- 2. Module Vertical Cards --}}
        <div class="col-span-12 lg:col-span-4 space-y-6">
            
            {{-- CMD Card --}}
            @if($canSeeCmd)
            <div class="bg-gradient-to-br from-blue-600 to-blue-800 p-8 rounded-[3rem] text-white shadow-xl shadow-blue-900/20 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <span class="p-3 bg-white/20 backdrop-blur-md rounded-2xl">
                            <i class="ph-fill ph-shield-check text-2xl"></i>
                        </span>
                        <a href="{{ route('reportes.supervision') }}" class="text-[10px] font-black uppercase tracking-widest text-blue-200 hover:text-white transition-colors">Detalles</a>
                    </div>
                    <h4 class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-blue-200 mb-1">Operación CMD</h4>
                    <div class="text-4xl font-black tracking-tighter mb-4">{{ number_format($cmdData['total']) }} <span class="text-lg font-bold text-blue-300/60">Servicios</span></div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-[10px] font-bold text-blue-100 uppercase">Eficiencia de Entrega</span>
                            <span class="text-xs font-black text-white">{{ $cmdData['total'] > 0 ? round(($cmdData['completados'] / $cmdData['total']) * 100, 1) : 0 }}%</span>
                        </div>
                        <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden border border-white/5">
                            <div class="bg-white h-full rounded-full" style="width: {{ $cmdData['total'] > 0 ? ($cmdData['completados'] / $cmdData['total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Afiliacion Card --}}
            @if($canSeeAfiliacion)
            <div class="bg-white p-8 rounded-[3rem] shadow-xl shadow-slate-200/40 border border-slate-100 relative overflow-hidden group border-l-4 border-l-indigo-500">
                <div class="flex items-center justify-between mb-6">
                    <span class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                        <i class="ph-fill ph-user-plus text-2xl"></i>
                    </span>
                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-widest bg-indigo-50 px-3 py-1 rounded-full">+{{ $afiliacionData['hoy'] }} HOY</span>
                </div>
                <h4 class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Solicitudes Afiliación</h4>
                <div class="text-4xl font-black text-slate-900 tracking-tighter mb-6">{{ number_format($afiliacionData['total']) }}</div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-emerald-50 rounded-2xl">
                        <p class="text-[9px] font-black text-emerald-700 uppercase mb-1">Aprobadas</p>
                        <p class="text-xl font-black text-emerald-900">{{ number_format($afiliacionData['aprobadas']) }}</p>
                    </div>
                    <div class="p-4 bg-amber-50 rounded-2xl">
                        <p class="text-[9px] font-black text-amber-700 uppercase mb-1">Pendientes</p>
                        <p class="text-xl font-black text-amber-900">{{ number_format($afiliacionData['pendientes']) }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Call Center Card --}}
            <div class="bg-indigo-900 p-8 rounded-[3rem] text-white shadow-xl shadow-indigo-900/20 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-tr from-indigo-500/10 to-transparent"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <span class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10">
                            <i class="ph-fill ph-headset text-2xl text-indigo-300"></i>
                        </span>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-indigo-300 uppercase tracking-widest leading-none">Efectividad</p>
                            <p class="text-sm font-black">{{ $callCenterData['porcentaje'] }}%</p>
                        </div>
                    </div>
                    <h4 class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-indigo-300 mb-1">Productividad Call Center</h4>
                    <div class="text-4xl font-black tracking-tighter mb-4">{{ number_format($callCenterData['total']) }} <span class="text-lg font-bold text-indigo-400/60">Llamadas</span></div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-white/5 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-indigo-300 uppercase mb-1">Cerradas</p>
                            <p class="text-lg font-black">{{ number_format($callCenterData['efectivas']) }}</p>
                        </div>
                        <div class="p-3 bg-white/5 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-indigo-300 uppercase mb-1">Pendientes</p>
                            <p class="text-lg font-black text-amber-400">{{ number_format($callCenterData['pendientes']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Traspasos Card --}}
            @if($canSeeTraspasos)
            <div class="bg-slate-900 p-8 rounded-[3rem] shadow-2xl relative overflow-hidden group text-white">
                <div class="absolute inset-0 bg-gradient-to-tr from-amber-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <span class="p-3 bg-amber-500/10 text-amber-500 rounded-2xl border border-amber-500/20">
                            <i class="ph-fill ph-swap text-2xl"></i>
                        </span>
                        <div class="text-right">
                            <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest leading-none">Meta Mes</p>
                            <p class="text-sm font-black">{{ number_format($traspasosData['meta']) }}</p>
                        </div>
                    </div>
                    <h4 class="text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-500 mb-1">Traspasos Efectivos</h4>
                    <div class="text-4xl font-black text-white tracking-tighter mb-4">{{ number_format($traspasosData['efectivos']) }}</div>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-[9px] font-bold text-slate-500 uppercase">Progreso Meta</span>
                                <span class="text-xs font-black text-amber-500">{{ $traspasosData['meta'] > 0 ? round(($traspasosData['mes'] / $traspasosData['meta']) * 100, 1) : 0 }}%</span>
                            </div>
                            <div class="w-full h-1 bg-slate-800 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full transition-all duration-1000" style="width: {{ $traspasosData['meta'] > 0 ? ($traspasosData['mes'] / $traspasosData['meta']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('mainTrendChart').getContext('2d');
        
        // Gradients
        const blueGrad = ctx.createLinearGradient(0, 0, 0, 400);
        blueGrad.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        blueGrad.addColorStop(1, 'rgba(59, 130, 246, 0)');

        const indigoGrad = ctx.createLinearGradient(0, 0, 0, 400);
        indigoGrad.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        indigoGrad.addColorStop(1, 'rgba(99, 102, 241, 0)');

        const amberGrad = ctx.createLinearGradient(0, 0, 0, 400);
        amberGrad.addColorStop(0, 'rgba(245, 158, 11, 0.4)');
        amberGrad.addColorStop(1, 'rgba(245, 158, 11, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($tendencia['labels']) !!},
                datasets: [
                    @if($canSeeCmd)
                    {
                        label: 'Carnetización',
                        data: {!! json_encode($tendencia['cmd']) !!},
                        borderColor: '#3b82f6',
                        backgroundColor: blueGrad,
                        fill: true,
                        tension: 0.45,
                        borderWidth: 4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#3b82f6',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    },
                    @endif
                    @if($canSeeAfiliacion)
                    {
                        label: 'Afiliación',
                        data: {!! json_encode($tendencia['afil']) !!},
                        borderColor: '#6366f1',
                        backgroundColor: indigoGrad,
                        fill: true,
                        tension: 0.45,
                        borderWidth: 4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#6366f1',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    },
                    @endif
                    @if($canSeeTraspasos)
                    {
                        label: 'Traspasos',
                        data: {!! json_encode($tendencia['tra']) !!},
                        borderColor: '#f59e0b',
                        backgroundColor: amberGrad,
                        fill: true,
                        tension: 0.45,
                        borderWidth: 4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#f59e0b',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }
                    @endif
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleFont: { size: 10, weight: 'bold', family: 'Inter' },
                        bodyFont: { size: 12, family: 'Inter' },
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: true
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                    },
                    y: {
                        border: { display: false, dash: [4, 4] },
                        grid: { color: '#f1f5f9' },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                    }
                }
            }
        });
    });
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
