@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Cortes (Periodos)</h2>
            <p class="text-on-surface-variant text-[0.875rem] mt-1">Gestión de los cortes mensuales de carnetización.</p>
        </div>
        <a href="{{ route('cortes.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-[0.875rem] font-semibold flex items-center gap-2 shadow-lg shadow-primary/20 hover:bg-blue-800 transition-colors">
            <span class="material-symbols-outlined text-lg">add</span>
            Nuevo Corte
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-high">
                <tr>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">ID</th>
                    <th class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-wider text-on-surface-variant">Periodo</th>
                    <th class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-wider text-on-surface-variant text-center">Total</th>
                    <th class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-wider text-on-surface-variant text-center">En Safe</th>
                    <th class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-wider text-on-surface-variant text-center">Cerrados</th>
                    <th class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">ARS CMD</th>
                    <th class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Otros</th>
                    <th class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-wider text-on-surface-variant">Estado</th>
                    <th class="px-6 py-4 text-[0.65rem] font-bold uppercase tracking-wider text-on-surface-variant text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-low">
                @forelse($cortes as $corte)
                <tr class="hover:bg-slate-50 transition-all group">
                    <td class="px-6 py-5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800 tracking-tighter uppercase">{{ $corte->nombre }}</span>
                            <span class="text-[0.65rem] text-slate-400 font-bold uppercase">ID: #{{ $corte->id }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center font-bold text-slate-600 text-sm">{{ number_format($corte->afiliados_count) }}</td>
                    <td class="px-6 py-5 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-sm font-black text-blue-600">{{ number_format($corte->entregados_count) }}</span>
                            <span class="text-[0.6rem] text-slate-400 font-bold uppercase">Entregados</span>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-center">
                        @php 
                            $perc = $corte->afiliados_count > 0 ? round(($corte->completados_count / $corte->afiliados_count) * 100) : 0;
                        @endphp
                        <div class="flex flex-col items-center">
                            <span class="text-sm font-black text-emerald-600">{{ $perc }}%</span>
                            <div class="w-12 bg-slate-100 h-1 rounded-full mt-1 overflow-hidden">
                                <div class="bg-emerald-500 h-full" style="width: {{ $perc }}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-right font-black text-slate-700 text-xs">
                        RD$ {{ number_format($corte->monto_ars, 2) }}
                    </td>
                    <td class="px-6 py-5 text-right font-black text-slate-400 text-xs">
                        RD$ {{ number_format($corte->monto_no_ars, 2) }}
                    </td>
                    <td class="px-6 py-5">
                        @if($corte->activo)
                            <span class="px-3 py-1 bg-primary text-white font-black text-[0.6rem] rounded-lg uppercase tracking-widest shadow-lg shadow-primary/20">Activo</span>
                        @else
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 font-black text-[0.6rem] rounded-lg uppercase tracking-widest">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-right">
                        <a href="{{ route('cortes.edit', $corte) }}" class="text-slate-400 hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">edit</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-5 text-center text-slate-500 text-sm">No hay cortes registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $cortes->links() }}
        </div>
    </div>
</div>
@endsection
