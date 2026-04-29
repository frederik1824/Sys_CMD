<div wire:poll.5s="updateStats" class="relative group">
    <!-- UI Container: Premium Glassmorphism Shell -->
    <div class="bg-gradient-to-br from-slate-900/60 to-slate-900/30 backdrop-blur-3xl border border-white/10 rounded-[2rem] p-7 shadow-[0_8px_30px_rgb(0,0,0,0.12)] overflow-hidden transition-all duration-700 hover:shadow-blue-500/10 hover:border-white/20 relative">
        
        <!-- Decoration: Kinetic Energy Core (Animated background glow) -->
        <div class="absolute -right-20 -bottom-20 w-72 h-72 bg-blue-500/10 rounded-full blur-[100px] animate-pulse pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-48 h-48 bg-indigo-500/5 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="flex items-center gap-6 relative z-10">
            <!-- DATA CORE: The Aerospace Pulse Orb -->
            <div class="relative flex-shrink-0">
                <!-- Inner Tech Core -->
                <div class="w-16 h-16 rounded-[1.25rem] bg-slate-800/50 border border-white/10 flex items-center justify-center relative z-20 shadow-inner backdrop-blur-xl">
                    <span class="material-symbols-outlined text-[32px] transition-all duration-700 ease-out"
                         style="color: {{ $status === 'running' ? '#60a5fa' : ($status === 'failed' ? '#f87171' : '#34d399') }}; {{ $status === 'running' ? 'filter: drop-shadow(0 0 10px rgba(96,165,250,0.4));' : ($status !== 'failed' ? 'filter: drop-shadow(0 0 10px rgba(52,211,153,0.3));' : '') }}">
                         {{ $status === 'running' ? 'rocket_launch' : ($status === 'failed' ? 'report_problem' : 'satellite_alt') }}
                    </span>
                </div>

                <!-- HUD Radial Rings (Animated when active) -->
                @if($status === 'running')
                    <div class="absolute inset-0 scale-[1.6] rounded-[1.25rem] border border-blue-400/20 animate-[ping_3s_ease-out_infinite] opacity-50"></div>
                    <div class="absolute inset-0 scale-[1.3] rounded-[1.25rem] border border-blue-400/30 animate-[spin_10s_linear_infinite] border-dashed opacity-40"></div>
                @endif
            </div>

            <!-- MISSION CONTROL HUD -->
            <div class="flex-1">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h4 class="text-[0.65rem] font-medium text-slate-400 uppercase tracking-widest mb-1">Estado del Sistema</h4>
                        <div class="flex items-center gap-3">
                             <div class="relative flex items-center justify-center">
                                <div class="w-2.5 h-2.5 rounded-full @if($status == 'running') bg-blue-400 animate-pulse @elseif($status == 'failed') bg-red-500 @else bg-emerald-400 animate-pulse @endif shadow-[0_0_12px_currentColor]"></div>
                                @if($status == 'running')
                                    <div class="absolute w-2.5 h-2.5 rounded-full bg-blue-400 animate-ping opacity-60"></div>
                                @elseif($status !== 'failed')
                                    <div class="absolute w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping opacity-50" style="animation-duration: 2s;"></div>
                                @endif
                             </div>
                            <span class="text-[1.35rem] font-bold text-white tracking-tight leading-none">
                                {{ $status === 'running' ? 'Sincronizando' : ($status === 'failed' ? 'Error de Enlace' : 'Enlace Estable') }}
                            </span>
                        </div>
                    </div>
                    <div class="bg-white/5 border border-white/10 px-4 py-2 rounded-2xl backdrop-blur-md">
                        <div class="text-[0.6rem] font-semibold text-slate-400 uppercase tracking-wider mb-1">Rendimiento</div>
                        <div class="text-xl font-bold font-mono text-white leading-none">
                            {{ number_format($intensity, 1) }}<span class="text-[0.6em] text-slate-400 ml-0.5">%</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-HUD Modules -->
                <div class="grid grid-cols-2 gap-4 mt-5 pt-5 border-t border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-slate-800/50 border border-white/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[16px] text-slate-300">grid_view</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[0.65rem] text-slate-400 font-medium uppercase tracking-wider mb-0.5">Tareas</span>
                            <span class="text-[0.8rem] font-bold text-white">{{ $activeProcesses }} PROCESOS</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-slate-800/50 border border-white/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[16px] text-slate-300">query_stats</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[0.65rem] text-slate-400 font-medium uppercase tracking-wider mb-0.5">Última Actividad</span>
                            <span class="text-[0.8rem] font-bold text-white">{{ $lastSync ? str_replace(['ago', 'hours', 'hour', 'minutes', 'minute', 'seconds', 'second'], ['atrás', 'horas', 'hora', 'minutos', 'minuto', 'segundos', 'segundo'], $lastSync->created_at->diffForHumans(null, true)) : 'DESCONECTADO' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aerospace Progress Strip -->
        <div class="mt-7 h-1.5 w-full bg-slate-800/50 rounded-full overflow-hidden relative border border-white/5">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-blue-500/20 to-transparent animate-[shimmer_2s_infinite]"></div>
            <div class="h-full bg-gradient-to-r from-blue-500 to-cyan-300 shadow-[0_0_10px_rgba(59,130,246,0.5)] transition-all duration-1000 ease-out" 
                 style="width: {{ $intensity }}%"></div>
        </div>
    </div>

    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</div>

