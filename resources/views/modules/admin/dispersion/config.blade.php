@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    {{-- Header --}}
    <div class="flex items-end justify-between gap-6">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-slate-900/10 rounded-lg">
                    <i class="ph-duotone ph-gear-six text-2xl text-slate-900"></i>
                </div>
                <h2 class="text-3xl font-black tracking-tight text-slate-900">System Configuration</h2>
            </div>
            <p class="text-slate-500 font-medium ml-11">Gestión de catálogos, indicadores y parámetros del módulo.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="{ tab: 'indicators' }">
        {{-- Navigation Cards --}}
        <div class="lg:col-span-1 space-y-4">
            <button @click="tab = 'indicators'" 
                    :class="tab === 'indicators' ? 'bg-slate-900 text-white shadow-xl translate-x-2' : 'bg-white text-slate-500 hover:bg-slate-50'"
                    class="w-full flex items-center gap-4 p-6 rounded-[2rem] border border-slate-200 transition-all text-left group">
                <div class="p-3 rounded-2xl transition-colors" :class="tab === 'indicators' ? 'bg-emerald-500/20' : 'bg-slate-100 group-hover:bg-emerald-50'">
                    <i class="ph ph-chart-line-up text-xl" :class="tab === 'indicators' ? 'text-emerald-400' : 'text-slate-400 group-hover:text-emerald-600'"></i>
                </div>
                <div>
                    <h4 class="font-black text-sm uppercase tracking-widest">Indicadores</h4>
                    <p class="text-[10px] font-bold opacity-60">Métricas de Dispersión</p>
                </div>
            </button>

            <button @click="tab = 'bajas'" 
                    :class="tab === 'bajas' ? 'bg-slate-900 text-white shadow-xl translate-x-2' : 'bg-white text-slate-500 hover:bg-slate-50'"
                    class="w-full flex items-center gap-4 p-6 rounded-[2rem] border border-slate-200 transition-all text-left group">
                <div class="p-3 rounded-2xl transition-colors" :class="tab === 'bajas' ? 'bg-rose-500/20' : 'bg-slate-100 group-hover:bg-rose-50'">
                    <i class="ph ph-user-minus text-xl" :class="tab === 'bajas' ? 'text-rose-400' : 'text-slate-400 group-hover:text-rose-600'"></i>
                </div>
                <div>
                    <h4 class="font-black text-sm uppercase tracking-widest">Tipos de Bajas</h4>
                    <p class="text-[10px] font-bold opacity-60">Catálogo de Salidas</p>
                </div>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="lg:col-span-2">
            {{-- TAB: Indicators --}}
            <div x-show="tab === 'indicators'" x-transition class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900">Configuración de Indicadores</h4>
                    <button class="px-4 py-2 bg-slate-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-emerald-600 transition-all">
                        Nuevo Indicador
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                <th class="px-8 py-4">Nombre Institucional</th>
                                <th class="px-8 py-4">Categoría</th>
                                <th class="px-8 py-4 text-center">Orden</th>
                                <th class="px-8 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($indicators as $ind)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black text-slate-800">{{ $ind->name }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Cod: {{ $ind->code }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="px-2 py-1 {{ $ind->category === 'Montos' ? 'bg-amber-50 text-amber-600' : 'bg-blue-50 text-blue-600' }} rounded-lg text-[9px] font-black uppercase tracking-widest border border-current opacity-70">
                                        {{ $ind->category }}
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-center">
                                    <span class="text-xs font-black text-slate-400">{{ $ind->order_weight }}</span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <button class="text-slate-400 hover:text-slate-900 transition-colors">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB: Bajas --}}
            <div x-show="tab === 'bajas'" x-transition class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-900">Catálogo de Tipos de Bajas</h4>
                    <button class="px-4 py-2 bg-slate-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-rose-600 transition-all">
                        Nueva Categoría
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[9px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                <th class="px-8 py-4">Motivo de Baja</th>
                                <th class="px-8 py-4">Prioridad</th>
                                <th class="px-8 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($bajaTypes as $type)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4 text-xs font-black text-slate-800">{{ $type->name }}</td>
                                <td class="px-8 py-4">
                                    <span class="text-xs font-black text-slate-400">#{{ $type->order_weight }}</span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <button class="text-slate-400 hover:text-slate-900 transition-colors">
                                        <i class="ph ph-pencil-simple text-lg"></i>
                                    </button>
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
