@extends('layouts.app')

@section('content')
<!-- Ambient Light Background -->
<div class="fixed inset-0 bg-[#f8fafc] z-0 pointer-events-none">
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-100/50 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/3"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-indigo-50/50 rounded-full blur-[120px] translate-y-1/2 -translate-x-1/3"></div>
</div>

<div class="relative z-10 p-4 lg:p-8 space-y-8">
    <!-- TOP HUD: Crystal Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white/70 backdrop-blur-2xl p-8 rounded-3xl border border-white shadow-xl shadow-slate-200/50">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="w-2 h-8 bg-primary rounded-full"></div>
                <h2 class="text-4xl font-extrabold font-headline text-slate-900 tracking-tighter">COMANDO <span class="text-primary font-light italic">STRATEGO</span></h2>
            </div>
            <p class="text-slate-500 font-mono text-[10px] uppercase tracking-[0.4em] pl-5">Control Central Operativo • ARS CMD</p>
        </div>

    </div>

    <!-- MAIN GRID: Premium KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-6">
        <!-- KPI: Total Affiliates -->
        <div class="bg-white/60 backdrop-blur-md p-6 rounded-3xl border border-white shadow-sm hover:shadow-md transition-all group overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono text-slate-400 font-bold uppercase tracking-widest">Base de Datos</span>
                <span class="material-symbols-outlined text-primary/40 group-hover:text-primary transition-colors">database</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black font-headline text-slate-900">{{ number_format($totalAfiliados) }}</span>
                <span class="text-[10px] font-bold text-slate-400 uppercase">nodos</span>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex justify-between items-center text-[10px]">
                <span class="text-emerald-600 font-bold uppercase flex items-center gap-1">
                     <span class="material-symbols-outlined text-[14px]">trending_up</span> 
                     Estable
                </span>
                <div class="flex gap-1 h-3 items-end">
                    @foreach(range(1, 8) as $i)
                        <div class="w-0.5 bg-primary/20" style="height: {{ rand(30, 100) }}%"></div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- KPI: Pendiente Validación -->
        <a href="{{ route('afiliados.index', ['estado_id' => 7]) }}" class="bg-white p-6 rounded-3xl border border-white shadow-sm hover:shadow-lg transition-all group border-l-4 border-amber-400">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono text-amber-600 font-bold uppercase tracking-widest">Validación CMD</span>
                <span class="material-symbols-outlined text-amber-500 animate-pulse">security</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black font-headline text-slate-900">{{ number_format($totalPendienteValidacion) }}</span>
                <span class="text-[10px] font-bold text-amber-600/50 uppercase">alertas</span>
            </div>
            <div class="mt-4 bg-amber-50 rounded-xl p-3 flex items-center justify-between group-hover:bg-amber-100 transition-colors">
                <span class="text-[9px] font-black text-amber-700 uppercase tracking-tighter">Atender ahora</span>
                <span class="material-symbols-outlined text-xs text-amber-600">arrow_forward</span>
            </div>
        </a>

        <!-- KPI: Completados -->
        <div class="bg-white/60 backdrop-blur-md p-6 rounded-3xl border border-white shadow-sm border-l-4 border-emerald-400">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono text-emerald-600 font-bold uppercase tracking-widest">Ciclos Cerrados</span>
                <span class="material-symbols-outlined text-emerald-500">verified</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black font-headline text-slate-900">{{ number_format($totalCompletados) }}</span>
                <span class="text-[10px] font-bold text-emerald-600 uppercase">{{ $porcentajeCompletado }}%</span>
            </div>
            <div class="mt-4 w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500" style="width: {{ $porcentajeCompletado }}%"></div>
            </div>
        </div>

         <!-- KPI: Fuera de SLA -->
         <div class="bg-white/60 backdrop-blur-md p-6 rounded-3xl border border-white shadow-sm border-l-4 border-rose-400">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-mono text-rose-600 font-bold uppercase tracking-widest">SLA Crítico</span>
                <span class="material-symbols-outlined text-rose-500">warning</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-black font-headline text-slate-900">{{ $fueraSlaCount }}</span>
                <span class="text-[10px] font-bold text-rose-600 uppercase">excesos</span>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 text-[9px] font-bold text-rose-500 uppercase tracking-tighter">
                Supera límite de 20 días
            </div>
        </div>

        @if(auth()->user()->hasRole(['Admin']))
        <!-- KPI: Finanzas -->
        <div class="bg-white/80 p-6 rounded-3xl border border-white shadow-md xl:col-span-2 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-2">
                <div class="p-2 bg-primary/5 rounded-full">
                    <span class="material-symbols-outlined text-primary text-xl">payments</span>
                </div>
            </div>
            <div class="flex flex-col mb-6">
                <span class="text-[10px] font-mono text-slate-400 font-bold uppercase tracking-widest">Carga Financiera Total</span>
                <span class="text-2xl font-black text-slate-900 mt-1">${{ number_format($montoArs + $montoNoArs, 0) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-[10px] font-bold">
                        <span class="text-slate-500">ARS CMD</span>
                        <span class="text-primary">${{ number_format($montoArs, 0) }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-primary" style="width: {{ ($montoArs + $montoNoArs) > 0 ? ($montoArs / ($montoArs + $montoNoArs)) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-[10px] font-bold">
                        <span class="text-slate-500">OTROS</span>
                        <span class="text-slate-900">${{ number_format($montoNoArs, 0) }}</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-800" style="width: {{ ($montoArs + $montoNoArs) > 0 ? ($montoNoArs / ($montoArs + $montoNoArs)) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- ANALYTICS HUD -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- TREND LINE -->
        <div class="lg:col-span-2 bg-white/70 backdrop-blur-xl p-8 rounded-[40px] border border-white shadow-xl shadow-slate-200/40 relative">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-sm font-black font-headline text-slate-900 uppercase tracking-widest">Tendencia de Operaciones</h3>
                    <p class="text-[10px] font-mono text-slate-400 mt-1 uppercase">Flujo mensual de carnetización</p>
                </div>
                <div class="flex gap-1 bg-slate-100 p-1 rounded-full">
                    <button class="px-4 py-1.5 rounded-full text-[9px] font-bold text-slate-400 hover:text-slate-600 transition-colors uppercase">Semanal</button>
                    <button class="px-4 py-1.5 rounded-full bg-white shadow-sm text-[9px] font-bold text-primary uppercase">Mensual</button>
                </div>
            </div>
            <div class="h-[350px] relative">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        @if(auth()->user()->hasRole(['Admin']))
        <!-- DISTRIBUTION -->
        <div class="bg-white/70 backdrop-blur-xl p-8 rounded-[40px] border border-white shadow-xl shadow-slate-200/40">
             <h3 class="text-sm font-black font-headline text-slate-900 uppercase tracking-widest mb-10 text-center">Ecosistema de Estados</h3>
             <div class="relative h-[280px] flex items-center justify-center">
                <canvas id="estadoChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                    <span class="text-4xl font-black font-headline text-slate-900">{{ number_format($totalAfiliados) }}</span>
                    <span class="text-[10px] font-mono text-slate-400 uppercase tracking-widest">Expedientes</span>
                </div>
            </div>
            <div class="mt-10 space-y-4">
                @foreach($afiliadosPorEstado->take(4) as $stat)
                     <div class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-md" style="background-color: {{ ['#00346f', '#0060ac', '#10b981', '#fbbf24', '#f43f5e'][$loop->index % 5] }}"></span>
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-tight">{{ $stat->estado->nombre }}</span>
                        </div>
                        <span class="text-xs font-mono font-black text-slate-900 bg-slate-50 px-2 py-0.5 rounded">{{ $stat->total }}</span>
                     </div>
                @endforeach
            </div>
        </div>
        @else
        @php
            $userStats = \App\Http\Controllers\ProfileController::getUserStats(auth()->user());
        @endphp
        <!-- For Operators: My Daily Goal -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-10 rounded-[40px] shadow-2xl text-white flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <h3 class="text-2xl font-black tracking-tight mb-2">Tu Meta Diaria</h3>
                <p class="text-blue-100 text-xs font-bold uppercase tracking-[0.2em]">Enfoque en {{ ($userStats['is_callcenter'] ?? false) ? 'contactabilidad' : 'efectividad' }}</p>
            </div>

            <div class="relative z-10 py-12 text-center">
                <div class="text-7xl font-black mb-4">{{ $userStats['efectividad'] }}%</div>
                <p class="text-sm font-medium text-blue-100 italic">"La constancia es la clave del éxito"</p>
                
                <div class="mt-10 flex gap-4">
                    <div class="flex-1 bg-white/10 backdrop-blur-md p-4 rounded-3xl border border-white/10">
                        <p class="text-[0.6rem] font-black uppercase text-blue-200 mb-1">{{ ($userStats['is_callcenter'] ?? false) ? 'Llamadas Hoy' : 'Entregados Hoy' }}</p>
                        <p class="text-xl font-black">{{ $userStats['entregados_hoy'] }}</p>
                    </div>
                    <div class="flex-1 bg-white/10 backdrop-blur-md p-4 rounded-3xl border border-white/10">
                        <p class="text-[0.6rem] font-black uppercase text-blue-200 mb-1">Pendientes</p>
                        <p class="text-xl font-black">{{ $userStats['pendientes'] }}</p>
                    </div>
                </div>
            </div>

            @if($userStats['is_callcenter'] ?? false)
                <a href="{{ route('callcenter.worklist') }}" class="relative z-10 w-full py-4 bg-white text-blue-700 hover:bg-blue-50 rounded-2xl text-[0.7rem] font-black uppercase tracking-[0.3em] transition-all text-center shadow-xl">
                    Continuar Llamadas
                </a>
            @else
                <a href="{{ route('afiliados.index') }}" class="relative z-10 w-full py-4 bg-white text-blue-700 hover:bg-blue-50 rounded-2xl text-[0.7rem] font-black uppercase tracking-[0.3em] transition-all text-center shadow-xl">
                    Continuar Labores
                </a>
            @endif
        </div>
        @endif
    </div>

    <!-- ACTIVITY MONITOR -->
    <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/30">
        <div class="flex justify-between items-center mb-10 border-b border-slate-50 pb-6">
             <div>
                <h3 class="text-sm font-black font-headline text-slate-900 uppercase tracking-widest">Eventos en Vivo</h3>
                <p class="text-[10px] font-mono text-slate-400 mt-1">Últimas interacciones procesadas</p>
             </div>
             <div class="flex items-center gap-2">
                 <span class="flex h-2 w-2 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                 </span>
                 <span class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">Sistema Activo</span>
             </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.25em]">
                        <th class="pb-6 pr-4">Identificador</th>
                        <th class="pb-6 px-4 text-center">Protocolo</th>
                        <th class="pb-6 px-4">Ejecutor</th>
                        <th class="pb-6 pl-4 text-right">Tiempo Relativo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($actividadReciente as $act)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="py-5 pr-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800 group-hover:text-primary transition-colors">{{ $act->afiliado->nombre_completo ?? 'SYSTEM_NODE' }}</span>
                                <span class="text-[10px] font-mono text-slate-400 tracking-tighter">{{ $act->afiliado->cedula ?? '000-0000000-0' }}</span>
                            </div>
                        </td>
                        <td class="py-5 px-4 text-center">
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-[9px] font-black text-slate-600 uppercase group-hover:bg-primary group-hover:text-white transition-all shadow-sm">
                                {{ $act->estadoNuevo->nombre }}
                            </span>
                        </td>
                        <td class="py-5 px-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-primary/10 border border-primary/5 flex items-center justify-center text-[10px] text-primary font-black uppercase">
                                    {{ substr($act->user->name ?? 'S', 0, 1) }}
                                </div>
                                <span class="text-xs text-slate-600 font-bold">{{ $act->user->name ?? 'Sistema' }}</span>
                            </div>
                        </td>
                        <td class="py-5 pl-4 text-right">
                             <div class="flex flex-col items-end">
                                <span class="text-xs text-slate-900 font-black">{{ $act->created_at->format('H:i:s') }}</span>
                                <span class="text-[9px] text-slate-400 font-bold uppercase">{{ $act->created_at->diffForHumans() }}</span>
                             </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- DATA HUB FOR CHARTS -->
<div id="dashboard-data" class="hidden"
    data-estados-labels='{{ json_encode($afiliadosPorEstado->map(fn($i) => $i->estado->nombre)) }}'
    data-estados-total='{{ json_encode($afiliadosPorEstado->pluck("total")) }}'
    data-trend-labels='{{ json_encode($statsPorMes->pluck("mes")) }}'
    data-trend-total='{{ json_encode($statsPorMes->pluck("total")) }}'>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dataContainer = document.getElementById('dashboard-data');
    if (!dataContainer) return;

    Chart.defaults.color = '#94a3b8';
    Chart.defaults.font.family = "'Space Grotesk', 'Inter', sans-serif";
    Chart.defaults.font.size = 11;

    // 1. Gráfica de Estados (Doughnut)
    const ctxEstado = document.getElementById('estadoChart').getContext('2d');
    new Chart(ctxEstado, {
        type: 'doughnut',
        data: {
            labels: JSON.parse(dataContainer.dataset.estadosLabels),
            datasets: [{
                data: JSON.parse(dataContainer.dataset.estadosTotal),
                backgroundColor: [
                    '#00346f', '#0060ac', '#10b981', '#fbbf24', '#f43f5e', '#6366f1', '#8b5cf6'
                ],
                hoverOffset: 30,
                borderWidth: 8,
                borderColor: '#ffffff',
                spacing: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            cutout: '80%'
        }
    });

    // 2. Gráfica de Tendencia (Line)
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    const gradient = ctxTrend.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(0, 52, 111, 0.1)');
    gradient.addColorStop(1, 'rgba(0, 52, 111, 0)');

    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: JSON.parse(dataContainer.dataset.trendLabels),
            datasets: [{
                label: 'Interacciones',
                data: JSON.parse(dataContainer.dataset.trendTotal),
                borderColor: '#00346f',
                borderWidth: 4,
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#00346f',
                pointBorderWidth: 2,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#00346f',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 16,
                    displayColors: false,
                    cornerRadius: 12
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: { padding: 10 }
                },
                x: { 
                    grid: { display: false },
                    ticks: { padding: 10 }
                }
            }
        }
    });
});
</script>
@endsection
