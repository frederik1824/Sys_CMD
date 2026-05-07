@extends('layouts.app')

@section('content')
<!-- Background Base -->
<div class="fixed inset-0 bg-[#f1f5f9] z-0 pointer-events-none"></div>

<div class="relative z-10 p-4 lg:p-8 space-y-8">
    <!-- TOP HUD: Professional Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 bg-blue-700 rounded-full"></div>
                <h2 class="text-3xl font-bold font-headline text-slate-900 tracking-tight">Centro de <span class="text-blue-700">Operaciones</span></h2>
            </div>
            <p class="text-slate-500 font-bold text-[10px] uppercase tracking-widest pl-5">Gestión de Procesos • ARS CMD</p>
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
                <span class="text-slate-500 font-bold uppercase flex items-center gap-1">
                     Estado de Red
                </span>
                <span class="text-emerald-600 font-bold uppercase">Sincronizado</span>
            </div>
        </div>

        <!-- KPI: Pendiente Validación -->
        <a href="{{ route('carnetizacion.afiliados.index', ['estado_id' => 7]) }}" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:border-amber-400 transition-all group border-l-4 border-l-amber-400">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Validación CMD</span>
                <span class="material-symbols-outlined text-amber-500">security</span>
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
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm border-l-4 border-l-emerald-500">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Ciclos Cerrados</span>
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
         <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm border-l-4 border-l-rose-500">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-bold text-rose-600 uppercase tracking-widest">SLA Crítico</span>
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
        <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm relative">
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
                <a href="{{ route('carnetizacion.afiliados.index') }}" class="relative z-10 w-full py-4 bg-white text-blue-700 hover:bg-blue-50 rounded-2xl text-[0.7rem] font-black uppercase tracking-[0.3em] transition-all text-center shadow-xl">
                    Continuar Labores
                </a>
            @endif
        </div>
        @endif
    </div>

    <!-- OPERATIONAL INTELLIGENCE: Critical Alerts (SEMANA 4) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-8 border-b border-slate-50 pb-6">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Alertas Críticas de SLA</h3>
                    <p class="text-[0.65rem] text-rose-500 font-bold uppercase mt-1">Intervención Requerida Inmediata</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-rose-500 rounded-full animate-ping"></span>
                    <span class="text-[0.65rem] font-bold text-rose-600 uppercase tracking-widest">Atención Prioritaria</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50">
                            <th class="pb-4">Afiliado / Cédula</th>
                            <th class="pb-4">Estado Actual</th>
                            <th class="pb-4">Retraso</th>
                            <th class="pb-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @php
                            $criticos = \App\Models\Afiliado::whereHas('estado', function($q) {
                                $q->where('nombre', '!=', 'Completado');
                            })
                            ->whereNotNull('fecha_entrega_safesure')
                            ->where('fecha_entrega_safesure', '<=', now()->subDays(15))
                            ->orderBy('fecha_entrega_safesure', 'asc')
                            ->take(6)
                            ->get();
                        @endphp

                        @forelse($criticos as $critico)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-4">
                                <div class="flex flex-col">
                                    <span class="text-[0.8rem] font-bold text-slate-800">{{ $critico->nombre_completo }}</span>
                                    <span class="text-[0.65rem] font-mono text-slate-400 tracking-tighter">{{ $critico->cedula }}</span>
                                </div>
                            </td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-[0.6rem] font-bold text-slate-600 uppercase">
                                    {{ $critico->estado->nombre }}
                                </span>
                            </td>
                            <td class="py-4">
                                <span class="text-[0.7rem] font-bold text-rose-600 uppercase">{{ $critico->dias_transcurridos }} Días</span>
                            </td>
                            <td class="py-4 text-right">
                                <a href="{{ route('carnetizacion.afiliados.show', $critico) }}" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-slate-200 text-slate-400 rounded-lg hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
                                    <span class="material-symbols-outlined text-[1.1rem]">visibility</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-outlined text-emerald-500 text-3xl mb-2">verified</span>
                                    <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Sin alertas críticas pendientes</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RIGHT PANEL: Operational Pulse -->
        <div class="lg:col-span-1 bg-slate-900 p-8 rounded-2xl shadow-xl text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
            <div class="relative z-10">
                <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-6">Eficiencia de Red</h3>
                <div class="space-y-6">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[0.65rem] font-bold text-slate-400 uppercase">Tasa de Entrega</span>
                            <span class="text-lg font-bold">92%</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: 92%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[0.65rem] font-bold text-slate-400 uppercase">Documentación Validada</span>
                            <span class="text-lg font-bold">78%</span>
                        </div>
                        <div class="w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-blue-500 h-full rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 p-6 bg-white/5 rounded-xl border border-white/5">
                    <p class="text-[0.65rem] font-bold text-slate-500 uppercase tracking-widest mb-2">Nota del Sistema</p>
                    <p class="text-[0.7rem] text-slate-300 leading-relaxed italic">"El tiempo promedio de respuesta ha disminuido un 12% desde la última optimización de flujos."</p>
                </div>
            </div>
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
