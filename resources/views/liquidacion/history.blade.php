@extends('layouts.app')

@section('header')
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Historial de Liquidaciones
        </h2>
        <a href="{{ route('liquidacion.index') }}" class="text-xs font-black uppercase tracking-widest bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-200 hover:bg-slate-50 transition-colors">
            Volver a Pendientes
        </a>
    </div>
@endsection

@section('content')
<div class="py-12 px-4 max-w-7xl mx-auto">
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h3 class="font-black text-slate-800 uppercase tracking-tighter text-lg">Lotes Liquidados</h3>
                <p class="text-xs text-slate-500">Consulta y re-imprime tus relaciones de pago anteriores.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-100/30">
                        <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Fecha Pago</th>
                        <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Recibo / Referencia</th>
                        <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Responsable / Proveedor</th>
                        <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400 text-center">Registros</th>
                        <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400 text-right">Monto Total</th>
                        <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($lotes as $lote)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-sm font-bold text-slate-600">
                            {{ $lote->fecha->format('d/m/Y') }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="text-sm font-black text-slate-800 uppercase tracking-tight">{{ $lote->recibo }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700">{{ $lote->responsable->nombre ?? ($lote->proveedor->nombre ?? 'N/A') }}</span>
                                <span class="text-[0.6rem] text-slate-400 font-bold uppercase tracking-widest">{{ $lote->responsable_id ? 'Responsable Directo' : 'Proveedor Externo' }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-slate-100 text-slate-800">
                                {{ $lote->conteo_registros }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="text-sm font-black text-slate-900 font-mono">RD$ {{ number_format($lote->monto_total, 2) }}</span>
                        </td>
                        <td class="py-4 px-6 text-center flex items-center justify-center gap-2">
                            @if($lote->evidencia_path)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($lote->evidencia_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700" title="Ver Comprobante">
                                    <span class="material-symbols-outlined text-lg">description</span>
                                </a>
                            @endif
                            <a href="{{ route('liquidacion.print', $lote->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-xl text-[0.65rem] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all">
                                <span class="material-symbols-outlined text-sm">print</span>
                                Ver Relación
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 opacity-20">history</span>
                            <p class="text-sm font-bold">No hay liquidaciones registradas aún.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 bg-slate-50/30 border-t border-slate-50 text-xs text-slate-400">
            {{ $lotes->links() }}
        </div>
    </div>
</div>
@endsection
