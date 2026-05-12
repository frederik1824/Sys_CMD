<section class="max-w-3xl">
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-10" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Hidden input for avatar -->
        <input type="file" id="avatar-input" name="avatar" class="hidden" onchange="syncAvatarPreview(this)">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
            <!-- Name -->
            <div class="space-y-3">
                <label for="name" class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] ml-2 block">Nombre Completo</label>
                <input id="name" name="name" type="text" 
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 dark:text-white transition-all shadow-sm"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
                @if($errors->get('name'))
                    <p class="text-xs text-rose-500 font-bold ml-2 italic">{{ $errors->first('name') }}</p>
                @endif
            </div>

            <!-- Email -->
            <div class="space-y-3">
                <label for="email" class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] ml-2 block">Correo Institucional</label>
                <input id="email" name="email" type="email" 
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 dark:text-white transition-all shadow-sm"
                    value="{{ old('email', $user->email) }}" required autocomplete="username" />
                @if($errors->get('email'))
                    <p class="text-xs text-rose-500 font-bold ml-2 italic">{{ $errors->first('email') }}</p>
                @endif
            </div>

            <!-- Phone -->
            <div class="space-y-3">
                <label for="phone" class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] ml-2 block">Teléfono / Extensión</label>
                <input id="phone" name="phone" type="text" 
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 dark:text-white transition-all shadow-sm"
                    value="{{ old('phone', $user->phone) }}" autocomplete="tel" placeholder="Ej: 809-000-0000" />
                @if($errors->get('phone'))
                    <p class="text-xs text-rose-500 font-bold ml-2 italic">{{ $errors->first('phone') }}</p>
                @endif
            </div>

            <!-- Position -->
            <div class="space-y-3">
                <label for="position" class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] ml-2 block">Cargo Institucional</label>
                <input id="position" name="position" type="text" 
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 dark:text-white transition-all shadow-sm"
                    value="{{ old('position', $user->position) }}" placeholder="Ej: Supervisor de Operaciones" />
                @if($errors->get('position'))
                    <p class="text-xs text-rose-500 font-bold ml-2 italic">{{ $errors->first('position') }}</p>
                @endif
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-6 bg-amber-50 dark:bg-amber-900/10 rounded-3xl border border-amber-100 dark:border-amber-800">
                <p class="text-xs font-bold text-amber-700 dark:text-amber-500 flex items-center gap-2 mb-3">
                    <i class="ph-fill ph-warning-circle text-lg"></i>
                    Tu dirección de correo no ha sido verificada.
                </p>

                <button form="send-verification" class="text-[10px] font-black uppercase tracking-widest bg-amber-600 text-white px-4 py-2 rounded-xl hover:bg-amber-700 transition-colors">
                    Reenviar enlace de verificación
                </button>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-3 text-[10px] font-black text-emerald-600 uppercase tracking-widest animate-pulse">
                        Se ha enviado un nuevo enlace a tu correo.
                    </p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-6 pt-4">
            <button type="submit" class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-10 py-5 rounded-[20px] text-[11px] font-black uppercase tracking-[0.2em] hover:bg-indigo-600 dark:hover:bg-indigo-50 hover:text-white dark:hover:text-indigo-600 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3 group">
                Actualizar Perfil
                <i class="ph-bold ph-floppy-disk text-lg group-hover:translate-x-1 transition-transform"></i>
            </button>

            @if (session('status') === 'profile-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" 
                    class="flex items-center gap-2 text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-2 rounded-full border border-emerald-100 dark:border-emerald-800">
                    <i class="ph-bold ph-check text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Cambios guardados</span>
                </div>
            @endif
        </div>
    </form>
</section>
