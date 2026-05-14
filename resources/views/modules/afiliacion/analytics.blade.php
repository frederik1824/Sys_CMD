@extends('layouts.app')

@section('content')
<div class="p-8">
    <!-- Header: Strategic Vision -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-[1.25rem] bg-slate-900 flex items-center justify-center text-white shadow-xl shadow-slate-200">
                    <i class="ph-fill ph-chart-line-up text-2xl"></i>
                </div>
                <h1 class="text-3xl font-black tracking-tighter text-slate-900">Command Center <span class="text-indigo-600">Sisalril</span></h1>
            </div>
            <p class="text-slate-500 font-medium max-w-2xl text-sm">Monitoreo de cumplimiento normativo y eficiencia operativa para el régimen de pensionados.</p>
        </div>

        <div class="flex items-center gap-2 bg-white p-2 rounded-[1.5rem] border border-slate-100 shadow-xl shadow-slate-200/40">
            <a href="?periodo=month" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $periodo === 'month' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50' }}">Mensual</a>
            <a href="?periodo=quarter" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $periodo === 'quarter' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50' }}">Trimestral</a>
            <a href="?periodo=year" class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ $periodo === 'year' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50' }}">Anual</a>
        </div>
    </div>

    <!-- KPI Grid: The Strategic Core -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- Promedio Confirmación Pensionados (TSS - MANDATO SISALRIL) -->
        <div class="bg-slate-900 p-8 rounded-[2.5rem] border border-slate-800 shadow-2xl shadow-slate-900/40 group hover:-translate-y-1 transition-all duration-500 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/10 rounded-full blur-2xl"></div>
            <div class="w-14 h-14 bg-indigo-500/20 rounded-2xl flex items-center justify-center text-indigo-400 mb-6 group-hover:scale-110 transition-transform">
                <i class="ph-fill ph-identification-card text-2xl"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400/60 mb-2">Promedio Confirmación Pensionados</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-black text-white tracking-tighter">{{ number_format($tatPensionadosTSS, 1) }}</p>
                <span class="text-xs font-bold text-indigo-300">Días</span>
            </div>
            <div class="mt-4 inline-flex items-center gap-2 px-3 py-1 bg-indigo-500/10 rounded-lg text-[9px] font-black text-indigo-300 uppercase tracking-widest border border-indigo-500/20">
                <i class="ph-fill ph-shield-check"></i> Cumplimiento SISALRIL
            </div>
        </div>

        <!-- TAT Promedio General -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 group hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-500">
            <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                <i class="ph-fill ph-timer text-2xl"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">TAT Promedio General</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ number_format($tatPromedio, 1) }}</p>
                <span class="text-xs font-bold text-slate-400">Horas</span>
            </div>
            <p class="mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Meta de Servicio: <span class="text-indigo-600">24h</span></p>
        </div>

        <!-- Tasa de Rechazo -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 group hover:shadow-2xl hover:shadow-rose-500/10 transition-all duration-500">
            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 mb-6 group-hover:scale-110 transition-transform">
                <i class="ph-fill ph-x-circle text-2xl"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Tasa de Rechazo Global</p>
            <div class="flex items-baseline gap-2">
                <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ number_format($tasaRechazo, 1) }}</p>
                <span class="text-xs font-bold text-slate-400">%</span>
            </div>
            <p class="mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Incidentes de Calidad</p>
        </div>

        <!-- CSAT -->
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 group hover:shadow-2xl hover:shadow-amber-500/10 transition-all duration-500">
            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mb-6 group-hover:scale-110 transition-transform">
                <i class="ph-fill ph-smiley text-2xl"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Satisfacción Afiliado (CSAT)</p>
            <div class="flex items-center gap-1">
                <p class="text-4xl font-black text-slate-900 tracking-tighter mr-2">{{ number_format($csatPromedio, 1) }}</p>
                @for($i=1; $i<=5; $i++)
                    <i class="ph-fill ph-star {{ $i <= round($csatPromedio) ? 'text-amber-400' : 'text-slate-100' }} text-xl"></i>
                @endfor
            </div>
            <p class="mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Voz del Usuario</p>
        </div>
    </div>

    <!-- SISALRIL COMPLIANCE: ENVEJECIMIENTO Y EXCEPCIONES -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- Dashboard de Envejecimiento (Semáforo) -->
        <div class="lg:col-span-1 bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-slate-200/20">
            <div class="mb-10">
                <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none mb-1">Envejecimiento de Solicitudes</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Estado de cumplimiento de 90 días</p>
            </div>

            <div class="space-y-8">
                <!-- 0-30 Días -->
                <div class="group">
                    <div class="flex justify-between items-end mb-4">
                        <div>
                            <span class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1 block">Rango Óptimo</span>
                            <span class="text-sm font-black text-slate-900">0 - 30 Días</span>
                        </div>
                        <span class="text-2xl font-black text-slate-900">{{ number_format($pensionadosAging->range_30) }}</span>
                    </div>
                    <div class="h-4 w-full bg-emerald-50 rounded-2xl overflow-hidden border border-emerald-100/50">
                        <div class="h-full bg-emerald-500 transition-all duration-1000 group-hover:opacity-80" style="width: {{ $pensionadosAging->total > 0 ? ($pensionadosAging->range_30 / $pensionadosAging->total) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- 31-60 Días -->
                <div class="group">
                    <div class="flex justify-between items-end mb-4">
                        <div>
                            <span class="text-[10px] font-black text-amber-500 uppercase tracking-widest mb-1 block">En Observación</span>
                            <span class="text-sm font-black text-slate-900">31 - 60 Días</span>
                        </div>
                        <span class="text-2xl font-black text-slate-900">{{ number_format($pensionadosAging->range_60) }}</span>
                    </div>
                    <div class="h-4 w-full bg-amber-50 rounded-2xl overflow-hidden border border-amber-100/50">
                        <div class="h-full bg-amber-500 transition-all duration-1000 group-hover:opacity-80" style="width: {{ $pensionadosAging->total > 0 ? ($pensionadosAging->range_60 / $pensionadosAging->total) * 100 : 0 }}%"></div>
                    </div>
                </div>

                <!-- 61-90+ Días -->
                <div class="group">
                    <div class="flex justify-between items-end mb-4">
                        <div>
                            <span class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1 block">Riesgo Crítico</span>
                            <span class="text-sm font-black text-slate-900">61 - 90+ Días</span>
                        </div>
                        <span class="text-2xl font-black text-slate-900">{{ number_format($pensionadosAging->range_90) }}</span>
                    </div>
                    <div class="h-4 w-full bg-rose-50 rounded-2xl overflow-hidden border border-rose-100/50">
                        <div class="h-full bg-rose-500 transition-all duration-1000 group-hover:opacity-80" style="width: {{ $pensionadosAging->total > 0 ? ($pensionadosAging->range_90 / $pensionadosAging->total) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auditoría de No Coincidencias (Excepciones de Pago) -->
        <div class="lg:col-span-2 bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-slate-200/20">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none mb-1">Excepciones de Pago</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Pensionados con >30 días sin pago detectado</p>
                </div>
                <a href="{{ route('afiliacion.analytics.export', ['periodo' => $periodo]) }}" target="_blank" class="flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-900/10">
                    <i class="ph ph-file-pdf text-lg"></i>
                    Exportar Reporte SISALRIL
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left border-b border-slate-50">
                            <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Solicitud</th>
                            <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Afiliado</th>
                            <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Días en Espera</th>
                            <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($excepcionesPago as $ex)
                        <tr class="group/row hover:bg-slate-50/50 transition-colors">
                            <td class="py-5 text-sm font-black text-slate-900">{{ $ex->codigo_solicitud }}</td>
                            <td class="py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-tight">{{ $ex->nombre_completo }}</span>
                                    <span class="text-[10px] font-medium text-slate-400">{{ $ex->cedula }}</span>
                                </div>
                            </td>
                            <td class="py-5">
                                <span class="px-3 py-1 bg-rose-50 text-rose-600 rounded-lg text-[10px] font-black border border-rose-100">
                                    {{ now()->diffInDays($ex->created_at) }} Días
                                </span>
                            </td>
                            <td class="py-5 text-right">
                                <a href="{{ route('afiliacion.show', $ex) }}" class="inline-flex w-8 h-8 items-center justify-center bg-slate-100 text-slate-400 rounded-lg hover:bg-indigo-600 hover:text-white transition-all">
                                    <i class="ph ph-eye text-lg"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-300 font-bold italic text-sm">No hay excepciones críticas detectadas en este periodo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Chart: Trends -->
        <div class="lg:col-span-2 bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-slate-200/20">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none mb-1">Tendencia de Eficiencia Operativa</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Histórico comparativo de los últimos meses</p>
                </div>
            </div>

            <div class="h-80 w-full flex items-end justify-between px-4 pb-10 gap-6">
                @foreach($tendencia as $data)
                <div class="flex-1 flex flex-col items-center gap-6 group/bar">
                    <div class="w-full flex flex-col-reverse gap-1.5 h-64 relative">
                        <div class="bg-slate-900 rounded-t-xl transition-all group-hover/bar:bg-indigo-600" style="height: {{ ($data->completadas / max($data->total, 1)) * 100 }}%">
                            <div class="opacity-0 group-hover/bar:opacity-100 absolute -top-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-black px-3 py-1.5 rounded-lg whitespace-nowrap transition-all">
                                {{ $data->completadas }} Completas
                            </div>
                        </div>
                        <div class="bg-slate-100 rounded-xl" style="height: {{ 100 - (($data->completadas / max($data->total, 1)) * 100) }}%"></div>
                    </div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $data->mes }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Right Side: Type Breakdown -->
        <div class="bg-white p-10 rounded-[3.5rem] border border-slate-100 shadow-2xl shadow-slate-200/20">
            <div class="mb-10">
                <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none mb-1">Efectividad por Tipo</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Métricas de productividad Sisalril</p>
            </div>

            <div class="space-y-8">
                @foreach($metricasPorTipo as $tipo)
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $tipo->nombre }}</span>
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[10px] font-black">{{ number_format($tipo->tat, 1) }}h TAT</span>
                    </div>
                    <div class="h-3 w-full bg-slate-50 rounded-full overflow-hidden flex shadow-inner">
                        <div class="h-full bg-slate-900 rounded-full" style="width: {{ min(($tipo->total / max($metricasPorTipo->sum('total'), 1)) * 100 * 2, 100) }}%"></div>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest">
                        <i class="ph ph-stack"></i> {{ $tipo->total }} Solicitudes Procesadas
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-12 p-6 bg-slate-50 rounded-[2rem] border border-slate-100 relative overflow-hidden group">
                <i class="ph ph-shield-check absolute -right-4 -bottom-4 text-8xl text-slate-200/50 group-hover:scale-110 transition-transform"></i>
                <div class="relative z-10">
                    <h4 class="text-[11px] font-black text-slate-900 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <i class="ph ph-info-bold text-indigo-600"></i> Nota de Auditoría
                    </h4>
                    <p class="text-[10px] font-medium text-slate-500 leading-relaxed">
                        Los tiempos de respuesta (TAT) están calculados en horas naturales desde la radicación inicial hasta la confirmación de dispersión vía TSS o cierre definitivo.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
