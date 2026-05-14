@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-in fade-in duration-700 bg-slate-50/30 min-h-screen">
    <!-- Header de Contexto -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-600/30">
                    <i class="ph-bold ph-link text-xl"></i>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Bandeja de Enlaces Directos</h1>
            </div>
            <p class="text-slate-500 font-medium italic">Gestión prioritaria de derivaciones recibidas desde el departamento de Autorizaciones Médicas.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="px-6 py-3 bg-white border border-slate-200 rounded-2xl flex items-center gap-4 shadow-sm">
                <div class="flex flex-col items-end">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Pendientes</span>
                    <span class="text-xl font-black text-indigo-600 leading-none">{{ $registros->total() }}</span>
                </div>
                <div class="w-px h-8 bg-slate-100"></div>
                <i class="ph-bold ph-bell-ringing text-2xl text-indigo-400 animate-pulse"></i>
            </div>
        </div>
    </div>

    <!-- Buscador Específico -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
            <form action="{{ route('call-center.enlaces') }}" method="GET" class="flex items-center gap-6">
                <div class="flex-1 relative group">
                    <i class="ph-bold ph-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-500 transition-colors"></i>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           class="w-full bg-white border-slate-200 rounded-2xl py-4 pl-14 pr-6 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all placeholder:text-slate-300"
                           placeholder="Buscar por Nombre o Cédula en esta bandeja...">
                </div>
                <button type="submit" class="bg-slate-900 hover:bg-black text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                    <i class="ph-bold ph-funnel text-base"></i> Buscar
                </button>
                @if(request('search'))
                    <a href="{{ route('call-center.enlaces') }}" class="text-rose-500 font-black text-[10px] uppercase tracking-widest hover:underline">Limpiar</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Información del Afiliado</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado Actual</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Atención</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Fecha Recepción</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Gestionar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($registros as $reg)
                    <tr class="hover:bg-indigo-50/30 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-[20px] bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xl shadow-sm transition-all group-hover:rotate-6">
                                    {{ substr($reg->nombre, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-900 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $reg->nombre }}</h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg uppercase tracking-tighter">{{ $reg->cedula }}</span>
                                        <span class="text-[10px] font-black text-white bg-indigo-500 px-2 py-0.5 rounded-lg uppercase tracking-tighter shadow-sm">Enlace Directo</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-[0.1em]"
                                style="background-color: {{ $reg->estado->color }}10; color: {{ $reg->estado->color }}; border: 1px solid {{ $reg->estado->color }}20;">
                                <div class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $reg->estado->color }};"></div>
                                {{ $reg->estado->nombre }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            @if($reg->operador_id)
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($reg->operador->name) }}&background=6366f1&color=fff" class="w-8 h-8 rounded-full">
                                    <div>
                                        <p class="text-xs font-black text-slate-900 leading-none">{{ $reg->operador->name }}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter mt-1">Gestionando ahora</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center gap-2 text-rose-500 animate-pulse">
                                    <i class="ph-bold ph-warning-circle text-lg"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest">Sin Agente</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="text-[10px] font-black text-slate-600 uppercase">{{ $reg->created_at->format('d M, Y') }}</span>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $reg->created_at->format('h:i A') }}</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('call-center.manage', $reg->uuid) }}" 
                               class="inline-flex items-center justify-center gap-3 px-6 py-3 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 active:scale-95">
                                <span class="text-[10px] font-black uppercase tracking-widest">Atender</span>
                                <i class="ph-bold ph-arrow-right text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="max-w-xs mx-auto space-y-6">
                                <div class="w-24 h-24 bg-indigo-50 rounded-[40px] border border-indigo-100 flex items-center justify-center mx-auto text-indigo-300">
                                    <i class="ph-bold ph-link-break text-5xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900">Bandeja Vacía</h3>
                                    <p class="text-slate-400 font-medium italic mt-2">No hay derivaciones pendientes de Autorizaciones en este momento.</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registros->hasPages())
        <div class="p-8 border-t border-slate-50 bg-slate-50/30">
            {{ $registros->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
