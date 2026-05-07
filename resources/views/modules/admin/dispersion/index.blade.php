@extends('layouts.app')

@section('content')
<div class="space-y-10 animate-in fade-in slide-in-from-bottom-4 duration-700" x-data="{}">
    {{-- Header Ejecutivo --}}
    <div class="flex items-center justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 bg-slate-900 rounded-2xl flex items-center justify-center shadow-2xl shadow-slate-900/20 rotate-3">
                    <i class="ph-fill ph-chart-pie-slice text-2xl text-emerald-400"></i>
                </div>
                <h2 class="text-4xl font-black tracking-tight text-slate-900">Executive Insight</h2>
            </div>
            <p class="text-slate-500 font-bold ml-16 uppercase tracking-[0.2em] text-[10px]">Portal de Control de Dispersión y Auditoría Mensual</p>
        </div>

        <button @click="$dispatch('open-modal', 'create-period')" 
                class="flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-xs uppercase tracking-widest hover:bg-emerald-600 hover:scale-105 hover:rotate-1 transition-all shadow-2xl shadow-slate-900/20">
            <i class="ph ph-plus-circle text-xl"></i>
            Nuevo Periodo Fiscal
        </button>
    </div>

    {{-- KPI Cards - Executive Glassmorphism --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- KPI: Afiliados -->
        <div class="relative group overflow-hidden bg-gradient-to-br from-slate-900 to-slate-800 p-8 rounded-[3rem] shadow-2xl transition-all hover:-translate-y-2">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-emerald-500/10 rounded-2xl border border-emerald-500/20">
                        <i class="ph ph-users-four text-2xl text-emerald-400"></i>
                    </div>
                    <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20">Audited</span>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-white tabular-nums">{{ number_format($stats['total_afiliados'] ?? 0) }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Afiliados PDSS</p>
                </div>
            </div>
        </div>

        <!-- KPI: Montos -->
        <div class="relative group overflow-hidden bg-white p-8 rounded-[3rem] shadow-xl border border-slate-100 transition-all hover:-translate-y-2">
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-blue-500/10 rounded-2xl border border-blue-500/20 text-blue-600">
                        <i class="ph ph-bank text-2xl"></i>
                    </div>
                    <div class="flex items-center gap-1 text-[10px] font-black text-blue-600 uppercase tracking-widest">
                        <i class="ph ph-trend-up"></i> +2.4%
                    </div>
                </div>
                <div>
                    <h3 class="text-3xl font-black text-slate-900 tabular-nums">RD$ {{ number_format($stats['monto_total'] ?? 0, 2) }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Monto Total Dispersado</p>
                </div>
            </div>
        </div>

        <!-- KPI: Dispersados -->
        <div class="relative group overflow-hidden bg-white p-8 rounded-[3rem] shadow-xl border border-slate-100 transition-all hover:-translate-y-2">
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-violet-500/10 rounded-2xl border border-violet-500/20 text-violet-600">
                        <i class="ph ph-paper-plane-tilt text-2xl"></i>
                    </div>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-slate-900 tabular-nums">{{ number_format($stats['total_dispersados'] ?? 0) }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Afiliados Dispersados</p>
                </div>
            </div>
        </div>

        <!-- KPI: Bajas -->
        <div class="relative group overflow-hidden bg-white p-8 rounded-[3rem] shadow-xl border border-slate-100 transition-all hover:-translate-y-2">
            <div class="relative z-10 space-y-4">
                <div class="flex items-center justify-between">
                    <div class="p-3 bg-rose-500/10 rounded-2xl border border-rose-500/20 text-rose-600">
                        <i class="ph ph-user-minus text-2xl"></i>
                    </div>
                    <span class="text-[10px] font-black text-rose-600 uppercase tracking-widest bg-rose-500/10 px-3 py-1 rounded-full">Alert</span>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-slate-900 tabular-nums">{{ number_format($stats['total_bajas'] ?? 0) }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Bajas Recibidas</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Performance Chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-10 rounded-[3rem] shadow-xl border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h4 class="text-xl font-black text-slate-900">Tendencia de Gestión</h4>
                    <p class="text-xs font-medium text-slate-400">Comparativa semestral de afiliados y dispersión financiera.</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Afiliados</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Montos</span>
                    </div>
                </div>
            </div>
            <div id="performanceChart" style="min-height: 350px;"></div>
        </div>

        <div class="bg-slate-900 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute bottom-0 right-0 -mr-16 -mb-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="relative z-10 flex flex-col h-full justify-between">
                <div class="space-y-2">
                    <h4 class="text-xl font-black text-white">Estado de Cierre</h4>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Auditoría de Periodo Actual</p>
                </div>

                <div class="py-10">
                    <div class="relative h-48 w-48 mx-auto">
                        <svg class="h-full w-full" viewBox="0 0 36 36">
                            <path class="text-slate-800" stroke-dasharray="100, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3"></path>
                            <path class="text-emerald-500 animate-pulse" stroke-dasharray="85, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-4xl font-black text-white">85%</span>
                            <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest">Validado</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-slate-400">
                        <span>Consolidación</span>
                        <span class="text-white">En Proceso</span>
                    </div>
                    <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full w-[85%] bg-emerald-500 rounded-full shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lista de Periodos Recientes --}}
    <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h4 class="text-xs font-black uppercase tracking-[0.3em] text-slate-900">Periodos Fiscales Recientes</h4>
            <a href="{{ route('dispersion.history') }}" class="text-[10px] font-black uppercase tracking-widest text-emerald-600 hover:text-emerald-700 transition-colors">
                Ver Histórico Completo <i class="ph ph-arrow-right"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                        <th class="px-8 py-5">Mes / Año</th>
                        <th class="px-8 py-5">Afiliados</th>
                        <th class="px-8 py-5">Dispersados</th>
                        <th class="px-8 py-5">Monto Total</th>
                        <th class="px-8 py-5 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($periods as $period)
                    @php $pStats = App\Http\Controllers\Modules\Admin\DispersionController::getStats($period); @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 rounded-full {{ $period->status === 'closed' ? 'bg-slate-400' : 'bg-emerald-500' }}"></div>
                                <span class="text-sm font-black text-slate-900">{{ $period->month_name }} {{ $period->year }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-sm font-bold text-slate-600 tabular-nums">{{ number_format($pStats['total_afiliados']) }}</td>
                        <td class="px-8 py-5 text-sm font-bold text-slate-600 tabular-nums">{{ number_format($pStats['total_dispersados']) }}</td>
                        <td class="px-8 py-5 text-sm font-black text-emerald-600 tabular-nums">RD$ {{ number_format($pStats['monto_total'], 2) }}</td>
                        <td class="px-8 py-5 text-right">
                            <a href="{{ route('dispersion.show', $period->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-600 rounded-xl font-black text-[10px] uppercase tracking-widest group-hover:bg-slate-900 group-hover:text-white transition-all">
                                Gestionar
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL: Create Period --}}
    <x-modal name="create-period">
        <div class="p-8 space-y-6">
            <div class="space-y-1">
                <h3 class="text-xl font-black text-slate-900">Apertura de Periodo Fiscal</h3>
                <p class="text-xs font-medium text-slate-400 uppercase tracking-widest">Inicie un nuevo registro de dispersión mensual</p>
            </div>

            <form action="{{ route('dispersion.periods.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Año</label>
                        <select name="year" class="w-full bg-slate-50 border-none rounded-2xl px-4 py-3 font-black text-sm text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all">
                            @for($i = date('Y'); $i >= 2024; $i--)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Mes</label>
                        <select name="month" class="w-full bg-slate-50 border-none rounded-2xl px-4 py-3 font-black text-sm text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all">
                            @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $idx => $m)
                                <option value="{{ $idx + 1 }}" {{ (date('n') == $idx + 1) ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <button type="button" @click="$dispatch('close-modal', 'create-period')" class="flex-1 py-4 bg-slate-100 text-slate-500 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-[2] py-4 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-slate-900/10">
                        Aperturar Periodo
                    </button>
                </div>
            </form>
        </div>
    </x-modal>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const options = {
            series: [{
                name: 'Afiliados',
                type: 'column',
                data: @json($chartData['afiliados'])
            }, {
                name: 'Monto Dispersado',
                type: 'line',
                data: @json($chartData['montos'])
            }],
            chart: {
                height: 350,
                type: 'line',
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            stroke: {
                width: [0, 4],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    borderRadius: 10,
                    columnWidth: '35%',
                }
            },
            colors: ['#10b981', '#3b82f6'],
            dataLabels: {
                enabled: false,
            },
            labels: @json($chartData['labels']),
            xaxis: {
                type: 'category',
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#94a3b8',
                        fontWeight: 700,
                        fontSize: '10px'
                    }
                }
            },
            yaxis: [{
                title: { text: 'Afiliados', style: { color: '#10b981', fontWeight: 900, textTransform: 'uppercase' } },
                labels: { style: { colors: '#10b981', fontWeight: 700 } }
            }, {
                opposite: true,
                title: { text: 'Montos (RD$)', style: { color: '#3b82f6', fontWeight: 900, textTransform: 'uppercase' } },
                labels: { 
                    style: { colors: '#3b82f6', fontWeight: 700 },
                    formatter: function (val) { return "RD$ " + (val / 1000000).toFixed(1) + "M"; }
                }
            }],
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
            },
            legend: { show: false }
        };

        const chart = new ApexCharts(document.querySelector("#performanceChart"), options);
        chart.render();
    });
</script>
@endsection
