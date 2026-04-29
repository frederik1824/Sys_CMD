@extends('layouts.portal')

@section('content')
@php
    $iconMap = [
        'badge' => 'ph-duotone ph-identification-badge',
        'person_add' => 'ph-duotone ph-user-plus',
        'groups' => 'ph-duotone ph-users-four',
        'swap_horiz' => 'ph-duotone ph-arrows-left-right',
        'folder_open' => 'ph-duotone ph-folder-open',
        'query_stats' => 'ph-duotone ph-chart-pie-slice',
        'admin_panel_settings' => 'ph-duotone ph-shield-checkered',
    ];
@endphp

<div class="p-6 lg:p-8 max-w-[1440px] mx-auto" x-data="{ search: '', filterStatus: 'all' }">
    
    <!-- Institutional Header & HUD -->
    <header class="flex flex-col gap-8 mb-10 animate-in fade-in slide-in-from-top duration-700">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
                <h1 class="text-3xl font-[800] tracking-tight text-slate-900 mb-1">
                    Centro de <span class="text-primary">Comando</span>
                </h1>
                <p class="text-slate-400 font-bold flex items-center gap-3">
                    <span class="px-2 py-0.5 bg-primary/10 text-primary rounded-md text-[9px] font-black uppercase tracking-wider">
                        {{ auth()->user()->departamento->nombre ?? 'Corporativo' }}
                    </span>
                    <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                    <span class="text-xs">{{ now()->isoFormat('D MMMM, YYYY') }}</span>
                </p>
            </div>

            <!-- Global Search -->
            <div class="relative group w-full md:w-80">
                <i class="ph-bold ph-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-all"></i>
                <input 
                    type="text" 
                    x-model="search"
                    placeholder="Buscar módulo..." 
                    class="w-full pl-12 pr-6 py-3.5 bg-white border border-slate-200 rounded-2xl shadow-sm focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-bold text-xs"
                >
            </div>
        </div>

        <!-- Operational HUD -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                    <i class="ph-duotone ph-files text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Solicitudes</p>
                    <h4 class="text-xl font-black text-slate-900 leading-none">{{ $stats['total_solicitudes'] }}</h4>
                </div>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex items-center gap-4 border-l-4 border-l-rose-500">
                <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center text-rose-500">
                    <i class="ph-duotone ph-warning-octagon text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-rose-500 uppercase tracking-widest leading-none mb-1">Urgentes</p>
                    <h4 class="text-xl font-black text-rose-600 leading-none">{{ $stats['urgentes'] }}</h4>
                </div>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-500">
                    <i class="ph-duotone ph-users-three text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest leading-none mb-1">Departamento</p>
                    <h4 class="text-xl font-black text-indigo-600 leading-none">{{ $stats['pendientes_depto'] }}</h4>
                </div>
            </div>
            <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500">
                    <i class="ph-duotone ph-trend-up text-xl"></i>
                </div>
                <div>
                    <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest leading-none mb-1">Eficiencia</p>
                    <h4 class="text-xl font-black text-emerald-600 leading-none">{{ $stats['eficiencia'] }}%</h4>
                </div>
            </div>
        </div>
    </header>

    <!-- Filters -->
    <div class="flex items-center gap-2 mb-8">
        <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-dark text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-100'" class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border border-slate-100">Todos</button>
        <button @click="filterStatus = 'active'" :class="filterStatus === 'active' ? 'bg-primary text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-100'" class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border border-slate-100">Producción</button>
        <button @click="filterStatus = 'development'" :class="filterStatus === 'development' ? 'bg-amber-500 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-100'" class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all border border-slate-100">Lab</button>
    </div>

    <!-- Bento Apps Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($modules as $key => $module)
            <div 
                x-show="(search === '' || '{{ strtolower($module['name']) }}'.includes(search.toLowerCase())) && (filterStatus === 'all' || filterStatus === '{{ $module['status'] }}')"
                class="group animate-in fade-in zoom-in duration-500"
            >
                <div class="h-full bg-white rounded-[32px] p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 flex flex-col relative overflow-hidden {{ !$module['has_access'] ? 'opacity-60 grayscale-[0.5]' : '' }}">
                    
                    <!-- App Branding -->
                    <div class="flex items-start justify-between mb-5">
                        <div class="w-12 h-12 rounded-2xl bg-{{ $module['color'] }}-50 flex items-center justify-center text-{{ $module['color'] }}-600 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300 shadow-sm">
                            <i class="{{ $iconMap[$module['icon']] ?? 'ph-duotone ph-app-window' }} text-2xl"></i>
                        </div>
                        @if($module['status'] !== 'active')
                            <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-tighter shadow-sm
                                {{ $module['status'] === 'development' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $module['status'] === 'development' ? 'Lab' : 'Pronto' }}
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg font-black text-slate-800 tracking-tight mb-2 leading-none">{{ $module['name'] }}</h3>
                    <p class="text-slate-400 text-[11px] font-bold leading-snug mb-6 flex-grow">{{ $module['description'] }}</p>

                    <div class="mt-auto">
                        @if(!$module['has_access'])
                            <div class="w-full py-3.5 bg-slate-50 text-slate-400 rounded-2xl text-[9px] font-black uppercase tracking-widest flex items-center justify-center gap-2 border border-slate-100">
                                <i class="ph-bold ph-lock text-xs"></i> Restringido
                            </div>
                        @elseif($module['status'] !== 'active')
                            <div class="w-full py-3.5 bg-slate-50 text-slate-300 rounded-2xl text-[9px] font-black uppercase tracking-widest flex items-center justify-center">
                                En Desarrollo
                            </div>
                        @else
                            <a href="{{ $module['url'] }}" class="w-full py-3.5 bg-primary text-white rounded-2xl text-[10px] font-black uppercase tracking-widest flex items-center justify-center gap-2 shadow-md shadow-primary/20 hover:bg-dark transition-all">
                                Abrir <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Subtle Ambient Glow -->
                    <div class="absolute -bottom-10 -right-10 w-24 h-24 bg-{{ $module['color'] }}-50/40 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Empty Search -->
    <div x-show="document.querySelectorAll('.grid > div[style*=\'display: block\']').length === 0 && search !== ''" x-cloak class="py-24 text-center">
        <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
            <i class="ph-duotone ph-magnifying-glass text-3xl"></i>
        </div>
        <h4 class="text-xl font-black text-slate-800">Sin resultados</h4>
        <p class="text-slate-400 text-sm font-bold mt-2">Prueba con otros términos de búsqueda.</p>
    </div>
</div>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    
    .bg-blue-50 { background-color: #eff6ff; } .text-blue-600 { color: #2563eb; }
    .bg-indigo-50 { background-color: #eef2ff; } .text-indigo-600 { color: #4f46e5; }
    .bg-emerald-50 { background-color: #ecfdf5; } .text-emerald-600 { color: #059669; }
    .bg-amber-50 { background-color: #fffbeb; } .text-amber-600 { color: #d97706; }
    .bg-violet-50 { background-color: #f5f3ff; } .text-violet-600 { color: #7c3aed; }
    .bg-rose-50 { background-color: #fff1f2; } .text-rose-600 { color: #e11d48; }
</style>
@endsection
