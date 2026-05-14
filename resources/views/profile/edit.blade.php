@extends('layouts.app')

@section('content')
<!-- BACKGROUND & MAIN CONTAINER -->
<div class="min-h-screen bg-[#f4f7fe] dark:bg-[#0b1120] pb-20 pt-8 px-4 md:px-8 font-sans selection:bg-indigo-500 selection:text-white" x-data="{ tab: 'dashboard' }">
    
    <div class="max-w-[1600px] mx-auto space-y-8 animate-in fade-in duration-700">
        
        <!-- TOP NAVIGATION BAR (Pill Style) -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <!-- Brand / Title -->
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white dark:bg-slate-900 rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 dark:border-slate-800">
                    <i class="ph-fill ph-radar text-indigo-600 text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight leading-none">Nexus Workspace <span class="text-xs text-indigo-500 font-bold ml-2">v{{ $systemVersion }}</span></h1>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Centro Operativo Personal</p>
                </div>
            </div>

            <!-- Tab Pills -->
            <div class="flex items-center gap-2 p-1.5 bg-white/60 dark:bg-slate-900/60 backdrop-blur-xl border border-white dark:border-slate-800 rounded-full shadow-sm w-fit">
                <button @click="tab = 'dashboard'" :class="tab === 'dashboard' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:bg-white dark:hover:bg-slate-800'" class="px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-300">Dashboard</button>
                <button @click="tab = 'actividad'" :class="tab === 'actividad' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:bg-white dark:hover:bg-slate-800'" class="px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-300">Actividad</button>
                <button @click="tab = 'seguridad'" :class="tab === 'seguridad' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:bg-white dark:hover:bg-slate-800'" class="px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-300">Seguridad</button>
                <button @click="tab = 'ajustes'" :class="tab === 'ajustes' ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-500 hover:bg-white dark:hover:bg-slate-800'" class="px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all duration-300">Ajustes</button>
            </div>
            
            <!-- Quick Actions -->
            <div class="hidden md:flex items-center gap-3">
                <a href="{{ route('portal') }}" class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-full text-[10px] font-black text-slate-600 dark:text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm group">
                    <i class="ph-bold ph-house text-lg group-hover:scale-110 transition-transform"></i>
                    Portal Principal
                </a>
                <button class="w-10 h-10 rounded-full bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 flex items-center justify-center text-slate-400 hover:text-indigo-600 shadow-sm transition-all relative">
                    <i class="ph ph-bell text-lg"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full border border-white"></span>
                </button>
            </div>
        </div>

        <!-- MAIN GRID LAYOUT -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
            
            <!-- ================= LEFT COLUMN: IDENTITY & PROFILE ================= -->
            <div class="xl:col-span-3 space-y-6 sticky top-8">
                <!-- Premium Identity Card -->
                <div class="bg-gradient-to-b from-white to-slate-50 dark:from-slate-900 dark:to-slate-800/80 rounded-[2.5rem] p-8 border border-white dark:border-slate-700 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
                    <!-- Subtle Glow Header -->
                    <div class="absolute top-0 inset-x-0 h-40 bg-gradient-to-b from-indigo-500/10 to-transparent pointer-events-none"></div>
                    
                    <div class="relative flex flex-col items-center mt-4">
                        <!-- Avatar Container -->
                        <div class="relative w-36 h-36 rounded-[2rem] border-4 border-white dark:border-slate-800 shadow-2xl overflow-hidden group/avatar bg-slate-100 mb-6">
                            <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover/avatar:scale-110" id="avatar-preview">
                            <label for="avatar-sidebar-input" class="absolute inset-0 bg-indigo-900/40 backdrop-blur-[2px] flex items-center justify-center opacity-0 group-hover/avatar:opacity-100 transition-all cursor-pointer">
                                <i class="ph ph-camera-plus text-white text-3xl"></i>
                            </label>
                        </div>

                        <!-- Level Overlay Badge -->
                        <div class="-mt-12 mb-6 relative z-10 bg-slate-900 text-white text-[9px] font-black uppercase tracking-[0.2em] px-5 py-2 rounded-full border-[3px] border-white dark:border-slate-800 shadow-lg flex items-center gap-2 hover:scale-105 transition-transform cursor-help" title="Nivel Operativo basado en Puntos de Experiencia (XP)">
                            <i class="ph-fill ph-star text-amber-400"></i>
                            Nivel {{ floor($stats['puntos_experiencia'] / 100) + 1 }}
                        </div>

                        <!-- User Info -->
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1">{{ $user->name }}</h2>
                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $user->getRoleNames()->first() ?? 'Representante' }}</p>

                        <!-- Dynamic Status -->
                        <div class="mt-4 flex items-center gap-2 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 px-4 py-1.5 rounded-full border border-emerald-100 dark:border-emerald-800/30 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] font-black uppercase tracking-widest">En Línea & Operando</span>
                        </div>

                        @if($stats['is_callcenter'])
                        <!-- WIDGET DE PONCHADOR (Call Center / Servicio al Cliente) -->
                        <div class="w-full mt-8 bg-slate-900 text-white rounded-[1.5rem] p-5 shadow-lg relative overflow-hidden group/ponchador border border-slate-800">
                            <div class="absolute -right-10 -top-10 w-24 h-24 bg-indigo-500/30 rounded-full blur-2xl"></div>
                            
                            <div class="flex justify-between items-center mb-4 relative z-10">
                                <span class="text-[10px] font-black uppercase tracking-widest text-indigo-300 flex items-center gap-2"><i class="ph-bold ph-clock"></i> Ponchador</span>
                                <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest animate-pulse">{{ $stats['ponchador']['estado_turno'] }}</span>
                            </div>
                            
                            <div class="flex items-end justify-between relative z-10">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Horas Activo</p>
                                    <p class="text-3xl font-black leading-none mt-1">{{ $stats['ponchador']['horas_trabajadas_hoy'] }}<span class="text-sm text-slate-400">h</span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Entrada</p>
                                    <p class="text-sm font-black">{{ $stats['ponchador']['hora_entrada'] ?? '--:--' }}</p>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-white/10 flex justify-between gap-2 relative z-10">
                                <button class="flex-1 py-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-[9px] font-black uppercase tracking-widest transition-colors"><i class="ph-bold ph-coffee mr-1"></i> Pausa</button>
                                <button class="flex-1 py-1.5 rounded-lg bg-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white text-[9px] font-black uppercase tracking-widest transition-colors border border-rose-500/30"><i class="ph-bold ph-sign-out mr-1"></i> Salida</button>
                            </div>
                        </div>
                        @else
                        <!-- Gamification / XP Bar (Analistas & Supervisores) -->
                        <div class="w-full mt-10">
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Experiencia</span>
                                <span class="text-xs font-black text-indigo-600">{{ $stats['puntos_experiencia'] }} <span class="text-[9px] text-slate-400">/ {{ (floor($stats['puntos_experiencia'] / 100) + 1) * 100 }}</span></span>
                            </div>
                            <div class="h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden border border-slate-200/50 dark:border-slate-700/50 shadow-inner">
                                @php $progress = ($stats['puntos_experiencia'] % 100); @endphp
                                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full relative overflow-hidden" style="width: {{ $progress }}%">
                                    <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Quick Info List -->
                        <div class="w-full mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 space-y-4">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-400 flex items-center gap-2"><i class="ph ph-briefcase"></i> Operación</span>
                                <span class="font-black text-slate-700 dark:text-slate-300">{{ $stats['is_callcenter'] ? 'Call Center' : ($stats['is_supervisor'] ? 'Gestión Estratégica' : 'Backoffice') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-400 flex items-center gap-2"><i class="ph ph-calendar-blank"></i> Ingreso</span>
                                <span class="font-black text-slate-700 dark:text-slate-300">{{ $user->created_at->translatedFormat('M Y') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-400 flex items-center gap-2"><i class="ph ph-shield-star"></i> Rango</span>
                                <span class="font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">{{ $stats['nivel_operativo'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Badges Widget -->
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm flex justify-between items-center gap-2">
                    <div class="flex-1 flex flex-col items-center justify-center p-3 rounded-2xl hover:-translate-y-1 {{ $stats['efectividad'] > 90 ? 'bg-amber-50 text-amber-500 shadow-sm' : 'bg-slate-50 text-slate-300' }} transition-all cursor-help" title="Top Performer: Efectividad superior al 90%">
                        <i class="ph-fill ph-medal text-2xl mb-1"></i>
                        <span class="text-[8px] font-black uppercase tracking-tighter">Élite</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center justify-center p-3 rounded-2xl hover:-translate-y-1 {{ $stats['sla_cumplimiento'] > 95 ? 'bg-emerald-50 text-emerald-500 shadow-sm' : 'bg-slate-50 text-slate-300' }} transition-all cursor-help" title="Rápida Resolución: SLA cumplido sobre el 95%">
                        <i class="ph-fill ph-lightning text-2xl mb-1"></i>
                        <span class="text-[8px] font-black uppercase tracking-tighter">Rápido</span>
                    </div>
                    <div class="flex-1 flex flex-col items-center justify-center p-3 rounded-2xl hover:-translate-y-1 bg-indigo-50 text-indigo-500 shadow-sm transition-all cursor-help" title="Cuenta verificada y segura">
                        <i class="ph-fill ph-shield-check text-2xl mb-1"></i>
                        <span class="text-[8px] font-black uppercase tracking-tighter">Seguro</span>
                    </div>
                </div>
            </div>

            <!-- ================= RIGHT COLUMN: WORKSPACE TABS ================= -->
            <div class="xl:col-span-9">
                
                <!-- 🚀 TAB: DASHBOARD (MAIN) -->
                <div x-show="tab === 'dashboard'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-6">
                    
                    <!-- HIGH-DENSITY KPI ROW -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- KPI 1: Trabajados -->
                        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex justify-between items-start mb-2 relative z-10">
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Trabajados Hoy</h4>
                                    <p class="text-4xl font-black text-slate-900 dark:text-white leading-none">{{ $stats['entregados_hoy'] }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-[1rem] bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="ph-fill ph-folder-check text-xl"></i>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 mt-4 z-10 relative">
                                <span class="bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center"><i class="ph-bold ph-arrow-up-right"></i> Activo</span>
                                <span class="text-[9px] font-bold text-slate-400">Volumen diario</span>
                            </div>
                            <!-- Mini Sparkline SVG -->
                            <div class="absolute bottom-0 right-0 w-24 opacity-30 pointer-events-none text-indigo-500 transition-transform group-hover:scale-105">
                                <svg viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M0,25 L20,20 L40,25 L60,10 L80,15 L100,5" /></svg>
                            </div>
                        </div>

                        <!-- KPI 2: Pendientes -->
                        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex justify-between items-start mb-2 relative z-10">
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">En Cola</h4>
                                    <p class="text-4xl font-black text-slate-900 dark:text-white leading-none">{{ $stats['pendientes'] }}</p>
                                </div>
                                <div class="w-10 h-10 rounded-[1rem] bg-amber-50 dark:bg-amber-500/10 text-amber-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="ph-fill ph-stack text-xl"></i>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 mt-4 z-10 relative">
                                <span class="bg-amber-100 dark:bg-amber-500/20 text-amber-600 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center"><i class="ph-bold ph-minus"></i> {{ $stats['pendientes'] }}</span>
                                <span class="text-[9px] font-bold text-slate-400">Esperando gestión</span>
                            </div>
                             <div class="absolute bottom-0 right-0 w-24 opacity-30 pointer-events-none text-amber-400 transition-transform group-hover:scale-105">
                                <svg viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M0,5 L20,10 L40,15 L60,15 L80,20 L100,25" /></svg>
                            </div>
                        </div>

                        <!-- KPI 3: SLA -->
                        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex justify-between items-start mb-2 relative z-10">
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Cumplimiento SLA</h4>
                                    <div class="flex items-end gap-1">
                                        <p class="text-4xl font-black text-slate-900 dark:text-white leading-none">{{ $stats['sla_cumplimiento'] }}</p>
                                        <span class="text-xl font-bold text-slate-400 mb-0.5">%</span>
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-[1rem] bg-emerald-50 dark:bg-emerald-500/10 text-emerald-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="ph-fill ph-shield-check text-xl"></i>
                                </div>
                            </div>
                            <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full mt-4 overflow-hidden relative z-10">
                                <div class="h-full bg-emerald-500 transition-all duration-1000" style="width: {{ $stats['sla_cumplimiento'] }}%"></div>
                            </div>
                        </div>

                        <!-- KPI 4: AVG Time -->
                        <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-6 border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                            <div class="absolute inset-0 bg-gradient-to-br from-sky-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex justify-between items-start mb-2 relative z-10">
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Tiempo Respuesta</h4>
                                    <div class="flex items-end gap-1">
                                        <p class="text-4xl font-black text-slate-900 dark:text-white leading-none">{{ $stats['tiempo_promedio_min'] }}</p>
                                        <span class="text-xl font-bold text-slate-400 mb-0.5">m</span>
                                    </div>
                                </div>
                                <div class="w-10 h-10 rounded-[1rem] bg-sky-50 dark:bg-sky-500/10 text-sky-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <i class="ph-fill ph-clock-countdown text-xl"></i>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 mt-4 z-10 relative">
                                <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded text-[10px] font-bold flex items-center">Promedio</span>
                                <span class="text-[9px] font-bold text-slate-400">resolución global</span>
                            </div>
                             <div class="absolute bottom-0 right-0 w-24 opacity-30 pointer-events-none text-sky-400 transition-transform group-hover:scale-105">
                                <svg viewBox="0 0 100 30" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M0,15 L20,10 L40,20 L60,5 L80,15 L100,25" /></svg>
                            </div>
                        </div>
                    </div>

                    <!-- CHARTS ROW -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Main Productivity Area Chart -->
                        <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-100 dark:border-slate-800 shadow-sm relative overflow-hidden group">
                            <!-- Background Glow for Depth -->
                            <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50 dark:bg-indigo-500/5 rounded-full blur-3xl -z-10 transition-opacity opacity-0 group-hover:opacity-100"></div>

                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Rendimiento Operativo</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Tendencia de expedientes - 7 Días</p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-3 h-3 rounded-full bg-indigo-500 shadow-sm shadow-indigo-500/50"></span>
                                        <span class="text-[10px] font-black text-slate-500 uppercase">Completados</span>
                                    </div>
                                    <div class="h-6 w-px bg-slate-200 dark:bg-slate-700"></div>
                                    <button class="w-8 h-8 rounded-xl border border-slate-100 dark:border-slate-700 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-slate-50 transition-colors" title="Exportar Datos"><i class="ph ph-download-simple text-sm"></i></button>
                                </div>
                            </div>
                            
                            <div class="relative h-72 w-full">
                                <canvas id="performanceChart"></canvas>
                            </div>
                        </div>

                        <!-- Efficiency Time Tracker Ring -->
                        <div class="lg:col-span-4 bg-slate-900 text-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-900/10 relative overflow-hidden flex flex-col justify-between group">
                            <!-- Background Abstract Layer -->
                            <div class="absolute -right-16 -top-16 w-48 h-48 bg-indigo-500/30 rounded-full blur-3xl pointer-events-none group-hover:bg-indigo-400/40 transition-colors duration-1000"></div>
                            <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
                            
                            <div class="relative z-10 flex justify-between items-start">
                                <div>
                                    <h3 class="text-lg font-black tracking-tight mb-1">Efectividad General</h3>
                                    <p class="text-[10px] font-bold text-indigo-300 uppercase tracking-widest">Éxito vs Observaciones</p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-md">
                                    <i class="ph ph-target text-sm"></i>
                                </div>
                            </div>

                            <div class="relative w-44 h-44 mx-auto my-6 z-10">
                                <canvas id="successDonut"></canvas>
                                <!-- Center Stats -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                    <span class="text-5xl font-black">{{ $stats['efectividad'] }}<span class="text-xl text-indigo-400">%</span></span>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 mt-1">Calidad</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 relative z-10">
                                <div class="bg-white/10 backdrop-blur-md border border-white/5 rounded-[1.5rem] p-4 hover:bg-white/20 transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-300">Aprobados</p>
                                        <div class="w-2 h-2 rounded-full bg-indigo-400 shadow-[0_0_8px_rgba(129,140,248,0.8)]"></div>
                                    </div>
                                    <p class="text-3xl font-black">{{ $stats['completados_mes'] }}</p>
                                </div>
                                <div class="bg-white/10 backdrop-blur-md border border-white/5 rounded-[1.5rem] p-4 hover:bg-white/20 transition-colors">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-slate-300">Devueltos</p>
                                        <div class="w-2 h-2 rounded-full bg-rose-400 shadow-[0_0_8px_rgba(251,113,133,0.8)]"></div>
                                    </div>
                                    <p class="text-3xl font-black">{{ $stats['rechazados_mes'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- BOTTOM METRICS & ACTIVITY ROW -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        
                        <!-- Benchmarks / Progress -->
                        <div class="lg:col-span-7 bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                            <div class="mb-8 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Benchmarks Operativos</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Objetivos Mensuales de Desempeño</p>
                                </div>
                                <div class="bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                    En curso
                                </div>
                            </div>
                            
                            <div class="space-y-8 flex-1 flex flex-col justify-center">
                                <!-- Progress Item -->
                                <div class="group">
                                    <div class="flex justify-between items-end mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center transition-transform group-hover:scale-110"><i class="ph-fill ph-chart-line-up"></i></div>
                                            <div>
                                                <p class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">Meta de Volumen</p>
                                                <p class="text-[9px] font-bold text-slate-400 mt-0.5">{{ $stats['completados_mes'] }} de 500 requeridos</p>
                                            </div>
                                        </div>
                                        <span class="text-base font-black text-indigo-600">{{ min(round(($stats['completados_mes'] / 500) * 100), 100) }}%</span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000 relative overflow-hidden" style="width: {{ min(($stats['completados_mes'] / 500) * 100, 100) }}%">
                                             <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite]"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Item -->
                                <div class="group">
                                    <div class="flex justify-between items-end mb-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center transition-transform group-hover:scale-110"><i class="ph-fill ph-check-circle"></i></div>
                                            <div>
                                                <p class="text-xs font-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">Precisión Analítica</p>
                                                <p class="text-[9px] font-bold text-slate-400 mt-0.5">Tasa de trabajo sin devoluciones</p>
                                            </div>
                                        </div>
                                        <span class="text-base font-black text-emerald-600">{{ 100 - ($stats['rechazados_mes'] > 0 ? round(($stats['rechazados_mes'] / ($stats['completados_mes'] + $stats['rechazados_mes'])) * 100, 1) : 0) }}%</span>
                                    </div>
                                    <div class="w-full h-2.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden shadow-inner">
                                        <div class="h-full bg-emerald-500 rounded-full transition-all duration-1000 relative overflow-hidden" style="width: {{ 100 - ($stats['rechazados_mes'] > 0 ? round(($stats['rechazados_mes'] / ($stats['completados_mes'] + $stats['rechazados_mes'])) * 100, 1) : 0) }}%">
                                             <div class="absolute inset-0 bg-white/20 animate-[shimmer_2s_infinite] delay-700"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mini Activity Feed -->
                        <div class="lg:col-span-5 bg-white dark:bg-slate-900 rounded-[2.5rem] p-8 border border-slate-100 dark:border-slate-800 shadow-sm relative flex flex-col">
                            <div class="flex justify-between items-center mb-8">
                                <div>
                                    <h3 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">Actividad Reciente</h3>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Timeline Operativo</p>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-800 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 cursor-pointer transition-colors">
                                    <i class="ph ph-dots-three text-xl"></i>
                                </div>
                            </div>
                            
                            <!-- Timeline container -->
                            <div class="space-y-6 relative before:absolute before:inset-y-0 before:left-[15px] before:w-px before:bg-slate-200 dark:before:bg-slate-700 flex-1">
                                <!-- Item 1 -->
                                <div class="relative flex gap-4 group">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 dark:bg-emerald-500/10 border-4 border-white dark:border-slate-900 flex items-center justify-center relative z-10 shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="ph-fill ph-check-circle text-emerald-500 text-sm"></i>
                                    </div>
                                    <div class="pt-1 flex-1">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-black text-slate-800 dark:text-white">Inicio de Sesión Autorizado</p>
                                            <span class="text-[9px] font-black text-slate-400">{{ now()->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-500 mt-1">IP Segura validada correctamente.</p>
                                    </div>
                                </div>
                                <!-- Item 2 -->
                                <div class="relative flex gap-4 group">
                                    <div class="w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-500/10 border-4 border-white dark:border-slate-900 flex items-center justify-center relative z-10 shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="ph-fill ph-arrows-clockwise text-indigo-500 text-sm"></i>
                                    </div>
                                    <div class="pt-1 flex-1">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-black text-slate-800 dark:text-white">Sincronización de Dashboard</p>
                                            <span class="text-[9px] font-black text-slate-400">Hace 5 min</span>
                                        </div>
                                        <p class="text-[10px] font-bold text-slate-500 mt-1">Métricas operativas actualizadas.</p>
                                    </div>
                                </div>
                                <!-- Item 3 -->
                                <div class="relative flex gap-4 group">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 border-4 border-white dark:border-slate-900 flex items-center justify-center relative z-10 shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="ph-fill ph-calendar text-slate-400 text-sm"></i>
                                    </div>
                                    <div class="pt-1 flex-1">
                                        <div class="flex justify-between items-start">
                                            <p class="text-sm font-black text-slate-800 dark:text-white">Jornada Iniciada</p>
                                            <span class="text-[9px] font-black text-slate-400">08:00 AM</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button @click="tab = 'actividad'" class="w-full mt-6 py-3 bg-slate-50 dark:bg-slate-800/50 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all shadow-sm">Explorar Historial Completo</button>
                        </div>
                    </div>
                </div>

                <!-- 🚀 TAB: ACTIVIDAD (Detailed Log) -->
                <div x-show="tab === 'actividad'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" class="bg-white dark:bg-slate-900 rounded-[3rem] p-10 lg:p-14 border border-slate-100 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-10 pb-8 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1">Registro de Auditoría</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Trazabilidad de seguridad y accesos al ecosistema</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 flex items-center justify-center border border-indigo-100 dark:border-indigo-800/30">
                            <i class="ph-bold ph-list-dashes text-2xl"></i>
                        </div>
                    </div>
                    @include('profile.partials.security-information')
                </div>

                <!-- 🚀 TAB: SEGURIDAD -->
                <div x-show="tab === 'seguridad'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" class="space-y-8">
                    <div class="bg-white dark:bg-slate-900 p-10 lg:p-14 rounded-[3rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                        <div class="flex items-center justify-between mb-12 pb-8 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1">Centro de Seguridad</h3>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Protección de credenciales y administración de la cuenta</p>
                            </div>
                            <div class="w-14 h-14 rounded-2xl bg-slate-900 text-white flex items-center justify-center shadow-lg shadow-slate-900/20">
                                <i class="ph-bold ph-shield-check text-2xl"></i>
                            </div>
                        </div>
                        
                        <div class="space-y-16">
                            @include('profile.partials.update-password-form')
                            
                            <div class="bg-rose-50/50 dark:bg-rose-500/5 rounded-3xl border border-rose-100 dark:border-rose-900/30 p-10 relative overflow-hidden group">
                                <div class="absolute -right-10 -top-10 w-64 h-64 bg-rose-500/10 rounded-full blur-3xl pointer-events-none transition-transform group-hover:scale-110"></div>
                                <div class="flex items-center gap-4 mb-8 text-rose-600 relative z-10 border-b border-rose-100 dark:border-rose-900/30 pb-4">
                                    <i class="ph-fill ph-warning-octagon text-3xl"></i>
                                    <div>
                                        <h4 class="text-base font-black uppercase tracking-widest">Zona de Riesgo</h4>
                                        <p class="text-xs font-bold text-rose-400 mt-1">Acciones irreversibles sobre la identidad digital</p>
                                    </div>
                                </div>
                                <div class="relative z-10">
                                    @include('profile.partials.delete-user-form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🚀 TAB: AJUSTES (Información General) -->
                <div x-show="tab === 'ajustes'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" class="bg-white dark:bg-slate-900 p-10 lg:p-14 rounded-[3rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-12 pb-8 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1">Configuración del Perfil</h3>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Información personal, datos de contacto e identidad</p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-white flex items-center justify-center">
                            <i class="ph-bold ph-identification-card text-2xl"></i>
                        </div>
                    </div>
                    <div class="max-w-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="py-8 text-center opacity-40">
            <p class="text-[0.65rem] font-black text-slate-500 uppercase tracking-[0.5em]">Nexus Intelligent Profile &bull; Enterprise SaaS Design &bull; 2026</p>
        </div>

    </div>

    <!-- Hidden Form for Avatar Upload -->
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="avatar-form">
        @csrf
        @method('patch')
        <input type="file" name="avatar" id="avatar-sidebar-input" class="hidden" accept="image/*" onchange="syncAvatarPreview(this); submitAvatar();">
    </form>
</div>

<!-- ================= SCRIPTS & STYLES ================= -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<!-- MODAL DE RECORTE (Cropper) -->
<div id="cropper-modal" class="fixed inset-0 z-[200] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-slate-900/90 backdrop-blur-sm" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-2xl sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-white/10">
            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                <h3 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">Ajustar Imagen de Perfil</h3>
                <button onclick="closeCropper()" class="text-slate-400 hover:text-rose-500 transition-colors"><i class="ph-bold ph-x text-2xl"></i></button>
            </div>
            
            <div class="p-8">
                <div class="max-h-[400px] overflow-hidden rounded-3xl bg-slate-50 dark:bg-slate-950">
                    <img id="cropper-image" src="" class="max-w-full">
                </div>
                
                <div class="mt-8 flex justify-center gap-4">
                    <button onclick="cropper.rotate(-90)" class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-indigo-50 transition-colors"><i class="ph-bold ph-arrow-counter-clockwise text-xl"></i></button>
                    <button onclick="cropper.rotate(90)" class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-indigo-50 transition-colors"><i class="ph-bold ph-arrow-clockwise text-xl"></i></button>
                    <button onclick="cropper.zoom(0.1)" class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-indigo-50 transition-colors"><i class="ph-bold ph-magnifying-glass-plus text-xl"></i></button>
                    <button onclick="cropper.zoom(-0.1)" class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-indigo-50 transition-colors"><i class="ph-bold ph-magnifying-glass-minus text-xl"></i></button>
                </div>
            </div>

            <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 flex flex-col sm:flex-row gap-4">
                <button onclick="closeCropper()" class="flex-1 px-8 py-4 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-200 transition-all">Cancelar</button>
                <button onclick="applyCrop()" class="flex-[2] px-8 py-4 rounded-2xl bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all">Guardar Recorte</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let cropper;
    const modal = document.getElementById('cropper-modal');
    const image = document.getElementById('cropper-image');
    let currentInput;

    function submitAvatar() {
        // Esta función ya no se llama directamente al cambiar, sino tras el recorte
        document.getElementById('avatar-form').submit();
    }

    function syncAvatarPreview(input) {
        if (input.files && input.files[0]) {
            currentInput = input;
            const reader = new FileReader();
            reader.onload = function(e) {
                image.src = e.target.result;
                modal.classList.remove('hidden');
                
                if (cropper) cropper.destroy();
                
                cropper = new Cropper(image, {
                    aspectRatio: 1,
                    viewMode: 2,
                    dragMode: 'move',
                    background: false,
                    autoCropArea: 1,
                    responsive: true,
                });
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function closeCropper() {
        modal.classList.add('hidden');
        if (cropper) cropper.destroy();
        if (currentInput) currentInput.value = '';
    }

    function applyCrop() {
        const canvas = cropper.getCroppedCanvas({
            width: 512,
            height: 512,
        });

        canvas.toBlob((blob) => {
            // Creamos un nuevo archivo a partir del blob
            const file = new File([blob], "avatar.jpg", { type: "image/jpeg" });
            
            // Reemplazamos el archivo en el input del formulario de avatar
            const container = new DataTransfer();
            container.items.add(file);
            document.getElementById('avatar-sidebar-input').files = container.files;
            
            // Actualizamos la previsualización local antes de enviar
            document.getElementById('avatar-preview').src = canvas.toDataURL();
            
            // Cerramos modal y enviamos
            modal.classList.add('hidden');
            submitAvatar();
        }, 'image/jpeg', 0.9);
    }

    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. PERFORMANCE AREA CHART (Glass/Gradient Style) ---
        const perfCtx = document.getElementById('performanceChart').getContext('2d');
        
        // Premium Linear Gradient for Line Fill
        let gradientFill = perfCtx.createLinearGradient(0, 0, 0, 350);
        gradientFill.addColorStop(0, 'rgba(99, 102, 241, 0.45)'); // Indigo-500 stronger
        gradientFill.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        new Chart(perfCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($stats['tendencia_semanal'], 'day')) !!},
                datasets: [{
                    label: 'Expedientes',
                    data: {!! json_encode(array_column($stats['tendencia_semanal'], 'count')) !!},
                    borderColor: '#6366f1', // Indigo 500
                    backgroundColor: gradientFill,
                    borderWidth: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    fill: true,
                    tension: 0.45 // Even smoother curves
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 12, family: "'Inter', sans-serif", weight: 'bold' },
                        bodyFont: { size: 16, family: "'Inter', sans-serif", weight: 'black' },
                        padding: 14,
                        cornerRadius: 16,
                        displayColors: false,
                        callbacks: {
                            label: function(context) { return context.parsed.y + ' Trabajados'; }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(200, 200, 200, 0.15)', borderDash: [5, 5] },
                        border: { display: false },
                        ticks: { font: { size: 11, weight: 'bold' }, color: '#94a3b8', padding: 10 }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 11, weight: 'bold' }, color: '#94a3b8', padding: 10 }
                    }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });

        // --- 2. SUCCESS DONUT CHART (Tracker Ring) ---
        const donutCtx = document.getElementById('successDonut').getContext('2d');
        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Aprobados', 'Devueltos'],
                datasets: [{
                    data: [{{ $stats['completados_mes'] }}, {{ $stats['rechazados_mes'] }}],
                    backgroundColor: ['#6366f1', '#fb7185'], // Indigo & Rose
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '82%', // Extra thin elegant ring
                borderRadius: 20, // Rounded edges on segments
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#ffffff',
                        titleColor: '#0f172a',
                        bodyColor: '#0f172a',
                        bodyFont: { size: 14, weight: 'black' },
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: true,
                        boxPadding: 4
                    }
                }
            }
        });
    });
</script>

<style>
    /* Utility Hide for Alpine Tabs */
    [x-cloak] { display: none !important; }
    
    /* Elegant Form Overrides within the Profile */
    input:focus, textarea:focus, select:focus {
        border-color: #6366f1 !important; 
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
        transition: all 0.3s ease;
    }
    
    /* Shimmer effect for XP bar and buttons */
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
</style>
@endsection
