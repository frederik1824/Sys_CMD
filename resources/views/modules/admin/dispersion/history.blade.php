@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    {{-- Header --}}
    <div class="flex items-end justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-emerald-500/10 rounded-lg">
                    <i class="ph-duotone ph-calendar-check text-2xl text-emerald-600"></i>
                </div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900">Histórico de Periodos</h2>
            </div>
            <p class="text-slate-500 font-medium ml-11">Archivo cronológico de dispersión y auditoría de afiliación.</p>
        </div>

        <div class="flex items-center gap-3 bg-white p-2 rounded-[2rem] border border-slate-200 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-4">Año Fiscal</span>
            <select class="bg-slate-50 border-none rounded-2xl px-4 py-2 font-black text-xs text-slate-900 focus:ring-0">
                <option value="2026">2026</option>
                <option value="2025">2025</option>
                <option value="2024">2024</option>
            </select>
        </div>
    </div>

    {{-- History Grid/List --}}
    <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h4 class="text-xs font-black uppercase tracking-[0.3em] text-slate-900">Registro Histórico Consolidado</h4>
            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                {{ $periods->total() }} periodos registrados
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                        <th class="px-8 py-5">Mes / Año</th>
                        <th class="px-8 py-5">Afiliados Totales</th>
                        <th class="px-8 py-5">Variación vs Anterior</th>
                        <th class="px-8 py-5">Responsable</th>
                        <th class="px-8 py-5">Estado</th>
                        <th class="px-8 py-5 text-right">Detalle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($periods as $period)
                    @php 
                        $stats = App\Http\Controllers\Modules\Admin\DispersionController::getStats($period);
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full {{ $period->status === 'closed' ? 'bg-slate-900' : 'bg-emerald-500 animate-pulse' }}"></div>
                                <span class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $period->month_name }} {{ $period->year }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-bold text-slate-600">{{ number_format($stats['total_afiliados'] ?? 0) }} Afiliados</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">
                                <i class="ph ph-trend-up"></i> +0.0%
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-medium text-slate-500">{{ $period->createdBy->name }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest 
                                {{ $period->status === 'closed' ? 'bg-slate-100 text-slate-600' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $period->status === 'closed' ? 'Archivado' : 'En Gestión' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="{{ route('dispersion.show', $period->id) }}" class="p-2 text-slate-400 hover:text-emerald-600 transition-colors">
                                <i class="ph ph-arrow-square-out text-xl"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($periods->hasPages())
        <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-100">
            {{ $periods->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
