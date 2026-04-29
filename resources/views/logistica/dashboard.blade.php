@extends('layouts.app')

@section('content')
<div class="space-y-8 pb-12">
    {{-- Top Header --}}
    <div class="flex items-center justify-between bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden transition-all hover:shadow-xl hover:shadow-primary/5">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-black text-slate-900 font-headline tracking-tight">Monitor Logístico</h1>
            <p class="text-slate-500 font-medium mt-1">Estado de la flota y eficiencia de entregas en tiempo real.</p>
        </div>
        <div class="relative z-10 flex gap-4">
             <a href="{{ route('despachos.create_batch') }}" class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
                <span class="material-symbols-outlined">rocket_launch</span>
                Nuevo Despacho
            </a>
        </div>
    </div>

    {{-- Core Metrics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:-translate-y-1">
            <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-4">
                <span class="material-symbols-outlined font-black">pending_actions</span>
            </div>
            <p class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest leading-none">En Espera de Carga</p>
            <h3 class="text-4xl font-black text-slate-900 mt-2">{{ number_format($totalPendientes) }}</h3>
            <p class="text-[0.6rem] font-bold text-slate-400 mt-2 italic">Carnets listos para despachar</p>
        </div>

        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group transition-all hover:-translate-y-1">
             <div class="absolute -right-10 -bottom-10 w-24 h-24 bg-primary/20 rounded-full blur-2xl group-hover:bg-primary/30 transition-colors"></div>
             <div class="w-12 h-12 bg-primary/20 text-primary rounded-xl flex items-center justify-center mb-4 relative z-10">
                <span class="material-symbols-outlined font-black">local_shipping</span>
            </div>
            <p class="text-[0.65rem] font-black text-white/40 uppercase tracking-widest leading-none relative z-10">Activos en Ruta</p>
            <h3 class="text-4xl font-black text-white mt-2 relative z-10">{{ number_format($totalEnRuta) }}</h3>
            <p class="text-[0.6rem] font-bold text-white/30 mt-2 italic relative z-10">{{ $despachosActivos }} despachos en curso</p>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:-translate-y-1">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-4">
                <span class="material-symbols-outlined font-black">task_alt</span>
            </div>
            <p class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest leading-none">Entregados Hoy</p>
            <h3 class="text-4xl font-black text-emerald-600 mt-2">{{ number_format($entregasHoy) }}</h3>
            <div class="flex items-center gap-1.5 mt-2">
                 <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                 <p class="text-[0.6rem] font-bold text-slate-400 italic">Actualizado en vivo</p>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:-translate-y-1">
            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center mb-4">
                <span class="material-symbols-outlined font-black">report_problem</span>
            </div>
            <p class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest leading-none">Incidencias Hoy</p>
            <h3 class="text-4xl font-black text-rose-600 mt-2">{{ number_format($fallosHoy) }}</h3>
            <p class="text-[0.6rem] font-bold text-slate-400 mt-2 italic">Requieren atención / reprogramar</p>
        </div>
    </div>

    {{-- Main Activity Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Recent Dispatches --}}
        <div class="lg:col-span-8 bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden group">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <h3 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary">history</span>
                    Últimos Despachos Iniciados
                </h3>
                <a href="{{ route('despachos.index') }}" class="text-[0.6rem] font-black text-primary uppercase tracking-widest hover:underline">Ver Todo</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white">
                            <th class="px-8 py-4 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Lote</th>
                            <th class="px-4 py-4 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Mensajero</th>
                            <th class="px-4 py-4 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Carga</th>
                            <th class="px-4 py-4 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Estado</th>
                            <th class="px-4 py-4 text-right pr-8 text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Hora</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($ultimosDespachos as $desp)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <span class="text-sm font-black text-slate-800 tracking-tighter italic">#{{ str_pad($desp->id, 5, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg text-white text-[0.6rem] font-black flex items-center justify-center shadow-md" style="background-color: {{ $desp->mensajero->color }}">
                                            {{ substr($desp->mensajero->nombre, 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-slate-700">{{ $desp->mensajero->nombre }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-5">
                                    <span class="text-xs font-black text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg border border-slate-200">{{ $desp->items_count }} items</span>
                                </td>
                                <td class="px-4 py-5 font-bold">
                                     <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.55rem] font-black uppercase tracking-widest {{ $desp->status == 'finalizado' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary/10 text-primary' }}">
                                        {{ $desp->status == 'finalizado' ? 'Terminado' : 'En Ruta' }}
                                    </span>
                                </td>
                                <td class="px-4 py-5 text-right pr-8 text-xs font-bold text-slate-400">
                                    {{ $desp->created_at->format('H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Demanda Geográfica --}}
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm relative overflow-hidden transition-all hover:shadow-xl hover:shadow-primary/5">
                <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2 tracking-tight">
                    <span class="material-symbols-outlined text-amber-500">location_searching</span>
                    Zonas Demandadas
                </h3>
                <div class="space-y-4">
                    @foreach($distribucionProvincias as $dist)
                        <div class="space-y-2">
                             <div class="flex justify-between items-center text-xs font-black uppercase tracking-widest text-slate-600">
                                 <span>{{ $dist->provincia }}</span>
                                 <span class="text-primary">{{ $dist->total }}</span>
                             </div>
                             <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden border border-slate-200 shadow-inner">
                                 @php
                                     $pct = $totalPendientes > 0 ? ($dist->total / $totalPendientes) * 100 : 0;
                                 @endphp
                                 <div class="bg-primary h-full transition-all duration-1000" style="width: {{ $pct }}%"></div>
                             </div>
                        </div>
                    @endforeach
                    @if($distribucionProvincias->isEmpty())
                        <p class="text-xs italic text-slate-400">No hay carga pendiente actualmente.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm group">
                 <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2 tracking-tight">
                    <span class="material-symbols-outlined text-emerald-500">stars</span>
                    Top Mensajeros
                </h3>
                <div class="space-y-6">
                    @foreach($productividadMensajeros as $msg)
                        <div class="flex items-center justify-between group-hover/msg:translate-x-1 transition-transform">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-white text-sm font-black shadow-lg" style="background-color: {{ $msg->color }}">
                                    {{ substr($msg->nombre, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-800 leading-none">{{ $msg->nombre }}</p>
                                    <p class="text-[0.65rem] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ $msg->vehiculo_tipo }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-emerald-600 leading-none">{{ $msg->despachos_count }}</p>
                                <p class="text-[0.55rem] font-black text-slate-300 uppercase tracking-widest">Éxitos</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
