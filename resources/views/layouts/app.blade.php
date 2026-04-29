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
            background-color: #f8f9fa;
            color: #191c1d;
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
            background: #e1e3e4;
            border-radius: 10px;
        }

        /* --- Transitions & Animations --- */
        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .page-transition {
            animation: fadeIn 0.4s ease-out forwards;
        }

        .hover-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -10px rgba(0, 52, 111, 0.15);
        }

        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }

        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
    <body class="bg-surface text-on-surface" 
          data-success="{{ session('success') }}" 
          data-error="{{ session('error') ?? ($errors->any() ? 'Existen errores de validación en el formulario.' : '') }}">

    {{-- Impersonation Banner --}}
    @if(session()->has('impersonate_original_id'))
    <div class="fixed top-0 left-0 right-0 z-[9999] bg-rose-600 text-white text-[11px] font-black uppercase tracking-[0.2em] py-2 px-4 flex items-center justify-center gap-4 shadow-xl border-b border-rose-500/30 backdrop-blur-sm bg-rose-600/95 no-print">
        <span class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[14px] animate-pulse">security</span>
            MODO IMPERSONACIÓN ACTIVO: Estás navegando como {{ auth()->user()->name }}
        </span>
        <a href="{{ route('usuarios.stop_impersonating') }}" class="bg-white text-rose-600 px-3 py-1 rounded-full font-bold hover:bg-rose-50 transition-all flex items-center gap-1 shadow-sm">
            <span class="material-symbols-outlined text-[14px]">logout</span>
            VOLVER A MI SESIÓN
        </a>
    </div>
    @endif

    @php
        $isTraspasos = request()->is('traspasos*');
        $isAfiliacion = request()->is('solicitudes-afiliacion*');
        $isExecutive = request()->routeIs('executive.suite', 'reportes.executive') || request()->query('context') === 'executive';
        $isCMD = request()->routeIs('dashboard', 'afiliados.*', 'callcenter.*', 'import.*', 'lotes.*', 'cierre.*', 'mensajeros.*', 'rutas.*', 'despachos.*', 'evidencias.*', 'liquidacion.*', 'reportes.*', 'empresas.*', 'proveedores.*', 'catalogo.*', 'admin.audit.index', 'usuarios.*') && !$isExecutive;
    @endphp

    <!-- SideNavBar Component -->
    <aside class="no-print h-screen w-80 fixed left-0 top-0 border-r border-slate-200 bg-white flex flex-col py-8 z-50 shadow-2xl shadow-slate-200/50">
        @php /** @var \App\Models\User $user */ $user = Auth::user(); @endphp
        <div class="px-8 mb-10 w-full">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gradient-to-br {{ $isTraspasos ? 'from-amber-500 to-amber-700 shadow-amber-500/30' : ($isAfiliacion ? 'from-indigo-500 to-indigo-700 shadow-indigo-500/30' : 'from-blue-500 to-blue-700 shadow-blue-500/30') }} rounded-2xl flex items-center justify-center text-white shadow-lg">
                    <i class="{{ $isTraspasos ? 'ph-fill ph-swap' : ($isAfiliacion ? 'ph-fill ph-user-plus' : 'ph-fill ph-shield-check') }} text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tighter text-slate-800 leading-none">
                        {{ $isTraspasos ? 'Traspasos' : ($isAfiliacion ? 'Afiliación' : 'Servicios') }}
                    </h1>
                    <p class="text-[0.65rem] font-bold uppercase tracking-[0.2em] {{ $isTraspasos ? 'text-amber-600' : ($isAfiliacion ? 'text-indigo-600' : 'text-blue-600') }} mt-1">
                        {{ $isTraspasos ? 'Módulo Operativo' : ($isAfiliacion ? 'Gestión Interna' : 'Gestión Operativa') }}
                    </p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar" x-data="{ 
            activeGroup: '{{ 
                request()->routeIs('import.*', 'afiliados.cmd', 'afiliados.otros', 'afiliados.salida_inmediata') ? 'admision' : (
                request()->routeIs('solicitudes-afiliacion.*') ? 'afiliacion' : (
                request()->routeIs('callcenter.*') ? 'callcenter' : (
                request()->routeIs('afiliados.index', 'lotes.*', 'cierre.*', 'mensajeros.*', 'rutas.*', 'despachos.*') ? 'logistica' : (
                request()->routeIs('evidencias.*', 'liquidacion.*', 'pagos.*') ? 'gestion' : (
                request()->routeIs('reportes.*') ? 'reportes' : (
                request()->routeIs('empresas.*', 'proveedores.*', 'catalogo.*', 'admin.audit.index', 'usuarios.*') ? 'sistema' : ''
            )))))) }}' 
        }">
            <!-- Portal Return Button (NUEVO) -->
            <div class="px-2 mb-6">
                <a href="{{ route('portal') }}" class="flex items-center gap-4 px-6 py-4 bg-slate-900 text-white rounded-2xl hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/10 group overflow-hidden relative">
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-600/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <i class="ph-fill ph-circles-four text-[22px] text-blue-400 group-hover:scale-110 transition-transform relative z-10"></i>
                    <div class="flex flex-col relative z-10">
                        <span class="text-[9px] font-black uppercase tracking-[0.2em] text-blue-300 mb-0.5">Volver al</span>
                        <span class="text-[11px] font-black uppercase tracking-widest text-white leading-none">Centro de Apps</span>
                    </div>
                    <i class="ph ph-arrow-right text-xs ml-auto text-slate-500 group-hover:text-white group-hover:translate-x-1 transition-all relative z-10"></i>
                </a>
            </div>

            @if($isTraspasos)
                <!-- MENU: TRASPASOS -->
                <a class="{{ request()->routeIs('traspasos.index') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.index') }}">
                    <i class="ph ph-list-bullets text-[22px] {{ request()->routeIs('traspasos.index') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Bandeja Global</span>
                </a>
                
                <a class="{{ request()->routeIs('traspasos.import') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.import') }}">
                    <i class="ph ph-upload-simple text-[22px] {{ request()->routeIs('traspasos.import') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Importar Unipago</span>
                </a>

                <a class="{{ request()->routeIs('traspasos.bulk.effective') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.bulk.effective') }}">
                    <i class="ph ph-lightning text-[22px] {{ request()->routeIs('traspasos.bulk.effective') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Efectividad Masiva</span>
                </a>

                @hasanyrole('Admin|Supervisor de Traspasos')
                <div class="pt-4 mt-4 border-t border-slate-100">
                    <p class="px-6 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Administración</p>
                    <a class="{{ request()->routeIs('traspasos.usuarios.*') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.usuarios.index') }}">
                        <i class="ph ph-users text-[22px] {{ request()->routeIs('traspasos.usuarios.*') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Gestión de Personal</span>
                    </a>
                    <a class="{{ request()->routeIs('traspasos.departamentos.*') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.departamentos.index') }}">
                        <i class="ph ph-buildings text-[22px] {{ request()->routeIs('traspasos.departamentos.*') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Estructura / Dptos</span>
                    </a>
                    <a class="{{ request()->routeIs('traspasos.config.agentes') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.config.agentes') }}">
                        <i class="ph ph-users-three text-[22px] {{ request()->routeIs('traspasos.config.agentes') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Agentes</span>
                    </a>
                </div>
                @endhasanyrole
            @elseif($isAfiliacion)
                <!-- MENU: AFILIACIÓN -->
                <a class="{{ request()->routeIs('solicitudes-afiliacion.index') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('solicitudes-afiliacion.index') }}">
                    <i class="ph ph-list-checks text-[22px] {{ request()->routeIs('solicitudes-afiliacion.index') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Bandeja Operativa</span>
                </a>
                
                <a class="{{ request()->routeIs('solicitudes-afiliacion.create') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('solicitudes-afiliacion.create') }}">
                    <i class="ph ph-plus-circle text-[22px] {{ request()->routeIs('solicitudes-afiliacion.create') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Nueva Solicitud</span>
                </a>

                <a class="{{ request()->routeIs('solicitudes-afiliacion.reports') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('solicitudes-afiliacion.reports') }}">
                    <i class="ph ph-chart-pie-slice text-[22px] {{ request()->routeIs('solicitudes-afiliacion.reports') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Reportes y KPIs</span>
                </a>

                @hasanyrole('Admin|Supervisor|Supervisor de Afiliación|Supervisor de Autorizaciones|Supervisor de Cuentas Médicas|Supervisor de Servicio al Cliente')
                <a class="{{ request()->routeIs('solicitudes-afiliacion.workload') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('solicitudes-afiliacion.workload') }}">
                    <i class="ph ph-chart-bar text-[22px] {{ request()->routeIs('solicitudes-afiliacion.workload') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Balanceo de Carga</span>
                </a>
                @endhasanyrole

                @hasanyrole('Admin|Supervisor|Supervisor de Afiliación|Supervisor de Autorizaciones|Supervisor de Cuentas Médicas|Supervisor de Servicio al Cliente')
                <div class="pt-4 mt-4 border-t border-slate-100">
                    <p class="px-6 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Administración</p>
                    <a class="{{ request()->routeIs('solicitudes-afiliacion.usuarios.*') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('solicitudes-afiliacion.usuarios.index') }}">
                        <i class="ph ph-users text-[22px] {{ request()->routeIs('solicitudes-afiliacion.usuarios.*') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Gestión de Personal</span>
                    </a>
                    <a class="{{ request()->routeIs('solicitudes-afiliacion.departamentos.*') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('solicitudes-afiliacion.departamentos.index') }}">
                        <i class="ph ph-buildings text-[22px] {{ request()->routeIs('solicitudes-afiliacion.departamentos.*') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Estructura / Dptos</span>
                    </a>
                    <a class="{{ request()->routeIs('solicitudes-afiliacion.config') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('solicitudes-afiliacion.config') }}">
                        <i class="ph ph-gear text-[22px] {{ request()->routeIs('solicitudes-afiliacion.config') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Configuración</span>
                    </a>
                </div>
                @endhasanyrole
            @else
                @if($isCMD)
                    {{-- GRUPO: ADMISIÓN --}}
                    @can('manage_affiliates')
                    <div class="space-y-1">
                        <button @click="activeGroup = activeGroup === 'admision' ? '' : 'admision'" 
                                :class="activeGroup === 'admision' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
                                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-user-plus text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'admision' ? 'text-blue-600' : ''"></i>
                                <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'admision' ? 'text-blue-700' : ''">Admisión</span>
                            </div>
                            <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'admision' ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="activeGroup === 'admision'" x-collapse class="pl-4 space-y-1">
                            <x-nav-link route="import.index" icon="ph ph-upload-simple" label="Importar Excel" />
                            <x-nav-link route="afiliados.cmd" icon="ph ph-identification-card" label="CMD (Paso 1)" />
                            <x-nav-link route="afiliados.otros" icon="ph ph-users" label="Otros (Paso 1)" />
                            <x-nav-link route="afiliados.salida_inmediata" icon="ph ph-lightning" label="Salida Inmediata" />
                        </div>
                    </div>
                    @endcan

                    {{-- GRUPO: CALL CENTER --}}
                    @can('manage_calls')
                    <div class="space-y-1">
                        <button @click="activeGroup = activeGroup === 'callcenter' ? '' : 'callcenter'" 
                                :class="activeGroup === 'callcenter' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
                                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-headset text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'callcenter' ? 'text-blue-600' : ''"></i>
                                <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'callcenter' ? 'text-blue-700' : ''">Call Center</span>
                            </div>
                            <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'callcenter' ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="activeGroup === 'callcenter'" x-collapse class="pl-4 space-y-1">
                            <x-nav-link route="callcenter.dashboard" icon="ph ph-phone-call" label="Panel de Control" />
                            <x-nav-link route="callcenter.worklist" icon="ph ph-list-bullets" label="Bandeja de Llamadas" />
                        </div>
                    </div>
                    @endcan

                    {{-- GRUPO: LOGÍSTICA --}}
                    @can('manage_logistics')
                    <div class="space-y-1">
                        <button @click="activeGroup = activeGroup === 'logistica' ? '' : 'logistica'" 
                                :class="activeGroup === 'logistica' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
                                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-truck text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'logistica' ? 'text-blue-600' : ''"></i>
                                <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'logistica' ? 'text-blue-700' : ''">Logística</span>
                            </div>
                            <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'logistica' ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="activeGroup === 'logistica'" x-collapse class="pl-4 space-y-1">
                            <x-nav-link route="afiliados.index" icon="ph ph-users-four" label="Expedientes (Carnets)" />
                            <x-nav-link route="lotes.index" icon="ph ph-package" label="Control de Lotes" />
                            <x-nav-link route="cierre.index" icon="ph ph-lock-key" label="Cierre de Cortes" />
                            <x-nav-link route="mensajeros.index" icon="ph ph-moped" label="Mensajeros" />
                            <x-nav-link route="rutas.index" icon="ph ph-map-trifold" label="Rutas de Entrega" />
                            <x-nav-link route="despachos.index" icon="ph ph-paper-plane-tilt" label="Despachos" />
                        </div>
                    </div>
                    @endcan

                    {{-- GRUPO: GESTIÓN --}}
                    @can('manage_administration')
                    <div class="space-y-1">
                        <button @click="activeGroup = activeGroup === 'gestion' ? '' : 'gestion'" 
                                :class="activeGroup === 'gestion' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
                                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-files text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'gestion' ? 'text-blue-600' : ''"></i>
                                <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'gestion' ? 'text-blue-700' : ''">Gestión</span>
                            </div>
                            <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'gestion' ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="activeGroup === 'gestion'" x-collapse class="pl-4 space-y-1">
                            <x-nav-link route="evidencias.index" icon="ph ph-camera" label="Evidencias Digitales" />
                            <x-nav-link route="liquidacion.index" icon="ph ph-hand-coins" label="Liquidación" />
                        </div>
                    </div>
                    @endcan

                    {{-- GRUPO: REPORTES --}}
                    @can('view_reports')
                    <div class="space-y-1">
                        <button @click="activeGroup = activeGroup === 'reportes' ? '' : 'reportes'" 
                                :class="activeGroup === 'reportes' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
                                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-chart-pie-slice text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'reportes' ? 'text-blue-600' : ''"></i>
                                <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'reportes' ? 'text-blue-700' : ''">Reportes</span>
                            </div>
                            <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'reportes' ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="activeGroup === 'reportes'" x-collapse class="pl-4 space-y-1">
                            <x-nav-link route="reportes.index" icon="ph ph-chart-line-up" label="Estadísticas" />
                            <x-nav-link route="reportes.supervision" icon="ph ph-eye" label="Supervisión" />
                            <x-nav-link route="reportes.heatmap" icon="ph ph-globe-hemisphere-west" label="Mapa Global" />
                        </div>
                    </div>
                    @endcan

                    {{-- GRUPO: SISTEMA --}}
                    @can('manage_system')
                    <div class="space-y-1">
                        <button @click="activeGroup = activeGroup === 'sistema' ? '' : 'sistema'" 
                                :class="activeGroup === 'sistema' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
                                class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
                            <div class="flex items-center gap-4">
                                <i class="ph ph-gear text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'sistema' ? 'text-blue-600' : ''"></i>
                                <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'sistema' ? 'text-blue-700' : ''">Sistema</span>
                            </div>
                            <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'sistema' ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="activeGroup === 'sistema'" x-collapse class="pl-4 space-y-1">
                            <x-nav-link route="empresas.index" icon="ph ph-buildings" label="Empresas" />
                            <x-nav-link route="proveedores.index" icon="ph ph-truck" label="Proveedores" />
                            <x-nav-link route="catalogo.index" icon="ph ph-books" label="Catálogo" />
                            <x-nav-link route="admin.audit.index" icon="ph ph-clock-counter-clockwise" label="Auditoría" />
                            <x-nav-link route="usuarios.index" icon="ph ph-users-three" label="Usuarios y Roles" />
                        </div>
                    </div>
                    @endcan
                @else
                    <!-- Navigation Links (Suite Ejecutiva / Otros) -->
                    <a class="{{ request()->routeIs('executive.suite') ? 'flex items-center gap-4 px-6 py-3 text-blue-700 font-black bg-blue-50/50 border-l-[3px] border-blue-600 shadow-sm rounded-r-xl transition-all relative overflow-hidden' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-4 mt-0.5" href="{{ route('executive.suite') }}">
                        @if(request()->routeIs('executive.suite'))
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-transparent"></div>
                        @endif
                        <i class="ph ph-crown text-[22px] {{ request()->routeIs('executive.suite') ? 'text-blue-600' : 'group-hover/link:text-blue-700 text-slate-400' }} transition-colors relative z-10"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold relative z-10">Suite Ejecutiva</span>
                    </a>

                    {{-- Reportes removidos para comenzar desde cero --}}
                @endif
            @endif
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

    <!-- Main Canvas -->
    <main class="ml-80 print:ml-0 min-h-screen">
        <!-- TopNavBar Component -->
        @if($isCMD)
        <header class="no-print sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 px-8 flex justify-between items-center shadow-sm border-b border-slate-100">
            <div class="flex items-center gap-8">
                <div class="relative w-80 group">
                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-hover:text-blue-600 transition-colors"></i>
                    <input id="navbar-search" class="w-full bg-slate-50 border border-slate-200 rounded-full pl-11 pr-16 py-2.5 text-[0.875rem] font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition-all placeholder:font-medium shadow-inner" placeholder="Buscar afiliado o empresa..." type="text" autocomplete="off"/>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 px-2 py-1 bg-white rounded-md border border-slate-200 shadow-sm pointer-events-none opacity-60 group-hover:opacity-100 transition-opacity">
                        <span class="text-[0.6rem] font-black text-slate-400">CTRL</span>
                        <span class="text-[0.6rem] font-black text-slate-400">K</span>
                    </div>
                    
                    <!-- Search Results Dropdown -->
                    <div id="search-results" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden hidden animate-in fade-in slide-in-from-top-2 duration-200 z-50">
                        <div class="p-3 border-b border-slate-50 bg-slate-50/50">
                            <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">Resultados sugeridos</span>
                        </div>
                        <div id="results-container" class="max-h-[400px] overflow-y-auto custom-scrollbar divide-y divide-slate-50">
                            <!-- JS Will fill this -->
                        </div>
                        <div id="search-empty" class="p-8 text-center hidden">
                            <i class="ph ph-user-focus text-slate-200 text-4xl mb-2"></i>
                            <p class="text-xs font-bold text-slate-400">No se encontraron coincidencias.</p>
                        </div>
                    </div>
                </div>
                <nav class="flex gap-6">
                    <a class="{{ request()->routeIs('dashboard') ? 'text-blue-600 font-bold border-b-[3px] border-blue-600 pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-blue-600 border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem] flex items-center gap-2" href="{{ route('dashboard') }}">
                        Operación CMD
                    </a>
                    @can('manage_affiliates')
                    <a class="{{ request()->routeIs('afiliados.cmd') ? 'text-blue-600 font-bold border-b-[3px] border-blue-600 pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-blue-600 border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem]" href="{{ route('afiliados.cmd') }}">Afiliados</a>
                    @endcan
                    @can('manage_companies')
                    <a class="{{ request()->routeIs('empresas.*') ? 'text-blue-600 font-bold border-b-[3px] border-blue-600 pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-blue-600 border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem]" href="{{ route('empresas.index') }}">Empresas</a>
                    @endcan
                </nav>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Background Jobs Monitor -->
                @unless(auth()->user()->hasRole('Operador'))
                <div id="queue-monitor" class="relative hidden" x-data="{ open: false, imports: [] }">
                    <button @click="open = !open" class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 rounded-full border border-blue-100 hover:bg-blue-100 transition-colors">
                        <i class="ph-fill ph-cpu text-blue-600 animate-spin-slow"></i>
                        <span id="queue-count" class="text-[0.65rem] font-black text-blue-700 uppercase tracking-tight">Procesando...</span>
                        <i class="ph ph-caret-down text-[10px] text-blue-400"></i>
                    </button>
                    <!-- Detalle de procesos -->
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         class="absolute right-0 mt-3 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden">
                        <div class="p-4 border-b border-slate-50 bg-slate-50/50">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Tareas en curso</h3>
                        </div>
                        <div id="queue-details-list" class="p-4 space-y-4 max-h-60 overflow-y-auto custom-scrollbar">
                            <div class="flex items-center justify-center py-4">
                                <p class="text-[0.7rem] text-slate-400 italic">Obteniendo detalles...</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endunless

                <!-- Notifications -->
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
                    <button @click="open = !open; if(open) markRead()" class="w-10 h-10 flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors rounded-full relative group">
                        <i class="ph ph-bell text-[22px] group-hover:scale-110 transition-transform"></i>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-2 right-2 w-4 h-4 bg-red-500 rounded-full border-[2px] border-white text-[0.6rem] text-white flex items-center justify-center font-bold">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                        @endif
                    </button>
                </div>
                
                <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                
                <!-- User Profile -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 p-1 pr-4 bg-white hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-full shadow-sm hover:shadow transition-all duration-200">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="User">
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-[0.75rem] font-black text-slate-800 leading-tight">{{ $user->name }}</span>
                            <span class="text-[0.6rem] font-bold text-blue-600 uppercase tracking-tighter">{{ $user->getRoleNames()->first() ?? 'Usuario' }}</span>
                        </div>
                    </button>
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

            searchInput.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                clearTimeout(searchTimeout);

                if (query.length < 3) {
                    searchResults.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    const quickActions = [
                        { nombre: 'Nueva Empresa', url: '{{ route("empresas.create") }}', icon: 'add_business', keywords: ['nueva', 'crear', 'empresa'] },
                        { nombre: 'Importar Excel', url: '{{ route("import.index") }}', icon: 'upload_file', keywords: ['importar', 'excel', 'subir'] },
                        { nombre: 'Ver Auditoría', url: '{{ route("admin.audit.index") }}', icon: 'history', keywords: ['auditoria', 'logs', 'historial'] },
                        { nombre: 'Reporte Supervisión', url: '{{ route("reportes.supervision") }}', icon: 'monitoring', keywords: ['reporte', 'supervision', 'graficos'] }
                    ];

                    const filteredActions = quickActions.filter(a => 
                        a.keywords.some(k => k.includes(query.toLowerCase())) || 
                        a.nombre.toLowerCase().includes(query.toLowerCase())
                    );

                    fetch(`{{ route('afiliados.search_ajax') }}?q=${encodeURIComponent(query)}`, {
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

            // --- Command Palette (Ctrl+K) ---
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });

            // --- Queue Monitor Logic (Mejorada) ---
            const queueMonitor = document.getElementById('queue-monitor');
            const queueCount = document.getElementById('queue-count');
            const queueDetailsList = document.getElementById('queue-details-list');
            
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
                            queueCount.textContent = `Procesando (${data.count})`;
                            
                            // Actualizar lista de detalles
                            if (data.active_imports.length > 0) {
                                queueDetailsList.innerHTML = data.active_imports.map(imp => `
                                    <div class="space-y-1.5">
                                        <div class="flex justify-between items-center text-[0.65rem]">
                                            <span class="font-bold text-slate-700 truncate max-w-[140px]">${imp.nombre}</span>
                                            <span class="font-black text-blue-600">${imp.progress}%</span>
                                        </div>
                                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-500 transition-all duration-500" style="width: ${imp.progress}%"></div>
                                        </div>
                                        <div class="text-[0.6rem] text-slate-400 flex justify-between">
                                            <span>${imp.current} de ${imp.total} registros</span>
                                            <span class="italic truncate max-w-[100px]">${imp.logs.length > 0 ? imp.logs[0] : ''}</span>
                                        </div>
                                    </div>
                                `).join('');
                            } else {
                                queueDetailsList.innerHTML = `
                                    <div class="py-2 text-center">
                                        <p class="text-[0.7rem] text-slate-500">Preparando datos...</p>
                                        <p class="text-[0.6rem] text-slate-400 mt-1">El archivo se está leyendo.</p>
                                    </div>
                                `;
                            }
                        } else {
                            queueMonitor.classList.add('hidden');
                        }
                    })
                    .catch(() => {});
            }

            setInterval(updateQueueStatus, 10000); // Cada 10 segundos
            updateQueueStatus();

            // Close search when clicking outside
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    @stack('scripts')
    @livewireScripts
</body>
</html>
