<section class="space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <h4 class="text-[11px] font-black text-rose-600 uppercase tracking-[0.2em] mb-1">Eliminación Definitiva</h4>
            <p class="text-xs text-slate-500 font-medium max-w-md">Una vez eliminada la cuenta, todos sus recursos y datos históricos se perderán permanentemente de SysCarnet.</p>
        </div>
        <button 
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-8 py-4 bg-rose-100 hover:bg-rose-600 text-rose-600 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm border border-rose-200">
            Eliminar Mi Cuenta
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-10 bg-white dark:bg-slate-900 rounded-[3rem]">
            @csrf
            @method('delete')

            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center">
                    <i class="ph-bold ph-warning text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white tracking-tight">¿Confirmar Eliminación?</h2>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Esta acción es irreversible</p>
                </div>
            </div>

            <p class="text-sm text-slate-500 leading-relaxed mb-8">
                Por favor, ingresa tu contraseña actual para verificar que eres el propietario de la cuenta y proceder con la eliminación total de tus datos.
            </p>

            <div class="space-y-3">
                <label for="password" class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] ml-2 block">Contraseña de Seguridad</label>
                <input id="password" name="password" type="password" 
                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-2 border-transparent focus:border-rose-500 focus:bg-white dark:focus:bg-slate-800 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 dark:text-white transition-all shadow-sm"
                    placeholder="Contraseña actual..." />
                @if($errors->userDeletion->get('password'))
                    <p class="text-xs text-rose-500 font-bold ml-2 italic">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="mt-10 flex items-center justify-end gap-4">
                <button type="button" x-on:click="$dispatch('close')" 
                    class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-colors">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="px-8 py-4 bg-rose-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-700 transition-all shadow-xl shadow-rose-900/20">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
