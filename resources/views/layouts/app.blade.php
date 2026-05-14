<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'ARS CMD Dashboard') }}</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Manrope:wght@600;700;800&amp;display=swap" rel="stylesheet"/>
    <!-- Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @livewireStyles
    
    <!-- Tom Select CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            border: none !important;
            padding: 12px 16px !important;
            border-radius: 12px !important;
            background-color: #f3f4f5 !important; /* surface-container-low */
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            color: #191c1d !important;
            box-shadow: none !important;
        }
        .ts-wrapper.focus .ts-control {
            ring: 2px solid #00346f !important; /* primary */
        }
        .ts-dropdown {
            border-radius: 16px !important;
            margin-top: 8px !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
            border: 1px solid #f3f4f5 !important;
            padding: 8px !important;
        }
        .ts-dropdown .active {
            background-color: #00346f !important;
            color: #ffffff !important;
            border-radius: 8px !important;
        }
    </style>
    
    <!-- Alpine.js is included automatically by Livewire 3 -->
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Tailwind CSS (CDN for prototype, should be compiled via Vite in prod) -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-container": "#ffa77e",
                        "on-surface": "#191c1d",
                        "surface": "#f8f9fa",
                        "primary": "#00346f",
                        "secondary": "#0060ac",
                        "surface-container-low": "#f3f4f5",
                        "error": "#ba1a1a",
                        "surface-container-high": "#e7e8e9",
                        "outline-variant": "#c2c6d3",
                        "on-surface-variant": "#424751",
                        // Kinetic Command Design System
                        "kinetic-bg": "#10141a",
                        "kinetic-surface": "#181c22",
                        "kinetic-card": "#1c2026",
                        "kinetic-accent": "#00daf3",
                        "kinetic-primary": "#c3f5ff",
                        // Crystal Lux (Light Mode Premium)
                        "crystal-bg": "#f0f4f8",
                        "crystal-surface": "#ffffff",
                        "crystal-card": "rgba(255, 255, 255, 0.7)",
                        "crystal-accent": "#0060ac",
                        "on-crystal": "#1e293b",
                    },
                    fontFamily: {
                        "headline": ["Space Grotesk", "Manrope", "sans-serif"],
                        "body": ["Inter", "sans-serif"],
                        "label": ["Inter", "sans-serif"],
                        "mono": ["JetBrains Mono", "monospace"]
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');
        
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9; /* Slate-100 for better contrast with cards */
            color: #1e293b; /* Slate-800 */
        }
        h1, h2, h3, .font-headline {
            font-family: 'Space Grotesk', 'Manrope', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* --- Transitions & Animations (Saneadas) --- */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .page-transition {
            animation: fadeIn 0.3s ease-out forwards;
        }

        /* Sombras simplificadas para evitar fatiga */
        .shadow-sm { box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); }
        .shadow-md { box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05); }
        .shadow-xl { box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.03), 0 4px 6px -4px rgb(0 0 0 / 0.03); }

        .hover-card {
            transition: all 0.2s ease;
        }
        .hover-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.05);
        }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
    <body class="bg-surface text-on-surface" 
          data-success="{{ session('success') }}" 
          data-error="{{ session('error') ?? ($errors->any() ? 'Existen errores de validación en el formulario.' : '') }}">

    <!-- Impersonation Floating Hub -->
    @if(session()->has('impersonate_original_id'))
    <div class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] animate-in slide-in-from-bottom duration-700 no-print">
        <div class="bg-slate-900/90 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-3 pl-6 pr-4 shadow-2xl flex items-center gap-8 shadow-slate-900/40 min-w-[500px]">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-600/30">
                        <i class="ph-fill ph-user-focus text-2xl"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 w-4 h-4 bg-emerald-500 rounded-full border-4 border-slate-900 animate-pulse"></div>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 leading-none mb-1">Sesión de Soporte Activa</p>
                    <p class="text-sm font-black text-white leading-none">Viendo como: <span class="text-blue-400">{{ auth()->user()->name }}</span></p>
                </div>
            </div>
            
            <div class="h-10 w-px bg-white/10"></div>
            
            <form action="{{ route('admin.access.impersonate.stop') }}" method="POST">
                @csrf
                <button type="submit" class="group flex items-center gap-3 px-6 py-3 bg-white text-slate-900 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-rose-600 hover:text-white transition-all shadow-xl active:scale-95">
                    <i class="ph-bold ph-arrow-left text-lg group-hover:-translate-x-1 transition-transform"></i>
                    Cerrar y Volver
                </button>
            </form>
        </div>
    </div>
    @endif

    @php
        $currentApp = $appContext->getCurrentApp();
        $appMeta = $appContext->getAppMeta();
        
        // Context flags for legacy support in existing view logic
        $isTraspasos = $currentApp === 'traspasos';
        $isAfiliacion = $currentApp === 'afiliacion';
        $isExecutive = $currentApp === 'executive';
        $isAccessControl = $currentApp === 'access_control';
        $isCallCenter = $currentApp === 'call_center';
        $isUpdateManager = $currentApp === 'update_manager';
        $isDispersion = $currentApp === 'dispersion';
        $isPyp = $currentApp === 'pyp';
        $isAsistencia = $currentApp === 'asistencia';
        $isCMD = $currentApp === 'cmd';
    @endphp

    <!-- SideNavBar Component -->
    @unless(request()->routeIs('profile.edit') || request()->routeIs('autorizaciones.*'))
    <aside class="no-print h-screen w-72 fixed left-0 top-0 border-r border-slate-200 bg-white flex flex-col py-8 z-50">
        @php /** @var \App\Models\User $user */ $user = Auth::user(); @endphp
        <div class="px-8 mb-10 w-full">
            <div class="flex items-center gap-4">
                <div class="h-10 flex items-center">
                    <img src="{{ asset('img/Logo.png') }}" alt="Logo CMD" class="h-full w-auto">
                </div>
                <div class="w-px h-6 bg-slate-200"></div>
                <div>
                    <h1 class="text-sm font-black tracking-tight text-slate-900 leading-none">
                        {{ $appMeta['name'] }}
                    </h1>
                    <p class="text-[0.6rem] font-bold uppercase tracking-wider text-{{ $appMeta['color'] }}-600 mt-0.5">
                        {{ $appMeta['type'] }}
                    </p>
                </div>
            </div>
        </div>        <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar" x-data="{ 
            activeGroup: '{{ 
                request()->routeIs('carnetizacion.import.*', 'carnetizacion.afiliados.cmd', 'carnetizacion.afiliados.otros', 'carnetizacion.afiliados.salida_inmediata', 'carnetizacion.afiliados.call_center') ? 'admision' : (
                request()->routeIs('afiliacion.*') ? 'afiliacion' : (
                request()->routeIs('call-center.*') ? 'callcenter' : (
                request()->routeIs('carnetizacion.afiliados.index', 'lotes.*', 'cierre.*', 'mensajeros.*', 'rutas.*', 'despachos.*') ? 'logistica' : (
                request()->routeIs('evidencias.*', 'liquidacion.*') ? 'gestion' : (
                request()->routeIs('reportes.*') ? 'reportes' : (
                request()->routeIs('pyp.*') ? 'pyp' : (
                request()->routeIs('sistema.*', 'intranet.catalogo.index') ? 'sistema' : ''
            ))))))) }}' 
        }">
            <!-- Portal Return Button (SANEADO) -->
            <div class="px-2 mb-6">
                <a href="{{ route('portal') }}" class="flex items-center gap-3 px-5 py-3 bg-slate-800 text-white rounded-xl hover:bg-slate-900 transition-all group overflow-hidden relative">
                    <i class="ph ph-circles-four text-lg text-slate-400 group-hover:text-blue-400 transition-colors"></i>
                    <div class="flex flex-col">
                        <span class="text-[0.65rem] font-bold uppercase tracking-wider text-white leading-none">Centro de Aplicaciones</span>
                    </div>
                </a>
            </div>

            @includeFirst([
                "layouts.navigation.$currentApp",
                "layouts.navigation.cmd"
            ])
        </nav>

        <div class="px-6 mt-auto pt-6 border-t border-slate-100">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl hover:bg-slate-100 border border-slate-100 transition-colors group relative overflow-hidden">
                <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm relative z-10" alt="Avatar">
                <div class="overflow-hidden relative z-10 flex-1">
                    <p class="text-[0.75rem] font-black text-slate-800 truncate leading-none mb-1">{{ $user->name }}</p>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[0.55rem] font-black uppercase px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded-md tracking-tighter">
                            {{ $user->getRoleNames()->first() ?? 'User' }}
                        </span>
                        @if($user->departamento)
                        <span class="text-[0.55rem] font-black uppercase px-1.5 py-0.5 bg-slate-200 text-slate-600 rounded-md tracking-tighter truncate max-w-[80px]">
                            {{ $user->departamento->codigo }}
                        </span>
                        @endif
                    </div>
                </div>
                <i class="ph ph-caret-right text-slate-400 text-sm ml-auto group-hover:translate-x-1 transition-transform relative z-10"></i>
            </a>
        </div>
    </aside>
    @endunless

    <!-- Main Canvas -->
    <main class="{{ request()->routeIs('profile.edit') || request()->routeIs('autorizaciones.*') ? 'ml-0' : 'ml-72' }} print:ml-0 min-h-screen">
        <!-- TopNavBar Component -->
        @if(($isCMD || $isAfiliacion || $isPyp || $isTraspasos || $isCallCenter || $isDispersion || $isAsistencia) && !request()->routeIs('profile.edit') && !request()->routeIs('autorizaciones.*'))
        @php
            $hasActiveShift = false;
            if(auth()->check()) {
                $empleado = \App\Models\Asistencia\Empleado::where('user_id', auth()->id())->first();
                if($empleado) {
                    $registroHoy = \App\Models\Asistencia\Registro::where('empleado_id', $empleado->id)->where('fecha', now()->toDateString())->first();
                    $hasActiveShift = $registroHoy && $registroHoy->hora_entrada && !$registroHoy->hora_salida;
                    
                    // Verificar si ya pasó su hora de salida
                    $salidaEsperada = \Carbon\Carbon::parse(now()->toDateString() . ' ' . $empleado->turno->salida_esperada);
                    $isOvertime = now()->greaterThan($salidaEsperada);
                }
            }
        @endphp
        <header class="no-print sticky top-0 z-40 bg-white/60 backdrop-blur-2xl h-20 px-10 flex justify-between items-center border-b border-slate-200/50 shadow-[0_4px_30px_rgba(0,0,0,0.03)] transition-all duration-500">
            <div class="flex items-center gap-10">
                <!-- Smart Search Command -->
                <div class="relative w-96 group">
                    <div class="absolute inset-0 bg-blue-500/5 rounded-2xl blur-xl opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                    <i class="ph-bold ph-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-blue-600 transition-colors"></i>
                    <input id="navbar-search" class="w-full bg-slate-50/50 border border-slate-200/60 rounded-2xl pl-14 pr-16 py-3.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/50 focus:bg-white outline-none transition-all placeholder:text-slate-300 shadow-sm" placeholder="Comando de búsqueda..." type="text" autocomplete="off"/>
                    <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-1.5 px-2.5 py-1.5 bg-white rounded-xl border border-slate-200 shadow-sm pointer-events-none opacity-40 group-hover:opacity-100 transition-opacity">
                        <span class="text-[9px] font-black text-slate-500">CTRL</span>
                        <span class="text-[9px] font-black text-slate-500 text-blue-600">K</span>
                    </div>
                    
                    @if($hasActiveShift && $isOvertime)
                    <div class="flex items-center gap-3 px-5 py-2.5 bg-rose-50 border border-rose-100 rounded-2xl animate-pulse shadow-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-600"></span>
                        </span>
                        <span class="text-[10px] font-black text-rose-700 uppercase tracking-[0.1em]">Atención: No has marcado salida</span>
                    </div>
                    @endif
                </div>
                    
                    <!-- Dropdown de Resultados (Crystal Lux) -->
                    <div id="search-results" class="absolute top-full left-0 right-0 mt-4 bg-white/90 backdrop-blur-3xl rounded-[2.5rem] shadow-[0_30px_100px_rgba(0,0,0,0.2)] border border-white/20 overflow-hidden hidden animate-in fade-in zoom-in-95 duration-500 z-[100]">
                        <div class="p-6 border-b border-slate-100/50 bg-slate-50/30 flex justify-between items-center">
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-600">OmniSearch Intelligence</span>
                            <div class="flex gap-1">
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-300 animate-pulse delay-75"></div>
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-100 animate-pulse delay-150"></div>
                            </div>
                        </div>
                        <div id="results-container" class="max-h-[500px] overflow-y-auto custom-scrollbar divide-y divide-slate-50">
                            <!-- Inyectado por JS con estilo enriquecido -->
                        </div>
                    </div>
                </div>

                    <!-- Strategic Navigation Contextual -->
                    @if($isCMD)
                        @php
                            $moduleDashboard = route('carnetizacion.dashboard');
                            $isAtDashboard = request()->routeIs('carnetizacion.dashboard');
                        @endphp

                        <a class="{{ $isAtDashboard ? 'text-blue-600 font-black relative after:content-[\'\'] after:absolute after:-bottom-[26px] after:left-0 after:w-full after:h-1 after:bg-blue-600 after:rounded-full' : 'text-slate-400 font-bold hover:text-slate-900 transition-colors' }} text-[11px] uppercase tracking-widest flex items-center gap-2 py-6" href="{{ $moduleDashboard }}">
                            <i class="ph ph-chart-pie-slice text-lg"></i> Dashboard
                        </a>

                        @can('manage_affiliates')
                        <a class="{{ request()->routeIs('carnetizacion.afiliados.cmd') ? 'text-blue-600 font-black relative after:content-[\'\'] after:absolute after:-bottom-[26px] after:left-0 after:w-full after:h-1 after:bg-blue-600 after:rounded-full' : 'text-slate-400 font-bold hover:text-slate-900 transition-colors' }} text-[11px] uppercase tracking-widest py-6" href="{{ route('carnetizacion.afiliados.cmd') }}">Afiliados</a>
                        @endcan
                        @can('manage_companies')
                        <a class="{{ request()->routeIs('sistema.empresas.*') ? 'text-blue-600 font-black relative after:content-[\'\'] after:absolute after:-bottom-[26px] after:left-0 after:w-full after:h-1 after:bg-blue-600 after:rounded-full' : 'text-slate-400 font-bold hover:text-slate-900 transition-colors' }} text-[11px] uppercase tracking-widest py-6" href="{{ route('sistema.empresas.index') }}">Empresas</a>
                        @endcan
                    @elseif($isTraspasos)
                        @php
                            $moduleDashboard = route('traspasos.dashboard');
                            $isAtDashboard = request()->routeIs('traspasos.dashboard');
                        @endphp
                        <a class="{{ $isAtDashboard ? 'text-amber-600 font-black relative after:content-[\'\'] after:absolute after:-bottom-[26px] after:left-0 after:w-full after:h-1 after:bg-amber-600 after:rounded-full' : 'text-slate-400 font-bold hover:text-slate-900 transition-colors' }} text-[11px] uppercase tracking-widest flex items-center gap-2 py-6" href="{{ $moduleDashboard }}">
                            <i class="ph ph-lightning text-lg"></i> Dashboard Traspasos
                        </a>
                        <a class="{{ request()->routeIs('traspasos.index') ? 'text-amber-600 font-black relative after:content-[\'\'] after:absolute after:-bottom-[26px] after:left-0 after:w-full after:h-1 after:bg-amber-600 after:rounded-full' : 'text-slate-400 font-bold hover:text-slate-900 transition-colors' }} text-[11px] uppercase tracking-widest py-6" href="{{ route('traspasos.index') }}">Bandeja</a>
                    @elseif($isCallCenter)
                        <a class="{{ request()->routeIs('call-center.worklist') ? 'text-emerald-600 font-black relative after:content-[\'\'] after:absolute after:-bottom-[26px] after:left-0 after:w-full after:h-1 after:bg-emerald-600 after:rounded-full' : 'text-slate-400 font-bold hover:text-slate-900 transition-colors' }} text-[11px] uppercase tracking-widest flex items-center gap-2 py-6" href="{{ route('call-center.worklist') }}">
                            <i class="ph ph-headset text-lg"></i> Mi Consola
                        </a>
                    @elseif($isAfiliacion)
                         <a class="{{ request()->routeIs('afiliacion.index') ? 'text-indigo-600 font-black relative after:content-[\'\'] after:absolute after:-bottom-[26px] after:left-0 after:w-full after:h-1 after:bg-indigo-600 after:rounded-full' : 'text-slate-400 font-bold hover:text-slate-900 transition-colors' }} text-[11px] uppercase tracking-widest flex items-center gap-2 py-6" href="{{ route('afiliacion.index') }}">
                            <i class="ph ph-list-checks text-lg"></i> Bandeja Afiliación
                        </a>
                    @endif
                </nav>
            </div>
            
            <div class="flex items-center gap-6">
                @livewire('call-center.follow-up-reminder')
                
                <!-- Monitor de Procesos (Admin Only) -->
                @hasanyrole('Admin')
                <div id="queue-monitor" class="relative hidden" x-data="{ open: false, imports: [] }">
                    <button @click="open = !open" class="flex items-center gap-3 px-4 py-2.5 bg-blue-50/50 rounded-2xl border border-blue-100/50 hover:bg-blue-100 transition-all group">
                        <div class="relative">
                            <i class="ph-fill ph-cpu text-blue-600 animate-spin-slow text-lg"></i>
                            <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                        </div>
                        <span id="queue-count" class="text-[10px] font-black text-blue-700 uppercase tracking-widest">Tareas en curso</span>
                    </button>
                    <!-- Popover de Procesos -->
                    <div x-show="open" @click.outside="open = false" x-transition class="absolute right-0 mt-4 w-80 bg-white rounded-[2rem] shadow-2xl border border-slate-100 z-50 overflow-hidden">
                        <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                            <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Monitor de Infraestructura</h3>
                            <i class="ph ph-activity text-blue-500"></i>
                        </div>
                        <div id="queue-details-list" class="p-6 space-y-5 max-h-72 overflow-y-auto custom-scrollbar">
                            <p class="text-[10px] text-slate-400 text-center font-bold italic py-4">Actualizando procesos...</p>
                        </div>
                    </div>
                </div>
                @endhasanyrole

                <!-- Notification Engine -->
                <div class="relative" x-data='{ 
                    open: false,
                    markRead() {
                        if ({{ auth()->user()->unreadNotifications->count() }} > 0) {
                            fetch("{{ route('notifications.markAllAsRead') }}", {
                                method: "POST",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                    "Accept": "application/json",
                                    "X-Requested-With": "XMLHttpRequest"
                                }
                            });
                        }
                    }
                }'>
                    <button @click="open = !open; if(open) markRead()" class="w-12 h-12 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50/50 rounded-2xl border border-transparent hover:border-blue-100/50 transition-all relative group">
                        <i class="ph ph-bell text-2xl group-hover:scale-110 transition-transform"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-2.5 right-2.5 w-5 h-5 bg-rose-500 rounded-lg border-2 border-white text-[9px] text-white flex items-center justify-center font-black shadow-lg shadow-rose-200 animate-bounce">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown (Crystal Redesign) -->
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-4 w-[400px] bg-white rounded-[2.5rem] shadow-[0_30px_100px_rgba(0,0,0,0.15)] border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <div class="p-8 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-1">Notificaciones</h3>
                                <p class="text-[10px] font-bold text-slate-400">Recibidas recientemente</p>
                            </div>
                            <span class="px-3 py-1.5 bg-blue-600 text-white text-[9px] font-black rounded-xl shadow-lg shadow-blue-200">HISTORIAL</span>
                        </div>
                        <div class="max-h-[450px] overflow-y-auto custom-scrollbar divide-y divide-slate-50">
                            @forelse(auth()->user()->notifications->take(15) as $notification)
                                <div class="p-6 hover:bg-slate-50/80 transition-all flex gap-5 group/notif {{ $notification->read_at ? 'opacity-50' : '' }}">
                                    <div class="w-12 h-12 rounded-[1.2rem] bg-{{ $notification->data['icon_color'] ?? 'blue' }}-50 flex items-center justify-center shrink-0 group-hover/notif:scale-110 transition-transform">
                                        <i class="ph-fill ph-{{ $notification->data['icon'] ?? 'bell' }} text-2xl text-{{ $notification->data['icon_color'] ?? 'blue' }}-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="text-xs font-black text-slate-900 truncate pr-4">{{ $notification->data['title'] ?? 'Notificación' }}</p>
                                            <span class="text-[9px] font-black text-slate-300 whitespace-nowrap">{{ $notification->created_at->diffForHumans(null, true) }}</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-20 text-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-inner">
                                        <i class="ph ph-bell-slash text-4xl text-slate-200"></i>
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em]">Bandeja Despejada</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-6 bg-white border-t border-slate-50">
                            <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                @csrf
                                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-slate-200 flex items-center justify-center gap-3">
                                    <i class="ph ph-check-circle text-lg"></i> Marcar todo como leído
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="h-10 w-px bg-slate-200/60 mx-2"></div>
                
                <!-- Advanced Profile Launcher -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-4 p-1.5 pr-6 bg-white hover:bg-slate-50 border border-slate-200/60 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 active:scale-95 group">
                        <div class="relative">
                            <img src="{{ $user->avatar_url }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-md" alt="User">
                            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-xs font-black text-slate-900 leading-tight">{{ $user->name }}</span>
                            <span class="text-[9px] font-black text-blue-600 uppercase tracking-widest">{{ $user->getRoleNames()->first() ?? 'Usuario' }}</span>
                        </div>
                        <i class="ph ph-caret-down text-xs text-slate-300 group-hover:text-slate-900 transition-colors ml-2"></i>
                    </button>

                    <!-- Profile Dropdown (Crystal Style) -->
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-4 w-64 bg-white rounded-[2.5rem] shadow-[0_30px_100px_rgba(0,0,0,0.15)] border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 italic">Sesión de Usuario</p>
                            <p class="text-xs font-black text-slate-800 truncate">{{ $user->email }}</p>
                        </div>
                        <div class="p-3">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 p-4 text-[11px] font-black text-slate-600 uppercase tracking-widest hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-all group/item">
                                <i class="ph ph-user-circle text-2xl group-hover/item:scale-110 transition-transform"></i> Perfil Personal
                            </a>
                            <div class="h-px bg-slate-50 my-2"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-4 p-4 text-[11px] font-black text-rose-500 uppercase tracking-widest hover:bg-rose-50 rounded-2xl transition-all group/item">
                                    <i class="ph ph-power text-2xl group-hover/item:scale-110 transition-transform"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @elseif($isUpdateManager)
        <header class="no-print sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 px-8 flex justify-between items-center shadow-sm border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 px-4 py-2 bg-slate-900 text-white rounded-2xl shadow-lg">
                    <i class="ph-duotone ph-rocket-launch text-xl text-blue-400"></i>
                    <span class="text-xs font-black uppercase tracking-widest">Release Manager & On-Premise Suite</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right mr-4 hidden md:block">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Entorno Local</p>
                    <p class="text-[11px] font-bold text-blue-600 flex items-center justify-end gap-1">
                        <i class="ph ph-shield-check"></i>
                        Infraestructura Protegida
                    </p>
                </div>
                
                <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                
                @php $user = Auth::user(); @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 p-1 pr-4 bg-white hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-full shadow-sm hover:shadow transition-all duration-200">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="User">
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-[0.75rem] font-black text-slate-800 leading-tight">{{ $user->name }}</span>
                            <span class="text-[0.6rem] font-bold text-blue-600 uppercase tracking-tighter">{{ $user->getRoleNames()->first() ?? 'Usuario' }}</span>
                        </div>
                        <i class="ph ph-caret-down text-[10px] text-slate-400 ml-1"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-3 w-56 bg-white rounded-[32px] shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <div class="p-2">
                            <a href="{{ route('portal') }}" class="flex items-center gap-3 p-4 text-[0.75rem] font-bold text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-colors">
                                <i class="ph ph-circles-four text-lg"></i> Volver al Portal
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 p-4 text-[0.75rem] font-black text-rose-500 hover:bg-rose-50 rounded-2xl transition-colors">
                                    <i class="ph ph-power text-lg"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @elseif($isDispersion)
        <header class="no-print sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 px-8 flex justify-between items-center shadow-sm border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 px-4 py-2 bg-slate-900 text-white rounded-2xl shadow-lg">
                    <i class="ph-duotone ph-chart-pie-slice text-xl text-slate-400"></i>
                    <span class="text-xs font-black uppercase tracking-widest">Control de Dispersión & Auditoría</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right mr-4 hidden md:block">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Entorno Administrativo</p>
                    <p class="text-[11px] font-bold text-slate-600">Gestión de Periodos</p>
                </div>
                @php $user = Auth::user(); @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 p-1 pr-4 bg-white rounded-full border border-slate-100 shadow-sm transition-all">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                        <span class="text-[0.75rem] font-black text-slate-800">{{ $user->name }}</span>
                        <i class="ph ph-caret-down text-[10px] text-slate-400 ml-1"></i>
                    </button>
                    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-3 w-56 bg-white rounded-[32px] shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <div class="p-2 text-left">
                            <a href="{{ route('portal') }}" class="flex items-center gap-3 p-4 text-[0.75rem] font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-2xl transition-colors">
                                <i class="ph ph-circles-four text-lg"></i> Volver al Portal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @elseif($isAccessControl)
        <header class="no-print sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 px-8 flex justify-between items-center shadow-sm border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3 px-4 py-2 bg-slate-900 text-white rounded-2xl shadow-lg">
                    <i class="ph-duotone ph-shield-checkered text-xl text-blue-400"></i>
                    <span class="text-xs font-black uppercase tracking-widest">Consola de Seguridad Centralizada</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right mr-4 hidden md:block">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Estado de la Matriz</p>
                    <p class="text-[11px] font-bold text-emerald-600 flex items-center justify-end gap-1">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        Protección Activa
                    </p>
                </div>
                
                <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                
                <!-- User Profile (Same as CMD for consistency) -->
                @php $user = Auth::user(); @endphp
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 p-1 pr-4 bg-white hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-full shadow-sm hover:shadow transition-all duration-200">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="User">
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-[0.75rem] font-black text-slate-800 leading-tight">{{ $user->name }}</span>
                            <span class="text-[0.6rem] font-bold text-blue-600 uppercase tracking-tighter">{{ $user->getRoleNames()->first() ?? 'Usuario' }}</span>
                        </div>
                        <i class="ph ph-caret-down text-[10px] text-slate-400 ml-1"></i>
                    </button>
                    <!-- User Dropdown (Simplified for Admin) -->
                    <div x-show="open" @click.outside="open = false" class="absolute right-0 mt-3 w-56 bg-white rounded-[32px] shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <div class="p-2">
                            <a href="{{ route('portal') }}" class="flex items-center gap-3 p-4 text-[0.75rem] font-bold text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-colors">
                                <i class="ph ph-circles-four text-lg"></i> Volver al Portal
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 p-4 text-[0.75rem] font-black text-rose-500 hover:bg-rose-50 rounded-2xl transition-colors">
                                    <i class="ph ph-power text-lg"></i> Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @else
        <!-- TopNavBar REMOVED for non-CMD apps -->
        @endif

        <!-- Page Header (Optional Slot) -->
        @if (isset($header))
            <div class="px-8 py-6 bg-white border-b border-slate-100 mb-6">
                {{ $header }}
            </div>
        @endif

        <!-- Page Content -->
        <div class="p-10 max-w-[1600px] mx-auto space-y-10">
            {{ $slot ?? '' }}
            @yield('content')
        </div>
    </main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global Toast Configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        const CloudToast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true,
            background: 'rgba(255, 255, 255, 0.95)',
            backdrop: 'blur(8px)',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Global Confirmation Interceptor for Forms
        function confirmActionForm(event, title = '¿Estás seguro?', text = 'Esta acción no se puede deshacer.') {
            event.preventDefault();
            const form = event.target;
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#004a99',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }

        document.addEventListener("DOMContentLoaded", () => {
            const successMsg = document.body.dataset.success;
            const errorMsg = document.body.dataset.error;
            const cloudSuccess = "{{ session('cloud_success') }}";

            if (cloudSuccess) {
                CloudToast.fire({
                    html: `
                        <div class="flex items-center gap-4 text-left">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-700 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                                <span class="material-symbols-outlined text-2xl animate-pulse">cloud_done</span>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-600 mb-0.5">Sincronización Exitosa</p>
                                <p class="text-xs font-bold text-slate-800 leading-tight">${cloudSuccess}</p>
                            </div>
                        </div>
                    `
                });
            } else if (successMsg) {
                Toast.fire({ icon: 'success', title: successMsg });
            }

            if (errorMsg) {
                Toast.fire({ icon: 'error', title: errorMsg });
            }

            // --- Smart Search Logic ---
            const searchInput = document.getElementById('navbar-search');
            const searchResults = document.getElementById('search-results');
            const resultsContainer = document.getElementById('results-container');
            const searchEmpty = document.getElementById('search-empty');
            let searchTimeout;

            if (searchInput && searchResults) {
                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.trim();
                    clearTimeout(searchTimeout);

                    if (query.length < 3) {
                        searchResults.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        const quickActions = [
                            @if($isTraspasos)
                                { nombre: 'Importar Unipago', url: '{{ route("traspasos.import") }}', icon: 'upload_file', keywords: ['importar', 'unipago', 'excel'] },
                                { nombre: 'Bandeja Traspasos', url: '{{ route("traspasos.index") }}', icon: 'list', keywords: ['bandeja', 'traspasos', 'solicitudes'] },
                                { nombre: 'Producción Traspasos', url: '{{ route("reportes.produccion_traspasos") }}', icon: 'monitoring', keywords: ['reporte', 'produccion', 'traspasos'] },
                            @elseif($isAfiliacion)
                                { nombre: 'Nueva Solicitud', url: '{{ route("afiliacion.create") }}', icon: 'add_circle', keywords: ['nueva', 'afiliacion', 'solicitud'] },
                                { nombre: 'Bandeja Afiliación', url: '{{ route("afiliacion.index") }}', icon: 'list_alt', keywords: ['bandeja', 'afiliacion'] },
                            @else
                                { nombre: 'Nueva Empresa', url: '{{ route("sistema.empresas.create") }}', icon: 'add_business', keywords: ['nueva', 'crear', 'empresa'] },
                                { nombre: 'Importar Excel', url: '{{ route("carnetizacion.import.index") }}', icon: 'upload_file', keywords: ['importar', 'excel', 'subir'] },
                                { nombre: 'Ver Auditoría', url: '{{ route("admin.access.audit") }}', icon: 'history', keywords: ['auditoria', 'logs', 'historial'] },
                                { nombre: 'Reporte Supervisión', url: '{{ route("reportes.supervision") }}', icon: 'monitoring', keywords: ['reporte', 'supervision', 'graficos'] }
                            @endif
                        ];

                        const filteredActions = quickActions.filter(a => 
                            a.keywords.some(k => k.includes(query.toLowerCase())) || 
                            a.nombre.toLowerCase().includes(query.toLowerCase())
                        );

                        fetch(`{{ route('carnetizacion.afiliados.search_ajax') }}?q=${encodeURIComponent(query)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(res => res.json())
                            .then(data => {
                                searchResults.classList.remove('hidden');
                                resultsContainer.innerHTML = '';
                                
                                if (data.length === 0 && filteredActions.length === 0) {
                                    searchEmpty.classList.remove('hidden');
                                    resultsContainer.classList.add('hidden');
                                } else {
                                    searchEmpty.classList.add('hidden');
                                    resultsContainer.classList.remove('hidden');
                                    
                                    // Render Actions First
                                    if (filteredActions.length > 0) {
                                        const actionHeader = document.createElement('div');
                                        actionHeader.className = 'p-3 bg-slate-50/50 border-b border-slate-50';
                                        actionHeader.innerHTML = '<span class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-400">Acciones Rápidas</span>';
                                        resultsContainer.appendChild(actionHeader);

                                        filteredActions.forEach(action => {
                                            const row = document.createElement('a');
                                            row.href = action.url;
                                            row.className = 'flex items-center gap-4 p-4 hover:bg-primary/5 transition-colors group cursor-pointer border-l-4 border-transparent hover:border-primary';
                                            row.innerHTML = `
                                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:rotate-12 transition-transform">
                                                    <span class="material-symbols-outlined text-xl">${action.icon}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-bold text-slate-800">${action.nombre}</p>
                                                    <p class="text-[0.65rem] font-medium text-slate-400">Acceso directo al módulo</p>
                                                </div>
                                                <span class="material-symbols-outlined text-slate-300 text-sm group-hover:translate-x-1 transition-transform">chevron_right</span>
                                            `;
                                            resultsContainer.appendChild(row);
                                        });
                                    }

                                    // Render Affiliates
                                    if (data.length > 0) {
                                        const affiliateHeader = document.createElement('div');
                                        affiliateHeader.className = 'p-3 bg-slate-50/50 border-y border-slate-50';
                                        affiliateHeader.innerHTML = `<span class="text-[0.6rem] font-black uppercase tracking-[0.2em] text-slate-400">Expedientes (${data.length})</span>`;
                                        resultsContainer.appendChild(affiliateHeader);

                                        data.forEach(item => {
                                            const row = document.createElement('a');
                                            row.href = item.url;
                                            row.className = 'flex items-center gap-4 p-4 hover:bg-slate-50 transition-colors group cursor-pointer';
                                            row.innerHTML = `
                                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                                    <span class="material-symbols-outlined text-xl">person</span>
                                                </div>
                                                <div class="flex-1 overflow-hidden">
                                                    <div class="flex justify-between items-center mb-0.5">
                                                        <p class="text-sm font-bold text-slate-800 truncate">${item.nombre}</p>
                                                        <span class="text-[0.6rem] font-black uppercase text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">${item.estado}</span>
                                                    </div>
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-[0.7rem] font-medium text-slate-400">ID: ${item.cedula}</span>
                                                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                                        <span class="text-[0.7rem] font-medium text-slate-400">Póliza: ${item.poliza}</span>
                                                    </div>
                                                </div>
                                            `;
                                            resultsContainer.appendChild(row);
                                        });
                                    }
                                }
                            });
                    }, 300);
                });

                // Close search when clicking outside
                document.addEventListener('click', (e) => {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });

                // Command Palette (Ctrl+K)
                document.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        searchInput.focus();
                    }
                });
            }

            // --- Queue Monitor Logic ---
            const queueMonitor = document.getElementById('queue-monitor');
            const queueCount = document.getElementById('queue-count');
            const queueDetailsList = document.getElementById('queue-details-list');
            
            if (queueMonitor) {
                function updateQueueStatus() {
                    fetch('{{ route("api.queue_status") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.count > 0 || data.active_imports.length > 0) {
                                queueMonitor.classList.remove('hidden');
                                if (queueCount) queueCount.textContent = `Procesando (${data.count})`;
                                
                                if (queueDetailsList) {
                                    if (data.active_imports.length > 0) {
                                        queueDetailsList.innerHTML = data.active_imports.map(imp => `
                                            <div class="space-y-1.5 p-3 bg-blue-50/30 rounded-xl border border-blue-100/50">
                                                <div class="flex justify-between items-center text-[0.65rem]">
                                                    <span class="font-bold text-slate-700 truncate max-w-[140px]">${imp.nombre}</span>
                                                    <span class="font-black text-blue-600">${imp.progress}%</span>
                                                </div>
                                                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                    <div class="h-full bg-blue-500 transition-all duration-500" style="width: ${imp.progress}%"></div>
                                                </div>
                                            </div>
                                        `).join('');
                                    } else {
                                        queueDetailsList.innerHTML = `
                                            <div class="py-6 text-center">
                                                <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3 animate-pulse">
                                                    <i class="ph ph-gear text-blue-400 text-xl"></i>
                                                </div>
                                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Tareas del Sistema</p>
                                                <p class="text-[9px] text-slate-400 font-bold mt-1 italic">Procesando cola de trabajo en segundo plano...</p>
                                            </div>
                                        `;
                                    }
                                }
                            } else {
                                queueMonitor.classList.add('hidden');
                            }
                        })
                        .catch(() => {});
                }

                setInterval(updateQueueStatus, 10000);
                updateQueueStatus();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    @stack('scripts')
    
    <!-- Flash Notifications Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            @if (session('error'))
                Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
            @endif

            @if (session('info'))
                Toast.fire({ icon: 'info', title: '{{ session('info') }}' });
            @endif

            @if (session('warning'))
                Toast.fire({ icon: 'warning', title: '{{ session('warning') }}' });
            @endif
        });
    </script>

    @livewireScripts
</body>
</html>
