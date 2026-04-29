<section class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-100">
        {{-- Último Acceso --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-blue-600">
                <i class="ph ph-sign-in text-2xl"></i>
            </div>
            <div>
                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest leading-tight">Último Inicio de Sesión</p>
                <p class="text-[0.85rem] font-black text-slate-700 mt-0.5">
                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'No registrado' }}
                </p>
                <p class="text-[0.6rem] text-slate-500 italic mt-0.5">
                    Hace {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : '---' }}
                </p>
            </div>
        </div>

        {{-- IP de Origen --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-slate-200 flex items-center justify-center text-slate-500">
                <i class="ph ph-globe text-2xl"></i>
            </div>
            <div>
                <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest leading-tight">Dirección IP de Origen</p>
                <p class="text-[0.85rem] font-black text-slate-700 mt-0.5">
                    {{ $user->last_login_ip ?? 'N/A' }}
                </p>
                <p class="text-[0.6rem] text-slate-500 italic mt-0.5">
                    Ubicación detectada por el servidor
                </p>
            </div>
        </div>
    </div>

    <!-- User Roles -->
    <div class="p-6 bg-blue-50/50 rounded-2xl border border-blue-100/50">
        <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest leading-tight mb-4">Roles de la Cuenta</p>
        <div class="flex flex-wrap gap-2">
            @forelse($user->getRoleNames() as $role)
                <span class="px-3 py-1 bg-blue-600 text-white text-[0.65rem] font-extrabold uppercase tracking-wider rounded-lg shadow-sm border border-blue-700">
                    {{ $role }}
                </span>
            @empty
                <span class="px-3 py-1 bg-slate-200 text-slate-500 text-[0.65rem] font-extrabold uppercase tracking-wider rounded-lg shrink-0">
                    Ninguno
                </span>
            @endforelse
        </div>
    </div>

    <div class="p-4 bg-amber-50 rounded-xl border border-amber-100 flex gap-3 items-start">
        <i class="ph-fill ph-shield-check text-amber-500 text-xl mt-0.5"></i>
        <div>
            <p class="text-xs font-bold text-amber-800">Verificación de Identidad Autónoma</p>
            <p class="text-[0.7rem] text-amber-700 leading-relaxed mt-1">
                Tu cuenta está protegida por el sistema de auditoría global. Cualquier cambio administrativo quedará registrado bajo tu firma digital en tu dispositivo.
            </p>
        </div>
    </div>
</section>
