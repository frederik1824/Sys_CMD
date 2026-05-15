@extends('layouts.portal')

@section('content')

<div class="relative min-h-screen overflow-hidden bg-[#fafbfc]" x-data="{ search: '', filterStatus: 'all' }">
    
    <!-- Background Mesh Layer (The Aura) -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-[20%] -left-[10%] w-[60%] h-[60%] bg-[#00346f]/5 rounded-full blur-[140px] animate-pulse"></div>
        <div class="absolute top-[30%] -right-[10%] w-[50%] h-[50%] bg-[#0060ac]/5 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-[10%] left-[20%] w-[40%] h-[40%] bg-[#00346f]/5 rounded-full blur-[140px]"></div>
    </div>

    <div class="p-6 md:p-12 lg:p-20 max-w-[1800px] mx-auto relative z-10">
        
        <!-- Header: The Command Unit -->
        <header class="flex flex-col md:flex-row items-center justify-between gap-10 mb-24 animate-in fade-in duration-1000">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="relative">
                    <img src="{{ asset('img/Logo.png') }}" alt="Logo CMD" class="h-16 w-auto drop-shadow-[0_10px_20px_rgba(0,52,111,0.2)]">
                </div>
                <div class="hidden md:block h-12 w-px bg-slate-200/60"></div>
                <div class="text-center md:text-left">
                    <div class="flex items-center gap-3 justify-center md:justify-start">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#00346f] animate-ping"></span>
                        <h2 class="text-[11px] font-black text-[#00346f] uppercase tracking-[0.5em] leading-none mb-1">Nexus OS v{{ $systemVersion }}</h2>
                    </div>
                    <p class="text-sm font-bold text-slate-400">Plataforma Unificada de Operaciones</p>
                </div>
            </div>

            <div class="flex items-center gap-6 bg-white/60 backdrop-blur-xl p-2 pr-6 rounded-[32px] border border-slate-100 shadow-xl shadow-slate-200/20 group hover:border-[#00346f]/20 transition-all duration-500">
                <div class="w-14 h-14 rounded-[24px] border-4 border-white shadow-2xl overflow-hidden group-hover:scale-105 transition-transform">
                    <img src="{{ auth()->user()->avatar_url }}" class="w-full h-full object-cover">
                </div>
                <div>
                    <span class="block text-sm font-black text-slate-900 leading-none mb-1 group-hover:text-[#00346f] transition-colors">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ auth()->user()->departamento->nombre ?? 'Admin Unit' }}</span>
                </div>
            </div>
        </header>

        <!-- Search & Discovery -->
        <div class="max-w-5xl mx-auto mb-24 text-center">
            <h1 class="text-6xl md:text-8xl font-black text-slate-900 tracking-tightest mb-12 animate-in slide-in-from-bottom-8 duration-1000">
                Explore sus <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00346f] via-[#0060ac] to-[#00346f] animate-gradient-x">Herramientas</span>
            </h1>

            <div class="relative group max-w-3xl mx-auto">
                <div class="absolute -inset-4 bg-gradient-to-r from-[#00346f]/10 to-[#0060ac]/10 rounded-[40px] blur-3xl opacity-0 group-focus-within:opacity-100 transition-all duration-1000"></div>
                <div class="relative flex items-center bg-white border border-slate-100 rounded-[32px] shadow-[0_30px_60px_-15px_rgba(0,0,0,0.05)] p-2 transition-all duration-500 group-focus-within:shadow-[0_40px_80px_-20px_rgba(0,52,111,0.1)] group-focus-within:border-[#00346f]/10">
                    <div class="pl-8 pr-4 text-slate-300">
                        <i class="ph-bold ph-magnifying-glass text-2xl"></i>
                    </div>
                    <input type="text" x-model="search" placeholder="Busque cualquier módulo o función..." 
                           class="flex-1 bg-transparent border-none focus:ring-0 font-bold text-xl text-slate-800 placeholder:text-slate-300 py-6">
                    <div class="hidden sm:flex items-center gap-3 pr-6">
                        <span class="px-4 py-2 bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest rounded-xl border border-slate-100">Búsqueda Global</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter System -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-16 gap-8 animate-in fade-in duration-1000">
            <div class="flex items-center gap-4">
                <div class="w-10 h-[2px] bg-[#00346f]"></div>
                <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-[0.5em]">Catálogo Operativo</h3>
            </div>
            
            <div class="flex items-center gap-2 p-1.5 bg-white/50 backdrop-blur-xl rounded-[24px] border border-slate-100 shadow-sm">
                <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-[#00346f] text-white shadow-xl shadow-[#00346f]/20' : 'text-slate-400 hover:text-slate-900'" class="px-8 py-3 rounded-[20px] text-[10px] font-black uppercase tracking-widest transition-all duration-500">Universal</button>
                <button @click="filterStatus = 'active'" :class="filterStatus === 'active' ? 'bg-[#00346f] text-white shadow-xl shadow-[#00346f]/20' : 'text-slate-400 hover:text-slate-900'" class="px-8 py-3 rounded-[20px] text-[10px] font-black uppercase tracking-widest transition-all duration-500">Activos</button>
                <button @click="filterStatus = 'development'" :class="filterStatus === 'development' ? 'bg-[#00346f] text-white shadow-xl shadow-[#00346f]/20' : 'text-slate-400 hover:text-slate-900'" class="px-8 py-3 rounded-[20px] text-[10px] font-black uppercase tracking-widest transition-all duration-500">Experimental</button>
            </div>
        </div>

        <!-- App Grid: The Neural Web -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
            
            @foreach($modules as $key => $module)
                <div x-show="(search === '' || '{{ strtolower($module['name']) }}'.includes(search.toLowerCase())) && (filterStatus === 'all' || filterStatus === '{{ $module['status'] }}')"
                     class="group animate-in zoom-in-95 duration-700" style="animation-delay: {{ $loop->index * 50 }}ms">
                    
                    <div class="h-full bg-white rounded-[56px] p-12 border border-slate-100 shadow-[0_20px_50px_rgba(0,0,0,0.02)] hover:shadow-[0_40px_80px_rgba(0,52,111,0.08)] hover:-translate-y-4 transition-all duration-700 flex flex-col relative overflow-hidden {{ !$module['has_access'] ? 'opacity-40 grayscale' : '' }}">
                        
                        <!-- Hover Aura Gradient -->
                        <div class="absolute -inset-2 bg-gradient-to-br from-[#00346f]/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                        
                        <div class="relative z-10 flex flex-col h-full">
                            <!-- Icon Shell -->
                            <div class="w-24 h-24 rounded-[36px] bg-slate-50 border border-slate-100 flex items-center justify-center mb-12 group-hover:rotate-6 group-hover:scale-110 transition-all duration-500 shadow-inner overflow-hidden relative">
                                <div class="absolute inset-0 bg-{{ $module['color'] }}-500 opacity-0 group-hover:opacity-10 transition-opacity"></div>
                                <i class="{{ $module['icon'] ?: 'ph-duotone ph-app-window' }} text-5xl text-{{ $module['color'] }}-600 transition-colors"></i>
                            </div>

                            <div class="mb-12">
                                <div class="flex items-center gap-3 mb-4">
                                    <h3 class="text-3xl font-black text-slate-900 tracking-tight leading-none group-hover:text-[#00346f] transition-colors">{{ $module['name'] }}</h3>
                                </div>
                                <p class="text-slate-400 text-sm font-bold leading-relaxed pr-2">{{ $module['description'] }}</p>
                            </div>

                            <div class="mt-auto pt-8 border-t border-slate-50 flex items-center justify-between">
                                @if($module['has_access'] && $module['status'] === 'active')
                                    <a href="{{ $module['url'] }}" class="flex items-center gap-4 text-[#00346f] font-black uppercase text-[10px] tracking-[0.2em] group-hover:gap-6 transition-all">
                                        Explorar <i class="ph-bold ph-arrow-right text-lg"></i>
                                    </a>
                                @else
                                    <div class="flex items-center gap-3 text-slate-300">
                                        <i class="ph-bold ph-lock-key text-lg"></i>
                                        <span class="text-[9px] font-black uppercase tracking-widest">{{ $module['status'] === 'development' ? 'Laboratorio' : 'Bloqueado' }}</span>
                                    </div>
                                @endif
                                
                                <div class="w-1.5 h-1.5 rounded-full bg-{{ $module['color'] }}-500 group-hover:scale-150 transition-transform"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- High Fidelity Module: Enlace Call Center -->
            <div x-show="(search === '' || 'autorizaciones tickets enlace call center'.includes(search.toLowerCase())) && (filterStatus === 'all' || filterStatus === 'active')" class="group animate-in zoom-in-95 duration-1000">
                <div class="h-full bg-[#00346f] rounded-[56px] p-12 shadow-[0_30px_70px_rgba(0,52,111,0.3)] hover:shadow-[0_40px_100px_rgba(0,52,111,0.5)] hover:-translate-y-4 transition-all duration-700 flex flex-col relative overflow-hidden group">
                    <!-- Glass Shine Effect -->
                    <div class="absolute -top-[50%] -left-[50%] w-[200%] h-[200%] bg-gradient-to-br from-white/10 to-transparent rotate-45 pointer-events-none group-hover:-translate-x-1/4 transition-transform duration-1000"></div>
                    
                    <div class="relative z-10 flex flex-col h-full">
                        <div class="w-24 h-24 rounded-[36px] bg-white/10 border border-white/20 flex items-center justify-center text-white mb-12 group-hover:scale-110 transition-transform duration-500 shadow-inner">
                            <i class="ph-bold ph-link text-5xl"></i>
                        </div>
                        <div class="mb-12">
                            <h3 class="text-3xl font-black text-white tracking-tight mb-4 leading-none">Enlace Directo</h3>
                            <p class="text-blue-200 text-sm font-bold leading-relaxed">Canal interdepartamental para derivación y monitoreo de carnetización.</p>
                        </div>
                        <div class="mt-auto pt-8 border-t border-white/10">
                            <a href="{{ route('autorizaciones.ticket.create') }}" class="flex items-center gap-4 text-white font-black uppercase text-[10px] tracking-[0.2em] group-hover:gap-6 transition-all">
                                Abrir Terminal <i class="ph-bold ph-arrow-right text-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Control Dock (Footer) -->
        <footer class="fixed bottom-12 left-1/2 -translate-x-1/2 w-fit bg-slate-900/95 backdrop-blur-3xl px-12 py-5 rounded-[40px] shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] border border-white/10 flex items-center gap-12 animate-in slide-in-from-bottom-full duration-1000 hover:scale-105 transition-transform z-[100]">
            <div class="flex items-center gap-4">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-[0_0_12px_rgba(52,211,153,0.8)]"></div>
                <span class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Red Activa</span>
            </div>
            <div class="w-px h-6 bg-white/10"></div>
            <div class="flex items-center gap-4">
                <i class="ph-bold ph-cloud-check text-blue-400 text-lg"></i>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Sync Cloud: 0.2ms</span>
            </div>
            <div class="w-px h-6 bg-white/10"></div>
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-black text-[#0060ac] uppercase tracking-[0.2em]">System v{{ $systemVersion }}</span>
            </div>
        </footer>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@300;500;800&display=swap');
    
    body { font-family: 'Manrope', sans-serif; }
    .tracking-tightest { letter-spacing: -0.06em; }
    
    .animate-gradient-x {
        background-size: 200% 200%;
        animation: gradient-x 15s ease infinite;
    }
    
    @keyframes gradient-x {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    /* Color Utilities for Dynamic Icons */
    .text-blue-600 { color: #00346f; } .bg-blue-500 { background-color: #00346f; }
    .text-indigo-600 { color: #4f46e5; } .bg-indigo-500 { background-color: #4f46e5; }
    .text-emerald-600 { color: #059669; } .bg-emerald-500 { background-color: #059669; }
    .text-amber-600 { color: #d97706; } .bg-amber-500 { background-color: #d97706; }
    .text-violet-600 { color: #7c3aed; } .bg-violet-500 { background-color: #7c3aed; }
    .text-rose-600 { color: #e11d48; } .bg-rose-500 { background-color: #e11d48; }
    .text-slate-600 { color: #475569; } .bg-slate-500 { background-color: #475569; }
</style>
@endsection
