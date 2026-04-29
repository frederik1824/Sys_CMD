@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden transition-all hover:shadow-xl hover:shadow-primary/5">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl font-black text-slate-900 font-headline tracking-tight">Historial de Despachos</h1>
            <p class="text-slate-500 font-medium mt-1">Sigue el estatus de las entregas en tiempo real.</p>
        </div>
        <a href="{{ route('despachos.create_batch') }}" class="group relative z-10 inline-flex items-center gap-3 px-8 py-4 bg-primary text-white rounded-[1.8rem] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-1 transition-all active:scale-95">
            <span class="material-symbols-outlined text-white">rocket_launch</span>
            Nuevo Despacho
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden border-b-0">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Despacho</th>
                        <th class="px-4 py-6 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Mensajero</th>
                        <th class="px-4 py-6 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Ruta / Zona</th>
                        <th class="px-4 py-6 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Carga</th>
                        <th class="px-4 py-6 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Estatus</th>
                        <th class="px-8 py-6 text-right text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($despachos as $despacho)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                             <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-800">LT-{{ str_pad($despacho->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-[0.6rem] font-medium text-slate-400 mt-1 uppercase tracking-wider">{{ $despacho->fecha_salida ? $despacho->fecha_salida->format('d/m/Y H:i') : 'No registrada' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs font-black shadow-lg" style="background-color: {{ $despacho->mensajero->color ?? '#3b82f6' }}">
                                        {{ substr($despacho->mensajero->nombre, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-700">{{ $despacho->mensajero->nombre }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-6">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-600 truncate max-w-[150px]">{{ $despacho->ruta->nombre ?? 'Despacho Directo' }}</span>
                                    <span class="text-[0.65rem] font-black text-slate-400 mt-1 uppercase tracking-widest">{{ $despacho->ruta->zona ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-6">
                                <span class="bg-primary/5 text-primary text-[0.6rem] font-black px-2 py-0.5 rounded-lg border border-primary/10">{{ $despacho->items_count }} items</span>
                            </td>
                            <td class="px-4 py-6">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-widest {{ $despacho->status == 'finalizado' ? 'bg-emerald-100 text-emerald-700' : 'bg-primary/10 text-primary animate-pulse' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $despacho->status == 'finalizado' ? 'bg-emerald-500' : 'bg-primary' }}"></span>
                                    {{ $despacho->status == 'finalizado' ? 'Finalizado' : 'En Ruta' }}
                                </span>
                            </td>
                             <td class="px-8 py-6 text-right flex justify-end gap-2">
                                <a href="{{ route('despachos.print', $despacho) }}" target="_blank" class="w-9 h-9 flex items-center justify-center bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-all font-black active:scale-95" title="Imprimir Hoja de Ruta">
                                    <span class="material-symbols-outlined text-lg">print</span>
                                </a>
                                <a href="{{ route('despachos.show', $despacho) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl text-[0.65rem] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-900/10 active:scale-95">
                                    <span class="material-symbols-outlined text-sm">visibility</span>
                                    Gestionar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-20 text-center text-slate-400 italic font-medium">No hay despachos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $despachos->links() }}
    </div>
</div>
@endsection
