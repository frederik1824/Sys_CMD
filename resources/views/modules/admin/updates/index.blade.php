@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f1f5f9] flex flex-col lg:flex-row" 
     x-data="{ 
        tab: window.location.hash ? window.location.hash.replace('#', '') : 'releases',
        ...healthMonitor() 
     }"
     x-init="window.addEventListener('hashchange', () => { tab = window.location.hash.replace('#', '') || 'releases' })">
    
    <!-- SIDEBAR DE NAVEGACIÓN TÉCNICA -->
    <div class="w-full lg:w-72 bg-white border-r border-slate-200 flex flex-col shadow-xl z-20">
        <div class="p-8 border-b border-slate-100 bg-slate-900 text-white relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-500/20 rounded-full blur-3xl"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-50 mb-1">SysCarnet ERP</p>
            <h1 class="text-xl font-black tracking-tight flex items-center gap-2">
                <i class="ph-bold ph-shield-check text-blue-400"></i>
                Nexus <span class="text-blue-400">Suite</span>
            </h1>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <button @click="tab = 'releases'; window.location.hash = 'releases'" 
                :class="tab === 'releases' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-slate-500 hover:bg-slate-50 border-transparent'" 
                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl border transition-all group">
                <i class="ph-bold ph-rocket-launch text-xl group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-black uppercase tracking-widest">Releases</span>
            </button>
            <button @click="tab = 'health'; window.location.hash = 'health'" 
                :class="tab === 'health' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-slate-500 hover:bg-slate-50 border-transparent'" 
                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl border transition-all group">
                <i class="ph-bold ph-heartbeat text-xl group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-black uppercase tracking-widest">Salud</span>
            </button>
            <button @click="tab = 'backups'; window.location.hash = 'backups'" 
                :class="tab === 'backups' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-slate-500 hover:bg-slate-50 border-transparent'" 
                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl border transition-all group">
                <i class="ph-bold ph-database text-xl group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-black uppercase tracking-widest">Snapshots</span>
            </button>
            <button @click="tab = 'logs'; window.location.hash = 'logs'; fetchLogs()" 
                :class="tab === 'logs' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-slate-500 hover:bg-slate-50 border-transparent'" 
                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl border transition-all group">
                <i class="ph-bold ph-terminal-window text-xl group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-black uppercase tracking-widest">Logs</span>
            </button>
            <button @click="tab = 'packer'; window.location.hash = 'packer'" 
                :class="tab === 'packer' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'text-slate-500 hover:bg-slate-50 border-transparent'" 
                class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl border transition-all group">
                <i class="ph-bold ph-package text-xl group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-black uppercase tracking-widest">Packer</span>
            </button>
        </nav>

        <div class="p-6">
            <a href="/recovery.php" class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 hover:bg-rose-600 hover:text-white transition-all text-[10px] font-black uppercase tracking-widest">
                <i class="ph-bold ph-warning-octagon text-lg"></i>
                Modo Emergencia
            </a>
        </div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOP BAR DE TELEMETRÍA -->
        <header class="h-20 bg-white border-b border-slate-200 px-8 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-8">
                <div class="hidden md:flex items-center gap-3">
                    <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter leading-none">Status</p>
                        <p class="text-xs font-bold text-slate-700">Sistema Online</p>
                    </div>
                </div>
                <div class="h-8 w-[1px] bg-slate-100"></div>
                <div class="flex items-center gap-3">
                    <i class="ph-bold ph-fingerprint text-slate-400 text-lg"></i>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter leading-none">Versión Actual</p>
                        <p class="text-xs font-bold text-slate-700">{{ $currentVersion->version ?? 'v1.0.0' }}</p>
                    </div>
                </div>
                <div class="h-8 w-[1px] bg-slate-100"></div>
                <div class="flex items-center gap-3">
                    <i class="ph-bold ph-users text-slate-400 text-lg"></i>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter leading-none">Sesiones</p>
                        <p class="text-xs font-bold text-slate-700">{{ $activeSessions }} Activas</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <button @click="fetchMetrics()" class="p-2 text-slate-400 hover:text-blue-600 transition-colors">
                    <i class="ph-bold ph-arrows-clockwise text-xl"></i>
                </button>
                <div class="px-4 py-2 bg-slate-900 rounded-xl text-white text-[10px] font-black uppercase tracking-widest">
                    {{ now()->format('H:i') }}
                </div>
            </div>
        </header>

        <!-- ÁREA DE TRABAJO DINÁMICA -->
        <main class="p-8 lg:p-12 overflow-y-auto">
            
            <!-- SECCIÓN: RELEASES -->
            <div x-show="tab === 'releases'" x-transition x-cloak class="max-w-6xl space-y-10">
                <!-- Semáforo de Requisitos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4 transition-colors"
                             :class="'{{ $requirements['php']['status'] }}' === 'ok' ? 'bg-emerald-50 text-emerald-500 group-hover:bg-emerald-500 group-hover:text-white' : 'bg-rose-50 text-rose-500'">
                            <i class="ph-bold ph-cpu text-2xl"></i>
                        </div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Entorno PHP</h4>
                        <p class="text-lg font-black text-slate-800">{{ $requirements['php']['version'] }}</p>
                    </div>
                    <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4 transition-colors"
                             :class="'{{ $requirements['disk']['status'] }}' === 'ok' ? 'bg-blue-50 text-blue-500 group-hover:bg-blue-500 group-hover:text-white' : 'bg-rose-50 text-rose-500'">
                            <i class="ph-bold ph-hard-drive text-2xl"></i>
                        </div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Almacenamiento</h4>
                        <p class="text-lg font-black text-slate-800">{{ $requirements['disk']['free'] }} Disponibles</p>
                    </div>
                    <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-4 transition-colors bg-amber-50 text-amber-500 group-hover:bg-amber-500 group-hover:text-white">
                            <i class="ph-bold ph-brackets-curly text-2xl"></i>
                        </div>
                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Caché de Rutas</h4>
                        <p class="text-lg font-black text-slate-800">Optimizado</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="lg:col-span-2 bg-white rounded-[40px] shadow-2xl border border-slate-200 overflow-hidden">
                        <div class="p-10 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Carga de Parches</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Despliegue Manual de ZIP</p>
                            </div>
                            <i class="ph-bold ph-upload-simple text-3xl text-slate-200"></i>
                        </div>
                        <div class="p-10">
                            <div class="relative group">
                                <input type="file" @change="handleFile" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="border-4 border-dashed border-slate-100 rounded-[32px] p-16 text-center group-hover:border-blue-400 group-hover:bg-blue-50 transition-all">
                                    <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-[24px] flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform">
                                        <i class="ph-bold ph-file-zip text-4xl"></i>
                                    </div>
                                    <h4 class="text-lg font-black text-slate-800 mb-2" x-text="file ? file.name : 'Arrastra tu archivo .zip'"></h4>
                                    <p class="text-sm text-slate-400 font-medium">Límite institucional: 100MB por paquete</p>
                                </div>
                            </div>
                            <button @click="uploadUpdate()" :disabled="!file" class="w-full mt-8 bg-slate-900 text-white py-6 rounded-3xl text-xs font-black uppercase tracking-[0.2em] shadow-xl hover:bg-blue-600 hover:-translate-y-1 transition-all disabled:opacity-30 disabled:pointer-events-none">
                                Validar e Instalar Parche
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="bg-indigo-600 rounded-[32px] p-8 text-white shadow-xl relative overflow-hidden group">
                            <div class="absolute -right-10 -bottom-10 opacity-10 group-hover:scale-110 transition-transform duration-700">
                                <i class="ph ph-shield-check text-[180px]"></i>
                            </div>
                            <h4 class="text-xs font-black uppercase tracking-widest mb-4 opacity-70">Seguridad Core</h4>
                            <p class="text-lg font-black leading-tight mb-6">Backup preventivo forzado antes de actualizar.</p>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                                    <i class="ph-bold ph-check text-lg"></i>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest">Protocolo Activo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: SALUD -->
            <div x-show="tab === 'health'" x-transition x-cloak class="max-w-6xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                    <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <i class="ph-bold ph-cpu text-2xl text-blue-500"></i>
                            <span class="text-[10px] font-black px-3 py-1 bg-blue-50 text-blue-600 rounded-full uppercase" x-text="metrics.server.os"></span>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">CPU LOAD</p>
                        <div class="flex items-end gap-2">
                            <p class="text-3xl font-black text-slate-800" x-text="metrics.server.cpu + '%'"></p>
                            <div class="flex-1 h-2 bg-slate-100 rounded-full mb-2 overflow-hidden">
                                <div class="h-full bg-blue-500 transition-all duration-1000" :style="'width: ' + metrics.server.cpu + '%'"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <i class="ph-bold ph-memory text-2xl text-indigo-500"></i>
                            <span class="text-[10px] font-black px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full uppercase">RAM</span>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">MEMORIA RAM</p>
                        <div class="flex items-end gap-2">
                            <p class="text-3xl font-black text-slate-800" x-text="metrics.server.ram.percent + '%'"></p>
                            <div class="flex-1 h-2 bg-slate-100 rounded-full mb-2 overflow-hidden">
                                <div class="h-full bg-indigo-500 transition-all duration-1000" :style="'width: ' + metrics.server.ram.percent + '%'"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <i class="ph-bold ph-database text-2xl text-rose-500"></i>
                            <span class="text-[10px] font-black px-3 py-1 bg-rose-50 text-rose-600 rounded-full uppercase">SQL</span>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">LATENCIA DB</p>
                        <p class="text-3xl font-black text-slate-800" x-text="metrics.database.latency_ms + 'ms'"></p>
                    </div>
                    <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <i class="ph-bold ph-cloud-arrow-up text-2xl text-emerald-500"></i>
                            <span class="text-[10px] font-black px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full uppercase">SYNC</span>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">FIREBASE SYNC</p>
                        <p class="text-3xl font-black text-slate-800" x-text="metrics.firebase.latency_ms + 'ms'"></p>
                    </div>
                </div>

                <div class="bg-slate-900 rounded-[40px] p-10 text-white relative overflow-hidden shadow-2xl">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-xl font-black tracking-tight">Estado de Microservicios</h3>
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500/30"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500/10"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <template x-for="service in metrics.services">
                            <div class="flex items-center justify-between p-6 rounded-[24px] border border-white/10 bg-white/5 backdrop-blur-md">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center">
                                        <i class="ph-bold text-xl" :class="service.status === 'online' ? 'ph-check-circle text-emerald-400' : 'ph-warning-circle text-rose-400'"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-black uppercase tracking-widest" x-text="service.name"></p>
                                        <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter" x-text="service.port ? 'Port: ' + service.port : 'Internal Service'"></p>
                                    </div>
                                </div>
                                <span class="px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border"
                                      :class="service.status === 'online' ? 'border-emerald-500/50 text-emerald-400 bg-emerald-500/10' : 'border-rose-500/50 text-rose-400 bg-rose-500/10'"
                                      x-text="service.status"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: LOGS -->
            <div x-show="tab === 'logs'" x-transition x-cloak class="max-w-6xl">
                <div class="bg-slate-950 rounded-[40px] shadow-2xl overflow-hidden border border-white/10">
                    <div class="p-10 flex items-center justify-between bg-slate-900/50 backdrop-blur-xl border-b border-white/5">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-2xl flex items-center justify-center">
                                <i class="ph-bold ph-terminal-window text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-white tracking-tight">System Log Terminal</h3>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mt-1">Live Feed: laravel.log</p>
                            </div>
                        </div>
                        <button @click="fetchLogs()" class="bg-white text-slate-900 px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-transform active:scale-95 shadow-xl shadow-white/5">
                            Forzar Refresh
                        </button>
                    </div>
                    <div class="p-1 font-mono text-[11px] leading-relaxed relative group">
                        <div class="bg-slate-950 p-10 h-[600px] overflow-y-auto scrollbar-hide text-emerald-400/90" id="log-viewer">
                            <pre class="whitespace-pre-wrap select-all" x-text="systemLogs"></pre>
                        </div>
                        <div class="absolute bottom-10 right-10 flex items-center gap-3">
                            <div class="flex items-center gap-2 px-4 py-2 bg-slate-900 border border-white/10 rounded-full shadow-2xl">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Streaming</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: SNAPSHOTS -->
            <div x-show="tab === 'backups'" x-transition x-cloak class="max-w-6xl space-y-10">
                <div class="bg-white p-10 rounded-[40px] border border-slate-200 shadow-xl flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-[32px] flex items-center justify-center shadow-inner">
                            <i class="ph-bold ph-shield-chevron text-4xl"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Puntos de Restauración</h3>
                            <p class="text-slate-500 font-medium">Gestión histórica de backups de base de datos y código fuente.</p>
                        </div>
                    </div>
                    <button @click="createBackup()" class="bg-slate-900 text-white px-10 py-5 rounded-3xl text-xs font-black uppercase tracking-widest hover:bg-rose-600 transition-all shadow-2xl shadow-slate-900/20">
                        Crear Nuevo Snapshot
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($backups as $backup)
                        <div class="bg-white rounded-[32px] border border-slate-100 p-8 shadow-sm hover:shadow-2xl transition-all group relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-8 opacity-[0.05] group-hover:scale-125 transition-transform duration-700">
                                <i class="ph-bold" :class="'{{ $backup->type }}' === 'code' ? 'ph-code-block' : 'ph-database'" style="font-size: 80px;"></i>
                            </div>
                            <div class="flex items-center justify-between mb-8">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                                     :class="'{{ $backup->type }}' === 'code' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600'">
                                    <i class="ph-bold text-2xl" :class="'{{ $backup->type }}' === 'code' ? 'ph-code-block' : 'ph-database'"></i>
                                </div>
                                <div class="flex gap-2 translate-x-10 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all">
                                    <a href="{{ route('admin.updates.download_release', $backup->filename) }}" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-blue-600 transition-all">
                                        <i class="ph-bold ph-download-simple"></i>
                                    </a>
                                    <button @click="confirmRollback('{{ $backup->id }}')" class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center hover:bg-rose-900 transition-all">
                                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-[8px] font-black uppercase px-2 py-1 rounded-md"
                                          :class="'{{ $backup->type }}' === 'code' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700'">
                                        {{ $backup->type === 'code' ? 'Source Code' : 'Full Database' }}
                                    </span>
                                </div>
                                <h4 class="text-xs font-black text-slate-800 truncate" title="{{ $backup->filename }}">{{ $backup->filename }}</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $backup->formatted_size }} • {{ $backup->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center border-4 border-dashed border-slate-200 rounded-[40px]">
                            <i class="ph-bold ph-ghost text-6xl text-slate-200 mb-4"></i>
                            <p class="text-slate-400 font-black uppercase tracking-widest text-xs">No hay snapshots registrados en este servidor</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- SECCIÓN: PACKER -->
            <div x-show="tab === 'packer'" x-transition x-cloak class="max-w-6xl grid grid-cols-1 lg:grid-cols-3 gap-10">
                <div class="lg:col-span-2 bg-white rounded-[40px] shadow-2xl border border-slate-200 overflow-hidden">
                    <div class="p-10 border-b border-slate-100 bg-slate-900 text-white flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-black tracking-tight">Empaquetador de Producción</h3>
                            <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mt-1">Generación de Paquetes Institucionales</p>
                        </div>
                        <i class="ph-bold ph-factory text-3xl text-white/20"></i>
                    </div>
                    <div class="p-10 space-y-8">
                        <div class="grid grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-2">Nueva Versión</label>
                                <input type="text" x-model="packVersion" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 font-black text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-2">Build ID (Auto)</label>
                                <div class="w-full bg-slate-100 border border-slate-100 rounded-2xl px-6 py-4 font-black text-slate-400">
                                    {{ now()->format('YmdHi') }}
                                </div>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-2">Notas del Cambio (Changelog)</label>
                            <textarea x-model="packChangelog" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-3xl px-6 py-4 font-medium text-slate-700 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all placeholder:text-slate-300" placeholder="Describe las mejoras aplicadas en este parche..."></textarea>
                        </div>
                        <button @click="generatePack()" class="w-full bg-blue-600 text-white py-6 rounded-3xl text-sm font-black uppercase tracking-[0.2em] shadow-xl hover:bg-slate-900 hover:-translate-y-1 transition-all">
                            Ejecutar Proceso de Compresión
                        </button>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-white rounded-[40px] border border-slate-200 p-10 shadow-sm relative overflow-hidden group">
                        <div class="absolute -right-10 -bottom-10 opacity-[0.03] group-hover:scale-125 transition-transform duration-700">
                            <i class="ph-bold ph-broom text-[180px]"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 mb-4 tracking-tight">Optimización</h3>
                        <p class="text-sm text-slate-500 font-medium leading-relaxed mb-8">
                            Libera recursos del servidor eliminando temporales de actualizaciones antiguas y regenerando la caché de la aplicación.
                        </p>
                        <button @click="purgeSystem()" class="w-full border-2 border-slate-900 text-slate-900 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">
                            Purga de Sistema
                        </button>
                    </div>

                    <div class="bg-blue-50 rounded-[40px] p-10 border border-blue-100">
                        <h4 class="text-xs font-black text-blue-900 uppercase tracking-widest mb-4">Output de Releases</h4>
                        <div class="space-y-4">
                            @foreach($generatedReleases as $release)
                                <div class="p-4 bg-white rounded-2xl border border-blue-200 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <i class="ph-bold ph-file-zip text-blue-500 text-xl"></i>
                                        <div class="min-w-0">
                                            <p class="text-[10px] font-black text-slate-800 truncate max-w-[120px]">{{ $release['name'] }}</p>
                                            <p class="text-[8px] font-bold text-slate-400 uppercase">{{ $release['size'] }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.updates.download_release', $release['name']) }}" class="text-blue-600 hover:text-blue-900 transition-colors">
                                        <i class="ph-bold ph-download-simple text-lg"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<script>
function healthMonitor() {
    return {
        metrics: {
            server: { cpu: 0, ram: { percent: 0 }, disk: { percent: 0 }, os: '' },
            database: { latency_ms: 0 },
            firebase: { latency_ms: 0 },
            services: [],
            queues: { pending: 0 }
        },
        file: null,
        packVersion: '{{ $currentVersion->version ?? "1.0.0" }}',
        packChangelog: '',
        updateLogs: [],
        systemLogs: 'Cargando logs...',
        init() {
            this.fetchMetrics();
            setInterval(() => this.fetchMetrics(), 5000);
            
            if (window.location.hash === '#logs') {
                this.tab = 'logs';
                this.fetchLogs();
            }
        },
        async fetchLogs() {
            try {
                const response = await fetch("{{ route('admin.updates.logs') }}");
                const data = await response.json();
                this.systemLogs = data.logs;
                
                setTimeout(() => {
                    const viewer = document.getElementById('log-viewer');
                    if (viewer) viewer.scrollTop = viewer.scrollHeight;
                }, 100);
            } catch (e) { this.systemLogs = 'Error al cargar los logs.'; }
        },
        async purgeSystem() {
            const result = await Swal.fire({
                title: '¿Ejecutar Limpieza?',
                text: 'Se borrarán archivos temporales y se limpiará la caché del sistema.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, limpiar',
                confirmButtonColor: '#2563eb'
            });

            if (result.isConfirmed) {
                Swal.fire({ title: 'Limpiando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                try {
                    const response = await fetch("{{ route('admin.updates.purge') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire('¡Limpio!', data.message, 'success');
                    }
                } catch (e) { Swal.fire('Error', 'Fallo la limpieza', 'error'); }
            }
        },
        async generatePack() {
            const result = await Swal.fire({
                title: '¿Generar Nuevo Paquete?',
                text: 'Esto comprimirá el estado actual del sistema. Asegúrate de que los cambios estén listos.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sí, empaquetar',
                confirmButtonColor: '#2563eb'
            });

            if (result.isConfirmed) {
                Swal.fire({ title: 'Empaquetando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                try {
                    const response = await fetch("{{ route('admin.updates.pack') }}", {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                        },
                        body: JSON.stringify({ 
                            version: this.packVersion,
                            changelog: this.packChangelog
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire('Éxito', data.message, 'success').then(() => window.location.reload());
                    }
                } catch (e) { Swal.fire('Error', 'No se pudo generar el paquete', 'error'); }
            }
        },
        async fetchMetrics() {
            try {
                const response = await fetch("{{ route('admin.updates.health') }}");
                this.metrics = await response.json();
            } catch (e) { console.error('Error fetching metrics', e); }
        },
        handleFile(event) {
            this.file = event.target.files[0];
        },
        async uploadUpdate() {
            if (!this.file) return;
            
            const formData = new FormData();
            formData.append('update_file', this.file);

            Swal.fire({
                title: 'Validando Paquete',
                html: 'Analizando integridad del ZIP...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading() }
            });

            try {
                const response = await fetch("{{ route('admin.updates.upload') }}", {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    throw new Error("El servidor devolvió una respuesta inválida. Verifica el tamaño del archivo y los logs.");
                }
                
                if (data.success) {
                    const result = await Swal.fire({
                        title: 'Paquete Validado',
                        text: 'El parche está listo. ¿Deseas aplicarlo ahora? El sistema entrará en modo mantenimiento.',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, aplicar actualización',
                        confirmButtonColor: '#0f172a'
                    });

                    if (result.isConfirmed) {
                        this.applyUpdate(data.temp_path);
                    }
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire('Fallo', error.message, 'error');
            }
        },
        async applyUpdate(tempPath, skipBackup = false) {
            this.updateLogs = ["🚀 Iniciando proceso de actualización..."];
            
            Swal.fire({
                title: skipBackup ? 'Aplicando (Sin Backup DB)' : 'Aplicando Actualización',
                html: `
                    <div class="mt-4 text-left">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-2 tracking-widest text-center">Consola de Despliegue</p>
                        <div id="update-console" class="bg-slate-900 rounded-2xl p-4 h-48 overflow-y-auto font-mono text-[10px] text-emerald-400 space-y-1">
                        </div>
                    </div>
                `,
                allowOutsideClick: false,
                didOpen: () => { 
                    Swal.showLoading();
                }
            });

            const addLog = (msg) => {
                this.updateLogs.push(msg);
                const consoleDiv = document.getElementById('update-console');
                if (consoleDiv) {
                    const newLog = document.createElement('div');
                    newLog.className = 'flex gap-2';
                    newLog.innerHTML = `<span class="text-slate-500">>></span><span>${msg}</span>`;
                    consoleDiv.appendChild(newLog);
                    consoleDiv.scrollTop = consoleDiv.scrollHeight;
                }
            };

            try {
                const response = await fetch("{{ url('admin/updates/apply') }}", {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' 
                    },
                    body: JSON.stringify({ 
                        temp_path: tempPath,
                        skip_backup: skipBackup
                    })
                });
                const data = await response.json();

                if (data.logs) {
                    data.logs.forEach(log => addLog(log));
                }

                if (data.success) {
                    setTimeout(() => {
                        Swal.fire('¡Éxito!', data.message, 'success').then(() => window.location.reload());
                    }, 1000);
                } else {
                    if (data.message.includes('backup preventivo')) {
                        const result = await Swal.fire({
                            title: 'Fallo de Seguridad',
                            text: 'No pudimos crear el backup automático. ¿Deseas aplicar el parche de todas formas? (Riesgoso)',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, aplicar sin backup',
                            cancelButtonText: 'No, cancelar',
                            confirmButtonColor: '#f59e0b'
                        });

                        if (result.isConfirmed) {
                            this.applyUpdate(tempPath, true);
                        }
                    } else {
                        throw new Error(data.message);
                    }
                }
            } catch (error) {
                Swal.fire('Fallo', error.message, 'error');
            }
        },
        async createBackup() {
            Swal.fire({ title: 'Creando Snapshot...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            try {
                const response = await fetch("{{ route('admin.updates.backup') }}", {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire('Éxito', data.message, 'success').then(() => window.location.reload());
                }
            } catch (e) { Swal.fire('Error', 'No se pudo crear el backup', 'error'); }
        },
        async confirmRollback(id) {
            const result = await Swal.fire({
                title: '¿Confirmar Restauración?',
                text: 'Esta acción es irreversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, Restaurar',
                confirmButtonColor: '#e11d48'
            });

            if (result.isConfirmed) {
                Swal.fire({ title: 'Restaurando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                try {
                    const response = await fetch(`{{ url('admin/updates/rollback') }}/${id}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        Swal.fire('Restaurado', data.message, 'success').then(() => window.location.reload());
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        }
    }
}
</script>
@endsection
