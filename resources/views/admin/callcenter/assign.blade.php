@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFDFD] pb-20" x-data="{ selectAll: false }">
    <!-- Header Premium -->
    <div class="bg-white border-b border-slate-100 pt-12 pb-16 mb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">
                        <span>Logística</span>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <span class="text-indigo-600">Asignación Estratégica</span>
                    </nav>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight leading-none">
                        Control de <span class="text-indigo-600">Delegación</span>
                    </h1>
                    <p class="text-slate-500 font-medium mt-3">Distribuye el universo de afiliados y prospectos entre tus gestores.</p>
                </div>

                <div class="flex gap-4">
                    <div class="bg-indigo-50 border border-indigo-100 px-8 py-4 rounded-[28px] flex flex-col items-center shadow-2xl shadow-indigo-500/10">
                        <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1">Total Disponible</span>
                        <span class="text-3xl font-black text-indigo-700 leading-none">842</span> <!-- Placeholder or dynamic if needed -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Panel de Control (Izquierda) -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-[40px] shadow-2xl shadow-slate-200/50 border border-slate-100 p-8 sticky top-28">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-600/20">
                            <span class="material-symbols-outlined text-white">filter_list</span>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight uppercase">Configurar Filtro</h3>
                    </div>

                    <form action="{{ route('callcenter.assign') }}" method="GET" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3 ml-1">Lote / Período</label>
                            <select name="lote_id" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="">Todos los Lotes</option>
                                @foreach($lotes as $l)
                                    <option value="{{ $l->id }}" {{ request('lote_id') == $l->id ? 'selected' : '' }}>{{ $l->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3 ml-1">Corporativo</label>
                            <select name="empresa_id" class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-indigo-500 transition-all">
                                <option value="">Todas las Empresas</option>
                                @foreach($empresas as $e)
                                    <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="p-6 bg-indigo-50 rounded-[32px] border border-indigo-100 group cursor-pointer hover:bg-indigo-100 transition-all">
                            <label class="flex items-center gap-4 cursor-pointer">
                                <div class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="solo_prospectos" value="1" {{ request('solo_prospectos') ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </div>
                                <div>
                                    <span class="text-[10px] font-black text-indigo-900 uppercase tracking-widest">Solo Prospectos</span>
                                    <p class="text-[9px] text-indigo-600/60 font-bold mt-1">Cargas externas (Roberitza)</p>
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="w-full h-16 bg-slate-900 text-white rounded-[24px] text-[10px] font-black uppercase tracking-[0.2em] shadow-xl shadow-slate-900/20 hover:bg-indigo-600 hover:-translate-y-1 transition-all">
                            Ejecutar Segmentación
                        </button>
                    </form>
                </div>
            </div>

            <!-- Resultados y Delegación (Derecha) -->
            <div class="lg:col-span-8">
                @if(request()->hasAny(['lote_id', 'empresa_id', 'solo_prospectos']))
                    <div class="bg-white rounded-[48px] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                        <form action="{{ route('callcenter.assign.store') }}" method="POST">
                            @csrf
                            
                            <div class="p-10 bg-indigo-600">
                                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                                            <span class="material-symbols-outlined text-white text-3xl">assignment_ind</span>
                                        </div>
                                        <div>
                                            <h3 class="text-white text-xl font-black tracking-tight">Delegar a Gestor</h3>
                                            <p class="text-indigo-100 text-[9px] font-black uppercase tracking-widest mt-1">Selecciona el responsable de estas llamadas</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <select name="usuario_id" required class="bg-white/10 border-white/20 text-white placeholder-indigo-200 rounded-2xl px-6 py-3 text-sm font-bold focus:ring-white/30 focus:bg-white/20 transition-all min-w-[200px]">
                                            <option value="" class="text-slate-900">-- Seleccionar --</option>
                                            @foreach($operadores as $op)
                                                <option value="{{ $op->id }}" class="text-slate-900">{{ $op->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="bg-white text-indigo-600 px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-xl shadow-indigo-900/20">
                                            Asignar Ahora
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                    Mostrando <span class="text-slate-900">{{ $afiliados->count() }}</span> de <span class="text-slate-900">{{ $afiliados->total() }}</span> registros
                                </span>
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest group-hover:text-indigo-700 transition-colors">Seleccionar Todos</span>
                                    <input type="checkbox" x-model="selectAll" class="w-5 h-5 rounded-lg border-slate-200 text-indigo-600 focus:ring-indigo-500/20 transition-all">
                                </label>
                            </div>

                            <div class="max-h-[600px] overflow-y-auto custom-scrollbar">
                                <table class="w-full">
                                    <tbody class="divide-y divide-slate-50">
                                        @forelse($afiliados as $a)
                                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                                <td class="pl-10 py-5 w-10">
                                                    <input type="checkbox" name="afiliados[]" value="{{ $a->id }}" :checked="selectAll" class="w-5 h-5 rounded-lg border-slate-200 text-indigo-600 focus:ring-indigo-500/20 transition-all">
                                                </td>
                                                <td class="px-6 py-5">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-white group-hover:text-indigo-600 transition-all">
                                                            <span class="material-symbols-outlined text-xl">person</span>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-black text-slate-900 leading-none mb-1">{{ $a->nombre_completo }}</div>
                                                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $a->cedula }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-5 text-right pr-10">
                                                    <div class="inline-flex items-center px-4 py-2 bg-slate-100 rounded-xl text-[10px] font-black text-slate-600 uppercase tracking-widest group-hover:bg-white transition-all">
                                                        {{ $a->empresaModel->nombre ?? ($a->empresa ?? 'Sin Empresa') }}
                                                        @if(!$a->empresaModel)
                                                            <span class="ml-2 w-2 h-2 rounded-full bg-amber-400"></span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="py-20 text-center">
                                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-200">
                                                        <span class="material-symbols-outlined text-4xl">search_off</span>
                                                    </div>
                                                    <h4 class="text-lg font-black text-slate-900 tracking-tight uppercase">Sin Coincidencias</h4>
                                                    <p class="text-slate-400 text-xs font-medium">Ajusta los filtros para encontrar afiliados.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="p-8 bg-slate-50/50 border-t border-slate-100">
                                {{ $afiliados->appends(request()->query())->links() }}
                            </div>
                        </form>
                    </div>
                @else
                    <div class="bg-white rounded-[48px] border-2 border-dashed border-slate-100 flex flex-col items-center justify-center py-40 text-center px-10">
                        <div class="w-32 h-32 bg-indigo-50 rounded-[40px] flex items-center justify-center mb-8 text-indigo-200">
                            <span class="material-symbols-outlined text-6xl">filter_alt</span>
                        </div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight leading-none mb-4">Listo para Segmentar</h3>
                        <p class="max-w-md text-slate-400 text-sm font-medium leading-relaxed">
                            Utiliza el panel de la izquierda para seleccionar un lote o empresa. El sistema te mostrará el universo disponible para delegar.
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection
