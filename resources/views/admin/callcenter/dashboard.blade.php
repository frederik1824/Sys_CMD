@extends('layouts.app')

@section('content')
<div class="space-y-10 animate-in fade-in duration-700">
    <!-- Header Profesional -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-blue-600 rounded-[24px] flex items-center justify-center text-white shadow-xl shadow-blue-500/20">
                <span class="material-symbols-outlined text-3xl">analytics</span>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter">Panel de Productividad</h1>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-600 mt-2">Métricas de Rendimiento y Efectividad</p>
            </div>
        </div>

        <form action="{{ route('callcenter.dashboard') }}" method="GET" class="flex items-center gap-3 bg-slate-50 p-2 rounded-3xl border border-slate-100">
            <div class="flex items-center gap-2 px-4">
                <span class="material-symbols-outlined text-slate-400 text-sm">calendar_today</span>
                <input type="date" name="fecha" value="{{ $fecha }}" class="bg-transparent border-none text-xs font-black text-slate-700 focus:ring-0">
            </div>
            @unless(auth()->user()->hasRole('Gestor de Llamadas'))
            <select name="usuario_id" class="bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase px-4 py-2 focus:ring-2 focus:ring-blue-500/20 outline-none">
                <option value="">Todos los Operadores</option>
                @foreach($operadores as $op)
                    <option value="{{ $op->id }}" {{ $usuario_id == $op->id ? 'selected' : '' }}>{{ $op->name }}</option>
                @endforeach
            </select>
            @endunless
            <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-lg active:scale-95">Filtrar</button>
        </form>
    </div>

    <!-- KPIs Principales (Hoy) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Producción Diaria -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Producción Hoy</span>
                <div class="flex items-end gap-3 mt-4">
                    <span class="text-5xl font-black text-slate-900 tracking-tighter">{{ $totalLlamadasHoy }}</span>
                    <span class="text-xs font-bold text-blue-600 mb-2 uppercase tracking-tighter">Llamadas</span>
                </div>
                <div class="mt-6 flex items-center gap-2 text-[10px] font-bold text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Actividad registrada este día
                </div>
            </div>
        </div>

        <!-- Tasa de Localización -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Localización</span>
                <div class="flex items-end gap-3 mt-4">
                    <span class="text-5xl font-black text-emerald-600 tracking-tighter">{{ $tasaLocalizacion }}%</span>
                </div>
                <div class="mt-6 flex items-center gap-2 text-[10px] font-bold text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Contactos exitosos vs total
                </div>
            </div>
        </div>

        <!-- Tasa de Efectividad -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
            <div class="relative z-10">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Efectividad</span>
                <div class="flex items-end gap-3 mt-4">
                    <span class="text-5xl font-black text-amber-600 tracking-tighter">{{ $tasaEfectividad }}%</span>
                </div>
                <div class="mt-6 flex items-center gap-2 text-[10px] font-bold text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    Cédulas efectivas vs contactos
                </div>
            </div>
        </div>

        <!-- Meta Diaria (Fase 1) -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm hover:shadow-xl transition-all group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full group-hover:scale-150 transition-transform duration-700 opacity-50"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cumplimiento Meta</span>
                    <span class="text-[10px] font-black text-indigo-600 uppercase">{{ $goalProgress }}%</span>
                </div>
                <div class="flex items-end gap-3">
                    <span class="text-5xl font-black text-indigo-600 tracking-tighter">{{ $efectivasHoy }}</span>
                    <span class="text-xs font-bold text-slate-400 mb-2 uppercase tracking-tighter">/ {{ $dailyGoal }} Cédulas</span>
                </div>
                <div class="mt-6">
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden p-0.5">
                        <div class="h-full bg-indigo-600 rounded-full transition-all duration-1000" style="width: {{ min($goalProgress, 100) }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline y Distribución -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Pipeline de Trabajo -->
        <div class="lg:col-span-2 bg-white p-10 rounded-[48px] border border-slate-100 shadow-sm">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-xl font-black text-slate-900 tracking-tighter">Análisis de Fallos (Motivos de No-Localización)</h3>
                <span class="px-4 py-1.5 bg-rose-50 text-rose-700 rounded-full text-[10px] font-black uppercase tracking-widest">Smart Audit</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $iconosFallo = [
                        'No contesta' => ['icon' => 'call_missed', 'color' => 'amber'],
                        'Correo de voz' => ['icon' => 'voicemail', 'color' => 'slate'],
                        'Fuera de servicio' => ['icon' => 'signal_disconnected', 'color' => 'rose'],
                        'Número equivocado' => ['icon' => 'wrong_location', 'color' => 'red'],
                        'No labora en la empresa' => ['icon' => 'person_off', 'color' => 'orange']
                    ];
                @endphp
                
                @foreach(['No contesta', 'Correo de voz', 'Fuera de servicio', 'Número equivocado', 'No labora en la empresa'] as $motivo)
                    <div class="flex flex-col items-center p-6 bg-slate-50/50 border border-slate-100 rounded-[32px] group/item hover:bg-white hover:border-blue-200 transition-all">
                        <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-{{ $iconosFallo[$motivo]['color'] ?? 'slate' }}-500 shadow-sm mb-4 group-hover/item:scale-110 transition-transform">
                            <span class="material-symbols-outlined">{{ $iconosFallo[$motivo]['icon'] ?? 'help' }}</span>
                        </div>
                        <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-1">{{ $motivo }}</p>
                        <p class="text-2xl font-black text-slate-800 tracking-tighter">{{ $motivosFallo[$motivo] ?? 0 }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-12 p-8 bg-blue-600 rounded-[40px] text-white relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 group-hover:scale-125 transition-transform duration-700"></div>
                <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                    <div>
                        <h4 class="text-xl font-black tracking-tight">Rendimiento Operativo</h4>
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-80 mt-1">Avance acumulado de asignación activa</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-4xl font-black">{{ $totalAsignados > 0 ? round(($completados / $totalAsignados) * 100, 1) : 0 }}%</span>
                        <div class="w-32 h-3 bg-white/20 rounded-full overflow-hidden p-0.5">
                            <div class="h-full bg-white rounded-full" style="width: {{ $totalAsignados > 0 ? ($completados / $totalAsignados) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución de Llamadas (Hoy) -->
        <div class="bg-white p-10 rounded-[48px] border border-slate-100 shadow-sm flex flex-col">
            <h3 class="text-xl font-black text-slate-900 tracking-tighter mb-8 text-center">Resultados de Hoy</h3>
            
            <div class="flex-1 space-y-4 overflow-y-auto custom-scrollbar pr-2">
                @forelse($resumenHoy as $estado => $count)
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-3xl border border-slate-100">
                        <div class="flex items-center gap-4">
                            <div class="w-2 h-2 rounded-full {{ str_contains($estado, 'Efectiva') ? 'bg-emerald-500' : (str_contains($estado, 'No') ? 'bg-red-500' : 'bg-blue-500') }}"></div>
                            <span class="text-xs font-bold text-slate-700">{{ $estado }}</span>
                        </div>
                        <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-slate-500 shadow-sm">{{ $count }}</span>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-center opacity-30">
                        <span class="material-symbols-outlined text-5xl">inbox</span>
                        <p class="text-xs font-bold mt-2">Sin actividad</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8 p-6 bg-slate-900 rounded-[32px] text-center">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Gestionado</p>
                <p class="text-3xl font-black text-white tracking-tighter">{{ $totalLlamadasHoy }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
