@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto">
    <div class="mb-10">
        <h1 class="text-3xl font-black text-slate-900 tracking-tighter mb-2">Histórico de Dispersiones</h1>
        <p class="text-slate-500 font-medium uppercase tracking-widest text-[10px]">Línea de tiempo de archivos TSS procesados</p>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-slate-200/30 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-10 py-6">Periodo</th>
                        <th class="px-10 py-6">Archivo Original</th>
                        <th class="px-10 py-6 text-right">Registros</th>
                        <th class="px-10 py-6 text-right">Monto Dispersado</th>
                        <th class="px-10 py-6">Usuario</th>
                        <th class="px-10 py-6">Estado</th>
                        <th class="px-10 py-6">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($cargas as $carga)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-10 py-8">
                            <span class="text-sm font-black text-slate-900">{{ $carga->periodo }}</span>
                        </td>
                        <td class="px-10 py-8">
                            <p class="text-xs font-bold text-slate-700 mb-1">{{ $carga->nombre_archivo }}</p>
                            <p class="text-[10px] font-medium text-slate-400">{{ $carga->fecha_carga->diffForHumans() }}</p>
                        </td>
                        <td class="px-10 py-8 text-right font-black text-slate-900 text-sm">
                            {{ number_format($carga->total_registros) }}
                        </td>
                        <td class="px-10 py-8 text-right font-black text-indigo-600 text-sm">
                            RD$ {{ number_format($carga->monto_total_dispersado, 2) }}
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black uppercase">
                                    {{ substr($carga->user->name, 0, 2) }}
                                </div>
                                <span class="text-xs font-bold text-slate-600">{{ $carga->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                {{ $carga->estado }}
                            </span>
                        </td>
                        <td class="px-10 py-8">
                            <a href="{{ route('dispersion.pensionados.show', $carga->uuid) }}" class="text-indigo-600 hover:text-indigo-900">
                                <i class="ph ph-arrow-square-out text-xl"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-20 text-center">
                            <p class="text-slate-400 font-bold uppercase tracking-widest">No hay registros de dispersión en el sistema.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-10 border-t border-slate-50 bg-slate-50/20">
            {{ $cargas->links() }}
        </div>
    </div>
</div>
@endsection
