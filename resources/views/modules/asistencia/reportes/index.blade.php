@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-10 space-y-10 min-h-screen bg-[#f8fafc]">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <span class="text-primary/60">Análisis</span>
                <span class="ph ph-caret-right text-[10px]"></span>
                <span class="text-primary">Consolidado de Asistencia</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-[900] text-slate-900 tracking-tighter leading-none flex items-center gap-4">
                Reportes de <span class="text-primary italic font-light">Cumplimiento</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-2xl hidden md:block">
                    <i class="ph ph-chart-bar text-3xl"></i>
                </span>
            </h1>
            <p class="text-slate-500 font-medium text-lg">Métricas de puntualidad y productividad por rango de fechas.</p>
        </div>

        <div class="flex items-center gap-4">
            <a href="{{ route('asistencia.reportes.export', ['fecha_desde' => $fecha_desde, 'fecha_hasta' => $fecha_hasta]) }}" 
               class="bg-emerald-600 text-white rounded-2xl px-8 py-4 text-xs font-black uppercase tracking-widest shadow-xl shadow-emerald-200 hover:scale-[1.05] active:scale-[0.95] transition-all flex items-center gap-2">
                <i class="ph ph-microsoft-excel-logo text-xl"></i>
                Exportar CSV
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <form action="{{ route('asistencia.reportes.index') }}" method="GET" class="flex flex-wrap items-end gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Fecha Desde</label>
                <input type="date" name="fecha_desde" value="{{ $fecha_desde }}" class="bg-slate-50 border-none rounded-2xl px-6 py-3 text-sm font-bold focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ $fecha_hasta }}" class="bg-slate-50 border-none rounded-2xl px-6 py-3 text-sm font-bold focus:ring-2 focus:ring-primary/20">
            </div>
            <button type="submit" class="bg-slate-900 text-white rounded-2xl px-8 py-3 text-xs font-black uppercase tracking-widest hover:bg-primary transition-all">
                Filtrar Reporte
            </button>
        </form>
    </div>

    <!-- Tabla de Reporte -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
        <div class="p-8 border-b border-slate-50">
            <h3 class="text-xl font-[900] text-slate-900 tracking-tight">Resumen por Empleado</h3>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">Total acumulado del periodo seleccionado</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Empleado</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Departamento</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Días Lab.</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Tardanzas</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Horas Totales</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Cumplimiento</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($reporte as $row)
                    <tr class="group hover:bg-slate-50/40 transition-all duration-300">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $row->nombre }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $row->codigo }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $row->departamento }}</span>
                        </td>
                        <td class="px-8 py-6 text-center text-sm font-black text-slate-900">{{ $row->total_dias }}</td>
                        <td class="px-8 py-6 text-center">
                            @if($row->tardanzas > 0)
                                <span class="px-3 py-1 bg-rose-50 text-rose-600 text-[10px] font-black rounded-lg border border-rose-100">{{ $row->tardanzas }}</span>
                            @else
                                <span class="text-slate-300 font-bold">0</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-center text-sm font-black text-slate-900">{{ $row->horas_totales }}h</td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-black {{ $row->cumplimiento >= 90 ? 'text-emerald-600' : ($row->cumplimiento >= 70 ? 'text-amber-600' : 'text-rose-600') }}">
                                    {{ $row->cumplimiento }}%
                                </span>
                                <div class="w-24 h-1 bg-slate-100 rounded-full mt-2 overflow-hidden">
                                    <div class="h-full bg-{{ $row->cumplimiento >= 90 ? 'emerald' : ($row->cumplimiento >= 70 ? 'amber' : 'rose') }}-500 rounded-full" style="width: {{ $row->cumplimiento }}%"></div>
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
@endsection
