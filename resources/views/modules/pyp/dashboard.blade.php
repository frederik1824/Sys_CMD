@extends('layouts.app')

@section('content')
<div class="p-8 page-transition" x-data="{ search: '' }">
    <!-- Header: Strategic Vision -->
    <div class="flex justify-between items-end mb-10">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                    <i class="ph-fill ph-activity text-2xl"></i>
                </div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Programa PyP</h1>
            </div>
            <p class="text-slate-500 font-medium max-w-2xl italic">Estratificación de riesgo clínico y monitoreo preventivo de la cartera de afiliados.</p>
        </div>
        <div class="flex gap-3">
            <button class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="ph ph-export text-lg"></i> Reporte Diario
            </button>
            <a href="{{ route('pyp.afiliados.index') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-xl shadow-indigo-100">
                <i class="ph ph-plus-circle text-lg"></i> Nueva Evaluación
            </a>
        </div>
    </div>

    <!-- KPI Grid: Clinical Pulse -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
        <!-- Total Card -->
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="ph-fill ph-users-three text-8xl text-indigo-900"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Población PyP</p>
            <h3 class="text-4xl font-black text-slate-900 leading-none mb-4">{{ number_format($stats['total']) }}</h3>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-xl w-fit">
                <i class="ph ph-chart-line-up font-bold"></i>
                <span class="text-[10px] font-black">ACTIVOS</span>
            </div>
        </div>

        <!-- Alto Riesgo -->
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-rose-100/50 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 border-l-4 border-l-rose-500">
            <p class="text-[10px] font-black uppercase tracking-widest text-rose-500 mb-1">Alto Riesgo</p>
            <h3 class="text-4xl font-black text-slate-900 leading-none mb-4">{{ number_format($stats['alto']) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 italic">Requieren Intervención</p>
        </div>

        <!-- Moderado -->
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-amber-100/50 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 border-l-4 border-l-amber-500">
            <p class="text-[10px] font-black uppercase tracking-widest text-amber-600 mb-1">Riesgo Moderado</p>
            <h3 class="text-4xl font-black text-slate-900 leading-none mb-4">{{ number_format($stats['moderado']) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 italic">Seguimiento Bimestral</p>
        </div>

        <!-- Bajo Riesgo -->
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-emerald-100/50 relative overflow-hidden group hover:scale-[1.02] transition-all duration-300 border-l-4 border-l-emerald-500">
            <p class="text-[10px] font-black uppercase tracking-widest text-emerald-600 mb-1">Riesgo Bajo</p>
            <h3 class="text-4xl font-black text-slate-900 leading-none mb-4">{{ number_format($stats['bajo']) }}</h3>
            <p class="text-[10px] font-bold text-slate-400 italic">Control Preventivo</p>
        </div>

        <!-- Descompensados -->
        <div class="bg-slate-900 p-6 rounded-[2rem] shadow-2xl relative overflow-hidden group hover:scale-[1.02] transition-all duration-300">
            <div class="absolute -bottom-4 -right-4 p-8 opacity-20">
                <i class="ph-fill ph-warning-octagon text-6xl text-rose-500 animate-pulse"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Descompensados</p>
            <h3 class="text-4xl font-black text-white leading-none mb-4">{{ number_format($stats['descompensados']) }}</h3>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-rose-500/20 text-rose-400 rounded-xl w-fit">
                <i class="ph ph-warning"></i>
                <span class="text-[10px] font-black">ALERTA ROJA</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Charts Area -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h4 class="text-lg font-black text-slate-900">Estratificación Poblacional</h4>
                        <p class="text-xs text-slate-400 font-medium">Distribución por programa y nivel de riesgo.</p>
                    </div>
                    <select class="bg-slate-50 border-none rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 px-4 py-2 outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option>Últimos 30 días</option>
                        <option>Año Actual</option>
                    </select>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Donut Chart Real -->
                    <div class="aspect-square bg-white rounded-[2rem] flex flex-col items-center justify-center p-8 relative">
                        <canvas id="riskChart"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-3xl font-black text-slate-900">{{ $stats['total'] }}</span>
                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 text-center">Total<br>Afiliados</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($programas as $prog)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:shadow-lg transition-all group">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $prog->color }}">
                                        <i class="ph-fill {{ $prog->icon }}"></i>
                                    </div>
                                    <span class="text-xs font-black text-slate-700">{{ $prog->nombre }}</span>
                                </div>
                                <span class="text-xs font-black text-indigo-600">{{ $prog->expedientes_count }}</span>
                            </div>
                            <div class="w-full bg-slate-200 h-1.5 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full group-hover:scale-x-105 transition-transform origin-left" style="width: {{ $stats['total'] > 0 ? ($prog->expedientes_count / $stats['total'] * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- List: Recent Activity -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
                <h4 class="text-lg font-black text-slate-900 mb-6">Programas Preventivos Activos</h4>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-slate-50">
                                <th class="pb-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Programa</th>
                                <th class="pb-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Población</th>
                                <th class="pb-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Estatus Salud</th>
                                <th class="pb-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Última Actividad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($programas as $prog)
                            <tr>
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2 h-2 rounded-full" style="background-color: {{ $prog->color }}"></div>
                                        <span class="text-sm font-bold text-slate-700">{{ $prog->nombre }}</span>
                                    </div>
                                </td>
                                <td class="py-4 font-black text-slate-600">{{ $prog->expedientes_count }}</td>
                                <td class="py-4">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg">OPERATIVO</span>
                                </td>
                                <td class="py-4 text-xs font-medium text-slate-400 italic">Actualizado hoy</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar: Critical Care & Alerts -->
        <div class="space-y-8">
            <div class="bg-rose-50 p-8 rounded-[2.5rem] border border-rose-100 shadow-xl shadow-rose-200/20 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-rose-200/30 rounded-full blur-3xl"></div>
                <h4 class="text-lg font-black text-rose-900 mb-6 flex items-center gap-2">
                    <i class="ph-fill ph-warning"></i> Cuidados Críticos
                </h4>
                <div class="space-y-4 relative z-10">
                    @forelse($alertas as $alert)
                    <div class="bg-white p-4 rounded-2xl border border-rose-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-rose-50 rounded-full flex items-center justify-center shrink-0">
                                <i class="ph-fill ph-user-circle text-2xl text-rose-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-black text-slate-900 truncate">{{ $alert->afiliado->nombre_completo }}</p>
                                <p class="text-[10px] font-bold text-rose-500 uppercase tracking-tighter">{{ $alert->riesgo_nivel }} Riesgo • {{ $alert->estado_clinico }}</p>
                                <div class="mt-3 flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-slate-400">Sin seguimiento hace 7d+</span>
                                    <button class="w-8 h-8 bg-slate-900 text-white rounded-lg flex items-center justify-center hover:bg-rose-600 transition-colors">
                                        <i class="ph-fill ph-phone-call"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="ph ph-check-circle text-emerald-500 text-4xl mb-2"></i>
                        <p class="text-xs font-bold text-slate-500">Sin alertas pendientes</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
                <h4 class="text-lg font-black text-slate-900 mb-6">Gestión de Cartera</h4>
                <div class="grid grid-cols-2 gap-4">
                    <button class="flex flex-col items-center gap-3 p-4 bg-slate-50 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition-all group">
                        <i class="ph ph-magnifying-glass text-2xl text-slate-400 group-hover:text-indigo-600"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Buscar Afiliado</span>
                    </button>
                    <button class="flex flex-col items-center gap-3 p-4 bg-slate-50 rounded-2xl hover:bg-emerald-50 hover:text-emerald-600 transition-all group">
                        <i class="ph ph-user-plus text-2xl text-slate-400 group-hover:text-emerald-600"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Matriculación</span>
                    </button>
                    <button class="flex flex-col items-center gap-3 p-4 bg-slate-50 rounded-2xl hover:bg-amber-50 hover:text-amber-600 transition-all group">
                        <i class="ph ph-calendar text-2xl text-slate-400 group-hover:text-amber-600"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Agenda PyP</span>
                    </button>
                    <button class="flex flex-col items-center gap-3 p-4 bg-slate-50 rounded-2xl hover:bg-indigo-50 hover:text-indigo-600 transition-all group">
                        <i class="ph ph-file-pdf text-2xl text-slate-400 group-hover:text-indigo-600"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Descargar KPIs</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('riskChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Alto', 'Moderado', 'Bajo'],
                datasets: [{
                    data: [{{ $stats['alto'] }}, {{ $stats['moderado'] }}, {{ $stats['bajo'] }}],
                    backgroundColor: ['#f43f5e', '#f59e0b', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                cutout: '80%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    });
</script>
@endpush
