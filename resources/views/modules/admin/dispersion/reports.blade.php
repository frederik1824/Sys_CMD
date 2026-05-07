@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    {{-- Header --}}
    <div class="flex items-end justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-rose-500/10 rounded-lg">
                    <i class="ph-duotone ph-file-pdf text-2xl text-rose-600"></i>
                </div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900">Executive Reporting Hub</h2>
            </div>
            <p class="text-slate-500 font-medium ml-11">Centro de descarga de reportes institucionales y auditoría.</p>
        </div>
    </div>

    {{-- Report List --}}
    <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h4 class="text-xs font-black uppercase tracking-[0.3em] text-slate-900">Reportes Generados por Periodo</h4>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">PDF Timbrado Ready</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                        <th class="px-8 py-5">Periodo Fiscal</th>
                        <th class="px-8 py-5">Afiliados Totales</th>
                        <th class="px-8 py-5">Monto Dispersado</th>
                        <th class="px-8 py-5">Estado Reporte</th>
                        <th class="px-8 py-5 text-right">Acciones de Exportación</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($periods as $period)
                    @php 
                        $stats = App\Http\Controllers\Modules\Admin\DispersionController::getStats($period);
                    @endphp
                    <tr class="group hover:bg-slate-50/80 transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-600 font-black text-xs">
                                    {{ $period->year }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">{{ $period->month_name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Periodo Consolidado</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-black text-slate-700 tabular-nums">{{ number_format($stats['total_afiliados'] ?? 0) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-black text-emerald-600 tabular-nums">RD$ {{ number_format($stats['monto_total'] ?? 0, 2) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2 text-blue-600 font-black text-[10px] uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full w-fit border border-blue-100">
                                <i class="ph ph-check-circle"></i> Auditoría OK
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('dispersion.reports.show', $period->id) }}" class="flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10">
                                    <i class="ph ph-eye text-sm"></i>
                                    Ver Reporte
                                </a>
                                <a href="{{ route('dispersion.report', $period->id) }}" class="p-2 text-slate-400 hover:text-rose-500 transition-colors" title="Descargar PDF">
                                    <i class="ph ph-file-pdf text-2xl"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="h-16 w-16 bg-slate-50 rounded-full flex items-center justify-center">
                                    <i class="ph ph-files text-3xl text-slate-300"></i>
                                </div>
                                <p class="text-slate-400 font-black text-xs uppercase tracking-widest">No hay reportes disponibles para periodos cerrados</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
