<section>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- El input del avatar está vinculado al header por ID -->
        <input type="file" id="avatar-input" name="avatar" class="hidden" onchange="syncAvatarPreview(this)">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div class="space-y-1.5">
                <x-input-label for="name" class="text-[0.65rem] font-black uppercase text-slate-400 tracking-widest ml-1" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-slate-50/50 border-slate-200 h-12 px-5 rounded-2xl focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-slate-700" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div class="space-y-1.5">
                <x-input-label for="email" class="text-[0.65rem] font-black uppercase text-slate-400 tracking-widest ml-1" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-slate-50/50 border-slate-200 h-12 px-5 rounded-2xl focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-slate-700" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div class="space-y-1.5">
                <x-input-label for="phone" class="text-[0.65rem] font-black uppercase text-slate-400 tracking-widest ml-1" value="Teléfono" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-slate-50/50 border-slate-200 h-12 px-5 rounded-2xl focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-slate-700" :value="old('phone', $user->phone)" autocomplete="tel" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>

            <div class="space-y-1.5">
                <x-input-label for="position" class="text-[0.65rem] font-black uppercase text-slate-400 tracking-widest ml-1" value="Cargo / Posición" />
                <x-text-input id="position" name="position" type="text" class="mt-1 block w-full bg-slate-50/50 border-slate-200 h-12 px-5 rounded-2xl focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-slate-700" :value="old('position', $user->position)" placeholder="Ej: Operador de Carnetización" />
                <x-input-error class="mt-2" :messages="$errors->get('position')" />
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <!-- ... Verification section omitted for brevity or kept if short ... -->
             <div>
                <p class="text-sm mt-2 text-gray-800">
                    {{ __('Your email address is unverified.') }}

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </p>
                @endif
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
