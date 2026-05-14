@extends('layouts.app')

@section('content')
<div class="p-8" x-data="{ activeTab: 'agents' }">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight uppercase italic">Intelligence Dashboard</h1>
            <p class="text-slate-500 font-medium">Análisis de Producción ARS • {{ \Carbon\Carbon::create(null, $currentMonth)->translatedFormat('F') }} {{ $currentYear }}</p>
        </div>
        
        <form action="{{ route('traspasos.dashboard') }}" method="GET" class="flex flex-wrap items-center gap-3 bg-white p-3 rounded-[32px] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-2 px-4">
                <i class="ph ph-calendar text-slate-400"></i>
                <select name="month" class="border-none bg-transparent text-sm font-black text-slate-900 focus:ring-0 cursor-pointer">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $currentMonth == $m ? 'selected' : '' }}>
                            {{ ucfirst(\Carbon\Carbon::create(null, $m)->translatedFormat('F')) }}
                        </option>
                    @endforeach
                </select>
                <select name="year" class="border-none bg-transparent text-sm font-black text-slate-900 focus:ring-0 cursor-pointer">
                    @foreach(range(date('Y'), date('Y')-2) as $y)
                        <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                Filtrar
            </button>
            <div class="w-px h-8 bg-slate-100 mx-1"></div>
            <a href="{{ route('traspasos.index') }}" class="px-6 py-3 text-slate-600 hover:text-slate-900 text-[10px] font-black uppercase tracking-widest transition-all">
                Bandeja
            </a>
        </form>
    </div>

    <!-- GENERAL KPIS (ALL TIME) -->
    <div class="mb-10 bg-slate-900 rounded-[40px] p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 p-8 opacity-10">
            <i class="ph ph-globe text-9xl"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex-1">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-400 mb-2">Desempeño Histórico Global</p>
                <h2 class="text-3xl font-black tracking-tighter italic">Visión General del Sistema</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 flex-[2]">
                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Total Traspasos</p>
                    <p class="text-2xl font-black">{{ number_format($statsGlobal['total_traspasos']) }}</p>
                </div>
                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Titulares</p>
                    <p class="text-2xl font-black text-blue-400">{{ number_format($statsGlobal['total_titulares']) }}</p>
                </div>
                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Efectivos</p>
                    <p class="text-2xl font-black text-emerald-400">{{ number_format($statsGlobal['efectivos']) }}</p>
                </div>
                <div class="text-center md:text-left">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Hit Rate Global</p>
                    <p class="text-2xl font-black text-amber-400">{{ $statsGlobal['hit_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN KPIS (MONTHLY) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Producción del Mes (Solicitudes) -->
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <i class="ph ph-calendar-check text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Volumen Mensual ({{ \Carbon\Carbon::create(null, $currentMonth)->translatedFormat('F') }})</span>
            </div>
            <div class="flex justify-between items-end mb-2">
                <div class="text-3xl font-black text-slate-900">
                    {{ number_format($stats['generados_mes'] + $stats['total_dependientes']) }} 
                    <span class="text-xs text-slate-400 font-bold uppercase">Traspasos</span>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-[10px] font-black {{ $crecimiento >= 0 ? 'text-emerald-600' : 'text-rose-600' }} flex items-center gap-1 bg-{{ $crecimiento >= 0 ? 'emerald' : 'rose' }}-50 px-2 py-1 rounded-lg">
                        <i class="ph ph-trend-{{ $crecimiento >= 0 ? 'up' : 'down' }}"></i>
                        {{ round(abs($crecimiento), 1) }}%
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 mt-4">
                <div class="p-2 bg-slate-50 rounded-xl border border-slate-100 flex flex-col">
                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter">Titulares</span>
                    <span class="text-sm font-black text-slate-900">{{ number_format($stats['generados_mes']) }}</span>
                </div>
                <div class="p-2 bg-blue-50 rounded-xl border border-blue-100 flex flex-col">
                    <span class="text-[8px] font-black text-blue-400 uppercase tracking-tighter">Dependientes</span>
                    <span class="text-sm font-black text-blue-600">{{ number_format($stats['total_dependientes']) }}</span>
                </div>
            </div>
        </div>

        <!-- Efectividad Histórica (Justa) -->
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                    <i class="ph ph-target text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Efectividad de Gestión</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mb-2">{{ round($hitRateHistorico, 1) }}%</div>
            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden mt-4">
                <div class="h-full bg-emerald-500" style="width: {{ $hitRateHistorico }}%"></div>
            </div>
            <p class="text-[10px] text-slate-400 font-bold uppercase mt-2 italic">Basado en solicitudes de hace 60 días</p>
        </div>

        <!-- Ciclo de Maduración -->
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                    <i class="ph ph-timer text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ciclo de Maduración</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mb-1">{{ round($maduracionPromedio, 1) }} <span class="text-xs text-slate-400 font-bold uppercase">Días</span></div>
            
            <div class="mt-4 p-2 {{ $casosEstancados > 0 ? 'bg-rose-50 border-rose-100 text-rose-600' : 'bg-slate-50 border-slate-100 text-slate-400' }} border rounded-xl flex items-center justify-between">
                <span class="text-[9px] font-black uppercase">Estancados (+60d)</span>
                <span class="text-xs font-black">{{ $casosEstancados }}</span>
            </div>
        </div>

        <!-- Ratio Familiar -->
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                    <i class="ph ph-users-three text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Ratio Familiar</span>
            </div>
            <div class="text-3xl font-black text-slate-900 mb-2">{{ round($ratioFamiliar, 2) }} <span class="text-xs text-slate-400 font-bold uppercase">Dep. / Traspaso</span></div>
            <p class="text-[10px] text-slate-400 font-bold uppercase mt-4">Carga promedio por contrato</p>
        </div>
    </div>

    <!-- GRÁFICOS Y TOP RECHAZOS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
        <!-- Gráfico de Tendencia -->
        <div class="md:col-span-2 bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter">Tendencia de Traspasos</h3>
                    <p class="text-[11px] text-slate-400 font-bold uppercase">Últimos 6 Meses • Solicitudes vs Efectivos</p>
                </div>
                <div class="flex gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-100 rounded-full"></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase">Solicitudes</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                        <span class="text-[9px] font-black text-slate-400 uppercase">Efectivos</span>
                    </div>
                </div>
            </div>
            <div class="h-[300px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <!-- Top Rechazos -->
        <div class="bg-slate-900 p-8 rounded-[40px] shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10">
                <i class="ph ph-warning-circle text-8xl text-white"></i>
            </div>
            <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">Fugas de Producción</h3>
            <p class="text-[11px] text-slate-400 font-bold uppercase mb-8">Top Motivos de Rechazo</p>
            
            <div class="space-y-4">
                @foreach($motivosRechazo as $rechazo)
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/10 group hover:bg-white/10 transition-all">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[10px] font-black text-rose-400 uppercase tracking-widest">Motivo #{{ $loop->iteration }}</span>
                            <span class="text-xs font-black text-white bg-rose-500/20 px-2 py-0.5 rounded-lg">{{ $rechazo->total }}</span>
                        </div>
                        <p class="text-xs font-bold text-slate-300 leading-tight">{{ $rechazo->motivos_estado }}</p>
                    </div>
                @endforeach
                
                @if($motivosRechazo->isEmpty())
                    <div class="flex flex-col items-center justify-center h-40 text-slate-500">
                        <i class="ph ph-check-circle text-4xl mb-2 opacity-20"></i>
                        <p class="text-[10px] font-black uppercase">Sin rechazos registrados</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- RANKINGS -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter">Rankings de Producción</h3>
                <p class="text-[11px] text-slate-400 font-bold uppercase">Top Desempeño • Periodo Actual</p>
            </div>
            <div class="flex bg-slate-50 p-1 rounded-2xl">
                <button @click="activeTab = 'agents'" :class="activeTab === 'agents' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase transition-all">
                    Por Agentes
                </button>
                <button @click="activeTab = 'teams'" :class="activeTab === 'teams' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'" class="px-6 py-2 rounded-xl text-[10px] font-black uppercase transition-all">
                    Por Equipos
                </button>
            </div>
        </div>

        <div class="p-0">
            <!-- TAB: AGENTES -->
            <div x-show="activeTab === 'agents'" x-transition class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">#</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Agente</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Titulares</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Dep.</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Total Traspasos</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Progreso</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($rankingAgentes as $ag)
                            <tr class="group hover:bg-slate-50/50 transition-all">
                                <td class="px-8 py-5">
                                    <span class="text-xs font-black text-slate-300">#{{ $loop->iteration }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-800 uppercase">{{ $ag->agenteRel->nombre ?? 'Desconocido' }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase italic">Eq. {{ $ag->agenteRel->supervisor->nombre ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-xs font-bold text-slate-600">{{ $ag->total_titulares }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-xs font-bold text-blue-600">{{ $ag->total_dep ?? 0 }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-3 py-1 bg-slate-900 text-white text-[10px] font-black rounded-lg">
                                        {{ $ag->total_titulares + $ag->total_dep }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3 justify-end">
                                        <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-600" style="width: 75%"></div>
                                        </div>
                                        <span class="text-[10px] font-black text-slate-400 italic">75%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- TAB: EQUIPOS -->
            <div x-show="activeTab === 'teams'" x-transition class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">#</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Supervisor / Equipo</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Titulares</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Dep.</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Total Traspasos</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($rankingEquipos as $eq)
                            <tr class="group hover:bg-slate-50/50 transition-all">
                                <td class="px-8 py-5">
                                    <span class="text-xs font-black text-slate-300">#{{ $loop->iteration }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600">
                                            <i class="ph ph-shield-star"></i>
                                        </div>
                                        <span class="text-xs font-black text-slate-800 uppercase">{{ $eq->nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-xs font-bold text-slate-600">{{ $eq->total_titulares }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-xs font-bold text-indigo-600">{{ $eq->total_dep ?? 0 }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="px-4 py-1.5 bg-indigo-600 text-white text-[10px] font-black rounded-xl shadow-lg shadow-indigo-100">
                                        {{ $eq->total_titulares + $eq->total_dep }}
                                    </span>
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
        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [
                    {
                        label: 'Efectivos',
                        data: {!! json_encode($chartData['efectivos']) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 4,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Solicitudes',
                        data: {!! json_encode($chartData['generados']) !!},
                        borderColor: '#e2e8f0',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.4,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        border: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                    }
                }
            }
        });
    });
</script>
@endsection
