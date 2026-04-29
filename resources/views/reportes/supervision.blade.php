@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-fade-in pb-12">
    {{-- Header & Filters --}}
    <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-slate-100">
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6">
            <div>
                <h2 class="text-3xl font-black text-slate-800 tracking-tighter">Supervisión Ejecutiva</h2>
                <p class="text-slate-400 text-sm font-medium mt-1">Monitoreo en tiempo real del flujo de servicios.</p>
            </div>
            
            <form method="GET" action="{{ route('reportes.supervision') }}" class="flex flex-wrap items-center gap-3 bg-slate-50 p-2 rounded-3xl border border-slate-100">
                <input type="hidden" name="context" value="{{ request('context') }}">
                <div class="flex items-center gap-2 px-3 border-r border-slate-200">
                    <span class="material-symbols-outlined text-slate-400 text-sm">calendar_month</span>
                    <input type="date" name="fecha_desde" value="{{ $fecha_desde }}" class="bg-transparent border-0 text-xs font-bold text-slate-600 focus:ring-0 p-1 w-32">
                    <span class="text-slate-300">/</span>
                    <input type="date" name="fecha_hasta" value="{{ $fecha_hasta }}" class="bg-transparent border-0 text-xs font-bold text-slate-600 focus:ring-0 p-1 w-32">
                </div>

                <select name="corte_id" class="bg-transparent border-0 text-xs font-bold text-slate-600 focus:ring-0 px-3 min-w-[120px]">
                    <option value="">Todos los Cortes</option>
                    @foreach($cortes as $c)
                        <option value="{{ $c->id }}" {{ $corte_id == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>

                <select name="responsable_id" class="bg-transparent border-0 text-xs font-bold text-slate-600 focus:ring-0 px-3 min-w-[140px] border-l border-slate-200">
                    <option value="">Responsables</option>
                    @foreach($responsables as $r)
                        <option value="{{ $r->id }}" {{ $responsable_id == $r->id ? 'selected' : '' }}>{{ $r->nombre }}</option>
                    @endforeach
                </select>

                <button type="submit" class="bg-primary text-white p-2.5 rounded-2xl hover:bg-slate-800 transition-all shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-lg">filter_alt</span>
                </button>

                <a href="{{ route('reportes.export', request()->merge(['context' => request('context', 'executive')])->all()) }}" class="bg-white text-slate-600 p-2.5 rounded-2xl border border-slate-200 hover:bg-slate-100 transition-all" title="Exportar Datos">
                    <span class="material-symbols-outlined text-lg">download</span>
                </a>
            </form>
        </div>
    </div>

    {{-- KPI Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- KPI: Ingresos --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                        <span class="material-symbols-outlined">inbox_customize</span>
                    </span>
                </div>
                <h4 class="text-[0.6rem] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Total Ingresos</h4>
                <div class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['ingresos']) }}</div>
                <div class="text-[0.65rem] font-bold text-slate-400 mt-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs text-blue-400">trending_up</span> Periodo seleccionado
                </div>
            </div>
        </div>

        {{-- KPI: Salidas --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                        <span class="material-symbols-outlined">outbox_alt</span>
                    </span>
                </div>
                <h4 class="text-[0.6rem] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Servicios Listos</h4>
                <div class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['salidas']) }}</div>
                <div class="text-[0.65rem] font-bold text-emerald-500 mt-2 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">check_circle</span> Proceso completado
                </div>
            </div>
        </div>

        {{-- KPI: Tasa --}}
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group text-white">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/20 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="p-3 bg-white/10 text-white rounded-2xl">
                        <span class="material-symbols-outlined">speed</span>
                    </span>
                </div>
                <h4 class="text-[0.6rem] font-black uppercase text-slate-300/50 tracking-[0.2em] mb-1">Tasa de Entrega</h4>
                <div class="text-4xl font-black text-white tracking-tighter">{{ round($stats['tasa_entrega'], 1) }}%</div>
                <div class="w-full bg-white/10 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-white h-full rounded-full transition-all duration-1000" style="width: {{ $stats['tasa_entrega'] }}%"></div>
                </div>
            </div>
        </div>

        {{-- KPI: Por Liquidar --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="p-3 bg-rose-50 text-rose-600 rounded-2xl">
                        <span class="material-symbols-outlined">account_balance_wallet</span>
                    </span>
                </div>
                <h4 class="text-[0.6rem] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Pendiente Cobro</h4>
                <div class="text-4xl font-black text-slate-800 tracking-tighter">RD$ {{ number_format($stats['por_liquidar'], 0) }}</div>
                <div class="text-[0.65rem] font-black text-rose-500 mt-2 flex items-center gap-1 uppercase tracking-tighter">
                    <span class="material-symbols-outlined text-xs">warning</span> Por Liquidar SAFESURE
                </div>
            </div>
        </div>
    </div>

    {{-- Main Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Trend Chart --}}
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Tendencia de Ingresos</h3>
                    <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest">Volumen diario registrado</p>
                </div>
                <span class="p-2 bg-slate-50 text-slate-400 rounded-xl">
                    <span class="material-symbols-outlined text-sm">show_chart</span>
                </span>
            </div>
            <div class="h-80 w-full">
                <canvas id="tendenciaChart"></canvas>
            </div>
        </div>

        {{-- Status Distribution --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Estado Operativo</h3>
                    <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest">Distribución del Flujo</p>
                </div>
                <span class="p-2 bg-slate-50 text-slate-400 rounded-xl">
                    <span class="material-symbols-outlined text-sm">pie_chart</span>
                </span>
            </div>
            <div class="h-64 w-full flex items-center justify-center">
                <canvas id="estadoChart"></canvas>
            </div>
            <div class="mt-8 space-y-2">
                @foreach($estados->take(4) as $e)
                <div class="flex items-center justify-between text-xs py-2 border-b border-slate-50 last:border-0">
                    <span class="text-slate-500 font-bold">{{ $e->nombre }}</span>
                    <span class="text-slate-800 font-black">{{ number_format($e->afiliados_count) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Secondary Charts Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Performance by Responsable --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter mb-8">Top Responsables</h3>
            <div class="h-72 w-full">
                <canvas id="responsableChart"></canvas>
            </div>
        </div>

        {{-- Volume by Corte --}}
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <h3 class="text-lg font-black text-slate-800 uppercase tracking-tighter mb-8">Producción por Corte</h3>
            <div class="h-72 w-full">
                <canvas id="corteChart"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Shared Options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            }
        };

        // 1. Tendencia Chart
        new Chart(document.getElementById('tendenciaChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($tendencia->pluck('fecha')) !!},
                datasets: [{
                    label: 'Ingresos',
                    data: {!! json_encode($tendencia->pluck('total_ingreso')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { border: { dash: [4, 4] } }
                }
            }
        });

        // 2. Estado Chart
        new Chart(document.getElementById('estadoChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($estados->pluck('nombre')) !!},
                datasets: [{
                    data: {!! json_encode($estados->pluck('afiliados_count')) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1', '#94a3b8'],
                    borderWidth: 0,
                    borderRadius: 8,
                    cutout: '75%'
                }]
            },
            options: commonOptions
        });

        // 3. Responsable Chart
        new Chart(document.getElementById('responsableChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($responsables_data->map(fn($r) => explode(' ', $r->nombre)[0])) !!},
                datasets: [{
                    data: {!! json_encode($responsables_data->pluck('afiliados_count')) !!},
                    backgroundColor: '#6366f1',
                    borderRadius: 12
                }]
            },
            options: {
                ...commonOptions,
                indexAxis: 'y',
                scales: {
                    x: { display: false },
                    y: { grid: { display: false } }
                }
            }
        });

        // 4. Corte Chart
        new Chart(document.getElementById('corteChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($cortes_data->pluck('nombre')) !!},
                datasets: [{
                    data: {!! json_encode($cortes_data->pluck('afiliados_count')) !!},
                    backgroundColor: '#f59e0b',
                    borderRadius: 12
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    x: { grid: { display: false } },
                    y: { display: false }
                }
            }
        });
    });
</script>
@endsection
