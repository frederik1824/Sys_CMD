@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-in fade-in duration-700">
    <!-- Header Premium -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 bg-white p-8 rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
            <div class="w-20 h-20 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center shadow-inner">
                <i class="ph-bold ph-chart-pie-slice text-4xl"></i>
            </div>
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">KPIs & Rendimiento</h1>
                <p class="text-slate-500 font-bold uppercase text-[10px] tracking-[0.2em]">Dashboard Analítico de Call Center</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
        <!-- Funnel de Conversión -->
        <div class="xl:col-span-5 space-y-8">
            <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50">
                <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8 flex items-center gap-3">
                    <i class="ph-bold ph-funnel text-indigo-500"></i>
                    Embudo de Conversión
                </h3>

                <div class="space-y-6">
                    <!-- Paso 1: Total -->
                    <div class="relative p-6 bg-slate-50 rounded-[32px] border border-slate-100 overflow-hidden group">
                        <div class="relative z-10 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Importados / Leads</p>
                                <p class="text-3xl font-black text-slate-900">{{ number_format($funnel['total']) }}</p>
                            </div>
                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-400 shadow-sm">
                                <i class="ph-bold ph-users text-2xl"></i>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-slate-200 w-full"></div>
                    </div>

                    <!-- Paso 2: Contactados -->
                    @php 
                        $pctContactado = $funnel['total'] > 0 ? ($funnel['contactados'] / $funnel['total']) * 100 : 0;
                    @endphp
                    <div class="relative p-6 bg-indigo-50/50 rounded-[32px] border border-indigo-100/50 overflow-hidden group">
                        <div class="relative z-10 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Contactados Efectivos</p>
                                <p class="text-3xl font-black text-indigo-600">{{ number_format($funnel['contactados']) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-indigo-600">{{ round($pctContactado) }}%</span>
                                <p class="text-[9px] font-black text-indigo-300 uppercase">Tasa Contacto</p>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-indigo-500 transition-all duration-1000" style="width: {{ $pctContactado }}%"></div>
                    </div>

                    <!-- Paso 3: Promovidos -->
                    @php 
                        $pctPromovido = $funnel['total'] > 0 ? ($funnel['promovidos'] / $funnel['total']) * 100 : 0;
                    @endphp
                    <div class="relative p-6 bg-emerald-50/50 rounded-[32px] border border-emerald-100/50 overflow-hidden group">
                        <div class="relative z-10 flex justify-between items-center">
                            <div>
                                <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-1">Promovidos a Carnet</p>
                                <p class="text-3xl font-black text-emerald-600">{{ number_format($funnel['promovidos']) }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-black text-emerald-600">{{ round($pctPromovido) }}%</span>
                                <p class="text-[9px] font-black text-emerald-300 uppercase">Eficiencia Final</p>
                            </div>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-emerald-500 transition-all duration-1000" style="width: {{ $pctPromovido }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking de Operadores -->
        <div class="xl:col-span-7 space-y-8">
            <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50">
                <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8 flex items-center justify-between">
                    <span class="flex items-center gap-3">
                        <i class="ph-bold ph-medal text-amber-500"></i>
                        Productividad por Operador
                    </span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Top Performance</span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-slate-50">
                                <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Operador</th>
                                <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Gestiones</th>
                                <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Promociones</th>
                                <th class="pb-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Efectividad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($operadores as $op)
                            @php 
                                $efectividad = $op->gestiones_totales > 0 ? ($op->promociones_totales / $op->gestiones_totales) * 100 : 0;
                            @endphp
                            <tr class="group hover:bg-slate-50 transition-colors">
                                <td class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl overflow-hidden border-2 border-white shadow-sm">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($op->name) }}&background=6366f1&color=fff" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900 tracking-tight">{{ $op->name }}</p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $op->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[3rem] bg-slate-100 text-slate-600 py-1 rounded-lg text-xs font-black">{{ $op->gestiones_totales }}</span>
                                </td>
                                <td class="py-4 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[3rem] bg-emerald-50 text-emerald-600 py-1 rounded-lg text-xs font-black">{{ $op->promociones_totales }}</span>
                                </td>
                                <td class="py-4 text-right">
                                    <div class="inline-flex flex-col items-end">
                                        <span class="text-sm font-black text-slate-900">{{ round($efectividad, 1) }}%</span>
                                        <div class="w-16 h-1 bg-slate-100 rounded-full mt-1 overflow-hidden">
                                            <div class="h-full bg-amber-500" style="width: {{ $efectividad }}%"></div>
                                        </div>
                                    </div>
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
