@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-12 space-y-12 min-h-screen bg-[#f1f5f9]">
    
    <!-- HEADER PREMIUM -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-8">
        <div class="space-y-3">
            <div class="flex items-center gap-3 text-[10px] font-black uppercase tracking-[0.3em] text-blue-600">
                <span class="bg-blue-600 text-white px-2 py-0.5 rounded-md">BI</span>
                <span>Análisis Operativo de Traspasos</span>
            </div>
            <h1 class="text-5xl md:text-6xl font-[1000] text-slate-900 tracking-tighter leading-[0.9] flex flex-wrap items-center gap-x-6">
                Dashboard de <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Producción</span>
            </h1>
            <p class="text-slate-500 font-medium text-lg max-w-2xl">Métricas de efectividad y volumen transaccional para la fuerza de ventas externa e interna.</p>
        </div>

        <!-- FILTROS INTELIGENTES -->
        <div class="w-full xl:w-auto">
            <form action="{{ route('reportes.produccion_traspasos') }}" method="GET" class="bg-white/70 backdrop-blur-xl p-3 rounded-[2.5rem] border border-white shadow-2xl shadow-blue-900/10 flex flex-col md:flex-row items-center gap-3">
                <div class="flex items-center gap-3 px-6 py-3 bg-slate-50 rounded-full border border-slate-100 flex-1 md:flex-none">
                    <i class="ph ph-calendar-blank text-blue-600 text-xl"></i>
                    <input type="date" name="fecha_desde" value="{{ $fecha_desde }}" class="border-none bg-transparent text-xs font-black text-slate-900 focus:ring-0 p-0 w-32">
                    <span class="text-slate-300 font-black text-[10px] mx-1">➜</span>
                    <input type="date" name="fecha_hasta" value="{{ $fecha_hasta }}" class="border-none bg-transparent text-xs font-black text-slate-900 focus:ring-0 p-0 w-32">
                </div>
                
                <div class="px-6 py-3 bg-slate-50 rounded-full border border-slate-100 flex-1 md:flex-none">
                    <select name="supervisor_id" class="border-none bg-transparent text-xs font-black text-slate-900 focus:ring-0 p-0 cursor-pointer min-w-[160px]">
                        <option value="">Todos los Equipos</option>
                        @foreach($supervisores as $sup)
                            <option value="{{ $sup->id }}" {{ $supervisor_id == $sup->id ? 'selected' : '' }}>{{ $sup->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-8 py-3.5 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all flex items-center gap-2 shadow-lg shadow-blue-200">
                    <i class="ph ph-arrow-clockwise text-lg"></i> Actualizar
                </button>
            </form>
        </div>
    </div>

    <!-- BOTONES DE EXPORTACIÓN FLOTANTES -->
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('reportes.produccion_traspasos.export', request()->all()) }}" 
           class="group px-8 py-4 bg-white text-slate-900 rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all shadow-xl shadow-slate-200/50 flex items-center gap-3 border border-slate-100">
            <i class="ph ph-file-xls text-2xl text-emerald-600 group-hover:text-white"></i>
            Excel Detallado (XLSX)
        </a>
        <a href="{{ route('reportes.produccion_traspasos.export_pdf', request()->all()) }}" 
           class="group px-8 py-4 bg-white text-slate-900 rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all shadow-xl shadow-slate-200/50 flex items-center gap-3 border border-slate-100">
            <i class="ph ph-file-pdf text-2xl text-rose-600 group-hover:text-white"></i>
            Reporte PDF
        </a>
    </div>

    <!-- DASHBOARD GRID -->
    <div class="grid grid-cols-12 gap-8">
        
        <!-- KPIs LATERALES -->
        <div class="col-span-12 xl:col-span-3 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-1 gap-6">
            
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 p-8 rounded-[3rem] text-white shadow-2xl shadow-blue-500/30 relative overflow-hidden group">
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-all duration-1000"></div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-70 mb-1">Efectivos (Titulares)</p>
                <div class="flex items-baseline gap-2">
                    <h4 class="text-6xl font-black tracking-tighter">{{ number_format($stats['total_efectivos']) }}</h4>
                    <span class="text-blue-200 text-sm font-bold uppercase">Cierres</span>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <div class="px-3 py-1 bg-white/20 rounded-lg text-[9px] font-black uppercase">KPI Meta: 100%</div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[3rem] border border-slate-200 shadow-sm relative overflow-hidden group">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Traspasos Totales</p>
                <div class="flex items-baseline gap-2">
                    <h4 class="text-5xl font-black text-slate-900 tracking-tighter">{{ number_format($stats['total_efectivos'] + $stats['total_dependientes_efectivos']) }}</h4>
                    <span class="text-slate-400 text-sm font-bold uppercase">Traspasos</span>
                </div>
                <div class="mt-6 p-4 bg-slate-50 rounded-2xl flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <i class="ph-fill ph-users"></i>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase">Dependientes</p>
                        <p class="text-sm font-black text-slate-700">+{{ number_format($stats['total_dependientes_efectivos']) }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-[2.5rem] border border-slate-200 shadow-sm">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-2">Pendientes</p>
                    <h4 class="text-2xl font-black text-amber-500 tracking-tighter">{{ number_format($stats['total_pendientes']) }}</h4>
                    <div class="w-full h-1 bg-amber-100 rounded-full mt-3 overflow-hidden">
                        <div class="h-full bg-amber-500 w-2/3"></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2.5rem] border border-slate-200 shadow-sm">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-2">Rechazados</p>
                    <h4 class="text-2xl font-black text-rose-500 tracking-tighter">{{ number_format($stats['total_rechazados']) }}</h4>
                    <div class="w-full h-1 bg-rose-100 rounded-full mt-3 overflow-hidden">
                        <div class="h-full bg-rose-500 w-1/3"></div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 p-8 rounded-[3rem] text-white shadow-2xl relative overflow-hidden group">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Hit Rate Global</p>
                    <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-primary">
                        <i class="ph-fill ph-lightning text-xl"></i>
                    </div>
                </div>
                <h4 class="text-5xl font-black text-white tracking-tighter">{{ $stats['hit_rate_promedio'] }}%</h4>
                <p class="text-[9px] font-medium text-slate-500 mt-2">Relación Efectivos vs Solicitudes</p>
            </div>
        </div>

        <!-- GRÁFICO CENTRAL -->
        <div class="col-span-12 xl:col-span-9 bg-white p-10 rounded-[4rem] border border-slate-200 shadow-xl shadow-slate-900/5 relative overflow-hidden">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                <div>
                    <h3 class="text-2xl font-[1000] text-slate-900 tracking-tight">Tendencia de Productividad Mensual</h3>
                    <p class="text-sm text-slate-400 font-medium">Histórico de los últimos 6 meses de operación.</p>
                </div>
                <div class="flex items-center gap-8 bg-slate-50 p-4 rounded-3xl border border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-slate-300 shadow-lg shadow-slate-200"></span>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Solicitudes</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-4 h-4 rounded-full bg-blue-600 shadow-lg shadow-blue-200"></span>
                        <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Efectivos</span>
                    </div>
                </div>
            </div>
            <div class="h-[450px] w-full">
                <canvas id="productionTrendChart"></canvas>
            </div>
        </div>

        <!-- TABLA DE RANKING -->
        <div class="col-span-12 bg-white rounded-[4rem] border border-slate-200 shadow-2xl shadow-slate-900/5 overflow-hidden">
            <div class="p-10 border-b border-slate-100 bg-slate-50/50 flex flex-col lg:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 bg-blue-600 rounded-[2rem] flex items-center justify-center text-white shadow-xl shadow-blue-200">
                        <i class="ph-fill ph-trophy text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-3xl font-[1000] text-slate-900 tracking-tighter">Ranking de Agentes</h3>
                        <p class="text-sm text-slate-400 font-medium mt-1">Desempeño ordenado por volumen de efectividad.</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="px-6 py-3 bg-white border border-slate-200 text-slate-500 rounded-full text-[10px] font-black uppercase tracking-widest">
                        Total Agentes: {{ $rankingAgentes->count() }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/30">
                            <th class="px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] w-24">Rank</th>
                            <th class="px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Agente Operativo</th>
                            <th class="px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Efectivos</th>
                            <th class="px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Dependientes</th>
                            <th class="px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Traspasos Totales</th>
                            <th class="px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Solicitados</th>
                            <th class="px-10 py-8 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Hit Rate</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($rankingAgentes as $ag)
                        <tr class="group hover:bg-blue-50/30 transition-all duration-300">
                            <td class="px-10 py-8">
                                <span class="flex items-center justify-center w-12 h-12 rounded-2xl text-lg font-black {{ $loop->iteration <= 3 ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $loop->iteration }}
                                </span>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex flex-col">
                                    <span class="text-base font-[900] text-slate-800 uppercase tracking-tight group-hover:text-blue-600 transition-colors">{{ $ag->agenteRel->nombre ?? 'N/A' }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                        <i class="ph ph-briefcase"></i> {{ $ag->agenteRel->supervisor->nombre ?? 'Sin Equipo' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <span class="text-lg font-black text-blue-600">{{ number_format($ag->efectivos) }}</span>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <span class="text-base font-black text-slate-500">{{ number_format($ag->dependientes_efectivos) }}</span>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <div class="inline-flex flex-col items-center p-3 bg-slate-900 rounded-2xl shadow-xl min-w-[80px]">
                                    <span class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1">Total</span>
                                    <span class="text-lg font-black text-white leading-none">{{ number_format($ag->total_vidas_efectivas) }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <span class="text-base font-black text-slate-400 italic">{{ number_format($ag->total_solicitudes) }}</span>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex flex-col items-end gap-2">
                                    <span class="text-2xl font-black text-slate-900 tracking-tighter">{{ $ag->hit_rate }}<small class="text-xs ml-0.5 opacity-50">%</small></span>
                                    <div class="w-32 h-2 bg-slate-100 rounded-full overflow-hidden border border-slate-50">
                                        <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full" style="width: {{ $ag->hit_rate }}%"></div>
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

<!-- CHART.JS INTEGRATION -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('productionTrendChart').getContext('2d');
    
    const labels = {!! json_encode(collect($tendencia)->pluck('label')) !!};
    const totals = {!! json_encode(collect($tendencia)->pluck('total')) !!};
    const effectives = {!! json_encode(collect($tendencia)->pluck('efectivos')) !!};

    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
    gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Traspasos Efectivos',
                    data: effectives,
                    borderColor: '#2563eb',
                    backgroundColor: gradient,
                    borderWidth: 5,
                    fill: true,
                    tension: 0.45,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 4,
                },
                {
                    label: 'Volumen Solicitudes',
                    data: totals,
                    borderColor: '#cbd5e1',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    borderDash: [8, 5],
                    tension: 0.45,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { size: 14, weight: '900' },
                    bodyFont: { size: 13 },
                    padding: 20,
                    cornerRadius: 20,
                    displayColors: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: { font: { weight: 'bold', size: 11 }, padding: 15 }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { weight: 'bold', size: 11 }, padding: 15 }
                }
            }
        }
    });
});
</script>
@endsection
