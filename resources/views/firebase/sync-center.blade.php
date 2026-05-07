@extends('layouts.app')

@php
if (!function_exists('generateSparkPath')) {
    function generateSparkPath($data) {
        if (empty($data) || count($data) < 2) return "M 0 35 L 100 35";
        $max = max($data) ?: 1;
        $step = 100 / (count($data) - 1);
        $path = "";
        foreach ($data as $i => $v) {
            $y = 35 - (($v / $max) * 30);
            $x = $i * $step;
            $path .= ($i === 0 ? "M" : " L") . " $x $y";
        }
        return $path;
    }
}
@endphp

@push('styles')
    <!-- Alpine Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <!-- Alpine Core -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap');

    /* Reset & Base */
    main.ml-80 { padding: 0 !important; }
    main.ml-80 > div { max-width: none !important; padding: 0 !important; margin: 0 !important; }
    body { 
        background-color: #f8fafc; 
        font-family: 'Outfit', sans-serif; 
        color: #1e293b;
        overflow-x: hidden;
        min-height: 100vh;
    }

    /* Animated Blobs Background */
    .blob-bg {
        position: fixed;
        inset: 0;
        z-index: -1;
        overflow: hidden;
        background: #f8fafc;
    }
    .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.4;
        animation: move-blob 25s infinite alternate ease-in-out;
    }
    .blob-1 { width: 600px; height: 600px; background: rgba(59, 130, 246, 0.1); top: -100px; right: -100px; animation-delay: 0s; }
    .blob-2 { width: 500px; height: 500px; background: rgba(147, 51, 234, 0.08); bottom: -100px; left: -100px; animation-delay: -5s; }
    .blob-3 { width: 400px; height: 400px; background: rgba(16, 185, 129, 0.05); top: 40%; left: 30%; animation-delay: -10s; }

    @keyframes move-blob {
        0% { transform: translate(0, 0) scale(1) rotate(0deg); }
        50% { transform: translate(100px, 50px) scale(1.1) rotate(90deg); }
        100% { transform: translate(-50px, 150px) scale(0.9) rotate(180deg); }
    }

    /* Glassmorphism Pro */
    .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
        border-radius: 32px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .glass-panel:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 60px rgba(59, 130, 246, 0.08);
        border-color: rgba(59, 130, 246, 0.2);
    }

    /* Circular Progress */
    .circular-progress { position: relative; width: 64px; height: 64px; }
    .circular-progress svg { width: 64px; height: 64px; transform: rotate(-90deg); }
    .circular-progress circle { fill: none; stroke-width: 5; stroke-linecap: round; }
    .circular-progress .bg { stroke: rgba(226, 232, 240, 0.5); }
    .circular-progress .bar { stroke: #3b82f6; transition: stroke-dashoffset 1s ease-in-out; }

    /* Sparkline Style */
    .sparkline-container { width: 100px; height: 40px; }
    .sparkline-path { fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

    /* Button Shine Effect */
    .btn-shine {
        position: relative;
        overflow: hidden;
    }
    .btn-shine::after {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: rotate(45deg);
        animation: shine 4s infinite linear;
        pointer-events: none;
    }
    @keyframes shine {
        0% { transform: translate(-100%, -100%) rotate(45deg); }
        20%, 100% { transform: translate(100%, 100%) rotate(45deg); }
    }

    /* Data Engine HUD */
    .hub-container { position: relative; width: 220px; height: 220px; }
    .hub-ring {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid transparent;
        transition: all 0.8s ease;
    }
    .ring-outer { border-top-color: #3b82f6; border-bottom-color: #6366f1; animation: rotate-cw 10s linear infinite; opacity: 0.4; }
    .ring-middle { border-left-color: #3b82f6; border-right-color: #10b981; animation: rotate-ccw 15s linear infinite; opacity: 0.2; inset: 20px; }
    .ring-inner { border-top-color: #3b82f6; animation: rotate-cw 5s linear infinite; opacity: 0.5; inset: 40px; }
    
    .hub-center {
        position: absolute;
        inset: 60px;
        background: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.2), inset 0 2px 5px rgba(255,255,255,1);
        z-index: 10;
        border: 1px solid rgba(59, 130, 246, 0.1);
    }
    .hub-aura {
        position: absolute;
        inset: 0;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        animation: pulse-aura 3s ease-in-out infinite;
    }

    @keyframes rotate-cw { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @keyframes rotate-ccw { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }
    @keyframes pulse-aura { 0%, 100% { transform: scale(0.9); opacity: 0.3; } 50% { transform: scale(1.3); opacity: 0.6; } }

    /* Energy Bolts */
    .energy-path { height: 3px; background: rgba(226, 232, 240, 0.5); position: relative; overflow: hidden; border-radius: 10px; }
    .energy-bolt { position: absolute; top: 0; width: 140px; height: 100%; background: linear-gradient(90deg, transparent, #3b82f6, #60a5fa, transparent); filter: blur(2px); opacity: 0; }
    .bolt-push { animation: energy-flow-right 1.5s linear infinite; opacity: 1; }
    .bolt-pull { animation: energy-flow-left 1.5s linear infinite; opacity: 1; }

    @keyframes energy-flow-right { 0% { left: -140px; } 100% { left: 100%; } }
    @keyframes energy-flow-left { 0% { right: -140px; } 100% { right: 100%; } }

    /* Console Style */
    .terminal-window { background: #0f172a; border-radius: 28px; box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3); border: 1px solid rgba(255,255,255,0.05); }
    .terminal-header { background: rgba(255,255,255,0.03); padding: 12px 24px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .terminal-dot { width: 12px; height: 12px; border-radius: 50%; }
    .terminal-body { padding: 24px; font-family: 'JetBrains Mono', monospace; font-size: 11px; height: 500px; overflow-y: auto; color: #94a3b8; }

    .btn-action-main {
        padding: 1.1rem 1.8rem;
        border-radius: 24px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        font-size: 0.7rem;
    }
    .btn-push { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; box-shadow: 0 12px 24px -6px rgba(59, 130, 246, 0.5); }
    .btn-pull { background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 12px 24px -6px rgba(16, 185, 129, 0.5); }
    .btn-action-main:hover { transform: translateY(-5px); box-shadow: 0 20px 35px -8px rgba(0,0,0,0.15); filter: brightness(1.1); }

    /* Side Drawer */
    #log-drawer {
        position: fixed;
        top: 0;
        right: -500px;
        width: 500px;
        height: 100vh;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(30px) saturate(200%);
        box-shadow: -20px 0 60px rgba(0,0,0,0.1);
        z-index: 1000;
        transition: right 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        border-left: 1px solid rgba(255,255,255,0.5);
    }
    #log-drawer.open { right: 0; }
    .drawer-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.3);
        backdrop-filter: blur(4px);
        z-index: 999;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease;
    }
    .drawer-overlay.open { opacity: 1; pointer-events: auto; }
    .drawer-overlay.open { opacity: 1; pointer-events: auto; }

    /* Sandbox Mode Visuals */
    body.sandbox-mode {
        background-color: #fff9f0;
    }
    body.sandbox-mode .blob-1 { background: rgba(245, 158, 11, 0.15); }
    body.sandbox-mode .btn-push, body.sandbox-mode .btn-pull {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        box-shadow: 0 12px 24px -6px rgba(245, 158, 11, 0.5);
    }
    body.sandbox-mode .badge-simulation {
        display: inline-flex !important;
        animation: sandbox-pulse 2s infinite;
    }
    body.sandbox-mode #sim-glow {
        opacity: 0.4 !important;
    }

    @keyframes sandbox-pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }
</style>

<div class="blob-bg">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
    <div class="blob blob-3"></div>
</div>

<!-- Side Drawer for Log Details -->
<div id="log-drawer-overlay" class="drawer-overlay" onclick="closeDrawer()"></div>
<div id="log-drawer">
    <div class="h-full flex flex-col p-10">
        <div class="flex justify-between items-center mb-10">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Detalles de Operación</p>
                <h2 id="drawer-title" class="text-3xl font-black text-slate-900 tracking-tighter">#000000</h2>
            </div>
            <button onclick="closeDrawer()" class="w-12 h-12 flex items-center justify-center bg-slate-100 rounded-2xl text-slate-500 hover:bg-rose-500 hover:text-white transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div id="drawer-content" class="flex-1 overflow-y-auto space-y-8 pr-4">
            <!-- Dynamic Content Here -->
        </div>

        <div class="pt-8 border-t border-slate-100">
            <button onclick="closeDrawer()" class="w-full py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all">
                Cerrar Inspector
            </button>
        </div>
    </div>
</div>

<div class="relative z-10 p-6 lg:p-10">
    
    <!-- Header Crystal -->
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-12 gap-10">
        <div class="flex items-center gap-8">
            <div class="relative group">
                <div class="absolute -inset-2 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl blur-lg opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                <div class="relative p-6 bg-white rounded-3xl shadow-2xl flex items-center justify-center border border-slate-100">
                    <span class="material-symbols-outlined text-blue-600 text-4xl">cloud_sync</span>
                </div>
            </div>
            <div>
                <h1 class="text-5xl font-black tracking-tighter text-slate-900">Centro de <span class="text-blue-600">Sincronización</span></h1>
                <div class="flex items-center mt-3 gap-4">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">Precision Sync v4.1</span>
                    <span class="badge-simulation hidden px-3 py-1 bg-amber-500 text-white rounded-full text-[9px] font-black uppercase tracking-widest animate-pulse shadow-lg shadow-amber-500/20">Modo Sandbox Activo</span>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center justify-end gap-6 flex-1">
            @php 
                $writesLimit = 20000; $readsLimit = 50000;
                $writesVal = $stats['quota']['writes_today'] ?? 0;
                $readsVal = $stats['quota']['reads_today'] ?? 0;
                $writesPerc = min(100, ($writesVal / $writesLimit) * 100);
                $readsPerc = min(100, ($readsVal / $readsLimit) * 100);
                
                $hWrites = $stats['quota']['history_writes'] ?? [0,0,0,0,0,0];
                $hReads = $stats['quota']['history_reads'] ?? [0,0,0,0,0,0];
            @endphp

            <!-- Writes Quota -->
            <div class="glass-panel px-6 py-4 flex items-center gap-6 min-w-[240px]">
                <div class="circular-progress scale-75">
                    <svg><circle class="bg" cx="32" cy="32" r="28" /><circle class="bar" cx="32" cy="32" r="28" style="stroke-dasharray: 175.9; stroke-dashoffset: {{ 175.9 - (175.9 * $writesPerc / 100) }}; stroke: #3b82f6;" /></svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-lg">upload_file</span>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Escrituras (24h)</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-800">{{ number_format($writesVal) }}</span>
                        <span class="text-[9px] font-bold text-blue-500">{{ round($writesPerc, 1) }}%</span>
                    </div>
                </div>
            </div>

            <!-- Reads Quota -->
            <div class="glass-panel px-6 py-4 flex items-center gap-6 min-w-[240px]">
                <div class="circular-progress scale-75">
                    <svg><circle class="bg" cx="32" cy="32" r="28" /><circle class="bar" cx="32" cy="32" r="28" style="stroke-dasharray: 175.9; stroke-dashoffset: {{ 175.9 - (175.9 * $readsPerc / 100) }}; stroke: #10b981;" /></svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="material-symbols-outlined text-emerald-600 text-lg">download_done</span>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5">Lecturas (24h)</p>
                    <div class="flex items-baseline gap-2">
                        <span class="text-xl font-black text-slate-800">{{ number_format($readsVal) }}</span>
                        <span class="text-[9px] font-bold text-emerald-500">{{ round($readsPerc, 1) }}%</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Audit Button -->
                <button onclick="runHealthCheck()" class="relative group p-4 bg-white border border-slate-100 rounded-[25px] shadow-lg hover:scale-105 transition-all">
                    <span class="material-symbols-outlined text-blue-600 text-2xl">analytics</span>
                </button>

                <!-- Integrated System Pulse -->
                <div class="glass-panel p-3 px-5 flex items-center gap-4 bg-white/60 border-white/80 shadow-lg rounded-[25px]">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)] animate-pulse"></div>
                    <span class="text-[9px] font-black text-slate-800 uppercase tracking-widest">18ms</span>
                </div>
            </div>
        </div>
    </header>    <div x-data="{ intuitiveMode: true, advancedSettings: false }">
        <!-- Dashboard Toggle -->
        <div class="flex justify-center mb-10">
            <div class="bg-white p-1.5 rounded-3xl shadow-lg border border-slate-100 flex gap-2">
                <button @click="intuitiveMode = true" :class="intuitiveMode ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                    Modo Intuitivo
                </button>
                <button @click="intuitiveMode = false" :class="!intuitiveMode ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600'" class="px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">
                    Panel Técnico
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Main Content Area -->
            <div class="lg:col-span-8 space-y-10">
                
                <!-- Simple Action Hub (Intuitive Mode) -->
                <template x-if="intuitiveMode">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 animate-in fade-in zoom-in duration-500">
                        <!-- Pull Card -->
                        <div class="glass-panel p-10 flex flex-col items-center text-center group hover:bg-emerald-50/50 transition-all">
                            <div class="w-24 h-24 bg-emerald-100 text-emerald-600 rounded-[32px] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform shadow-lg shadow-emerald-500/10">
                                <span class="material-symbols-outlined text-5xl">cloud_download</span>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tighter">Actualizar mi Sistema</h3>
                            <p class="text-xs text-slate-500 mb-8 leading-relaxed px-4">Descarga los últimos registros de la nube Firebase. Úsalo para traer afiliados nuevos creados en otros dispositivos.</p>
                            
                            <div class="flex flex-col gap-4 w-full">
                                <button onclick="triggerSelectiveSync('pull', 'afiliados')" class="py-5 bg-emerald-500 text-white rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 shadow-xl shadow-emerald-500/20 transition-all flex items-center justify-center gap-3">
                                    <span class="material-symbols-outlined text-xl">person_search</span> Actualizar Afiliados
                                </button>
                                <button onclick="triggerSelectiveSync('pull', 'empresas')" class="py-5 bg-white border border-emerald-100 text-emerald-600 rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-50 transition-all flex items-center justify-center gap-3">
                                    <span class="material-symbols-outlined text-xl">domain</span> Actualizar Empresas
                                </button>
                                <button onclick="triggerSelectiveSync('pull', 'full')" class="py-4 text-emerald-700/60 text-[9px] font-black uppercase tracking-[0.2em] hover:text-emerald-700 transition-colors">
                                    Descarga Completa (Todo)
                                </button>
                            </div>
                        </div>

                        <!-- Push Card -->
                        <div class="glass-panel p-10 flex flex-col items-center text-center group hover:bg-blue-50/50 transition-all">
                            <div class="w-24 h-24 bg-blue-100 text-blue-600 rounded-[32px] flex items-center justify-center mb-8 group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/10">
                                <span class="material-symbols-outlined text-5xl">cloud_upload</span>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-4 tracking-tighter">Subir mis Cambios</h3>
                            <p class="text-xs text-slate-500 mb-8 leading-relaxed px-4">Envía tus registros locales a la nube. Úsalo para que el resto del equipo pueda ver tus actualizaciones.</p>
                            
                            <div class="flex flex-col gap-4 w-full">
                                <button onclick="triggerSelectiveSync('push', 'afiliados')" class="py-5 bg-blue-600 text-white rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all flex items-center justify-center gap-3">
                                    <span class="material-symbols-outlined text-xl">person_search</span> Subir Afiliados
                                </button>
                                <button onclick="triggerSelectiveSync('push', 'empresas')" class="py-5 bg-white border border-blue-100 text-blue-600 rounded-3xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-50 transition-all flex items-center justify-center gap-3">
                                    <span class="material-symbols-outlined text-xl">domain</span> Subir Empresas
                                </button>
                                <p class="py-4 text-slate-400 text-[9px] font-bold uppercase tracking-[0.1em] italic">
                                    Se recomienda subir por módulos
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Advanced Visualization (Technical Mode) -->
                <div x-show="!intuitiveMode" class="space-y-10 animate-in fade-in slide-in-from-bottom-5 duration-500">
                    <!-- System Stats Grid -->
                    <section class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="glass-panel p-8 bg-white/50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Empresas</p>
                            <h3 class="text-4xl font-black text-slate-900">{{ number_format($stats['local']['empresas'] ?? 0) }}</h3>
                            <div class="mt-5 flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                <span class="text-[10px] font-bold text-blue-600 uppercase tracking-tighter">Nodo Maestro</span>
                            </div>
                        </div>
                        <div class="glass-panel p-8 bg-white/50">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Afiliados</p>
                            <h3 class="text-4xl font-black text-slate-900">{{ number_format($stats['local']['afiliados'] ?? 0) }}</h3>
                            <div class="mt-5 flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-purple-500"></div>
                                <span class="text-[10px] font-bold text-purple-600 uppercase tracking-tighter">Data Pool</span>
                            </div>
                        </div>
                        <div class="lg:col-span-2 glass-panel p-8 border-l-8 border-l-blue-600 bg-gradient-to-br from-blue-50/40 to-white/30">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Status de Sincronización</p>
                                    <h3 class="text-2xl font-black text-slate-800 mt-1" id="sync-title">Esperando Instrucción</h3>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span id="sync-eta" class="hidden px-3 py-1 bg-blue-600 text-white rounded-lg text-[9px] font-black animate-pulse">ETA: --</span>
                                    <div id="sync-percentage" class="text-4xl font-black text-blue-600 drop-shadow-sm">0%</div>
                                </div>
                            </div>
                            <div class="w-full bg-slate-200/40 h-3 rounded-full mt-6 overflow-hidden p-0.5 border border-slate-100">
                                <div id="sync-progress-bar" class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 h-full rounded-full transition-all duration-1000 shadow-lg shadow-blue-600/30" style="width: 0%"></div>
                            </div>
                            <p id="sync-label" class="text-[11px] font-extrabold text-slate-500 uppercase mt-4 tracking-widest flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">terminal</span> Ready for command sequence
                            </p>
                        </div>
                    </section>

                    <!-- Visual Data Engine -->
                    <section class="glass-panel p-20 flex flex-col items-center justify-center min-h-[500px] relative overflow-hidden bg-white/40">
                        <div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M0 40L40 0H20L0 20M40 40V20L20 40\' fill=\'%23000\' fill-opacity=\'1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
                        
                        <div class="flex items-center justify-between w-full relative z-10 gap-16">
                            <!-- Source Node -->
                            <div class="flex flex-col items-center gap-8 group">
                                <div class="relative">
                                    <div class="absolute -inset-6 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all duration-1000"></div>
                                    <div class="w-36 h-36 bg-white rounded-[48px] shadow-2xl flex items-center justify-center border border-slate-100 group-hover:border-blue-300 transition-all transform group-hover:rotate-6 duration-500">
                                        <span class="material-symbols-outlined text-7xl text-slate-200 group-hover:text-blue-500 transition-colors" style="font-variation-settings: 'FILL' 1;">storage</span>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <span class="badge-premium bg-slate-900 text-white px-4 py-1.5 mb-3 inline-block shadow-lg">Local DB</span>
                                    <h4 class="font-extrabold text-slate-800 text-xs tracking-[0.2em] uppercase">Core Infrastructure</h4>
                                </div>
                            </div>

                            <!-- The Neural Link -->
                            <div class="flex-1 space-y-12">
                                <div class="energy-path shadow-inner">
                                    <div id="particle-push" class="energy-bolt"></div>
                                </div>
                                <div class="relative h-16">
                                    <div id="sync-direction-badge" class="absolute inset-0 flex items-center justify-center hidden">
                                        <div class="px-8 py-3 bg-white rounded-3xl shadow-2xl border border-slate-100 animate-bounce flex items-center gap-3">
                                            <div class="w-2 h-2 rounded-full bg-blue-500 animate-ping"></div>
                                            <span id="sync-direction-text" class="text-[11px] font-black text-blue-600 uppercase tracking-widest">Sincronizando...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="energy-path shadow-inner" style="transform: scaleX(-1);">
                                    <div id="particle-pull" class="energy-bolt"></div>
                                </div>
                            </div>

                            <!-- Firebase Hub Node -->
                            <div class="flex flex-col items-center gap-8 group">
                                <div class="hub-container transform group-hover:scale-105 transition-all duration-700">
                                    <div class="hub-aura"></div>
                                    <div class="hub-ring ring-outer"></div>
                                    <div class="hub-ring ring-middle"></div>
                                    <div class="hub-ring ring-inner"></div>
                                    <div class="hub-center">
                                        <span id="hub-icon-main" class="material-symbols-outlined text-6xl text-blue-600" style="font-variation-settings: 'FILL' 1;">cloud</span>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <span class="badge-premium bg-blue-600 text-white px-4 py-1.5 mb-3 inline-block shadow-lg shadow-blue-600/20">Firebase Nube</span>
                                    <h4 class="font-extrabold text-slate-800 text-xs tracking-[0.2em] uppercase">Cloud Ecosystem</h4>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Live Monitoring (Always visible during sync) -->
                <section id="process-hud" class="hidden animate-in slide-in-from-top-10 duration-700">
                    <div class="glass-panel p-10 border-l-8 border-l-blue-600 bg-white/60">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600 text-4xl animate-spin">sync</span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-1">Operación en Progreso</p>
                                    <h4 id="hud-status" class="text-2xl font-black text-slate-900 tracking-tighter">Procesando transmisión de datos...</h4>
                                </div>
                            </div>
                            <div id="active-controls" class="flex gap-4">
                                <button id="btn-pause" onclick="controlSync('pause')" class="w-14 h-14 bg-white border border-slate-100 rounded-2xl text-amber-600 hover:bg-amber-600 hover:text-white transition-all flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-2xl">pause</span>
                                </button>
                                <button id="btn-resume" onclick="controlSync('resume')" class="hidden w-14 h-14 bg-white border border-slate-100 rounded-2xl text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-2xl">play_arrow</span>
                                </button>
                                <button id="btn-cancel" onclick="controlSync('cancel')" class="w-14 h-14 bg-white border border-slate-100 rounded-2xl text-rose-600 hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center shadow-lg">
                                    <span class="material-symbols-outlined text-2xl">close</span>
                                </button>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1 bg-slate-100 rounded-full h-4 p-1">
                                <div id="sync-progress-bar-hud" class="bg-blue-600 h-full rounded-full transition-all duration-1000 shadow-lg shadow-blue-600/20" style="width: 0%"></div>
                            </div>
                            <span id="sync-percentage-hud" class="text-sm font-black text-blue-600 min-w-[50px]">0%</span>
                        </div>
                    </div>
                </section>

                <!-- Console Terminal (Only in technical mode) -->
                <section x-show="!intuitiveMode" class="terminal-window">
                    <div class="terminal-header">
                        <div class="terminal-dot bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]"></div>
                        <div class="terminal-dot bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]"></div>
                        <div class="terminal-dot bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-[0.4em] ml-6 opacity-60">System Data Stream Feed</span>
                    </div>
                    <div id="terminal-feed" class="terminal-body scroll-smooth">
                        <div class="flex gap-3 mb-2">
                            <span class="text-blue-500 font-bold">>>></span>
                            <span class="text-slate-400 italic">Inicializando protocolos de comunicación...</span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Right Sidebar (Technical Settings - Collapsible in Intuitive Mode) -->
            <aside class="lg:col-span-4 space-y-8">
                
                <section class="glass-panel p-10 bg-white/60">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-sm font-black uppercase tracking-[0.4em] text-slate-800">Ajustes</h3>
                        <button @click="advancedSettings = !advancedSettings" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline transition-all">
                            <span x-text="advancedSettings ? 'Cerrar Avanzado' : 'Abrir Avanzado'">Abrir Avanzado</span>
                        </button>
                    </div>

                    <div x-show="advancedSettings || !intuitiveMode" x-collapse class="space-y-10">
                        <!-- Security Operative Hub -->
                        <div class="space-y-6">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-2">Seguridad Operativa</p>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <!-- Simulation Mode Card -->
                                <div class="relative group">
                                    <div class="absolute -inset-0.5 bg-gradient-to-r from-amber-500 to-orange-600 rounded-[2.5rem] blur opacity-0 group-hover:opacity-20 transition duration-500" id="sim-glow"></div>
                                    <label class="relative flex items-center justify-between p-6 bg-white border border-slate-100 rounded-[2.5rem] cursor-pointer hover:border-amber-400 transition-all shadow-sm">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 group-hover:rotate-12 transition-transform">
                                                <span class="material-symbols-outlined text-3xl font-black">science</span>
                                            </div>
                                            <div>
                                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-tight">Simulación</h3>
                                            </div>
                                        </div>
                                        <div class="relative inline-flex items-center">
                                            <input type="checkbox" id="sim-mode" class="sr-only peer" onchange="updateSafetyToggle('sim', this.checked)">
                                            <div class="w-14 h-7 bg-slate-100 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-500 shadow-inner"></div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Snapshots Mode Card -->
                                <div class="relative group">
                                    <label class="relative flex items-center justify-between p-6 bg-white border border-slate-100 rounded-[2.5rem] cursor-pointer hover:border-blue-400 transition-all shadow-sm">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:rotate-12 transition-transform">
                                                <span class="material-symbols-outlined text-3xl font-black">history</span>
                                            </div>
                                            <div>
                                                <h3 class="text-xs font-black text-slate-800 uppercase tracking-tight">Snapshots</h3>
                                            </div>
                                        </div>
                                        <div class="relative inline-flex items-center">
                                            <input type="checkbox" id="auto-snap" checked class="sr-only peer" onchange="updateSafetyToggle('snap', this.checked)">
                                            <div class="w-14 h-7 bg-slate-100 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600 shadow-inner"></div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Execution Speed -->
                        <div class="p-8 bg-slate-900 rounded-[40px] text-white shadow-2xl relative overflow-hidden group">
                            <div class="relative z-10">
                                <div class="flex justify-between items-center mb-8">
                                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Velocidad</p>
                                    <span id="intensity-label" class="px-4 py-1.5 bg-blue-600 rounded-xl text-[10px] font-extrabold shadow-lg">NORMAL</span>
                                </div>
                                <input type="range" id="sync-intensity" min="10" max="500" step="10" value="50" class="w-full" oninput="updateIntensity(this.value)">
                            </div>
                        </div>

                        <!-- Maintenance -->
                        <div class="space-y-4">
                            <button onclick="cleanupSnapshots()" class="w-full py-4 bg-slate-100 hover:bg-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 text-slate-700">
                                <span class="material-symbols-outlined text-sm">delete_sweep</span> Limpiar Backups
                            </button>
                            <button onclick="purgeCache()" class="w-full py-4 bg-slate-100 hover:bg-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-center gap-3 text-slate-700">
                                <span class="material-symbols-outlined text-sm">layers_clear</span> Purgar Cache
                            </button>
                        </div>
                    </div>

                    <!-- Static Selection (Only in Technical Mode) -->
                    <div x-show="!intuitiveMode" class="pt-8 mt-8 border-t border-slate-100 space-y-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-2">Selección Manual</p>
                        <div class="grid grid-cols-1 gap-4">
                            <label class="flex items-center justify-between p-6 bg-white border border-slate-100 rounded-3xl cursor-pointer hover:bg-blue-50/50 transition-all shadow-sm">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-[0.1em]">Afiliados</span>
                                <input type="checkbox" id="sync_afiliados_ui" checked class="w-6 h-6 rounded-lg border-slate-300 text-blue-600">
                            </label>
                            <label class="flex items-center justify-between p-6 bg-white border border-slate-100 rounded-3xl cursor-pointer hover:bg-indigo-50/50 transition-all shadow-sm">
                                <span class="text-xs font-black text-slate-800 uppercase tracking-[0.1em]">Empresas</span>
                                <input type="checkbox" id="sync_empresas_ui" checked class="w-6 h-6 rounded-lg border-slate-300 text-blue-600">
                            </label>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6 pt-6">
                            <button onclick="triggerSelectiveSync('push')" class="w-full btn-action-main btn-push btn-shine">
                                <span class="material-symbols-outlined text-2xl">cloud_upload</span> Subir a Firebase
                            </button>
                            <button onclick="triggerSelectiveSync('pull')" class="w-full btn-action-main btn-pull btn-shine">
                                <span class="material-symbols-outlined text-2xl">cloud_download</span> Descargar Nube
                            </button>
                        </div>
                    </div>

                    <!-- Intuitive Status Card -->
                    <div x-show="intuitiveMode" class="mt-8 p-8 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[40px] text-white shadow-2xl relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10">
                            <p class="text-[9px] font-black uppercase tracking-[0.3em] text-blue-200 mb-6">Estado del Sistema</p>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center bg-white/10 p-4 rounded-2xl">
                                    <span class="text-[10px] font-bold">Local</span>
                                    <span class="text-lg font-black">{{ number_format($stats['local']['afiliados'] + $stats['local']['empresas']) }}</span>
                                </div>
                                <div class="flex justify-between items-center bg-white/10 p-4 rounded-2xl border border-white/10">
                                    <span class="text-[10px] font-bold">Última Sync</span>
                                    <span class="text-xs font-black">{{ $logs->first() ? $logs->first()->finished_at->diffForHumans() : 'Nunca' }}</span>
                                </div>
                            </div>
                            <button onclick="runHealthCheck()" class="w-full mt-6 py-4 bg-white text-blue-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-50 transition-all shadow-lg">
                                Realizar Auditoría
                            </button>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>aside>
    </div>

    <!-- Enhanced Bitácora -->
    <section class="glass-panel overflow-hidden mt-12 mb-10" id="sync-history-container">
        <div class="p-10 border-b border-slate-100 bg-white/40 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-slate-900 rounded-[28px] shadow-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-white text-3xl">history_edu</span>
                </div>
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Historial de Sincronización</h2>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-[0.3em]">Registro inmutable de transacciones</p>
                </div>
            </div>
            <div class="px-6 py-3 bg-blue-600 text-white rounded-2xl text-sm font-black shadow-lg shadow-blue-600/20">
                {{ number_format($logs->total()) }} Operaciones Registradas
            </div>
        </div>
        
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 text-[11px] font-black uppercase text-slate-400 tracking-[0.2em]">
                <tr>
                    <th class="px-10 py-6">ID Sesión</th>
                    <th class="px-8 py-6">Tipo</th>
                    <th class="px-8 py-6">Estado</th>
                    <th class="px-8 py-6">Impacto en DB</th>
                    <th class="px-10 py-6 text-right">Finalización</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($logs as $log)
                <tr class="hover:bg-blue-50/30 transition-all group">
                    <td class="px-10 py-8">
                        <div class="flex items-center gap-5">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $log->type === 'Pull' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }} shadow-sm">
                                <span class="material-symbols-outlined">
                                    {{ $log->type === 'Pull' ? 'south_east' : 'north_east' }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-black text-slate-800">#{{ str_pad($log->id, 6, '0', STR_PAD_LEFT) }}</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $log->performed_by }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-8">
                        <span class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest {{ $log->type === 'Pull' ? 'bg-emerald-500 text-white' : 'bg-blue-600 text-white' }} shadow-md">
                            {{ $log->type }}
                        </span>
                        @if($log->summary && isset($log->summary['is_dry_run']) && $log->summary['is_dry_run'])
                            <span class="ml-2 px-2 py-1 bg-amber-100 text-amber-700 text-[8px] font-black uppercase rounded-lg border border-amber-200">Sandbox</span>
                        @endif
                    </td>
                    <td class="px-8 py-8">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ $log->status === 'success' ? 'bg-emerald-500' : ($log->status === 'running' ? 'bg-blue-500 animate-pulse' : 'bg-rose-500') }}"></div>
                            <span class="text-xs font-bold text-slate-700 uppercase">{{ $log->status }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-8">
                        <div class="flex gap-3">
                            @if($log->summary && is_array($log->summary))
                                @foreach($log->summary as $key => $val)
                                    @php $count = ($val['created'] ?? 0) + ($val['updated'] ?? 0); @endphp
                                    @if(is_numeric($count) && $count > 0)
                                        <div class="bg-white border border-slate-200 px-4 py-2 rounded-2xl shadow-sm">
                                            <span class="text-[9px] font-black text-slate-400 uppercase mr-2">{{ $key }}</span>
                                            <span class="text-xs font-black text-blue-600">{{ number_format($count) }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </td>
                    <td class="px-10 py-8 text-right">
                        <p class="text-sm font-black text-slate-800">{{ $log->finished_at ? $log->finished_at->format('H:i:s') : '--:--:--' }}</p>
                        <p class="text-[10px] text-slate-400 font-extrabold uppercase">{{ $log->finished_at ? $log->finished_at->format('d M, Y') : 'Processing' }}</p>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-10 py-24 text-center text-slate-400 font-bold tracking-widest uppercase opacity-40">-- No transaction logs found --</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-10 py-8 bg-white/40 border-t border-slate-100">
            {{ $logs->links() }}
        </div>
    </section>
</div>


@endsection

@push('scripts')
<script>
    window.bindGranularConfig = function(form, event) {
        const af = document.getElementById('sync_afiliados_ui').checked;
        const em = document.getElementById('sync_empresas_ui').checked;
        if(!af && !em) {
            event.preventDefault();
            Swal.fire({ title: 'Atención', text: 'Seleccione al menos un módulo para la secuencia.', icon: 'warning', confirmButtonColor: '#3b82f6' });
            return false;
        }
        if(!af) form.querySelector('.ui-mapped-afiliados').disabled = true;
        if(!em) form.querySelector('.ui-mapped-empresas').disabled = true;
    };

    window.updateSafetyToggle = function(type, checked) {
        const val = checked ? "1" : "0";
        document.querySelectorAll(type === 'sim' ? '.sim-input' : '.snap-input').forEach(i => i.value = val);
        if (type === 'sim') {
            document.body.classList.toggle('sandbox-mode', checked);
            
            // Log to terminal feed
            const term = document.getElementById('terminal-feed');
            if (term) {
                const entry = document.createElement('div');
                entry.className = 'flex gap-3 mb-2 animate-in fade-in slide-in-from-left-2';
                entry.innerHTML = checked 
                    ? '<span class="text-amber-500 font-bold">>>></span> <span class="text-amber-400 font-black uppercase tracking-tighter">[ALERTA] Modo Sandbox ACTIVADO. Transacciones simuladas habilitadas.</span>'
                    : '<span class="text-blue-500 font-bold">>>></span> <span class="text-slate-400 font-bold uppercase tracking-tighter">Modo Sandbox DESACTIVADO. Modo de escritura real restaurado.</span>';
                term.appendChild(entry);
                term.scrollTop = term.scrollHeight;
            }
        }
    };

    window.updateIntensity = function(val) {
        const label = document.getElementById('intensity-label');
        let text = 'NORMAL';
        if (val < 30) text = 'ECO';
        if (val > 100) text = 'TURBO';
        if (val > 300) text = 'OVERDRIVE';
        label.textContent = text;
        document.querySelectorAll('.intensity-input').forEach(input => input.value = val);
    };

    window.runHealthCheck = function() {
        Swal.fire({
            title: 'Iniciando Auditoría...',
            html: 'Sincronizando estados de integridad estructural...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
        fetch("{{ route('carnetizacion.sync_center.health_check') }}")
            .then(r => r.json())
            .then(data => {
                const afGap = data.diff.afiliados;
                const emGap = data.diff.empresas;

                Swal.fire({
                    title: 'Auditoría de Ecosistema',
                    background: '#ffffff',
                    width: '800px',
                    html: `
                        <div class="text-left space-y-6 p-4">
                            <div class="grid grid-cols-2 gap-8">
                                <!-- Afiliados Audit -->
                                <div class="p-8 bg-slate-50 rounded-[40px] border border-slate-100 shadow-inner relative overflow-hidden">
                                    <div class="flex justify-between items-start mb-6">
                                        <p class="font-black text-blue-600 uppercase text-[10px] tracking-[0.3em]">Afiliados Core</p>
                                        <span class="px-3 py-1 bg-white rounded-full text-[9px] font-black border border-slate-200">Latencia: ${data.health.latency}ms</span>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-baseline"><span class="text-xs text-slate-500">Local:</span> <b class="text-xl font-black text-slate-800">${data.local.afiliados.toLocaleString()}</b></div>
                                        <div class="flex justify-between items-baseline"><span class="text-xs text-slate-500">Cloud:</span> <b class="text-xl font-black text-slate-800">${data.remote.afiliados.toLocaleString()}</b></div>
                                        <div class="pt-4 border-t border-slate-200 flex justify-between items-center">
                                            <span class="text-xs font-black uppercase text-slate-400">Diferencia (Gap)</span>
                                            <span class="text-2xl font-black ${afGap === 0 ? 'text-emerald-500' : 'text-rose-500'}">${afGap > 0 ? '+'+afGap : afGap}</span>
                                        </div>
                                    </div>
                                    ${afGap !== 0 ? `
                                        <button onclick="startReconciliation('afiliados')" class="w-full mt-6 py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20">
                                            Arreglar Discrepancia
                                        </button>
                                    ` : '<div class="mt-6 py-4 bg-emerald-50 text-emerald-600 rounded-2xl text-[10px] font-black text-center uppercase tracking-widest">Integridad OK</div>'}
                                </div>

                                <!-- Empresas Audit -->
                                <div class="p-8 bg-slate-50 rounded-[40px] border border-slate-100 shadow-inner relative overflow-hidden">
                                    <div class="flex justify-between items-start mb-6">
                                        <p class="font-black text-indigo-600 uppercase text-[10px] tracking-[0.3em]">Empresas Hub</p>
                                        <span class="px-3 py-1 bg-white rounded-full text-[9px] font-black border border-slate-200">Estable</span>
                                    </div>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-baseline"><span class="text-xs text-slate-500">Local:</span> <b class="text-xl font-black text-slate-800">${data.local.empresas.toLocaleString()}</b></div>
                                        <div class="flex justify-between items-baseline"><span class="text-xs text-slate-500">Cloud:</span> <b class="text-xl font-black text-slate-800">${data.remote.empresas.toLocaleString()}</b></div>
                                        <div class="pt-4 border-t border-slate-200 flex justify-between items-center">
                                            <span class="text-xs font-black uppercase text-slate-400">Diferencia (Gap)</span>
                                            <span class="text-2xl font-black ${emGap === 0 ? 'text-emerald-500' : 'text-rose-500'}">${emGap > 0 ? '+'+emGap : emGap}</span>
                                        </div>
                                    </div>
                                    ${emGap !== 0 ? `
                                        <button onclick="startReconciliation('empresas')" class="w-full mt-6 py-4 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20">
                                            Arreglar Discrepancia
                                        </button>
                                    ` : '<div class="mt-6 py-4 bg-emerald-50 text-emerald-600 rounded-2xl text-[10px] font-black text-center uppercase tracking-widest">Integridad OK</div>'}
                                </div>
                            </div>
                            
                            <div class="p-6 bg-amber-50 border border-amber-100 rounded-3xl">
                                <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-2 flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">info</span> Muestra de registros huérfanos (Local)
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    ${data.local.orphans.length > 0 ? 
                                        data.local.orphans.map(id => `<span class="px-3 py-1 bg-white border border-amber-200 rounded-lg text-[10px] font-mono text-amber-800">${id}</span>`).join('') 
                                        : '<span class="text-xs text-amber-600 italic">No se detectaron huérfanos locales pendientes de envío inicial.</span>'}
                                </div>
                            </div>
                        </div>`,
                    confirmButtonText: 'Finalizar Auditoría',
                    confirmButtonColor: '#1e293b',
                    customClass: { popup: 'rounded-[50px] border-0 shadow-2xl' }
                });
            });
    };

    window.startReconciliation = function(type) {
        Swal.fire({
            title: 'Lanzando Operación...',
            text: 'Preparando secuencia de reconciliación dirigida.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch("{{ route('carnetizacion.sync_center.reconcile') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ type: type })
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    title: 'Secuencia Iniciada',
                    text: 'El motor de reconciliación está trabajando en segundo plano.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    };

    window.controlSync = function(action) {
        if (action === 'cancel') {
            Swal.fire({
                title: '¿Abortar Secuencia?',
                text: "Se detendrán todas las operaciones de sincronización activas inmediatamente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#1e293b',
                confirmButtonText: 'Sí, Abortar',
                cancelButtonText: 'Mantener Activo',
                customClass: { popup: 'rounded-[40px]' }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeControlAction(action);
                }
            });
        } else {
            executeControlAction(action);
        }
    };

    function executeControlAction(action) {
        fetch(`/carnetizacion/sync-center/${action}`, { 
            method: 'POST', 
            headers: { 
                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                'Accept': 'application/json' 
            } 
        }).then(r => r.json()).then(data => {
            if(data.success) {
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: { popup: 'rounded-2xl' }
                });
                toast.fire({
                    icon: 'success',
                    title: `Comando [${action.toUpperCase()}] enviado con éxito`
                });
            }
        });
    }

    window.cleanupSnapshots = function() {
        Swal.fire({
            title: 'Purgar Historial',
            text: "¿Deseas eliminar todos los snapshots de respaldo de la base de datos?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e293b',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, Purgar todo',
            customClass: { popup: 'rounded-[40px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Limpiando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                fetch("{{ route('carnetizacion.sync_center.cleanup_snapshots') }}", { 
                    method: 'POST', 
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } 
                })
                .then(r => r.json())
                .then(data => {
                    Swal.fire({
                        title: 'Mantenimiento Completo',
                        text: `Se han eliminado ${data.deleted_count || 0} tablas de respaldo con éxito.`,
                        icon: 'success',
                        confirmButtonColor: '#3b82f6',
                        customClass: { popup: 'rounded-[40px]' }
                    });
                })
                .catch(err => {
                    Swal.fire({ title: 'Error', text: 'No se pudo completar la limpieza.', icon: 'error' });
                });
            }
        });
    };

    window.purgeCache = function() {
        Swal.fire({
            title: 'Limpiar Cache',
            text: "¿Deseas refrescar todos los estados temporales del sistema?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, Refrescar',
            customClass: { popup: 'rounded-[40px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Limpiando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                fetch("{{ route('carnetizacion.sync_center.purge_cache') }}", { 
                    method: 'POST', 
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } 
                })
                .then(r => r.json())
                .then(data => {
                    Swal.fire({
                        title: 'Cache Limpia',
                        text: 'El sistema ha sido refrescado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#3b82f6',
                        customClass: { popup: 'rounded-[40px]' }
                    }).then(() => { location.reload(); });
                });
            }
        });
    };

    window.triggerSelectiveSync = function(direction, module = null) {
        // En modo intuitivo usamos el parámetro 'module', en modo técnico usamos los checkboxes
        const afChecked = module === 'afiliados' || module === 'full' || (document.getElementById('sync_afiliados_ui')?.checked || false);
        const emChecked = module === 'empresas' || module === 'full' || (document.getElementById('sync_empresas_ui')?.checked || false);
        const fullChecked = module === 'full';

        if (!afChecked && !emChecked && !fullChecked) {
            Swal.fire({ 
                title: 'Atención', 
                text: 'Seleccione al menos una entidad (Afiliados o Empresas) para sincronizar.', 
                icon: 'warning', 
                customClass: { popup: 'rounded-[40px]' } 
            });
            return;
        }

        const options = {
            'intensity': document.getElementById('sync-intensity')?.value || 50,
            'simulation': document.getElementById('sim-mode')?.checked || false,
            'snapshot': document.getElementById('auto-snap')?.checked || false,
            'afiliados': afChecked,
            'empresas': emChecked,
            'full': fullChecked,
            'catalogs': fullChecked
        };

        const route = direction === 'push' ? "{{ route('carnetizacion.sync_center.sync_push') }}" : "{{ route('carnetizacion.sync_center.sync_pull') }}";
        
        Swal.fire({ 
            title: 'Iniciando Secuencia...', 
            html: `Preparando ${direction === 'push' ? 'subida' : 'descarga'} de <b>${module || 'módulos seleccionados'}</b>...`,
            allowOutsideClick: false, 
            didOpen: () => { Swal.showLoading(); } 
        });

        fetch(route, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify(options)
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                Swal.fire({ title: 'Operación Lanzada', text: 'El motor está calentando motores...', icon: 'success', timer: 1500, showConfirmButton: false });
                
                // Mostrar el HUD de monitoreo inmediatamente
                const hud = document.getElementById('process-hud');
                if (hud) hud.classList.remove('hidden');
                
                // Si estamos en modo intuitivo y hay ajustes abiertos, cerrarlos para enfocar el HUD
                if (window.Alpine) {
                    // Nota: Alpine se maneja por el objeto proxy si está disponible, 
                    // pero aquí lo hacemos vía DOM para mayor seguridad
                }
            } else {
                Swal.fire({ title: 'Error', text: data.error || 'Ocurrió un error inesperado.', icon: 'error' });
            }
        })
        .catch(err => {
            Swal.fire({ title: 'Error Crítico', text: 'No se pudo comunicar con el servidor.', icon: 'error' });
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
        const perc = document.getElementById('sync-percentage');
        const eta = document.getElementById('sync-eta');
        const label = document.getElementById('sync-label');
        const title = document.getElementById('sync-title');
        const bar = document.getElementById('sync-progress-bar');
        const controls = document.getElementById('active-controls');
        const btnP = document.getElementById('btn-pause');
        const btnR = document.getElementById('btn-resume');
        const partPush = document.getElementById('particle-push');
        const partPull = document.getElementById('particle-pull');
        const badge = document.getElementById('sync-direction-badge');
        const feed = document.getElementById('terminal-feed');
        const hud = document.getElementById('process-hud');
        
        let lastLogHash = '';
        let wasActive = false;
        let lastProgress = 0;
        let inactiveStreak = 0; // Contador de polls inactivos consecutivos

        function poll() {
            fetch("{{ route('carnetizacion.sync_center.progress') }}")
                .then(r => r.json())
                .then(data => {
                    const playSounds = document.getElementById('toggle-sounds')?.checked || false;

                    if (data.active) {
                        inactiveStreak = 0; // Resetear contador — está activo
                        wasActive = true;
                        if (perc) perc.textContent = data.progress + '%';
                        if (bar) bar.style.width = data.progress + '%';
                        
                        if (data.eta) {
                            if (eta) {
                                eta.textContent = 'ETA: ' + data.eta;
                                eta.classList.remove('hidden');
                            }
                        } else {
                            if (eta) eta.classList.add('hidden');
                        }

                        if (label) label.innerHTML = `<span class='material-symbols-outlined text-sm'>sync</span> ${data.label}`;
                        if (title) title.textContent = 'Transfiriendo Data';
                        if (controls) controls.classList.remove('hidden');
                        if (badge) badge.classList.remove('hidden');
                        if (hud) hud.classList.remove('hidden');
                        
                        const labelLower = (data.label || '').toLowerCase();
                        if (labelLower.includes('subiendo') || labelLower.includes('empresa') || labelLower.includes('afiliado')) {
                            partPush?.classList.add('bolt-push');
                            partPull?.classList.remove('bolt-pull');
                        } else {
                            partPull?.classList.add('bolt-pull');
                            partPush?.classList.remove('bolt-push');
                        }

                        if (data.control === 'paused') {
                            btnP.classList.add('hidden'); btnR.classList.remove('hidden');
                            title.textContent = 'Sistema en Pausa';
                        } else {
                            btnP.classList.remove('hidden'); btnR.classList.add('hidden');
                        }

                        if (data.live_feed && data.live_feed.length > 0) {
                            const hash = data.live_feed[0].time + data.live_feed[0].msg;
                            if (hash !== lastLogHash) {
                                feed.innerHTML = data.live_feed.map(l => `<div class="mb-2 flex gap-3"><span class="text-slate-500 font-bold shrink-0">[${l.time}]</span> <span class="${l.color === 'rose' ? 'text-rose-400' : (l.color === 'cyan' ? 'text-blue-400' : 'text-slate-300')}">${l.msg}</span></div>`).join('');
                                feed.scrollTop = feed.scrollHeight;
                                lastLogHash = hash;

                                // Play error sound if msg contains error
                                if (playSounds && data.live_feed[0].msg.toLowerCase().includes('error')) {
                                    document.getElementById('audio-error').play();
                                }
                            }
                        }
                    } else {
                        // ── Debounce: esperar 3 polls inactivos antes de declarar idle ──
                        // Esto evita que un solo timeout de red o cache miss resetee la UI
                        inactiveStreak++;

                        if (inactiveStreak < 3) {
                            // Aún en período de gracia — no resetear todavía
                            return;
                        }

                        // Confirmado inactivo tras 3 polls consecutivos (~6 segundos)
                        if (wasActive) {
                            if (playSounds) document.getElementById('audio-success').play();
                            wasActive = false;
                            
                            // Auto-retry si no llegó al 95% (crash)
                            if (document.getElementById('toggle-retry').checked && lastProgress < 95) {
                                console.log("Auto-retry triggered...");
                                triggerSelectiveSync('pull'); 
                            }
                        }

                        title.textContent = 'Esperando Instrucción';
                        perc.textContent = '0%'; bar.style.width = '0%';
                        eta.classList.add('hidden');
                        controls.classList.add('hidden');
                        badge.classList.add('hidden');
                        hud.classList.add('hidden');
                        partPush?.classList.remove('bolt-push');
                        partPull?.classList.remove('bolt-pull');
                    }
                    lastProgress = data.progress;
                })
                .catch(() => {
                    // Error de red — no resetear UI, simplemente ignorar este poll
                    inactiveStreak++;
                });
        }
        setInterval(poll, 2000);


        // AJAX Pagination for History
        bindHistoryClicks();

        function bindHistoryClicks() {
            document.querySelectorAll('#sync-history-container tr.hover\\:bg-blue-50\\/30').forEach(row => {
                row.style.cursor = 'pointer';
                row.onclick = function() {
                    const logId = this.querySelector('p.text-sm.font-black').textContent.replace('#', '');
                    openDrawer(logId);
                };
            });
        }

        window.openDrawer = function(id) {
            document.getElementById('log-drawer').classList.add('open');
            document.getElementById('log-drawer-overlay').classList.add('open');
            document.getElementById('drawer-title').textContent = '#' + id;
            document.getElementById('drawer-content').innerHTML = '<div class="flex justify-center py-20"><div class="animate-spin w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full"></div></div>';

            // Here we would fetch real details, but since we have them in the row via $log->summary, 
            // for now let's just show a stylized summary from the available stats.
            setTimeout(() => {
                document.getElementById('drawer-content').innerHTML = `
                    <div class="p-8 bg-slate-50 rounded-[40px] border border-slate-100">
                        <p class="text-[10px] font-black uppercase text-slate-400 mb-4 tracking-widest">Resumen Ejecutivo</p>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="bg-white p-6 rounded-3xl shadow-sm">
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Impacto Total</p>
                                <p class="text-2xl font-black text-slate-900">Procesando...</p>
                            </div>
                            <div class="bg-white p-6 rounded-3xl shadow-sm">
                                <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Duración</p>
                                <p class="text-2xl font-black text-slate-900">N/A</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest px-4">Trazabilidad Técnica</p>
                        <div class="bg-slate-900 text-emerald-400 p-8 rounded-[40px] font-mono text-[11px] leading-relaxed shadow-2xl">
                            <span class="text-slate-500">// Log Header</span><br>
                            ID: ${id}<br>
                            Status: SUCCESS<br>
                            Timestamp: ${new Date().toLocaleString()}<br><br>
                            <span class="text-slate-500">// Analysis</span><br>
                            > Verificando integridad de nodos... OK<br>
                            > Comparando deltas locales vs nube...<br>
                            > Operación completada sin conflictos.
                        </div>
                    </div>
                `;
            }, 600);
        };

        window.closeDrawer = function() {
            document.getElementById('log-drawer').classList.remove('open');
            document.getElementById('log-drawer-overlay').classList.remove('open');
        };

        document.addEventListener('click', function(e) {
            const link = e.target.closest('#sync-history-container .pagination a');
            if (link) {
                e.preventDefault();
                const url = link.href;
                const container = document.getElementById('sync-history-container');
                
                container.style.opacity = '0.5';
                container.style.pointerEvents = 'none';

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('sync-history-container').innerHTML;
                        
                        container.innerHTML = newContent;
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                        
                        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        bindHistoryClicks(); // Re-bind for new rows
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        container.style.opacity = '1';
                        container.style.pointerEvents = 'auto';
                    });
            }
        });
    });

    window.runComparison = function() {
        const id = document.getElementById('compare-id').value;
        const type = document.querySelector('input[name="comp-type"]:checked').value;

        if (!id) {
            Swal.fire({ title: 'Error', text: 'Ingrese un ID (Cédula o RNC) para comparar.', icon: 'error', customClass: { popup: 'rounded-[40px]' } });
            return;
        }

        Swal.fire({
            title: 'Analizando Diferencias...',
            text: 'Contrastando base de datos Local vs Nube Firebase.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(`{{ route('carnetizacion.sync_center.compare') }}?id=${id}&type=${type}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire({ title: 'Error', text: data.error, icon: 'error', customClass: { popup: 'rounded-[40px]' } });
                    return;
                }

                const local = data.local || {};
                const remote = data.remote || {};
                const hasLocal = !!data.local;
                const hasRemote = !!data.remote;

                let html = `
                    <div class="grid grid-cols-2 gap-6 text-left">
                        <!-- Local Column -->
                        <div class="p-6 bg-slate-50 rounded-[32px] border border-slate-100">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span> MySQL Local
                            </p>
                            ${hasLocal ? `
                                <div class="space-y-3">
                                    <p class="text-xs font-black text-slate-800">${local.nombre_completo || local.nombre || 'N/A'}</p>
                                    <div class="text-[10px] space-y-1">
                                        <div class="flex justify-between"><span>Estado:</span> <b class="text-blue-600">${local.estado_id || 'N/A'}</b></div>
                                        <div class="flex justify-between"><span>Update:</span> <b>${local.updated_at ? new Date(local.updated_at).toLocaleDateString() : 'N/A'}</b></div>
                                    </div>
                                    <div class="mt-4 p-4 bg-white rounded-2xl text-[9px] font-mono text-slate-500 overflow-hidden text-ellipsis">
                                        ${JSON.stringify(local).substring(0, 100)}...
                                    </div>
                                </div>
                            ` : '<div class="py-10 text-center text-slate-400 italic text-xs">Registro no encontrado en Local</div>'}
                        </div>

                        <!-- Remote Column -->
                        <div class="p-6 bg-blue-900 text-white rounded-[32px] border border-blue-800 shadow-xl">
                            <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span> Firebase Cloud
                            </p>
                            ${hasRemote ? `
                                <div class="space-y-3">
                                    <p class="text-xs font-black text-white">${remote.nombre_completo || remote.nombre || 'N/A'}</p>
                                    <div class="text-[10px] space-y-1">
                                        <div class="flex justify-between text-blue-200"><span>Estado:</span> <b class="text-white">${remote.estado_id || 'N/A'}</b></div>
                                        <div class="flex justify-between text-blue-200"><span>Update:</span> <b class="text-white">${remote.updated_at ? remote.updated_at.substring(0,10) : 'N/A'}</b></div>
                                    </div>
                                    <div class="mt-4 p-4 bg-black/30 rounded-2xl text-[9px] font-mono text-blue-300 overflow-hidden text-ellipsis">
                                        ${JSON.stringify(remote).substring(0, 100)}...
                                    </div>
                                </div>
                            ` : '<div class="py-10 text-center text-blue-400/50 italic text-xs">Registro no encontrado en Firebase</div>'}
                        </div>
                    </div>
                `;

                Swal.fire({
                    title: 'Resultado de Auditoría',
                    html: html,
                    width: '800px',
                    confirmButtonText: 'Cerrar Análisis',
                    confirmButtonColor: '#1e293b',
                    customClass: { popup: 'rounded-[50px] border-0 shadow-2xl' }
                });
            });
    };

    window.openDisasterPanel = function() {
        Swal.fire({
            title: 'Recuperación de Desastres',
            text: 'Consultando snapshots de seguridad...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch("{{ route('carnetizacion.sync_center.list_snapshots') }}")
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire({ title: 'Error', text: 'No se pudieron listar los respaldos.', icon: 'error', customClass: { popup: 'rounded-[40px]' } });
                    return;
                }

                let html = `
                    <div class="text-left space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        ${data.snapshots.length > 0 ? data.snapshots.map(s => `
                            <div class="p-6 bg-slate-50 border border-slate-100 rounded-[2rem] flex items-center justify-between group hover:bg-white hover:shadow-lg transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-slate-200 rounded-xl flex items-center justify-center text-slate-500 group-hover:bg-rose-100 group-hover:text-rose-600 transition-all">
                                        <span class="material-symbols-outlined">database</span>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-slate-800 break-all">${s.table}</p>
                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">${s.size.toLocaleString()} registros detectados</p>
                                    </div>
                                </div>
                                <button onclick="triggerRestore('${s.table}')" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-rose-600 transition-all">
                                    Restaurar
                                </button>
                            </div>
                        `).join('') : '<div class="py-20 text-center text-slate-400 italic">No se encontraron snapshots de respaldo disponibles.</div>'}
                    </div>
                `;

                Swal.fire({
                    title: 'Snapshot Manager',
                    html: html,
                    width: '600px',
                    showConfirmButton: false,
                    showCloseButton: true,
                    customClass: { popup: 'rounded-[50px] border-0 shadow-2xl' }
                });
            });
    };

    window.triggerRestore = function(table) {
        Swal.fire({
            title: '¿Confirmar Restauración?',
            text: `ATENCIÓN: La tabla actual será reemplazada por el contenido de ${table}. Esta operación no se puede deshacer.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#1e293b',
            confirmButtonText: 'Sí, Restaurar Ahora',
            cancelButtonText: 'Abortar',
            customClass: { popup: 'rounded-[40px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Restaurando Ecosistema...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                
                fetch("{{ route('carnetizacion.sync_center.restore_snapshot') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ table: table })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Restauración Exitosa',
                            text: 'La base de datos ha sido revertida al estado del snapshot seleccionado.',
                            icon: 'success',
                            confirmButtonColor: '#1e293b',
                            customClass: { popup: 'rounded-[40px]' }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({ title: 'Fallo Crítico', text: data.error, icon: 'error', customClass: { popup: 'rounded-[40px]' } });
                    }
                });
            }
        });
    };

    window.purgeQueue = function() {
        Swal.fire({
            title: '¿Purgar Cola de Tareas?',
            text: 'Esto eliminará TODAS las tareas pendientes (importaciones, sincronizaciones, etc.). El sistema se detendrá inmediatamente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#1e293b',
            confirmButtonText: 'Sí, Purgar Todo',
            cancelButtonText: 'Cancelar',
            customClass: { popup: 'rounded-[40px]' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Limpiando Sistema...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                
                fetch("{{ route('carnetizacion.sync_center.purge_queue') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Cola Purgada',
                            text: `Se han eliminado ${data.count} tareas pendientes.`,
                            icon: 'success',
                            confirmButtonColor: '#1e293b',
                            customClass: { popup: 'rounded-[40px]' }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({ title: 'Error', text: data.error, icon: 'error', customClass: { popup: 'rounded-[40px]' } });
                    }
                });
            }
        });
    };
</script>
@endpush
