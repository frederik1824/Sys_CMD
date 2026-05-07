@extends('layouts.portal')

@section('content')

<div class="relative min-h-screen overflow-hidden bg-slate-50/50" x-data="{ search: '', filterStatus: 'all' }">
    
    <!-- Background Mesh Gradients (Aesthetic Layer) -->
    <div class="absolute top-0 left-0 w-full h-full -z-10 opacity-30">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-400 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-300 rounded-full blur-[120px]"></div>
    </div>

    <div class="p-6 lg:p-12 max-w-[1700px] mx-auto relative z-10">
        
        <!-- Top Navigation & Profile -->
        <nav class="flex items-center justify-between mb-16 animate-in fade-in duration-700">
            <div class="flex items-center gap-5">
                <div class="h-14 flex items-center">
                    <img src="{{ asset('img/Logo.png') }}" alt="Logo CMD" class="h-full w-auto drop-shadow-xl">
                </div>
                <div class="w-px h-8 bg-slate-200"></div>
                <div>
                    <h2 class="text-[10px] font-black text-blue-600 uppercase tracking-[0.4em] leading-none mb-1">Ecosistema CMD</h2>
                    <p class="text-xs font-bold text-slate-400 tracking-tight">Consola de Operaciones</p>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden md:flex flex-col items-end">
                    <span class="text-xs font-black text-slate-900 leading-none mb-1">{{ auth()->user()->name }}</span>
                    <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest">{{ auth()->user()->departamento->nombre ?? 'Sin Depto' }}</span>
                </div>
                <div class="w-12 h-12 rounded-2xl border-2 border-white shadow-xl overflow-hidden ring-4 ring-slate-100/50">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0f172a&color=fff" class="w-full h-full object-cover">
                </div>
            </div>
        </nav>

        <!-- Main Launcher Header -->
        <div class="max-w-4xl mb-20 animate-in fade-in slide-in-from-left-8 duration-1000">
            <h1 class="text-6xl lg:text-8xl font-[900] tracking-tight text-slate-900 leading-[1] mb-8">
                Centro de <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-700 via-indigo-600 to-blue-500">Aplicaciones</span>
            </h1>
            
            <!-- Unified Command Bar -->
            <div class="relative group mt-10">
                <div class="absolute inset-0 bg-blue-500 rounded-[32px] blur-2xl opacity-0 group-focus-within:opacity-10 transition-opacity"></div>
                <div class="relative flex items-center bg-white/90 backdrop-blur-2xl border border-slate-200 rounded-[32px] shadow-2xl shadow-slate-200/50 p-2.5 transition-all focus-within:ring-8 focus-within:ring-blue-500/5">
                    <div class="pl-6 pr-4 text-slate-400">
                        <i class="ph-bold ph-magnifying-glass text-2xl"></i>
                    </div>
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="Busca por nombre de aplicación, módulo o función..." 
                        class="flex-1 bg-transparent border-none focus:ring-0 font-bold text-lg text-slate-700 placeholder:text-slate-300 py-4"
                    >
                    <div class="hidden md:flex items-center gap-3 pr-8">
                        <kbd class="px-3 py-1.5 bg-slate-100 rounded-xl text-[10px] font-black text-slate-400 uppercase tracking-widest shadow-inner border border-slate-200">Búsqueda Inteligente</kbd>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Catálogo Completo -->
        <div class="mb-24">
            <div class="flex items-center justify-between mb-10">
                <div class="flex items-center gap-4">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Catálogo de Herramientas</h3>
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                </div>
                
                <div class="flex items-center gap-2 p-1 bg-white rounded-2xl border border-slate-200 shadow-sm">
                    <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-dark text-white' : 'text-slate-400 hover:text-slate-900'" class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Todos</button>
                    <button @click="filterStatus = 'active'" :class="filterStatus === 'active' ? 'bg-dark text-white' : 'text-slate-400 hover:text-slate-900'" class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Activos</button>
                    <button @click="filterStatus = 'development'" :class="filterStatus === 'development' ? 'bg-dark text-white' : 'text-slate-400 hover:text-slate-900'" class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all">Beta</button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($modules as $key => $module)
                    <div 
                        x-show="(search === '' || '{{ strtolower($module['name']) }}'.includes(search.toLowerCase())) && (filterStatus === 'all' || filterStatus === '{{ $module['status'] }}')"
                        class="group"
                    >
                        <div class="h-full bg-white rounded-[48px] p-10 border border-slate-100 shadow-2xl shadow-slate-200/40 hover:shadow-blue-500/10 hover:-translate-y-4 transition-all duration-700 flex flex-col relative overflow-hidden {{ !$module['has_access'] ? 'opacity-50 grayscale' : '' }}">
                            
                            <!-- Dynamic App Indicator -->
                            <div class="absolute top-10 right-10 w-2 h-2 rounded-full bg-{{ $module['color'] }}-500 shadow-[0_0_12px] shadow-{{ $module['color'] }}-500/50"></div>
                            
                            <div class="relative z-10 flex flex-col h-full">
                                <div class="w-20 h-20 rounded-[32px] bg-{{ $module['color'] }}-50 border border-{{ $module['color'] }}-100/50 flex items-center justify-center text-{{ $module['color'] }}-600 mb-10 group-hover:rotate-12 transition-transform duration-500 shadow-inner">
                                    <i class="{{ $module['icon'] ?: 'ph-duotone ph-app-window' }} text-4xl"></i>
                                </div>

                                <div class="mb-10">
                                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-4 leading-none">{{ $module['name'] }}</h3>
                                    <p class="text-slate-400 text-sm font-bold leading-relaxed pr-4">{{ $module['description'] }}</p>
                                </div>

                                <div class="mt-auto">
                                    @if($module['has_access'] && $module['status'] === 'active')
                                        <a href="{{ $module['url'] }}" class="inline-flex items-center gap-4 text-{{ $module['color'] }}-600 font-black uppercase text-xs tracking-widest group-hover:gap-6 transition-all">
                                            Abrir Aplicación <i class="ph-bold ph-arrow-right text-lg"></i>
                                        </a>
                                    @else
                                        <div class="flex items-center gap-3 text-slate-300">
                                            <i class="ph-bold ph-lock-key text-lg"></i>
                                            <span class="text-[10px] font-black uppercase tracking-widest">{{ $module['status'] === 'development' ? 'En Laboratorio' : 'No Disponible' }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Global System Status Bar -->
        <footer class="fixed bottom-8 left-1/2 -translate-x-1/2 w-fit bg-slate-900/90 backdrop-blur-xl px-8 py-4 rounded-[28px] shadow-2xl border border-white/10 flex items-center gap-10 animate-in slide-in-from-bottom-full duration-1000">
            <div class="flex items-center gap-3">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[10px] font-black text-white uppercase tracking-widest">Sincronización Real-Time Activa</span>
            </div>
            <div class="w-px h-4 bg-white/10"></div>
            <div class="flex items-center gap-3">
                <i class="ph-bold ph-cpu text-slate-400"></i>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">CPU: Óptimo</span>
            </div>
            <div class="w-px h-4 bg-white/10"></div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-blue-400 uppercase tracking-widest">Version 2.4.0</span>
            </div>
        </footer>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
    
    body { font-family: 'Space Grotesk', 'Inter', sans-serif; overflow-x: hidden; }
    [x-cloak] { display: none !important; }
    
    .bg-blue-50 { background-color: #eff6ff; } .text-blue-600 { color: #2563eb; } .border-blue-100 { border-color: #dbeafe; } .shadow-blue-500\/50 { --tw-shadow-color: rgba(59, 130, 246, 0.5); }
    .bg-indigo-50 { background-color: #eef2ff; } .text-indigo-600 { color: #4f46e5; } .border-indigo-100 { border-color: #e0e7ff; } .shadow-indigo-500\/50 { --tw-shadow-color: rgba(79, 70, 229, 0.5); }
    .bg-emerald-50 { background-color: #ecfdf5; } .text-emerald-600 { color: #059669; } .border-emerald-100 { border-color: #d1fae5; } .shadow-emerald-500\/50 { --tw-shadow-color: rgba(16, 185, 129, 0.5); }
    .bg-amber-50 { background-color: #fffbeb; } .text-amber-600 { color: #d97706; } .border-amber-100 { border-color: #fef3c7; } .shadow-amber-500\/50 { --tw-shadow-color: rgba(217, 119, 6, 0.5); }
    .bg-violet-50 { background-color: #f5f3ff; } .text-violet-600 { color: #7c3aed; } .border-violet-100 { border-color: #ede9fe; } .shadow-violet-500\/50 { --tw-shadow-color: rgba(124, 58, 237, 0.5); }
    .bg-rose-50 { background-color: #fff1f2; } .text-rose-600 { color: #e11d48; } .border-rose-100 { border-color: #ffe4e6; } .shadow-rose-500\/50 { --tw-shadow-color: rgba(225, 29, 72, 0.5); }
</style>
@endsection
