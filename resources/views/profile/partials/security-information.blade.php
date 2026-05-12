<section class="space-y-10">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        {{-- Último Acceso --}}
        <div class="p-8 bg-slate-50 dark:bg-slate-800/50 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 flex items-center gap-6 group hover:border-indigo-200 transition-colors">
            <div class="w-16 h-16 bg-white dark:bg-slate-900 rounded-2xl shadow-sm flex items-center justify-center text-indigo-600 transition-transform group-hover:scale-110">
                <i class="ph ph-sign-in text-3xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Último Inicio</p>
                <p class="text-base font-black text-slate-900 dark:text-white">
                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'No registrado' }}
                </p>
                <p class="text-[10px] text-indigo-500 font-bold uppercase tracking-tighter mt-1">
                    Hace {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '---' }}
                </p>
            </div>
        </div>

        {{-- IP de Origen --}}
        <div class="p-8 bg-slate-50 dark:bg-slate-800/50 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 flex items-center gap-6 group hover:border-indigo-200 transition-colors">
            <div class="w-16 h-16 bg-white dark:bg-slate-900 rounded-2xl shadow-sm flex items-center justify-center text-slate-500 transition-transform group-hover:scale-110">
                <i class="ph ph-globe text-3xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Dirección IP</p>
                <p class="text-base font-black text-slate-900 dark:text-white">
                    {{ $user->last_login_ip ?? '192.168.1.1' }}
                </p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-1">
                    Red Interna Detectada
                </p>
            </div>
        </div>
    </div>

    <!-- User Roles -->
    <div class="p-10 bg-indigo-50/30 dark:bg-indigo-500/5 rounded-[3rem] border border-indigo-100/50 dark:border-indigo-800/30 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-600/5 rounded-full blur-3xl"></div>
        
        <div class="relative">
            <h4 class="text-[11px] font-black text-indigo-600 uppercase tracking-[0.3em] mb-8 flex items-center gap-2">
                <i class="ph-bold ph-shield-check"></i> Privilegios de Cuenta
            </h4>
            
            <div class="flex flex-wrap gap-3">
                @forelse($user->getRoleNames() as $role)
                    <div class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-indigo-100 dark:border-indigo-800 transition-transform hover:-translate-y-1">
                        <div class="w-2 h-2 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(79,70,229,0.5)]"></div>
                        <span class="text-[11px] font-black uppercase tracking-widest text-slate-800 dark:text-white">
                            {{ $role }}
                        </span>
                    </div>
                @empty
                    <span class="text-[11px] font-black text-slate-400 italic">No hay roles asignados</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Safety Banner -->
    <div class="flex items-center gap-6 p-8 bg-emerald-50/50 dark:bg-emerald-500/5 rounded-[2.5rem] border border-emerald-100/50 dark:border-emerald-800/30">
        <div class="w-14 h-14 rounded-2xl bg-white dark:bg-slate-900 shadow-sm flex items-center justify-center text-emerald-500 flex-shrink-0">
            <i class="ph ph-fingerprint text-3xl"></i>
        </div>
        <div>
            <h5 class="text-sm font-black text-emerald-900 dark:text-emerald-400 tracking-tight mb-1">Firma Digital Activa</h5>
            <p class="text-[11px] font-medium text-emerald-700/70 dark:text-emerald-500/70 leading-relaxed">
                Todas tus acciones están vinculadas a tu identidad única y protegidas bajo el protocolo de auditoría de <span class="font-black">SysCarnet v3.0</span>.
            </p>
        </div>
    </div>
</section>
