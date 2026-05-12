@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-10 space-y-10 min-h-screen bg-[#f8fafc]">
    
    <!-- Header Ejecutivo -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <span class="text-primary/60">Operaciones</span>
                <span class="ph ph-caret-right text-[10px]"></span>
                <span class="text-primary">Control de Asistencia</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-[900] text-slate-900 tracking-tighter leading-none flex items-center gap-4">
                Monitor de <span class="text-primary italic font-light">Productividad</span>
                <span class="p-2 bg-blue-50 text-blue-600 rounded-2xl hidden md:block">
                    <i class="ph ph-clock-user text-3xl"></i>
                </span>
            </h1>
            <p class="text-slate-500 font-medium text-lg">Resumen operativo del personal de Servicio al Cliente.</p>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-white p-2 rounded-2xl border border-slate-100 shadow-sm flex items-center gap-4 pr-6">
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                    <i class="ph-fill ph-calendar text-2xl"></i>
                </div>
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Fecha de Hoy</p>
                    <p class="text-sm font-black text-slate-900">{{ now()->translatedFormat('d F, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Bento -->
    <div class="grid grid-cols-12 gap-6">
        
        <!-- KPIs Principales -->
        <div class="col-span-12 lg:col-span-4 grid grid-cols-2 gap-4">
            <!-- TOTAL ACTIVAS -->
            <div class="col-span-2 bg-slate-900 p-8 rounded-[3rem] text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="ph ph-users-four text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest opacity-60">Representantes Activas</p>
                </div>
                <h4 class="text-6xl font-black tracking-tighter relative z-10">{{ $stats['total_empleados'] }}</h4>
                <div class="mt-6 flex items-center gap-2 relative z-10">
                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] font-black rounded-lg border border-emerald-500/30">OPERATIVO</span>
                    <span class="text-[10px] font-bold opacity-40 uppercase">Capacidad Total</span>
                </div>
            </div>

            <!-- PRESENTES -->
            <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm relative group overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-emerald-50 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Presentes</p>
                <div class="flex items-end justify-between">
                    <h4 class="text-3xl font-black text-emerald-600 tracking-tighter">{{ $stats['presentes'] }}</h4>
                    <div class="w-8 h-8 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600">
                        <i class="ph ph-sign-in text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- AUSENTES -->
            <div class="bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm relative group overflow-hidden">
                <div class="absolute -right-4 -bottom-4 w-16 h-16 bg-rose-50 rounded-full blur-xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Ausentes</p>
                <div class="flex items-end justify-between">
                    <h4 class="text-3xl font-black text-rose-600 tracking-tighter">{{ $stats['ausentes'] }}</h4>
                    <div class="w-8 h-8 bg-rose-50 rounded-lg flex items-center justify-center text-rose-600">
                        <i class="ph ph-user-minus text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cumplimiento Visual -->
        <div class="col-span-12 lg:col-span-3 bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-2">Cumplimiento</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase">Asistencia General de Hoy</p>
            </div>
            
            <div class="py-6 flex flex-col items-center">
                <div class="relative w-32 h-32 flex items-center justify-center">
                    <svg class="w-full h-full -rotate-90">
                        <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-100" />
                        <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="12" fill="transparent" class="text-primary" 
                                stroke-dasharray="364.4" stroke-dashoffset="{{ 364.4 * (1 - ($stats['porcentaje_asistencia'] / 100)) }}" />
                    </svg>
                    <span class="absolute text-2xl font-black text-slate-900">{{ $stats['porcentaje_asistencia'] }}%</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-slate-50 pt-6">
                <div>
                    <p class="text-[10px] font-black text-amber-600 uppercase mb-1">Tardanzas</p>
                    <p class="text-xl font-black text-slate-900">{{ $stats['tardanzas_hoy'] }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-black text-blue-600 uppercase mb-1">Permisos</p>
                    <p class="text-xl font-black text-slate-900">0</p>
                </div>
            </div>
        </div>

        <!-- Alertas Operativas -->
        <div class="col-span-12 lg:col-span-5 bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden relative">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="ph ph-warning-circle text-amber-500 text-xl"></i>
                Alertas Críticas
            </h3>
            <div class="space-y-4">
                @if($stats['tardanzas_hoy'] > 0)
                    <div class="flex items-center gap-4 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                        <div class="w-10 h-10 bg-amber-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-amber-200">
                            <i class="ph-bold ph-timer text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Tardanzas Detectadas</p>
                            <p class="text-[10px] font-medium text-amber-700">{{ $stats['tardanzas_hoy'] }} representantes ingresaron después de su horario.</p>
                        </div>
                    </div>
                @endif

                @if($stats['ausentes'] > 0)
                    <div class="flex items-center gap-4 p-4 bg-rose-50 rounded-2xl border border-rose-100">
                        <div class="w-10 h-10 bg-rose-500 text-white rounded-xl flex items-center justify-center shadow-lg shadow-rose-200">
                            <i class="ph-bold ph-user-x text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Ausentismo Hoy</p>
                            <p class="text-[10px] font-medium text-rose-700">{{ $stats['ausentes'] }} representantes no han marcado entrada.</p>
                        </div>
                    </div>
                @endif

                @if($stats['tardanzas_hoy'] == 0 && $stats['ausentes'] == 0)
                    <div class="flex flex-col items-center justify-center py-10 opacity-40">
                        <i class="ph ph-check-circle text-5xl text-emerald-500 mb-4"></i>
                        <p class="text-xs font-black uppercase">Sin alertas operativas</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabla de Personal Presente -->
        <div class="col-span-12 bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-white/50 backdrop-blur-md sticky top-0 z-10">
                <div>
                    <h3 class="text-xl font-[900] text-slate-900 tracking-tight">Personal en Planta</h3>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">Monitoreo en tiempo real de registros activos</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-900 uppercase">Live</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Representante</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cargo / Departamento</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Entrada</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Almuerzo</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                            <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Efectividad</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($presentes as $reg)
                        <tr class="group hover:bg-slate-50/40 transition-all duration-300">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center font-black text-slate-400">
                                        {{ substr($reg->empleado->nombre_completo, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-900 tracking-tight uppercase group-hover:text-primary transition-colors">{{ $reg->empleado->nombre_completo }}</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $reg->empleado->codigo_empleado }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $reg->empleado->cargo->nombre ?? 'N/A' }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase italic">{{ $reg->empleado->cargo->departamento->nombre ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-slate-900">{{ $reg->hora_entrada->format('h:i A') }}</span>
                                    @if($reg->minutos_tardanza > 0)
                                        <span class="text-[9px] font-black text-rose-600 uppercase tracking-tighter">Tarde (+{{ $reg->minutos_tardanza }} min)</span>
                                    @else
                                        <span class="text-[9px] font-black text-emerald-600 uppercase tracking-tighter">A Tiempo</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if($reg->inicio_almuerzo)
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-black text-slate-900">{{ $reg->inicio_almuerzo->format('h:i') }} @if($reg->fin_almuerzo) - {{ $reg->fin_almuerzo->format('h:i A') }} @endif</span>
                                        <span class="text-[9px] font-black text-blue-600 uppercase tracking-tighter">{{ $reg->fin_almuerzo ? 'Completado' : 'En pausa' }}</span>
                                    </div>
                                @else
                                    <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Pendiente</span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-center">
                                @if(!$reg->hora_salida)
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase rounded-lg border border-emerald-100 flex items-center gap-2 justify-center">
                                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Presente
                                    </span>
                                @else
                                    <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-black uppercase rounded-lg border border-slate-200">
                                        Salió
                                    </span>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-black text-slate-900">{{ floor($reg->minutos_trabajados_actuales / 60) }}h {{ $reg->minutos_trabajados_actuales % 60 }}m</span>
                                    <div class="w-24 h-1 bg-slate-100 rounded-full mt-2 overflow-hidden">
                                        <div class="h-full bg-{{ $reg->cumplio_jornada ? 'emerald' : 'amber' }}-500 rounded-full" style="width: {{ min(100, ($reg->minutos_trabajados_neto / 480) * 100) }}%"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($presentes->isEmpty())
                            <tr>
                                <td colspan="6" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center opacity-30">
                                        <i class="ph ph-users-three text-6xl mb-4"></i>
                                        <p class="text-sm font-black uppercase">Sin personal registrado hoy</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
