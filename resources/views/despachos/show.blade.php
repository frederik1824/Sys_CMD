@extends('layouts.app')

@section('content')
<div class="space-y-8 pb-12">
    <div class="flex items-center justify-between">
        <a href="{{ route('despachos.index') }}" class="group inline-flex items-center gap-2 px-6 py-3 bg-white border border-slate-100 rounded-2xl text-slate-500 hover:text-primary transition-all text-sm font-bold shadow-sm">
            <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Volver a Despachos
        </a>
        <div class="flex items-center gap-4">
             <a href="{{ route('despachos.print', $despacho) }}" target="_blank" class="group inline-flex items-center gap-2 px-6 py-3 bg-slate-950 text-white rounded-2xl hover:bg-slate-800 transition-all text-sm font-black shadow-xl shadow-slate-900/10 active:scale-95">
                <span class="material-symbols-outlined text-lg">print</span>
                Imprimir para Mensajero
             </a>
             <span class="text-[0.6rem] font-black uppercase text-slate-400 tracking-widest bg-slate-50 px-3 py-1 rounded-full border border-slate-200 shadow-sm leading-none">Cotejando Despacho LT-{{ str_pad($despacho->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Fleet Info (Left) --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-sm relative group overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-primary/5 rounded-full blur-3xl"></div>
                
                <h2 class="text-2xl font-black text-slate-900 font-headline mb-8 flex items-center gap-3">
                    <span class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary transition-all duration-500 group-hover:bg-primary group-hover:text-white">
                        <span class="material-symbols-outlined">directions_run</span>
                    </span>
                    Ficha de Salida
                </h2>

                <div class="space-y-8">
                    {{-- Mensajero --}}
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 shadow-sm transition-all hover:shadow-lg hover:shadow-primary/5">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center text-white text-lg font-black shadow-lg" style="background-color: {{ $despacho->mensajero->color ?? '#3b82f6' }}">
                            {{ substr($despacho->mensajero->nombre, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none">Mensajero Asignado</p>
                            <p class="text-lg font-black text-slate-900 mt-1 leading-tight">{{ $despacho->mensajero->nombre }}</p>
                            <p class="text-[0.65rem] font-bold text-slate-500 mt-1 uppercase tracking-tighter">{{ $despacho->mensajero->vehiculo_tipo }} - {{ $despacho->mensajero->vehiculo_placa ?: 'Sin Placa' }}</p>
                        </div>
                    </div>

                    {{-- Ruta --}}
                    <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 shadow-sm transition-all hover:shadow-lg hover:shadow-primary/5">
                        <div class="w-14 h-14 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 shadow-lg">
                            <span class="material-symbols-outlined">map</span>
                        </div>
                        <div>
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none">Ruta de Entrega</p>
                            <p class="text-lg font-black text-slate-900 mt-1 leading-tight">{{ $despacho->ruta->nombre ?? 'Directo' }}</p>
                            <p class="text-[0.65rem] font-bold text-slate-500 mt-1 uppercase tracking-tighter">Zona: {{ $despacho->ruta->zona ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Horarios --}}
                    <div class="p-4 bg-slate-900 rounded-3xl text-white shadow-2xl relative group overflow-hidden">
                        <div class="absolute -right-4 -top-4 w-16 h-16 bg-white/5 rounded-full blur-xl transition-all group-hover:bg-white/10"></div>
                        <div class="space-y-4 relative z-10">
                            <div class="flex justify-between items-center border-b border-white/10 pb-4">
                                <span class="text-[0.6rem] font-black uppercase text-white/30 tracking-widest">Salida</span>
                                <span class="text-xs font-black">{{ $despacho->fecha_salida ? $despacho->fecha_salida->format('d/m/Y H:i') : '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center bg-white/5 p-3 rounded-2xl">
                                <span class="text-[0.6rem] font-black uppercase text-white/30 tracking-widest leading-none">Cotejados</span>
                                <span class="text-xl font-black text-primary-container leading-none">{{ $despacho->items->where('status', '!=', 'pendiente')->count() }} <span class="text-xs text-white/40 font-black">/ {{ $despacho->items->count() }}</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Observaciones --}}
                     <div class="space-y-2 p-4 bg-slate-50 rounded-2xl border border-slate-100 shadow-sm overflow-hidden group">
                         <div class="flex items-center gap-2 mb-2">
                             <span class="material-symbols-outlined text-[16px] text-slate-400">notes</span>
                             <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-1">Observaciones de Despacho</p>
                         </div>
                         <p class="text-xs font-medium text-slate-600 italic leading-relaxed whitespace-pre-line">{{ $despacho->observaciones ?: 'Sin observaciones registradas.' }}</p>
                     </div>
                </div>
            </div>
        </div>

        {{-- Items Management (Right) --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden group transition-all hover:shadow-xl hover:shadow-primary/5">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="text-xl font-black text-slate-900 flex items-center gap-2 tracking-tight">
                        <span class="material-symbols-outlined text-primary">view_list</span>
                        Control de Entrega por Ítem
                    </h3>
                </div>

                <div class="overflow-x-auto min-h-[400px]">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-5 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Afiliado</th>
                                <th class="px-8 py-5 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Empresa</th>
                                <th class="px-8 py-5 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest text-center">Estatus</th>
                                <th class="px-8 py-5 text-right text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Accion de Cierre</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($despacho->items as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-800">{{ $item->afiliado->nombre_completo }}</span>
                                            <span class="text-[0.65rem] font-medium text-slate-400 mt-1 uppercase tracking-tighter">Cédula: {{ $item->afiliado->cedula }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-slate-600 truncate max-w-[150px]">{{ $item->afiliado->empresaModel->nombre ?? '-' }}</span>
                                            <span class="text-[0.6rem] font-bold text-slate-400 mt-1 uppercase tracking-widest italic">{{ $item->afiliado->municipio ?? 'N/A' }}, {{ $item->afiliado->provincia ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if($item->status == 'pendiente')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary text-[0.6rem] font-black uppercase rounded-full shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                                                Pendiente
                                            </span>
                                        @elseif($item->status == 'entregado')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-700 text-[0.6rem] font-black uppercase rounded-full">
                                                <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                                Entregado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-100 text-rose-700 text-[0.6rem] font-black uppercase rounded-full group-hover:shadow-lg transition-all" title="{{ $item->motivo_fallo }}">
                                                <span class="material-symbols-outlined text-[14px]">error</span>
                                                Fallido
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        @if($item->status == 'pendiente')
                                            <form action="{{ route('despachos.item_status', $item) }}" method="POST" class="flex justify-end gap-2" x-data="{ closing: false, mode: '' }">
                                                @csrf
                                                <input type="hidden" name="status" :value="mode">
                                                
                                                <div x-show="closing" class="flex flex-col gap-2 items-end">
                                                    <template x-if="mode === 'fallido'">
                                                        <input type="text" name="motivo_fallo" placeholder="Motivo de fallo..." required
                                                               class="text-[0.65rem] font-bold px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-rose-200 transition-all">
                                                    </template>
                                                    <div class="flex gap-1">
                                                        <button type="submit" class="px-4 py-1.5 bg-slate-900 text-white text-[0.6rem] font-black uppercase rounded-lg hover:bg-black transition-all shadow-md active:scale-95">Confirmar</button>
                                                        <button @click.prevent="closing = false" class="px-4 py-1.5 bg-slate-200 text-slate-500 text-[0.6rem] font-black uppercase rounded-lg hover:bg-slate-300 transition-all active:scale-95">X</button>
                                                    </div>
                                                </div>

                                                <div x-show="!closing" class="flex gap-2">
                                                    <button @click.prevent="closing = true; mode = 'entregado'" class="w-9 h-9 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-500 hover:text-white transition-all shadow-md shadow-emerald-500/10 active:scale-95">
                                                        <span class="material-symbols-outlined text-lg">check</span>
                                                    </button>
                                                    <button @click.prevent="closing = true; mode = 'fallido'" class="w-9 h-9 flex items-center justify-center bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-500 hover:text-white transition-all shadow-md shadow-rose-500/10 active:scale-95">
                                                        <span class="material-symbols-outlined text-lg">close</span>
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <span class="text-[0.6rem] font-bold text-slate-300 uppercase italic tracking-tighter">{{ $item->fecha_evento ? $item->fecha_evento->format('H:i') : '' }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
