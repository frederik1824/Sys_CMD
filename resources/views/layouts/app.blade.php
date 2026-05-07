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

    {{-- Impersonation Banner --}}
    @if(session()->has('impersonate_original_id'))
    <div class="fixed top-0 left-0 right-0 z-[9999] bg-rose-600 text-white text-[11px] font-black uppercase tracking-[0.2em] py-2 px-4 flex items-center justify-center gap-4 shadow-xl border-b border-rose-500/30 backdrop-blur-sm bg-rose-600/95 no-print">
        <span class="flex items-center gap-2">
            <span class="material-symbols-outlined text-[14px] animate-pulse">security</span>
            MODO IMPERSONACIÓN ACTIVO: Estás navegando como {{ auth()->user()->name }}
        </span>
        <a href="{{ route('admin.access.stop_impersonating') }}" class="bg-white text-rose-600 px-3 py-1 rounded-full font-bold hover:bg-rose-50 transition-all flex items-center gap-1 shadow-sm">
            <span class="material-symbols-outlined text-[14px]">logout</span>
            VOLVER A MI SESIÓN
        </a>
    </div>
    @endif

    @php
        $isTraspasos = request()->is('traspasos*') || request()->routeIs('traspasos.*', 'reportes.produccion_traspasos');
        $isAfiliacion = request()->is('solicitudes-afiliacion*') || request()->routeIs('afiliacion.*');
        $isExecutive = request()->routeIs('reportes.executive.suite', 'reportes.executive') || request()->query('context') === 'executive';
        $isAccessControl = request()->is('admin/control-accesos*') || request()->routeIs('admin.access.*');
        $isCallCenter = request()->is('call-center*') || request()->routeIs('call-center.*');
        $isUpdateManager = request()->is('admin/updates*') || request()->routeIs('admin.updates.*');
        $isDispersion = request()->is('admin/dispersion*') || request()->routeIs('dispersion.*');
        
        // CMD is the default operational context for general system, carnetizacion and global reports
        $isCMD = !$isTraspasos && !$isAfiliacion && !$isExecutive && !$isAccessControl && !$isCallCenter && !$isUpdateManager && !$isDispersion;
    @endphp

    <!-- SideNavBar Component -->
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
                        {{ $isTraspasos ? 'Traspasos' : ($isAfiliacion ? 'Afiliación' : ($isExecutive ? 'Intelligence' : ($isAccessControl ? 'Seguridad' : ($isCallCenter ? 'Call Center' : ($isUpdateManager ? 'Update Manager' : ($isDispersion ? 'Dispersión' : 'Servicios')))))) }}
                    </h1>
                    <p class="text-[0.6rem] font-bold uppercase tracking-wider {{ $isTraspasos ? 'text-amber-600' : ($isAfiliacion ? 'text-indigo-600' : ($isExecutive ? 'text-rose-600' : ($isAccessControl ? 'text-slate-900' : ($isCallCenter ? 'text-emerald-600' : ($isUpdateManager ? 'text-blue-600' : ($isDispersion ? 'text-emerald-600' : 'text-blue-600')))))) }} mt-0.5">
                        {{ $isTraspasos ? 'Operativo' : ($isAfiliacion ? 'Interno' : ($isExecutive ? 'Ejecutivo' : ($isAccessControl ? 'Administración' : ($isCallCenter ? 'Gestión CRM' : ($isUpdateManager ? 'Release Hub' : ($isDispersion ? 'PDSS & Bajas' : 'CMD')))))) }}
                    </p>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 space-y-2 overflow-y-auto custom-scrollbar" x-data="{ 
            activeGroup: '{{ 
                request()->routeIs('carnetizacion.import.*', 'carnetizacion.afiliados.cmd', 'carnetizacion.afiliados.otros', 'carnetizacion.afiliados.salida_inmediata', 'carnetizacion.afiliados.call_center') ? 'admision' : (
                request()->routeIs('afiliacion.*') ? 'afiliacion' : (
                request()->routeIs('call-center.*') ? 'callcenter' : (
                request()->routeIs('carnetizacion.afiliados.index', 'lotes.*', 'cierre.*', 'mensajeros.*', 'rutas.*', 'despachos.*') ? 'logistica' : (
                request()->routeIs('evidencias.*', 'liquidacion.*') ? 'gestion' : (
                request()->routeIs('reportes.*') ? 'reportes' : (
                request()->routeIs('sistema.*', 'intranet.catalogo.index') ? 'sistema' : ''
            )))))) }}' 
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

            @if($isTraspasos)
                <!-- MENU: TRASPASOS -->
                <a href="{{ route('traspasos.dashboard') }}" class="flex items-center gap-4 px-6 py-3 rounded-r-xl transition-all {{ request()->routeIs('traspasos.dashboard') ? 'text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm' : 'text-slate-500 hover:text-amber-700 hover:bg-slate-50 group/link' }} mb-2">
                    <i class="ph ph-chart-line-up text-[22px] {{ request()->routeIs('traspasos.dashboard') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Executive Hub</span>
                </a>

                <a href="{{ route('reportes.produccion_traspasos') }}" class="flex items-center gap-4 px-6 py-3 rounded-r-xl transition-all {{ request()->routeIs('reportes.produccion_traspasos') ? 'text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm' : 'text-slate-500 hover:text-amber-700 hover:bg-slate-50 group/link' }} mb-2">
                    <i class="ph ph-presentation-chart text-[22px] {{ request()->routeIs('reportes.produccion_traspasos') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Reporte Producción</span>
                </a>
                
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
                    <div class="px-6 pt-6 pb-2">
                        <p class="text-[0.65rem] font-black uppercase tracking-[0.25em] text-slate-400">Administración</p>
                    </div>


                    <a class="{{ request()->routeIs('traspasos.departamentos.*') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.departamentos.index') }}">
                        <i class="ph ph-buildings text-[22px] {{ request()->routeIs('traspasos.departamentos.*') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Estructura / Dptos</span>
                    </a>

                    <a class="{{ request()->routeIs('traspasos.config.afiliacion-procesos.*') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.config.afiliacion-procesos.index') }}">
                        <i class="ph ph-notebook text-[22px] {{ request()->routeIs('traspasos.config.afiliacion-procesos.*') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Requisitos Afiliación</span>
                    </a>

                    <a class="{{ request()->routeIs('traspasos.config.motivos.*') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.config.motivos.index') }}">
                        <i class="ph ph-list-checks text-[22px] {{ request()->routeIs('traspasos.config.motivos.*') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Catálogo de Motivos</span>
                    </a>

                    <a class="{{ request()->routeIs('traspasos.config.agentes') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.config.agentes') }}">
                        <i class="ph ph-users-three text-[22px] {{ request()->routeIs('traspasos.config.agentes') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Agentes y Equipos</span>
                    </a>
                @endhasanyrole
            @elseif($isAfiliacion)
                <!-- MENU: AFILIACIÓN -->
                <a class="{{ request()->routeIs('solicitudes-afiliacion.index') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.index') }}">
                    <i class="ph ph-list-checks text-[22px] {{ request()->routeIs('solicitudes-afiliacion.index') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Bandeja Operativa</span>
                </a>
                
                <a class="{{ request()->routeIs('solicitudes-afiliacion.create') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.create') }}">
                    <i class="ph ph-plus-circle text-[22px] {{ request()->routeIs('solicitudes-afiliacion.create') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Nueva Solicitud</span>
                </a>

                <a class="{{ request()->routeIs('solicitudes-afiliacion.reports') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.reports') }}">
                    <i class="ph ph-chart-pie-slice text-[22px] {{ request()->routeIs('solicitudes-afiliacion.reports') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Reportes y KPIs</span>
                </a>

                @hasanyrole('Admin|Supervisor|Supervisor de Afiliación|Supervisor de Autorizaciones|Supervisor de Cuentas Médicas|Supervisor de Servicio al Cliente')
                <a class="{{ request()->routeIs('solicitudes-afiliacion.workload') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.workload') }}">
                    <i class="ph ph-chart-bar text-[22px] {{ request()->routeIs('solicitudes-afiliacion.workload') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Balanceo de Carga</span>
                </a>
                @endhasanyrole

                @hasanyrole('Admin|Supervisor|Supervisor de Afiliación|Supervisor de Autorizaciones|Supervisor de Cuentas Médicas|Supervisor de Servicio al Cliente')
                <div class="pt-4 mt-4 border-t border-slate-100">
                    <p class="px-6 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Administración</p>
                    <a class="{{ request()->routeIs('solicitudes-afiliacion.usuarios.*') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.usuarios.index') }}">
                        <i class="ph ph-users text-[22px] {{ request()->routeIs('solicitudes-afiliacion.usuarios.*') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Gestión de Personal</span>
                    </a>
                    <a class="{{ request()->routeIs('solicitudes-afiliacion.departamentos.*') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.departamentos.index') }}">
                        <i class="ph ph-buildings text-[22px] {{ request()->routeIs('solicitudes-afiliacion.departamentos.*') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Estructura / Dptos</span>
                    </a>
                    <a class="{{ request()->routeIs('solicitudes-afiliacion.config') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.config') }}">
                        <i class="ph ph-gear text-[22px] {{ request()->routeIs('solicitudes-afiliacion.config') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Configuración</span>
                    </a>
                </div>
                @endhasanyrole
            @elseif($isCallCenter)
                <!-- MENU: CALL CENTER V2 -->
                <a class="{{ request()->routeIs('call-center.worklist') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('call-center.worklist') }}">
                    <i class="ph ph-headset text-[22px] {{ request()->routeIs('call-center.worklist') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Mi Bandeja de Trabajo</span>
                </a>

                <a class="{{ request()->routeIs('call-center.import') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('call-center.import') }}">
                    <i class="ph ph-upload-simple text-[22px] {{ request()->routeIs('call-center.import') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Importar Prospectos</span>
                </a>

                <a class="{{ request()->routeIs('call-center.index') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('call-center.index') }}">
                    <i class="ph ph-list-numbers text-[22px] {{ request()->routeIs('call-center.index') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Histórico de Cargas</span>
                </a>

                <a class="{{ request()->routeIs('call-center.stats') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('call-center.stats') }}">
                    <i class="ph ph-chart-pie-slice text-[22px] {{ request()->routeIs('call-center.stats') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Estadísticas y KPIs</span>
                </a>
            @elseif($isUpdateManager)
                <!-- MENU: UPDATE MANAGER -->
                <a class="{{ request()->routeIs('admin.updates.index') && !request()->has('anchor') ? 'flex items-center gap-4 px-6 py-3 text-blue-700 font-black bg-blue-50/50 border-l-[3px] border-blue-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.updates.index') }}">
                    <i class="ph ph-rocket-launch text-[22px] {{ request()->routeIs('admin.updates.index') ? 'text-blue-600' : 'group-hover/link:text-blue-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Historial de Releases</span>
                </a>

                <a class="flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link mb-2" href="{{ route('admin.updates.index') }}#health">
                    <i class="ph ph-activity text-[22px] group-hover/link:text-blue-700 text-slate-400"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Monitor de Salud</span>
                </a>

                <a class="flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link mb-2" href="{{ route('admin.updates.index') }}#backups">
                    <i class="ph ph-database text-[22px] group-hover/link:text-blue-700 text-slate-400"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Snapshots & Backups</span>
                </a>

                <a class="flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link mb-2" href="{{ route('admin.updates.index') }}#packer">
                    <i class="ph ph-package text-[22px] group-hover/link:text-blue-700 text-slate-400"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Generador de Patches</span>
                </a>
            @elseif($isDispersion)
                <!-- MENU: DISPERSION -->
                <a class="{{ request()->routeIs('dispersion.index') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.index') }}">
                    <i class="ph ph-chart-pie-slice text-[22px] {{ request()->routeIs('dispersion.index') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Dashboard Mensual</span>
                </a>

                <a class="{{ request()->routeIs('dispersion.history') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.history') }}">
                    <i class="ph ph-calendar-check text-[22px] {{ request()->routeIs('dispersion.history') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Histórico de Periodos</span>
                </a>

                <a class="{{ request()->routeIs('dispersion.reports') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.reports') }}">
                    <i class="ph ph-file-pdf text-[22px] {{ request()->routeIs('dispersion.reports') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Reportes Ejecutivos</span>
                </a>

                <a class="{{ request()->routeIs('dispersion.config') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.config') }}">
                    <i class="ph ph-gear-six text-[22px] {{ request()->routeIs('dispersion.config') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
                    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Configuración</span>
                </a>
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
                            <x-nav-link route="carnetizacion.import.index" icon="ph ph-upload-simple" label="Importar Excel" />
                            <x-nav-link route="carnetizacion.afiliados.cmd" icon="ph ph-identification-card" label="CMD (Paso 1)" />
                            <x-nav-link route="carnetizacion.afiliados.call_center" icon="ph ph-headset" label="Entrada Call Center" />
                            <x-nav-link route="carnetizacion.afiliados.otros" icon="ph ph-users" label="Otros (Paso 1)" />
                            <x-nav-link route="carnetizacion.afiliados.salida_inmediata" icon="ph ph-lightning" label="Salida Inmediata" />
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
                            <x-nav-link route="carnetizacion.afiliados.index" icon="ph ph-users-four" label="Expedientes (Carnets)" />
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
                            <x-nav-link route="reportes.produccion_traspasos" icon="ph ph-chart-bar" label="Producción Traspasos" />
                            <x-nav-link route="reportes.supervision" icon="ph ph-eye" label="Supervisión" />
                            <x-nav-link route="reportes.export_center" icon="ph ph-file-csv" label="Centro de Exportación" />
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
                            <x-nav-link route="sistema.empresas.index" icon="ph ph-buildings" label="Empresas" />
                            <x-nav-link route="sistema.proveedores.index" icon="ph ph-truck" label="Proveedores" />
                            <x-nav-link route="intranet.catalogo.index" icon="ph ph-books" label="Catálogo" />
                            <x-nav-link route="admin.access.audit" icon="ph ph-clock-counter-clockwise" label="Auditoría" />
                            <x-nav-link route="carnetizacion.sync_center.index" icon="ph ph-arrows-clockwise" label="Sync Center" />
                            <x-nav-link route="sistema.backups.index" icon="ph ph-database" label="Copias de Seguridad" />
                            <x-nav-link route="admin.updates.index" icon="ph ph-rocket-launch" label="Update Manager" />
                            <x-nav-link route="admin.access.index" icon="ph ph-shield-checkered" label="Matriz de Accesos" />
                        </div>
                    </div>
                    @endcan
                @elseif($isAccessControl)
                    <!-- MENU: SEGURIDAD / ACCESOS -->
                    <div class="px-6 pt-2 pb-2">
                        <p class="text-[0.65rem] font-black uppercase tracking-[0.25em] text-slate-400">Control Maestro</p>
                    </div>

                    <a class="{{ request()->routeIs('admin.access.users*') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.access.users') }}">
                        <i class="ph ph-users-three text-[22px] {{ request()->routeIs('admin.access.users*') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Gestión de Nómina</span>
                    </a>

                    <a class="{{ request()->routeIs('admin.access.apps*') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.access.apps') }}">
                        <i class="ph ph-squares-four text-[22px] {{ request()->routeIs('admin.access.apps*') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Módulos Instalados</span>
                    </a>

                    <a class="{{ request()->routeIs('admin.access.roles*') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.access.roles') }}">
                        <i class="ph ph-shield-star text-[22px] {{ request()->routeIs('admin.access.roles*') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Diccionario de Roles</span>
                    </a>

                    <a class="{{ request()->routeIs('admin.access.index') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.access.index') }}">
                        <i class="ph ph-shield-checkered text-[22px] {{ request()->routeIs('admin.access.index') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
                        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Matriz de Accesos</span>
                    </a>
                @else
                    <!-- Navigation Links (Suite Ejecutiva / Otros) -->
                    <a class="{{ request()->routeIs('executive.suite') ? 'flex items-center gap-4 px-6 py-3 text-blue-700 font-black bg-blue-50/50 border-l-[3px] border-blue-600 shadow-sm rounded-r-xl transition-all relative overflow-hidden' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-4 mt-0.5" href="{{ route('reportes.executive.suite') }}">
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
    <main class="ml-72 print:ml-0 min-h-screen">
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
                    <a class="{{ request()->routeIs('afiliados.cmd') ? 'text-blue-600 font-bold border-b-[3px] border-blue-600 pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-blue-600 border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem]" href="{{ route('carnetizacion.afiliados.cmd') }}">Afiliados</a>
                    @endcan
                    @can('manage_companies')
                    <a class="{{ request()->routeIs('sistema.empresas.*') ? 'text-blue-600 font-bold border-b-[3px] border-blue-600 pb-[1.4rem] pt-6' : 'text-slate-500 font-medium hover:text-blue-600 border-b-[3px] border-transparent pb-[1.4rem] pt-6 transition-colors' }} text-[0.875rem]" href="{{ route('sistema.empresas.index') }}">Empresas</a>
                    @endcan
                </nav>
            </div>
            
            <div class="flex items-center gap-4">
                @livewire('call-center.follow-up-reminder')
                
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

                    <!-- Notifications Dropdown -->
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-3 w-80 bg-white rounded-[32px] shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <div class="p-6 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                            <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Notificaciones</h3>
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-[9px] font-black rounded-lg">RECIBIDAS</span>
                        </div>
                        <div class="max-h-80 overflow-y-auto custom-scrollbar">
                            @forelse(auth()->user()->notifications->take(10) as $notification)
                                <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors flex gap-4 {{ $notification->read_at ? 'opacity-60' : '' }}">
                                    <div class="w-10 h-10 rounded-xl bg-{{ $notification->data['icon_color'] ?? 'blue' }}-50 flex items-center justify-center shrink-0">
                                        <i class="ph-fill ph-{{ $notification->data['icon'] ?? 'bell' }} text-{{ $notification->data['icon_color'] ?? 'blue' }}-600"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[0.7rem] font-bold text-slate-800 leading-tight">{{ $notification->data['title'] ?? 'Notificación' }}</p>
                                        <p class="text-[0.65rem] text-slate-500 mt-1 leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                                        <p class="text-[0.6rem] text-slate-400 mt-2 font-medium">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="ph ph-bell-slash text-2xl text-slate-300"></i>
                                    </div>
                                    <p class="text-[0.7rem] text-slate-400 font-bold uppercase tracking-widest">Bandeja Vacía</p>
                                </div>
                            @endforelse
                        </div>
                        <div class="p-4 bg-slate-50/50">
                            <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-white border border-slate-200 rounded-2xl text-[10px] font-black text-slate-600 uppercase tracking-widest hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                                    Marcar todo como leído
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="h-8 w-[1px] bg-slate-200 mx-2"></div>
                
                <!-- User Profile -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 p-1 pr-4 bg-white hover:bg-slate-50 border border-slate-100 hover:border-slate-200 rounded-full shadow-sm hover:shadow transition-all duration-200">
                        <img src="{{ $user->avatar_url }}" class="w-8 h-8 rounded-full object-cover border border-slate-200" alt="User">
                        <div class="hidden md:flex flex-col text-left">
                            <span class="text-[0.75rem] font-black text-slate-800 leading-tight">{{ $user->name }}</span>
                            <span class="text-[0.6rem] font-bold text-blue-600 uppercase tracking-tighter">{{ $user->getRoleNames()->first() ?? 'Usuario' }}</span>
                        </div>
                        <i class="ph ph-caret-down text-[10px] text-slate-400 ml-1"></i>
                    </button>

                    <!-- User Dropdown -->
                    <div x-show="open" @click.outside="open = false" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 mt-3 w-56 bg-white rounded-[32px] shadow-2xl border border-slate-100 z-50 overflow-hidden" x-cloak>
                        <div class="p-6 border-b border-slate-50 bg-slate-50/50">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Sesión Activa</p>
                            <p class="text-xs font-black text-slate-800 truncate">{{ $user->email }}</p>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 p-4 text-[0.75rem] font-bold text-slate-600 hover:bg-blue-50 hover:text-blue-600 rounded-2xl transition-colors">
                                <i class="ph ph-user-circle text-lg"></i> Perfil Personal
                            </a>
                            <div class="h-[1px] bg-slate-50 my-1"></div>
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
                            { nombre: 'Nueva Empresa', url: '{{ route("sistema.empresas.create") }}', icon: 'add_business', keywords: ['nueva', 'crear', 'empresa'] },
                            { nombre: 'Importar Excel', url: '{{ route("carnetizacion.import.index") }}', icon: 'upload_file', keywords: ['importar', 'excel', 'subir'] },
                            { nombre: 'Ver Auditoría', url: '{{ route("admin.access.audit") }}', icon: 'history', keywords: ['auditoria', 'logs', 'historial'] },
                            { nombre: 'Reporte Supervisión', url: '{{ route("reportes.supervision") }}', icon: 'monitoring', keywords: ['reporte', 'supervision', 'graficos'] }
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
                                
                                if (queueDetailsList && data.active_imports.length > 0) {
                                    queueDetailsList.innerHTML = data.active_imports.map(imp => `
                                        <div class="space-y-1.5">
                                            <div class="flex justify-between items-center text-[0.65rem]">
                                                <span class="font-bold text-slate-700 truncate max-w-[140px]">${imp.nombre}</span>
                                                <span class="font-black text-blue-600">${imp.progress}%</span>
                                            </div>
                                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-500 transition-all duration-500" style="width: ${imp.progress}%"></div>
                                            </div>
                                        </div>
                                    `).join('');
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
    @livewireScripts
</body>
</html>
