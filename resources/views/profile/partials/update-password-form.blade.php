<section class="max-w-3xl">
    <form method="post" action="{{ route('password.update') }}" class="space-y-10">
        @csrf
        @method('put')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
            <!-- Current Password -->
            <div class="space-y-3">
                <label for="update_password_current_password" class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] ml-2 block">Contraseña Actual</label>
                <div class="relative group">
                    <input id="update_password_current_password" name="current_password" type="password" 
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 dark:text-white transition-all shadow-sm"
                        autocomplete="current-password" />
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-300">
                        <i class="ph-bold ph-key"></i>
                    </div>
                </div>
                @if($errors->updatePassword->get('current_password'))
                    <p class="text-xs text-rose-500 font-bold ml-2 italic">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <!-- New Password -->
            <div class="space-y-3">
                <label for="update_password_password" class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] ml-2 block">Nueva Contraseña</label>
                <div class="relative group">
                    <input id="update_password_password" name="password" type="password" 
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 dark:text-white transition-all shadow-sm"
                        autocomplete="new-password" />
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-300">
                        <i class="ph-bold ph-lock-key"></i>
                    </div>
                </div>
                @if($errors->updatePassword->get('password'))
                    <p class="text-xs text-rose-500 font-bold ml-2 italic">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div class="space-y-3">
                <label for="update_password_password_confirmation" class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] ml-2 block">Confirmar Contraseña</label>
                <div class="relative group">
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                        class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-indigo-500 focus:bg-white dark:focus:bg-slate-800 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 dark:text-white transition-all shadow-sm"
                        autocomplete="new-password" />
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 text-slate-300">
                        <i class="ph-bold ph-circle-dashed"></i>
                    </div>
                </div>
                @if($errors->updatePassword->get('password_confirmation'))
                    <p class="text-xs text-rose-500 font-bold ml-2 italic">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-6 pt-4">
            <button type="submit" class="bg-indigo-600 text-white px-10 py-5 rounded-[20px] text-[11px] font-black uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-900/10 flex items-center gap-3 group">
                Cambiar Contraseña
                <i class="ph-bold ph-shield-check text-lg group-hover:scale-110 transition-transform"></i>
            </button>

            @if (session('status') === 'password-updated')
                <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" 
                    class="flex items-center gap-2 text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-2 rounded-full border border-emerald-100 dark:border-emerald-800">
                    <i class="ph-bold ph-check text-sm"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Contraseña actualizada</span>
                </div>
            @endif
        </div>
    </form>
</section>
