@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;600;800&family=JetBrains+Mono:wght@400;500&display=swap');

    :root {
        --primary: #0066FF;
        --primary-dark: #0052CC;
        --success: #00D084;
        --warning: #FFAB00;
        --danger: #FF5630;
        --surface: #FFFFFF;
        --background: #F4F7FA;
        --text-main: #172B4D;
        --text-muted: #6B778C;
        --border: #DFE1E6;
        --radius-lg: 24px;
        --radius-md: 16px;
        --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        --shadow-lg: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    }

    body {
        background-color: var(--background);
        font-family: 'Inter', sans-serif;
        color: var(--text-main);
        -webkit-font-smoothing: antialiased;
    }

    .premium-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .premium-card:hover {
        box-shadow: var(--shadow-lg);
        border-color: #C1C7D0;
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-premium {
        padding: 0.8rem 1.5rem;
        border-radius: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        items-center;
        gap: 8px;
        font-size: 0.85rem;
    }

    .btn-primary-sync {
        background: var(--primary);
        color: white;
        box-shadow: 0 4px 14px rgba(0, 102, 255, 0.3);
    }

    .btn-primary-sync:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 102, 255, 0.4);
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .status-synced { background: #E3FCEF; color: #006644; }
    .status-pending { background: #FFF0B3; color: #826331; }
    .status-error { background: #FFEBE6; color: #BF2600; }
    .status-conflict { background: #EAE6FF; color: #403294; }

    /* Timeline Styles */
    .timeline-item {
        position: relative;
        padding-left: 32px;
        padding-bottom: 24px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--border);
    }

    .timeline-item:last-child::before { display: none; }

    .timeline-dot {
        position: absolute;
        left: 0;
        top: 0;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: white;
        border: 2px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    /* Pulse Effect */
    .pulse-online {
        width: 8px;
        height: 8px;
        background: var(--success);
        border-radius: 50%;
        position: relative;
    }

    .pulse-online::after {
        content: '';
        position: absolute;
        inset: -4px;
        border: 2px solid var(--success);
        border-radius: 50%;
        animation: pulse-ring 1.5s infinite;
    }

    @keyframes pulse-ring {
        0% { transform: scale(0.5); opacity: 0.8; }
        100% { transform: scale(1.5); opacity: 0; }
    }

    /* Progress HUD */
    .hud-progress-container {
        background: #091E42;
        color: white;
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .hud-progress-bar {
        height: 8px;
        background: rgba(255,255,255,0.1);
        border-radius: 4px;
        overflow: hidden;
    }

    .hud-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0066FF, #00B2FF);
        transition: width 0.5s ease;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #C1C7D0; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #97A0AF; }

    .font-outfit { font-family: 'Outfit', sans-serif; }
    .font-mono { font-family: 'JetBrains Mono', monospace; }

    [x-cloak] { display: none !important; }
</style>

<div class="p-6 lg:p-10 max-w-[1600px] mx-auto" x-data="syncCenter()">
    
    <!-- Top Header & Connectivity -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h1 class="text-4xl font-black tracking-tight text-slate-900 font-outfit">Sincronización <span class="text-blue-600">Cloud</span></h1>
            <div class="flex items-center gap-4 mt-2">
                <div class="flex items-center gap-2 px-3 py-1 bg-white border border-slate-200 rounded-full shadow-sm">
                    <div class="pulse-online"></div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Sistema en Línea</span>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Build 2026.05.11</span>
            </div>
        </div>

        <div class="flex items-center gap-4 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
            <!-- Firebase Status -->
            <div class="flex items-center gap-3 px-4 py-2 border-r border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500">
                    <i class="ph-fill ph-fire text-lg"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Firebase</p>
                    <p class="text-[11px] font-bold text-slate-900 leading-none">{{ strtoupper($stats['connectivity']['firebase']) }}</p>
                </div>
            </div>
            <!-- Node SAFE Status -->
            <div class="flex items-center gap-3 px-4 py-2 border-r border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="ph-fill ph-shield-check text-lg"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Nodo SAFE</p>
                    <p class="text-[11px] font-bold text-slate-900 leading-none">CONNECTED</p>
                </div>
            </div>
            <!-- Server Status -->
            <div class="flex items-center gap-3 px-4 py-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="ph-fill ph-hard-drives text-lg"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase leading-none mb-1">Servidor</p>
                    <p class="text-[11px] font-bold text-slate-900 leading-none">OPTIMAL</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Advanced Module Navigation -->
    <div class="flex flex-wrap gap-4 mb-10">
        <a href="{{ route('carnetizacion.sync_center.records') }}" class="btn-premium bg-white border border-slate-200 text-slate-700 hover:border-blue-300 hover:text-blue-600 shadow-sm">
            <i class="ph ph-database text-lg"></i> Explorador de Registros
        </a>
        <a href="{{ route('carnetizacion.sync_center.conflicts') }}" class="btn-premium bg-white border border-slate-200 text-slate-700 hover:border-indigo-300 hover:text-indigo-600 shadow-sm">
            <i class="ph ph-warning-diamond text-lg"></i> 
            Gestión de Conflictos
            <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full text-[9px] font-black">{{ $stats['overview']['conflicts'] }}</span>
        </a>
        <button onclick="window._syncCenterInstance && window._syncCenterInstance.runHealthCheck()" class="btn-premium bg-slate-900 text-white hover:bg-slate-800">
            <i class="ph ph-activity text-lg"></i> Auditoría de Integridad
        </button>
    </div>

    <!-- Main Operative Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <!-- Left: Monitoring & Content -->
        <div class="lg:col-span-8 space-y-10">
            
            <!-- Quick Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="premium-card p-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Total Afiliados</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-black text-slate-900 font-outfit">{{ number_format($stats['overview']['total']) }}</h3>
                        <span class="text-[10px] font-bold text-slate-400 mb-1">Registros</span>
                    </div>
                </div>
                <div class="premium-card p-6 border-l-4 border-l-emerald-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Sincronizados</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-black text-emerald-600 font-outfit">{{ number_format($stats['overview']['synced']) }}</h3>
                        <span class="text-[10px] font-bold text-emerald-500 mb-1">{{ round(($stats['overview']['synced'] / max(1, $stats['overview']['total'])) * 100, 1) }}%</span>
                    </div>
                </div>
                <div class="premium-card p-6 border-l-4 border-l-amber-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Pendientes</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-black text-amber-600 font-outfit">{{ number_format($stats['overview']['pending']) }}</h3>
                        <i class="ph-bold ph-clock-countdown text-amber-300 text-2xl mb-1"></i>
                    </div>
                </div>
                <div class="premium-card p-6 border-l-4 border-l-rose-500">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Conflictos</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-3xl font-black text-rose-600 font-outfit">{{ number_format($stats['overview']['conflicts']) }}</h3>
                        <i class="ph-bold ph-warning-diamond text-rose-300 text-2xl mb-1"></i>
                    </div>
                </div>
            </div>

            <!-- Active Sync Progress (Visible when active) -->
            <div x-show="syncActive" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 -translate-y-4" class="hud-progress-container shadow-2xl">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="px-3 py-1 bg-blue-500 text-white rounded-full text-[9px] font-black uppercase tracking-widest animate-pulse" x-text="syncLabel">Sincronizando...</span>
                            <span class="text-slate-400 text-xs font-medium" x-text="performedBy">Manual</span>
                        </div>
                        <h2 class="text-3xl font-black tracking-tight" x-text="currentTaskName">Procesando registros en la nube</h2>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-5xl font-black text-white font-outfit" x-text="syncProgress + '%'">0%</span>
                        <span class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mt-1" x-text="eta ? 'ETA: ' + eta : 'Calculando...'">Calculando ETA</span>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="hud-progress-bar">
                        <div class="hud-progress-fill" :style="'width: ' + syncProgress + '%'"></div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Módulo</p>
                            <p class="text-xs font-bold" x-text="currentModule">Afiliados</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Insertados</p>
                            <p class="text-xs font-bold text-emerald-400" x-text="stats.created || 0">0</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Actualizados</p>
                            <p class="text-xs font-bold text-blue-400" x-text="stats.updated || 0">0</p>
                        </div>
                        <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                            <p class="text-[8px] font-black text-slate-500 uppercase mb-1">Estatus</p>
                            <p class="text-xs font-bold text-amber-400 uppercase" x-text="controlState">Running</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button @click="controlSync('pause')" x-show="controlState !== 'paused'" class="w-12 h-12 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center transition-all">
                        <i class="ph-bold ph-pause text-xl"></i>
                    </button>
                    <button @click="controlSync('resume')" x-show="controlState === 'paused'" class="w-12 h-12 rounded-xl bg-emerald-500 hover:bg-emerald-600 flex items-center justify-center transition-all">
                        <i class="ph-bold ph-play text-xl"></i>
                    </button>
                    <button @click="controlSync('cancel')" class="w-12 h-12 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-500 hover:text-white flex items-center justify-center transition-all">
                        <i class="ph-bold ph-x text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Main Interactive Hub -->
            <div class="premium-card p-0 overflow-hidden" x-show="!syncActive">
                <div class="p-8 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Centro de Operaciones</h3>
                        <p class="text-xs text-slate-500 mt-1">Seleccione la dirección de sincronización deseada.</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                            <i class="ph-bold ph-question text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Download Card -->
                    <div class="group relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-[32px] blur opacity-0 group-hover:opacity-10 transition duration-500"></div>
                        <div class="relative p-10 bg-white border border-slate-100 rounded-[32px] hover:border-emerald-200 transition-all text-center">
                            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-[24px] flex items-center justify-center mx-auto mb-8 group-hover:scale-110 transition-transform">
                                <i class="ph-fill ph-cloud-arrow-down text-4xl"></i>
                            </div>
                            <h4 class="text-2xl font-black text-slate-900 mb-3 tracking-tighter">Descargar Nube</h4>
                            <p class="text-xs text-slate-500 mb-8 leading-relaxed">Trae las actualizaciones realizadas por SAFE y otros terminales remotos a tu base de datos local.</p>
                            
                            <div class="flex flex-col gap-3">
                                <button @click="startSync('pull', 'full')" class="w-full py-4 bg-emerald-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 shadow-xl shadow-emerald-500/20 transition-all flex items-center justify-center gap-3">
                                    <i class="ph-bold ph-lightning"></i> Sincronización Total
                                </button>
                                <button @click="startSync('pull', 'afiliados')" class="w-full py-4 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                                    Solo Afiliados
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Card -->
                    <div class="group relative">
                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[32px] blur opacity-0 group-hover:opacity-10 transition duration-500"></div>
                        <div class="relative p-10 bg-white border border-slate-100 rounded-[32px] hover:border-blue-200 transition-all text-center">
                            <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-[24px] flex items-center justify-center mx-auto mb-8 group-hover:scale-110 transition-transform">
                                <i class="ph-fill ph-cloud-arrow-up text-4xl"></i>
                            </div>
                            <h4 class="text-2xl font-black text-slate-900 mb-3 tracking-tighter">Subir Cambios</h4>
                            <p class="text-xs text-slate-500 mb-8 leading-relaxed">Envía tus gestiones locales (nuevos afiliados, cambios de estado) a la nube para que SAFE pueda verlos.</p>
                            
                            <div class="flex flex-col gap-3">
                                <button @click="startSync('push', 'full')" class="w-full py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 shadow-xl shadow-blue-600/20 transition-all flex items-center justify-center gap-3">
                                    <i class="ph-bold ph-paper-plane-tilt"></i> Publicar Pendientes
                                </button>
                                <button @click="startSync('push', 'afiliados')" class="w-full py-4 bg-white border border-slate-200 text-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                                    Solo Afiliados Pendientes
                                </button>
                                <button @click="startSync('push', 'full', true)" class="w-full py-3 bg-orange-50 border border-orange-200 text-orange-700 rounded-2xl text-[9px] font-black uppercase tracking-widest hover:bg-orange-100 transition-all flex items-center justify-center gap-2">
                                    <i class="ph-bold ph-warning"></i> Forzar Subida Total (todos los registros)
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Timeline (Linear Style) -->
            <div class="premium-card overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white">
                            <i class="ph-bold ph-activity text-xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight font-outfit">Línea de Tiempo Operativa</h3>
                    </div>
                    <a href="#" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Ver Reporte Completo</a>
                </div>

                <div class="p-8 max-h-[600px] overflow-y-auto">
                    @forelse($audits as $audit)
                        <div class="timeline-item">
                            <div class="timeline-dot">
                                @if(($audit->company_origin ?? '') === 'CMD')
                                    <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                @else
                                    <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                                @endif
                            </div>
                            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 group hover:border-blue-200 transition-all">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-black text-slate-500 uppercase tracking-tighter">{{ $audit->company_origin ?? 'N/A' }}</span>
                                        <span class="text-xs font-black text-slate-900">{{ $audit->user_name ?? 'Sistema' }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400">{{ $audit->created_at->diffForHumans() }}</span>
                                </div>
                                
                                <div class="flex flex-col gap-2">
                                    <p class="text-xs font-medium text-slate-600">
                                        Modificó registro <span class="font-bold text-slate-900">{{ $audit->auditable_id }}</span>
                                    </p>
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white rounded-xl border border-slate-100 shadow-sm">
                                            <span class="text-[9px] font-black text-slate-400 uppercase">{{ $audit->field }}:</span>
                                            <span class="text-[10px] font-bold text-rose-500 line-through">{{ $audit->old_value ?? 'N/A' }}</span>
                                            <i class="ph ph-arrow-right text-[10px] text-slate-400"></i>
                                            <span class="text-[10px] font-bold text-emerald-600">{{ $audit->new_value ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-20 opacity-40">
                            <i class="ph ph-ghost text-5xl mb-4"></i>
                            <p class="text-[10px] font-bold uppercase tracking-widest">No se detecta actividad reciente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Controls & Context -->
        <aside class="lg:col-span-4 space-y-10">
            
            <!-- System Health -->
            <div class="premium-card p-8 bg-slate-900 text-white relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-500/10 rounded-full blur-3xl"></div>
                
                <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.3em] mb-8">Estado de Salud Cloud</h4>
                
                <div class="space-y-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-emerald-400">
                                <i class="ph-bold ph-check-circle text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold">Base de Datos</p>
                                <p class="text-[9px] text-slate-400">Local Postgres/MySQL</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black text-emerald-400">OPTIMAL</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-blue-400">
                                <i class="ph-bold ph-cloud-check text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold">Firebase Realtime</p>
                                <p class="text-[9px] text-slate-400">Google Cloud Platform</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black text-blue-400">ACTIVE</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-amber-400">
                                <i class="ph-bold ph-users-three text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold">Cola de Procesos</p>
                                <p class="text-[9px] text-slate-400">Laravel Horizon</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-black text-amber-400" x-text="queueCount + ' PENDING'">{{ $stats['realtime']['queue_count'] }} PENDING</span>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-white/5">
                    <div class="flex items-center gap-4 mb-4">
                        <!-- Advanced Navigation -->
                        <a href="{{ route('carnetizacion.sync_center.records') }}" class="hidden lg:flex items-center gap-3 px-5 py-3 bg-white border border-slate-100 rounded-[25px] shadow-sm hover:border-blue-300 transition-all">
                            <span class="material-symbols-outlined text-blue-600 text-sm">database</span>
                            <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Registros</div>
                        </a>

                        <a href="{{ route('carnetizacion.sync_center.conflicts') }}" class="hidden lg:flex items-center gap-3 px-5 py-3 bg-white border border-slate-100 rounded-[25px] shadow-sm hover:border-indigo-300 transition-all">
                            <span class="material-symbols-outlined text-indigo-600 text-sm">warning</span>
                            <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">
                                Conflictos: <span class="text-indigo-900">{{ $stats['overview']['conflicts'] }}</span>
                            </div>
                        </a>
                    </div>

                    <button @click="runHealthCheck()" class="w-full py-4 bg-white/10 hover:bg-white text-white hover:text-slate-900 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                        Lanzar Diagnóstico Completo
                    </button>
                </div>
            </div>

            <!-- Quota Management -->
            <div class="premium-card p-8">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-8">Consumo de Cuotas (24h)</h4>
                
                <div class="space-y-8">
                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-[10px] font-black text-slate-800 uppercase">Lecturas</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ number_format($stats['quota']['reads_today']) }} / {{ number_format($stats['quota']['limit_reads']) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-emerald-500 h-full rounded-full" style="width: {{ ($stats['quota']['reads_today'] / max(1, $stats['quota']['limit_reads'])) * 100 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-end mb-3">
                            <span class="text-[10px] font-black text-slate-800 uppercase">Escrituras</span>
                            <span class="text-[10px] font-bold text-slate-400">{{ number_format($stats['quota']['writes_today']) }} / {{ number_format($stats['quota']['limit_writes']) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2">
                            <div class="bg-blue-600 h-full rounded-full" style="width: {{ ($stats['quota']['writes_today'] / max(1, $stats['quota']['limit_writes'])) * 100 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4">
                    <i class="ph-bold ph-info text-amber-600 text-xl"></i>
                    <p class="text-[10px] font-medium text-amber-800 leading-relaxed">Las cuotas se resetean cada 24 horas. Evite realizar sincronizaciones totales innecesarias.</p>
                </div>
            </div>

            <!-- Safety Controls -->
            <div class="premium-card p-8">
                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Controles de Seguridad</h4>
                
                <div class="space-y-4">
                    <label class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-slate-100 transition-all">
                        <div class="flex items-center gap-3">
                            <i class="ph ph-flask text-amber-600"></i>
                            <span class="text-xs font-black text-slate-800 uppercase">Modo Simulación</span>
                        </div>
                        <div class="relative inline-flex items-center">
                            <input type="checkbox" x-model="simulationMode" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                        </div>
                    </label>

                    <label class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl cursor-pointer hover:bg-slate-100 transition-all">
                        <div class="flex items-center gap-3">
                            <i class="ph ph-camera text-blue-600"></i>
                            <span class="text-xs font-black text-slate-800 uppercase">Auto-Snapshot</span>
                        </div>
                        <div class="relative inline-flex items-center">
                            <input type="checkbox" x-model="autoSnapshot" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6">
                    <button @click="purgeCache()" class="py-4 bg-slate-100 hover:bg-slate-200 rounded-2xl text-[9px] font-black uppercase text-slate-600 transition-all">Purgar Caché</button>
                    <button @click="cleanupSnapshots()" class="py-4 bg-slate-100 hover:bg-slate-200 rounded-2xl text-[9px] font-black uppercase text-slate-600 transition-all">Limpiar Backups</button>
                </div>
            </div>
        </aside>
    </div>

    <!-- History Log Table -->
    <section class="mt-20">
        <div class="flex justify-between items-end mb-10">
            <div>
                <h3 class="text-3xl font-black text-slate-900 font-outfit tracking-tighter">Historial de Operaciones</h3>
                <p class="text-xs text-slate-500 mt-2">Registro inmutable de todas las sincronizaciones ejecutadas.</p>
            </div>
            <div class="flex gap-3">
                <button class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2">
                    <i class="ph-bold ph-export text-lg"></i> Exportar
                </button>
            </div>
        </div>

        <div class="premium-card overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-[10px] font-black uppercase text-slate-400 tracking-[0.2em]">
                    <tr>
                        <th class="px-8 py-5">Sesión</th>
                        <th class="px-8 py-5">Tipo</th>
                        <th class="px-8 py-5">Estado</th>
                        <th class="px-8 py-5">Usuario</th>
                        <th class="px-8 py-5">Items</th>
                        <th class="px-8 py-5 text-right">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-all">
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs text-slate-400 font-bold">#{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $log->type === 'Pull' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                                        <i class="ph-fill {{ $log->type === 'Pull' ? 'ph-arrow-down-left' : 'ph-arrow-up-right' }}"></i>
                                    </div>
                                    <span class="text-xs font-black text-slate-800">{{ $log->type }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="status-badge {{ $log->status === 'success' ? 'status-synced' : ($log->status === 'failed' ? 'status-error' : 'status-pending') }}">
                                    {{ $log->status }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-bold text-slate-600">{{ $log->performed_by }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <span class="text-xs font-black text-slate-900">{{ number_format($log->items_count ?? 0) }}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-xs font-black text-slate-800">{{ $log->created_at->format('d/m/Y') }}</p>
                                <p class="text-[10px] font-medium text-slate-400">{{ $log->created_at->format('H:i') }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-6 bg-slate-50/50 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        </div>
    </section>
</div>

<!-- Scripts Section -->
@push('scripts')
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function syncCenter() {
        return {
            syncActive: false,
            syncProgress: 0,
            syncLabel: 'Idle',
            currentTaskName: 'Ready',
            performedBy: '',
            currentModule: '--',
            controlState: 'running',
            eta: null,
            stats: {},
            queueCount: {{ $stats['realtime']['queue_count'] }},
            simulationMode: false,
            autoSnapshot: true,

            init() {
                window._syncCenterInstance = this;
                this.pollStatus();
                setInterval(() => this.pollStatus(), 3000);
            },

            async pollStatus() {
                try {
                    const response = await fetch('{{ route("carnetizacion.sync_center.progress") }}');
                    const data = await response.json();
                    
                    this.syncActive = data.active;
                    this.syncProgress = data.progress;
                    this.syncLabel = data.label;
                    this.performedBy = data.performedBy;
                    this.controlState = data.control;
                    this.eta = data.eta;
                    this.stats = data.stats.afiliados || data.stats.empresas || {};
                    this.currentModule = data.stats.afiliados ? 'Afiliados' : (data.stats.empresas ? 'Empresas' : '--');
                    this.currentTaskName = data.label || 'Procesando...';
                } catch (e) {
                    console.error('Error polling status:', e);
                }
            },

            async startSync(type, scope, force = false) {
                const title = type === 'pull' ? 'Confirmar Descarga' : (force ? '⚠️ Forzar Subida Total' : 'Confirmar Subida');
                const text = type === 'pull' 
                    ? '¿Deseas descargar los registros desde Firebase?' 
                    : (force 
                        ? '¿Seguro? Esto subirá TODOS los registros (incluso los ya sincronizados). Puede tardar varios minutos.'
                        : '¿Deseas publicar tus cambios pendientes en la nube?');

                const result = await Swal.fire({
                    title: title,
                    text: text,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, iniciar',
                    confirmButtonColor: type === 'pull' ? '#10b981' : '#3b82f6',
                    cancelButtonText: 'Cancelar',
                    customClass: {
                        popup: 'rounded-[32px]',
                        confirmButton: 'rounded-xl px-8 py-3 font-bold',
                        cancelButton: 'rounded-xl px-8 py-3 font-bold'
                    }
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`{{ url('carnetizacion/sync-center') }}/${type}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                full: scope === 'full',
                                afiliados: scope === 'afiliados' || scope === 'full',
                                empresas: scope === 'empresas' || scope === 'full',
                                simulation: this.simulationMode,
                                snapshot: this.autoSnapshot,
                                force: force
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.syncActive = true;
                            this.pollStatus();
                        } else {
                            Swal.fire('Error', data.error, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'No se pudo conectar con el servidor', 'error');
                    }
                }
            },

            async controlSync(action) {
                try {
                    await fetch(`{{ url('carnetizacion/sync-center') }}/${action}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    this.pollStatus();
                } catch (e) { console.error(e); }
            },

            async runHealthCheck() {
                Swal.fire({
                    title: 'Auditoría en Progreso',
                    text: 'Conectando con Firebase Hub para verificar integridad...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                try {
                    const response = await fetch('{{ route("carnetizacion.sync_center.health_check") }}');
                    const data = await response.json();
                    
                    Swal.fire({
                        title: 'Diagnóstico Completado',
                        html: `
                            <div class="text-left space-y-4 p-4">
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-bold">Latencia:</span>
                                    <span class="text-blue-600">${data.health.latency}ms</span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-bold">Diferencia Afiliados:</span>
                                    <span class="${data.diff.afiliados !== 0 ? 'text-amber-600' : 'text-emerald-600'}">${data.diff.afiliados}</span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="font-bold">No Normalizados:</span>
                                    <span class="text-rose-600">${data.health.unnormalized}</span>
                                </div>
                            </div>
                        `,
                        icon: 'info',
                        customClass: { popup: 'rounded-[32px]' }
                    });
                } catch (e) {
                    Swal.fire('Error', 'No se pudo completar el diagnóstico', 'error');
                }
            },

            async purgeCache() {
                const res = await fetch('{{ route("carnetizacion.sync_center.purge_cache") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                if (res.ok) Swal.fire('Caché Purgada', 'El motor se ha refrescado correctamente.', 'success');
            },

            async cleanupSnapshots() {
                const res = await fetch('{{ route("carnetizacion.sync_center.cleanup_snapshots") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
                if (res.ok) Swal.fire('Limpieza Exitosa', 'Backups antiguos eliminados.', 'success');
            }
        };
    }
</script>
@endpush
@endsection
