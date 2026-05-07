@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-10 space-y-10 min-h-screen bg-[#f8fafc]">
    
    <!-- Header Inteligente -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <span class="text-primary/60">Business Intelligence</span>
                <span class="ph ph-caret-right text-[10px]"></span>
                <span class="text-primary">Producción de Traspasos</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-[900] text-slate-900 tracking-tighter leading-none flex items-center gap-4">
                Análisis de <span class="text-primary italic font-light">Efectividad</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-2xl hidden md:block">
                    <i class="ph ph-chart-bar-horizontal text-3xl"></i>
                </span>
            </h1>
            <p class="text-slate-500 font-medium">Monitoreo de hit-rate y volumen transaccional por fuerza de ventas.</p>
        </div>

        <!-- Filtros Flotantes -->
        <form action="{{ route('reportes.produccion_traspasos') }}" method="GET" class="w-full md:w-auto bg-white p-2 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/40 flex flex-wrap items-center gap-2">
            <div class="flex items-center gap-2 px-4 py-2 border-r border-slate-100">
                <i class="ph ph-calendar text-slate-400"></i>
                <input type="date" name="fecha_desde" value="{{ $fecha_desde }}" class="border-none bg-transparent text-xs font-black text-slate-900 focus:ring-0 p-0 w-28">
                <span class="text-slate-300 text-[10px] font-black">AL</span>
                <input type="date" name="fecha_hasta" value="{{ $fecha_hasta }}" class="border-none bg-transparent text-xs font-black text-slate-900 focus:ring-0 p-0 w-28">
            </div>
            <div class="px-4 py-2 border-r border-slate-100 hidden lg:block">
                <select name="supervisor_id" class="border-none bg-transparent text-xs font-black text-slate-900 focus:ring-0 p-0 cursor-pointer min-w-[150px]">
                    <option value="">Todos los Equipos</option>
                    @foreach($supervisores as $sup)
                        <option value="{{ $sup->id }}" {{ $supervisor_id == $sup->id ? 'selected' : '' }}>{{ $sup->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-slate-900 text-white px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-primary transition-all flex items-center gap-2">
                <i class="ph ph-funnel text-lg"></i>
                Filtrar
            </button>
        </form>
    </div>

    <!-- Dashboard Bento -->
    <div class="grid grid-cols-12 gap-6">
        
        <!-- KPIs Principales: Los 4 Números Clave -->
        <div class="col-span-12 lg:col-span-3 space-y-6">
            <!-- TITULARES EFECTIVOS -->
            <div class="bg-emerald-600 p-8 rounded-[3rem] text-white shadow-xl shadow-emerald-200/50 relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="ph-fill ph-check-circle text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-80">Traspasos Efectivos</p>
                </div>
                <h4 class="text-5xl font-black tracking-tighter relative z-10">{{ number_format($stats['total_efectivos']) }}</h4>
                <p class="text-[9px] font-bold mt-2 uppercase tracking-widest opacity-60">Titulares cerrados</p>
            </div>

            <!-- DEPENDIENTES EFECTIVOS -->
            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                        <i class="ph-fill ph-users text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cant. Dependientes</p>
                </div>
                <h4 class="text-4xl font-black text-slate-900 tracking-tighter relative z-10">{{ number_format($stats['total_dependientes_efectivos']) }}</h4>
                <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase tracking-tighter">De cierres efectivos</p>
            </div>

            <!-- PENDIENTES Y RECHAZADOS -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center text-amber-600">
                            <i class="ph ph-clock text-lg"></i>
                        </div>
                    </div>
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Pendientes</p>
                    <h4 class="text-2xl font-black text-amber-600 tracking-tighter">{{ number_format($stats['total_pendientes']) }}</h4>
                </div>
                <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center text-rose-600">
                            <i class="ph ph-x-circle text-lg"></i>
                        </div>
                    </div>
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Rechazados</p>
                    <h4 class="text-2xl font-black text-rose-600 tracking-tighter">{{ number_format($stats['total_rechazados']) }}</h4>
                </div>
            </div>

            <!-- HIT RATE SUMMARY -->
            <div class="bg-slate-900 p-8 rounded-[3rem] text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/20 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 relative z-10">Eficiencia Global</p>
                <h4 class="text-4xl font-black text-primary-light tracking-tighter relative z-10">{{ $stats['hit_rate_promedio'] }}%</h4>
            </div>
        </div>

        <!-- Gráfico de Tendencia -->
        <div class="col-span-12 lg:col-span-9 bg-white p-10 rounded-[40px] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h3 class="text-xl font-[900] text-slate-900 tracking-tight">Tendencia de Productividad</h3>
                    <p class="text-xs text-slate-400 font-medium">Comparativa mensual de cierres efectivos vs volumen.</p>
                </div>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-slate-200"></span>
                        <span class="text-[10px] font-black text-slate-400 uppercase">Volumen</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-600"></span>
                        <span class="text-[10px] font-black text-slate-400 uppercase">Efectivos</span>
                    </div>
                </div>
            </div>
            <div class="h-[350px] w-full">
                <canvas id="productionTrendChart"></canvas>
            </div>
        </div>

        <!-- Ranking por Agente (Tabla Premium) -->
        <div class="col-span-12 bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-10 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="text-2xl font-[900] text-slate-900 tracking-tight">Efectividad por Agentes</h3>
                    <p class="text-xs text-slate-400 font-medium mt-1">Ranking de desempeño basado en hit-rate histórico.</p>
                </div>
                <div class="flex gap-2">
                    <div class="px-4 py-2 bg-blue-50 text-blue-600 rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center gap-2">
                        <i class="ph ph-users-three text-lg"></i>
                        {{ $rankingAgentes->count() }} Agentes Activos
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left w-16">Rank</th>
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Agente / Equipo</th>
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Tit. Efectivos</th>
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Dependientes</th>
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Vidas Totales</th>
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Pendientes</th>
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Rechazados</th>
                            <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Efectividad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($rankingAgentes as $ag)
                        <tr class="group hover:bg-slate-50/40 transition-all">
                            <td class="px-10 py-6">
                                <span class="text-lg font-black {{ $loop->iteration <= 3 ? 'text-primary' : 'text-slate-300' }}">
                                    #{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-800 uppercase tracking-tight group-hover:text-primary transition-colors">{{ $ag->agenteRel->nombre ?? 'N/A' }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase italic">{{ $ag->agenteRel->supervisor->nombre ?? 'Sin Equipo' }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <span class="text-xs font-black text-emerald-600">{{ number_format($ag->efectivos) }}</span>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <span class="px-4 py-2 bg-slate-900 text-white text-[10px] font-black rounded-xl">
                                    {{ number_format($ag->total_vidas_efectivas) }}
                                </span>
                            </td>
                            <td class="px-10 py-6">
                                <div class="flex items-center gap-4 justify-end">
                                    <div class="flex flex-col items-end">
                                        <span class="text-lg font-black text-slate-900 leading-none">{{ $ag->hit_rate }}%</span>
                                        <div class="w-24 h-1 bg-slate-100 rounded-full mt-2 overflow-hidden">
                                            <div class="h-full bg-{{ $ag->hit_rate >= 70 ? 'emerald' : ($ag->hit_rate >= 40 ? 'amber' : 'rose') }}-500 rounded-full" style="width: {{ $ag->hit_rate }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('productionTrendChart').getContext('2d');
    
    const labels = {!! json_encode(collect($tendencia)->pluck('label')) !!};
    const totals = {!! json_encode(collect($tendencia)->pluck('total')) !!};
    const effectives = {!! json_encode(collect($tendencia)->pluck('efectivos')) !!};

    const blueGrad = ctx.createLinearGradient(0, 0, 0, 400);
    blueGrad.addColorStop(0, 'rgba(37, 99, 235, 0.4)');
    blueGrad.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Efectivos',
                    data: effectives,
                    borderColor: '#2563eb',
                    backgroundColor: blueGrad,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 3,
                    z: 10
                },
                {
                    label: 'Volumen',
                    data: totals,
                    borderColor: '#e2e8f0',
                    backgroundColor: 'rgba(226, 232, 240, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 15,
                    cornerRadius: 15,
                    titleFont: { size: 14, weight: '900' },
                    bodyFont: { size: 12, weight: 'bold' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: { font: { weight: 'bold' }, padding: 10 }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: 'bold' }, padding: 10 }
                }
            }
        }
    });
});
</script>
@endsection
