<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SysCarnet</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary': '#00346f',
                        'secondary': '#0060ac',
                    },
                    fontFamily: {
                        'headline': ['Manrope'],
                        'body': ['Inter'],
                    }
                }
            }
        }
    </script>
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .bg-mesh {
            background-color: #00346f;
            background-image: 
                radial-gradient(at 0% 0%, hsla(212,100%,35%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(206,100%,45%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(212,100%,25%,1) 0, transparent 50%);
        }
    </style>
</head>
<body class="font-body bg-slate-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-5xl w-full grid lg:grid-cols-2 bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-white">
        
        <!-- Left Side: Visual/Branding -->
        <div class="hidden lg:flex bg-mesh p-12 flex-col justify-between text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2 blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-3xl" style="font-variation-settings: 'FILL' 1;">shield_person</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black font-headline tracking-tighter">SysCarnet</h1>
                        <p class="text-[0.6rem] font-bold uppercase tracking-[0.2em] text-blue-200">ID Management System</p>
                    </div>
                </div>
                
                <h2 class="text-5xl font-extrabold font-headline leading-[1.1] mb-6">
                    Gestiona la <span class="text-blue-300">identidad</span> de tu organización.
                </h2>
                <p class="text-blue-100 text-lg font-medium leading-relaxed max-w-md">
                    Accede a la plataforma líder en carnetización y control de afiliados para ARS CMD y empresas aliadas.
                </p>
            </div>

            <div class="relative z-10 pt-10 border-t border-white/10">
                <div class="flex items-center gap-4">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full border-2 border-primary bg-blue-400"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-primary bg-blue-500"></div>
                        <div class="w-10 h-10 rounded-full border-2 border-primary bg-blue-600"></div>
                    </div>
                    <p class="text-sm font-bold text-blue-100">+5,000 Afiliados procesados hoy</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="p-8 lg:p-16 flex flex-col justify-center bg-white">
            <div class="mb-10 lg:hidden flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">shield_person</span>
                <span class="text-xl font-black text-primary font-headline">SysCarnet</span>
            </div>

            <div class="mb-10">
                <h3 class="text-3xl font-black text-slate-800 font-headline mb-2">Bienvenido de nuevo</h3>
                <p class="text-slate-500 font-medium italic">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Session Status -->
            @if(session('status'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 text-sm font-bold flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div class="space-y-2">
                    <label for="email" class="text-xs font-black uppercase tracking-widest text-slate-400 ml-4">Correo Electrónico</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">alternate_email</span>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                            class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none" 
                            placeholder="ejemplo@arscmd.com">
                    </div>
                    @if($errors->get('email'))
                        <p class="text-[0.7rem] text-rose-500 font-bold mt-1 ml-4 italic">{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <!-- Password -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center ml-4 mr-4">
                        <label for="password" class="text-xs font-black uppercase tracking-widest text-slate-400">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[0.65rem] font-bold text-primary hover:underline">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">lock_open</span>
                        <input id="password" type="password" name="password" required 
                            class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-4 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none" 
                            placeholder="••••••••">
                    </div>
                    @if($errors->get('password'))
                        <p class="text-[0.7rem] text-rose-500 font-bold mt-1 ml-4 italic">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                <!-- Remember Me -->
                <div class="flex items-center ml-4">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-none bg-slate-100 text-primary focus:ring-0 transition-all">
                        <span class="ml-3 text-xs font-bold text-slate-500 group-hover:text-slate-700 transition-colors">Recordar mi sesión</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-primary hover:bg-slate-800 text-white font-black py-4 rounded-2xl shadow-xl shadow-primary/20 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                    Iniciar Sesión
                    <span class="material-symbols-outlined text-xl">login</span>
                </button>
            </form>

            <div class="mt-12 text-center">
                <p class="text-[0.7rem] text-slate-400 font-bold uppercase tracking-widest leading-relaxed">
                    © {{ date('Y') }} ARS CMD - Departamento de TI<br>
                    <span class="text-slate-300">v2.1.0 - Sistema de Carnetización Masiva</span>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
