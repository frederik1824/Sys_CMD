<!DOCTYPE html>
<html lang="es" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Portal Empresarial') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" />
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css"/>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/duotone/style.css"/>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/bold/style.css"/>
    
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: '#00346f',
                        secondary: '#0060ac',
                        dark: '#0f172a',
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }
        body {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210,100%,96%,1) 0, transparent 50%), 
                radial-gradient(at 100% 100%, hsla(220,100%,94%,1) 0, transparent 50%);
            min-height: 100vh;
        }
    </style>
    @livewireStyles
</head>
<body class="font-sans antialiased text-slate-900 overflow-x-hidden" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 left-0 z-50 w-24 bg-dark text-white flex flex-col items-center py-8 gap-8 transition-transform lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="w-14 flex items-center justify-center mb-4">
                <img src="{{ asset('img/Logo.png') }}" alt="Logo CMD" class="w-full h-auto brightness-0 invert opacity-90">
            </div>
            
            <nav class="flex-1 flex flex-col gap-6">
                <a href="{{ route('portal') }}" class="w-12 h-12 rounded-2xl flex items-center justify-center bg-white/10 text-white hover:bg-primary transition-all group relative">
                    <i class="ph-bold ph-squares-four text-xl"></i>
                    <span class="absolute left-16 px-2 py-1 bg-slate-900 text-[10px] font-bold rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">Apps</span>
                </a>
                <a href="#" class="w-12 h-12 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-white/10 hover:text-white transition-all group relative">
                    <i class="ph-bold ph-chart-line-up text-xl"></i>
                    <span class="absolute left-16 px-2 py-1 bg-slate-900 text-[10px] font-bold rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">Analytics</span>
                </a>
                <a href="#" class="w-12 h-12 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-white/10 hover:text-white transition-all group relative">
                    <i class="ph-bold ph-bell text-xl"></i>
                    <span class="absolute left-16 px-2 py-1 bg-slate-900 text-[10px] font-bold rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">Notificaciones</span>
                </a>

                @hasanyrole('Admin|Super-Admin')
                <a href="{{ route('admin.updates.index') }}" class="w-12 h-12 rounded-2xl flex items-center justify-center text-slate-400 hover:bg-blue-500/20 hover:text-blue-400 transition-all group relative {{ request()->routeIs('admin.updates.*') ? 'bg-blue-500/10 text-blue-400' : '' }}">
                    <i class="ph-bold ph-rocket-launch text-xl"></i>
                    <span class="absolute left-16 px-2 py-1 bg-slate-900 text-[10px] font-bold rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-50">Update Manager</span>
                </a>
                @endhasanyrole
            </nav>

            <div class="flex flex-col gap-6 mt-auto">
                <a href="{{ route('profile.edit') }}" class="w-12 h-12 rounded-2xl overflow-hidden border-2 border-transparent hover:border-primary transition-all">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=00346f&color=fff" class="w-full h-full object-cover">
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-12 h-12 rounded-2xl flex items-center justify-center text-rose-400 hover:bg-rose-500/10 transition-all">
                        <i class="ph-bold ph-sign-out text-xl"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 lg:pl-24">
            <!-- Mobile Header -->
            <header class="lg:hidden flex items-center justify-between p-4 bg-white border-b border-slate-100 sticky top-0 z-40">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center text-white">
                    <i class="ph-bold ph-shield-check text-xl"></i>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 flex items-center justify-center text-slate-600">
                    <i class="ph-bold ph-list text-2xl"></i>
                </button>
            </header>

            <main class="flex-1">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Global Notification (Toast) -->
    <div x-data="{ 
            show: false, 
            message: '', 
            type: 'success',
            timeout: null 
        }" 
        @notify.window="
            show = true; 
            message = $event.detail[0].msg || $event.detail[0].message; 
            type = $event.detail[0].type || 'success';
            clearTimeout(timeout);
            timeout = setTimeout(() => show = false, 5000);
        "
        x-show="show" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="fixed bottom-8 right-8 z-[100]"
        x-cloak>
        
        <div :class="{
                'bg-emerald-600': type === 'success',
                'bg-rose-600': type === 'error',
                'bg-amber-600': type === 'warning',
                'bg-blue-600': type === 'info'
            }" 
            class="text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[300px]">
            <i class="ph-bold" :class="{
                'ph-check-circle': type === 'success',
                'ph-warning-octagon': type === 'error',
                'ph-warning': type === 'warning',
                'ph-info': type === 'info'
            } + ' text-xl'"></i>
            <span x-text="message" class="text-sm font-black tracking-tight"></span>
        </div>
    </div>

    @livewireScripts
</body>
</html>
