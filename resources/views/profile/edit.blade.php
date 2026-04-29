@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/50 pb-20 animate-in fade-in slide-in-from-bottom-4 duration-1000">
    <div class="max-w-4xl mx-auto px-6 pt-12">
        
        <!-- Header de Identidad -->
            <div class="relative group mb-6">
                <div class="absolute -inset-2 bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-[2.5rem] blur opacity-20 group-hover:opacity-40 transition duration-700"></div>
                <div class="relative w-40 h-40 rounded-[2.2rem] bg-white p-1.5 shadow-2xl overflow-hidden transform group-hover:scale-[1.03] transition-transform duration-500">
                    <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover rounded-[1.8rem]" alt="Avatar" id="avatar-preview-header">
                    <label for="avatar-input" class="absolute inset-0 bg-blue-900/60 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer backdrop-blur-sm">
                        <i class="ph ph-camera text-3xl mb-2"></i>
                        <span class="text-[0.6rem] font-black uppercase tracking-widest text-center px-4">Cambiar Foto</span>
                    </label>
                </div>
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-blue-600 border-4 border-white rounded-full flex items-center justify-center text-white shadow-lg">
                    <i class="ph-fill ph-check-circle text-xl"></i>
                </div>
            </div>
            
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
            <div class="flex items-center gap-3 mt-2">
                <p class="text-slate-500 font-bold uppercase tracking-[0.3em] text-[0.7rem] bg-slate-100 px-4 py-1.5 rounded-full inline-block">
                    {{ $user->getRoleNames()->first() ?? 'Usuario del Sistema' }}
                </p>
                @if($user->responsable_id)
                <span class="flex items-center gap-1 text-[0.65rem] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 px-3 py-1.5 rounded-full border border-emerald-100">
                    <span class="material-symbols-outlined text-[14px]">stars</span> Operativo Activo
                </span>
                @endif
            </div>

            <!-- Gamification Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">local_shipping</span>
                    </div>
                    <div>
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Entregados Hoy</p>
                        <p class="text-xl font-black text-slate-800">{{ $stats['entregados_hoy'] }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">pending_actions</span>
                    </div>
                    <div>
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Pendientes</p>
                        <p class="text-xl font-black text-slate-800">{{ $stats['pendientes'] }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl">bolt</span>
                    </div>
                    <div>
                        <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Efectividad</p>
                        <p class="text-xl font-black text-slate-800">{{ $stats['efectividad'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cohesive Profile Container -->
        <div class="bg-white rounded-[3rem] shadow-2xl shadow-blue-900/5 border border-slate-100 overflow-hidden" x-data="{ tab: 'general' }">
            
            <!-- Horizontal Premium Tabs -->
            <div class="flex items-center justify-center border-b border-slate-50 px-8 bg-slate-50/30">
                <button @click="tab = 'general'" :class="tab === 'general' ? 'text-blue-600 border-blue-600' : 'text-slate-400 border-transparent hover:text-slate-600'" class="px-8 py-6 font-black text-[0.7rem] uppercase tracking-widest border-b-2 transition-all flex items-center gap-2">
                    <i class="ph ph-user-circle text-lg"></i> General
                </button>
                <button @click="tab = 'seguridad'" :class="tab === 'seguridad' ? 'text-blue-600 border-blue-600' : 'text-slate-400 border-transparent hover:text-slate-600'" class="px-8 py-6 font-black text-[0.7rem] uppercase tracking-widest border-b-2 transition-all flex items-center gap-2">
                    <i class="ph ph-lock-key text-lg"></i> Seguridad
                </button>
                <button @click="tab = 'actividad'" :class="tab === 'actividad' ? 'text-blue-600 border-blue-600' : 'text-slate-400 border-transparent hover:text-slate-600'" class="px-8 py-6 font-black text-[0.7rem] uppercase tracking-widest border-b-2 transition-all flex items-center gap-2">
                    <i class="ph ph-clock-counter-clockwise text-lg"></i> Accesos
                </button>
            </div>

            <div class="p-12">
                <!-- Tab: General -->
                <div x-show="tab === 'general'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4">
                    <div class="mb-10">
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Información de Cuenta</h2>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Gestión de identidad y contacto</p>
                    </div>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <!-- Tab: Seguridad -->
                <div x-show="tab === 'seguridad'" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4">
                    <div class="mb-10">
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Llaves de Acceso</h2>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Cambio de contraseña y protección</p>
                    </div>
                    @include('profile.partials.update-password-form')
                    
                    <div class="mt-16 pt-12 border-t border-rose-50">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                                <i class="ph ph-warning text-xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-rose-900 tracking-tight">Acciones Críticas</h3>
                        </div>
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

                <!-- Tab: Actividad -->
                <div x-show="tab === 'actividad'" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4">
                    <div class="mb-10">
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Roles y Permisos</h2>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Capacidades asignadas en el sistema</p>
                    </div>
                    @include('profile.partials.security-information')

                    <div class="mt-12 p-8 bg-blue-50/50 rounded-[2rem] border border-blue-100/50">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-[0.65rem] font-black text-blue-600 uppercase tracking-widest mb-1">Última Sesión</p>
                                <p class="text-sm font-bold text-slate-700">{{ now()->format('d M Y, h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[0.65rem] font-black text-blue-600 uppercase tracking-widest mb-1">IP de Acceso</p>
                                <p class="text-sm font-bold text-slate-700">192.168.1.1</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="mt-8 text-center">
            <p class="text-[0.6rem] font-black text-slate-300 uppercase tracking-[0.4em]">SysCarnet Identity Management &bull; v2.0</p>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>

<script>
    function syncAvatarPreview(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview-header').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection


