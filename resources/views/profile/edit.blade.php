@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f8fafc] dark:bg-[#0f172a] pb-20 pt-8 px-4 md:px-8 animate-in fade-in duration-700" x-data="{ tab: 'general' }">
    
    <div class="max-w-[1400px] mx-auto">
        <!-- BREADCRUMBS & CONTEXT -->
        <div class="flex items-center gap-3 mb-8 ml-4">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Configuración</span>
            <span class="material-symbols-outlined text-slate-300 text-sm">chevron_right</span>
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-600">Perfil de Usuario</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- SIDEBAR: IDENTITY & NAVIGATION -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- CARD IDENTITY -->
                <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800 shadow-2xl shadow-indigo-900/5 p-10 relative overflow-hidden group">
                    <!-- Background Glow -->
                    <div class="absolute -right-20 -top-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl group-hover:bg-indigo-500/10 transition-colors duration-1000"></div>
                    
                    <div class="relative flex flex-col items-center text-center">
                        <div class="relative group/avatar mb-8">
                            <div class="absolute -inset-4 bg-gradient-to-tr from-indigo-500 to-purple-500 rounded-full blur opacity-10 group-hover/avatar:opacity-20 transition duration-700"></div>
                            <div class="relative w-32 h-32 rounded-full border-4 border-white dark:border-slate-800 shadow-xl overflow-hidden bg-slate-50">
                                <img src="{{ $user->avatar_url }}" class="w-full h-full object-cover transition-transform duration-700 group-hover/avatar:scale-110" alt="Avatar" id="avatar-preview-header">
                                <label for="avatar-input" class="absolute inset-0 bg-indigo-900/40 backdrop-blur-[2px] flex items-center justify-center opacity-0 group-hover/avatar:opacity-100 transition-opacity cursor-pointer">
                                    <i class="ph ph-camera text-white text-2xl"></i>
                                </label>
                            </div>
                            <div class="absolute bottom-1 right-1 w-8 h-8 bg-emerald-500 border-2 border-white dark:border-slate-800 rounded-full flex items-center justify-center text-white shadow-lg">
                                <i class="ph-fill ph-check-circle text-sm"></i>
                            </div>
                        </div>

                        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none mb-2">{{ $user->name }}</h2>
                        <p class="text-slate-400 font-bold text-[10px] uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                            <i class="ph-fill ph-shield-check text-indigo-500 text-xs"></i>
                            {{ $user->getRoleNames()->first() ?? 'Usuario' }}
                        </p>

                        <!-- QUICK STATS VERTICAL -->
                        <div class="w-full space-y-3 mt-4">
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 flex items-center justify-center">
                                        <i class="ph ph-trend-up text-sm"></i>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Efectividad</span>
                                </div>
                                <span class="text-sm font-black text-slate-900 dark:text-white">{{ $stats['efectividad'] }}%</span>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                                        <i class="ph ph-check-square text-sm"></i>
                                    </div>
                                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Logros Hoy</span>
                                </div>
                                <span class="text-sm font-black text-slate-900 dark:text-white">{{ $stats['entregados_hoy'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- NAVIGATION MENU -->
                <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-xl shadow-slate-900/5 p-4 space-y-2">
                    <button @click="tab = 'general'" :class="tab === 'general' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-600'" 
                        class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 group">
                        <i class="ph ph-user-circle text-xl transition-transform group-hover:scale-110"></i>
                        <span class="text-[11px] font-black uppercase tracking-widest">Información General</span>
                        <div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600" x-show="tab === 'general'"></div>
                    </button>
                    
                    <button @click="tab = 'seguridad'" :class="tab === 'seguridad' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-600'" 
                        class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 group">
                        <i class="ph ph-lock-key text-xl transition-transform group-hover:scale-110"></i>
                        <span class="text-[11px] font-black uppercase tracking-widest">Seguridad y Acceso</span>
                        <div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600" x-show="tab === 'seguridad'"></div>
                    </button>

                    <button @click="tab = 'actividad'" :class="tab === 'actividad' ? 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600' : 'text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-600'" 
                        class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 group">
                        <i class="ph ph-graph text-xl transition-transform group-hover:scale-110"></i>
                        <span class="text-[11px] font-black uppercase tracking-widest">Actividad y Sesiones</span>
                        <div class="ml-auto w-1.5 h-1.5 rounded-full bg-indigo-600" x-show="tab === 'actividad'"></div>
                    </button>
                </div>

                <!-- QUICK INFO BOX -->
                <div class="p-8 bg-indigo-600 rounded-[2rem] text-white relative overflow-hidden shadow-xl shadow-indigo-200">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="relative">
                        <p class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2">Estado del Sistema</p>
                        <p class="text-sm font-bold leading-relaxed mb-4">Tu cuenta está protegida y sincronizada con el ecosistema central.</p>
                        <div class="flex items-center gap-2 text-[9px] font-black uppercase bg-white/10 w-fit px-3 py-1 rounded-full border border-white/10">
                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></div>
                            Online
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT AREA -->
            <div class="lg:col-span-8">
                
                <div class="bg-white dark:bg-slate-900 rounded-[3rem] border border-slate-100 dark:border-slate-800 shadow-2xl shadow-indigo-900/5 min-h-[700px] relative overflow-hidden">
                    <!-- Subtle Header Decoration -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                    
                    <div class="p-8 md:p-14">
                        
                        <!-- TAB CONTENT: GENERAL -->
                        <div x-show="tab === 'general'" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" class="space-y-12">
                            <div class="flex items-center justify-between border-b border-slate-50 dark:border-slate-800 pb-8">
                                <div>
                                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1">Información Personal</h3>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Detalles de contacto y visualización pública</p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 flex items-center justify-center">
                                    <i class="ph-bold ph-identification-card text-xl"></i>
                                </div>
                            </div>
                            
                            <div class="animate-in fade-in slide-in-from-bottom-4 duration-700">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>

                        <!-- TAB CONTENT: SEGURIDAD -->
                        <div x-show="tab === 'seguridad'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" class="space-y-12">
                            <div class="flex items-center justify-between border-b border-slate-50 dark:border-slate-800 pb-8">
                                <div>
                                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1">Centro de Seguridad</h3>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Protección de acceso y llaves digitales</p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-indigo-600 text-white flex items-center justify-center shadow-lg shadow-indigo-100">
                                    <i class="ph-bold ph-shield-check text-xl"></i>
                                </div>
                            </div>

                            <div class="space-y-16">
                                @include('profile.partials.update-password-form')
                                
                                <div class="bg-rose-50/30 dark:bg-rose-500/5 rounded-3xl border border-rose-100 dark:border-rose-900/30 p-8">
                                    <div class="flex items-center gap-4 mb-6 text-rose-600">
                                        <i class="ph-bold ph-warning-octagon text-2xl"></i>
                                        <h4 class="text-sm font-black uppercase tracking-widest">Zona de Peligro</h4>
                                    </div>
                                    @include('profile.partials.delete-user-form')
                                </div>
                            </div>
                        </div>

                        <!-- TAB CONTENT: ACTIVIDAD -->
                        <div x-show="tab === 'actividad'" x-cloak x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8" class="space-y-12">
                            <div class="flex items-center justify-between border-b border-slate-50 dark:border-slate-800 pb-8">
                                <div>
                                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight mb-1">Auditoría y Permisos</h3>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Historial de accesos y facultades del sistema</p>
                                </div>
                                <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-lg shadow-emerald-100">
                                    <i class="ph-bold ph-key text-xl"></i>
                                </div>
                            </div>

                            <div class="space-y-8">
                                @include('profile.partials.security-information')

                                <!-- Audit Trail Card -->
                                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-[2rem] border border-slate-100 dark:border-slate-800 p-8">
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Últimas Interacciones</h4>
                                    <div class="space-y-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                                <div>
                                                    <p class="text-xs font-black text-slate-800 dark:text-white">Inicio de Sesión Exitoso</p>
                                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ now()->format('d M Y, h:i A') }}</p>
                                                </div>
                                            </div>
                                            <span class="text-[9px] font-black text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1 rounded-full border border-indigo-100 dark:border-indigo-800">192.168.1.1</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer Info -->
        <div class="mt-12 text-center opacity-30">
            <p class="text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.5em]">SysCarnet Identity Management &bull; v3.0 &bull; 2026</p>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Custom Input Styles Override */
    input:focus {
        border-color: #4f46e5 !important; /* Indigo 600 */
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.05) !important;
    }
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
