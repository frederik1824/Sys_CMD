@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto">
    <!-- HEADER -->
    <div class="mb-10 flex flex-col xl:flex-row justify-between items-start xl:items-end gap-8">
        <div>
            <h1 class="text-5xl font-black tracking-tighter text-slate-900 leading-none mb-3">{{ $tituloPagina }}</h1>
            <p class="text-slate-500 font-medium text-xl">Métricas de rendimiento, SLA y volumen operativo.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-4 w-full xl:w-auto">
            @if($isAdmin)
            <form action="{{ route('afiliacion.reports') }}" method="GET" class="flex items-center gap-3 bg-white p-2 pl-6 rounded-[24px] border border-slate-100 shadow-sm group">
                <i class="ph-bold ph-funnel text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                <select name="user_id" onchange="this.form.submit()" 
                    class="bg-transparent border-none focus:ring-0 text-xs font-black uppercase tracking-widest text-slate-700 cursor-pointer min-w-[250px]">
                    <option value="">Visión Global de Planta</option>
                    @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        Representante: {{ $u->name }}
                    </option>
                    @endforeach
                </select>
                @if(request('user_id'))
                <a href="{{ route('afiliacion.reports') }}" class="w-10 h-10 flex items-center justify-center text-rose-500 hover:bg-rose-50 rounded-full transition-colors">
                    <i class="ph ph-x-circle text-xl"></i>
                </a>
                @endif
            </form>
            @endif

            <div class="bg-white px-8 py-5 rounded-[24px] border border-slate-100 shadow-sm flex items-center gap-5">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                    <i class="ph-bold ph-calendar-check text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Hoy</p>
                    <p class="text-2xl font-black text-slate-900 leading-none">{{ $hoy }} <span class="text-xs font-bold text-slate-400">Trámites</span></p>
                </div>
            </div>

            <button onclick="window.print()" class="bg-slate-900 text-white rounded-[24px] px-10 py-5 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-slate-800 transition-all flex items-center gap-3 shadow-2xl shadow-slate-200 group no-print">
                <i class="ph ph-printer text-xl group-hover:scale-110 transition-transform"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- TOP KPI ROW -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm md:col-span-2">
            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-6">Cumplimiento de SLA (Aprobadas)</h3>
            <div class="flex items-center gap-12">
                <div class="relative w-32 h-32">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-50"/>
                        @php
                            $totalAprobadas = $dentroSla + $fueraSla;
                            $porcentajeSla = $totalAprobadas > 0 ? round(($dentroSla / $totalAprobadas) * 100) : 0;
                            $offsetSla = 364.4 - (364.4 * $porcentajeSla / 100);
                        @endphp
                        <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="12" fill="transparent" 
                            stroke-dasharray="364.4" stroke-dashoffset="{{ $offsetSla }}"
                            class="{{ $porcentajeSla > 80 ? 'text-emerald-500' : ($porcentajeSla > 50 ? 'text-amber-500' : 'text-rose-500') }} transition-all duration-1000"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-2xl font-black text-slate-900">{{ $porcentajeSla }}%</span>
                        <span class="text-[9px] font-black text-slate-400 uppercase">On Time</span>
                    </div>
                </div>
                <div class="flex-1 space-y-4">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            <span class="text-xs font-bold text-slate-600">Dentro de tiempo</span>
                        </div>
                        <span class="text-sm font-black text-slate-900">{{ $dentroSla }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <span class="text-xs font-bold text-slate-600">Fuera de tiempo</span>
                        </div>
                        <span class="text-sm font-black text-slate-900">{{ $fueraSla }}</span>
                    </div>
                    <div class="pt-4 border-t border-slate-50">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tiempo Promedio de Cierre</p>
                        <p class="text-xl font-black text-indigo-600">{{ number_format($promedioResolucion, 1) }} <span class="text-xs">horas</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 p-8 rounded-[40px] shadow-2xl shadow-slate-200 flex flex-col justify-between overflow-hidden relative group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <h3 class="text-sm font-black text-slate-500 uppercase tracking-widest relative z-10">Total Histórico</h3>
            <div class="relative z-10">
                <div class="text-5xl font-black text-white mb-2">{{ array_sum(array_column($porEstado->toArray(), 'total')) }}</div>
                <p class="text-slate-400 text-xs font-bold">Solicitudes gestionadas.</p>
            </div>
            <div class="mt-6 pt-6 border-t border-slate-800 relative z-10">
                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Aprobadas</p>
                        <p class="text-xs font-black text-emerald-400">{{ $porEstado->where('estado', 'Aprobada')->first()->total ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Devueltas</p>
                        <p class="text-xs font-black text-orange-400">{{ $totalDevueltas }}</p>
                    </div>
                    <div>
                        <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Rechazos</p>
                        <p class="text-xs font-black text-rose-400">{{ $porEstado->where('estado', 'Rechazada')->first()->total ?? 0 }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-800">
                    <p class="text-[9px] font-black text-slate-500 uppercase mb-1">Tasa de Devolución (Calidad)</p>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-1.5 bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500" style="width: {{ $tasaDevolucion }}%"></div>
                        </div>
                        <span class="text-xs font-black text-orange-400">{{ $tasaDevolucion }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-indigo-600 p-8 rounded-[40px] shadow-2xl shadow-indigo-100 flex flex-col justify-between overflow-hidden relative group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <h3 class="text-sm font-black text-indigo-300 uppercase tracking-widest relative z-10">Distribución Prioridad</h3>
            <div class="space-y-3 relative z-10">
                @foreach($porPrioridad as $p)
                <div class="space-y-1">
                    <div class="flex justify-between text-[9px] font-black uppercase text-indigo-100">
                        <span>{{ $p->prioridad }}</span>
                        <span>{{ $p->total }}</span>
                    </div>
                    <div class="w-full bg-indigo-800/50 h-1.5 rounded-full overflow-hidden">
                        @php $pw = array_sum(array_column($porPrioridad->toArray(), 'total')) > 0 ? ($p->total / array_sum(array_column($porPrioridad->toArray(), 'total'))) * 100 : 0; @endphp
                        <div class="bg-white h-full rounded-full" style="width: {{ $pw }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-6 pt-6 border-t border-indigo-500 relative z-10">
                <p class="text-[9px] font-bold text-indigo-100 italic">Sesgo hacia prioridades críticas.</p>
            </div>
        </div>
    </div>

    <!-- GRAPHS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-10 rounded-[40px] border border-slate-100 shadow-sm">
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Volumen por Proceso</h3>
            <div class="space-y-6">
                @foreach($porTipo as $tipo)
                <div class="space-y-2 group">
                    <div class="flex justify-between text-xs font-black uppercase tracking-widest text-slate-500">
                        <span class="group-hover:text-indigo-600 transition-colors">{{ $tipo->tipoSolicitud->nombre }}</span>
                        <span class="text-slate-900">{{ $tipo->total }}</span>
                    </div>
                    <div class="w-full bg-slate-50 h-4 rounded-full overflow-hidden p-1">
                        @php $w = array_sum(array_column($porTipo->toArray(), 'total')) > 0 ? ($tipo->total / array_sum(array_column($porTipo->toArray(), 'total'))) * 100 : 0; @endphp
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 h-full rounded-full transition-all duration-1000 group-hover:shadow-lg group-hover:shadow-indigo-500/20" style="width: {{ $w }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-10 rounded-[40px] border border-slate-100 shadow-sm">
            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Top Analistas (Productividad)</h3>
            <div class="space-y-4">
                @foreach($productividad as $p)
                <div class="flex items-center justify-between p-5 bg-slate-50 rounded-[32px] border border-slate-100 hover:border-indigo-200 transition-all group">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-white border-4 border-slate-100 flex items-center justify-center font-black text-slate-400 group-hover:text-indigo-600 group-hover:border-indigo-50 transition-all">
                            {{ substr($p->asignado->name, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-black text-slate-900 text-lg">{{ $p->asignado->name }}</p>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Resolutor Certificado</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-black text-slate-900">{{ $p->total }}</div>
                        <p class="text-[9px] font-black text-emerald-500 uppercase tracking-tighter">Aprobaciones</p>
                    </div>
                </div>
                @endforeach

                @if($productividad->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-200">
                        <i class="ph ph-users text-3xl"></i>
                    </div>
                    <p class="text-slate-400 font-bold italic">No hay datos de productividad acumulados.</p>
                </div>
                @endif
            </div>
        </div>
    <!-- CALENDARIO OPERATIVO -->
    <div class="mt-10 bg-white p-10 rounded-[40px] border border-slate-100 shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Calendario Operativo (Cierres del Mes)</h3>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ now()->translatedFormat('F Y') }}</span>
        </div>
        <div class="grid grid-cols-7 md:grid-cols-11 lg:grid-cols-16 xl:grid-cols-[repeat(auto-fill,minmax(45px,1fr))] gap-3">
            @for($i = 1; $i <= now()->daysInMonth; $i++)
                <div class="flex flex-col items-center justify-center p-3 rounded-2xl border {{ isset($productividadDiaria[$i]) ? 'bg-indigo-600 border-indigo-600 shadow-lg shadow-indigo-100' : 'bg-slate-50 border-slate-100 opacity-60' }} transition-all group hover:scale-110">
                    <span class="text-[9px] font-black {{ isset($productividadDiaria[$i]) ? 'text-indigo-200' : 'text-slate-400' }} mb-1">{{ $i }}</span>
                    <span class="text-sm font-black {{ isset($productividadDiaria[$i]) ? 'text-white' : 'text-slate-300' }}">
                        {{ $productividadDiaria[$i] ?? 0 }}
                    </span>
                </div>
            @endfor
        </div>
        <div class="mt-6 flex items-center gap-4 text-[9px] font-black text-slate-400 uppercase tracking-widest">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-indigo-600 rounded-md"></div>
                Día con Producción
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-slate-100 border border-slate-200 rounded-md"></div>
                Sin Cierres
            </div>
        </div>
    </div>
</div>
@endsection
