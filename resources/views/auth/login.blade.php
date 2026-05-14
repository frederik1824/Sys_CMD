<!DOCTYPE html>
<html class="light" lang="es">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CMD Operaciones | Health ERP</title>
    
    <!-- Fuentes Premium -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Iconos Phosphor -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'cmd-blue': '#00346f',
                        'cmd-yellow': '#ffcc00',
                        'cmd-indigo': '#4f46e5',
                    },
                    fontFamily: {
                        'outfit': ['Outfit', 'sans-serif'],
                        'inter': ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            overflow: hidden;
        }

        .bg-layer-1 {
            background: radial-gradient(circle at top right, #ffcc00, transparent 40%),
                        radial-gradient(circle at bottom left, #00346f, transparent 40%);
        }

        .main-container {
            width: 100%;
            max-width: 1200px;
            height: 750px;
            background: #ffffff;
            border-radius: 60px;
            box-shadow: 0 40px 100px -20px rgba(0,0,0,0.4);
            overflow: hidden;
            display: flex;
        }

        .visual-side {
            flex: 1;
            background: linear-gradient(135deg, #4f46e5 0%, #00346f 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            color: white;
        }

        .form-side {
            width: 550px;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
            border-radius: 60px 0 0 60px;
            margin-left: -60px;
            z-index: 10;
            box-shadow: -20px 0 50px rgba(0,0,0,0.05);
        }

        .doctor-mask {
            width: 320px;
            height: 320px;
            border-radius: 100px;
            background-image: url('/brain/7cadaca8-14ed-47fb-90d4-7f9783fe22bf/cmd_medical_professional_1778179543777.png');
            background-size: cover;
            background-position: center;
            border: 15px solid rgba(255, 255, 255, 0.1);
            margin: auto;
            position: relative;
        }

        .input-premium {
            background: #f8fafc;
            border: 2px solid #f1f5f9;
            border-radius: 30px;
            padding: 18px 24px 18px 56px;
            font-size: 0.875rem;
            font-weight: 700;
            color: #0f172a;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-premium:focus {
            background: #ffffff;
            border-color: #ffcc00;
            box-shadow: 0 10px 20px -10px rgba(255, 204, 0, 0.3);
            transform: translateY(-1px);
            outline: none;
            ring: none;
        }

        .btn-action {
            background: #4f46e5;
            border-radius: 30px;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            background: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -5px rgba(79, 70, 229, 0.5);
        }

        @keyframes slide-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-up {
            animation: slide-up 0.8s ease-out forwards;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-layer-1 p-6">
    
    <!-- Background Decor -->
    <div class="absolute inset-0 z-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-cmd-coral/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-[600px] h-[600px] bg-cmd-blue/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Main Login Card -->
    <main class="main-container relative z-10 animate-up">
        
        <!-- Left: Visual Section -->
        <section class="visual-side">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-md border border-white/30">
                    <i class="ph-fill ph-stethoscope text-white text-xl"></i>
                </div>
                <h1 class="text-xl font-black font-outfit uppercase tracking-widest">CMD Operaciones</h1>
            </div>

            <div class="relative">
                <!-- Abstract Elements (Circles from ref) -->
                <div class="absolute -top-10 -left-10 w-24 h-24 border-8 border-cmd-yellow/30 rounded-full"></div>
                <div class="absolute -bottom-6 -right-6 w-16 h-16 bg-white/20 rounded-full backdrop-blur-sm"></div>
                
                <div class="doctor-mask shadow-2xl"></div>
                
                <div class="mt-12 text-center space-y-4">
                    <h2 class="text-4xl font-extrabold font-outfit">Compromiso con la Salud</h2>
                    <p class="text-white/60 text-sm font-medium max-w-[300px] mx-auto leading-relaxed">
                        Gestionando la eficiencia operativa para ARS CMD y sus afiliados.
                    </p>
                </div>
            </div>

            <div class="text-[10px] font-bold text-white/30 uppercase tracking-[0.4em]">
                © {{ date('Y') }} ARS CMD • TECNOLOGÍA
            </div>
        </section>

        <!-- Right: Form Section -->
        <section class="form-side">
            <div class="mb-12 text-center">
                <img src="/img/Logo.png" alt="ARS CMD Logo" class="h-20 mx-auto mb-10">
                <div class="border-b-2 border-slate-50 pb-6">
                    <h3 class="text-2xl font-black text-cmd-blue uppercase tracking-tighter">Acceso Central</h3>
                    <p class="text-sm font-medium text-slate-400">Identifícate para gestionar operaciones</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 rounded-2xl flex items-center gap-4 animate-[bounce_2s_infinite]">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="ph-fill ph-warning-octagon text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-red-600 uppercase tracking-widest leading-none mb-1">Credenciales Inválidas</p>
                        <p class="text-[11px] font-bold text-red-400 leading-tight">
                            {{ $errors->first() }}
                        </p>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block ml-2">Usuario Corporativo</label>
                    <div class="relative group">
                        <i class="ph-bold ph-user absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-cmd-yellow text-xl transition-colors"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full input-premium @error('email') !border-red-300 !bg-red-50 @enderror"
                            placeholder="ej. juan.perez@arscmd.com.do">
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center px-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Contraseña</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-[9px] font-bold text-cmd-yellow uppercase tracking-tighter hover:underline">Recuperar</a>
                        @endif
                    </div>
                    <div class="relative group">
                        <i class="ph-bold ph-lock-key absolute left-6 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-cmd-yellow text-xl transition-colors"></i>
                        <input id="password" type="password" name="password" required
                            class="w-full input-premium @error('email') !border-red-300 !bg-red-50 @enderror"
                            placeholder="••••••••••••">
                    </div>
                </div>

                <div class="flex items-center">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-5 h-5 rounded-lg border-2 border-slate-100 text-cmd-indigo focus:ring-0">
                        <span class="ml-3 text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-800 transition-colors">Recordar mi sesión</span>
                    </label>
                </div>

                <button type="submit" class="w-full btn-action text-white font-black py-5 text-xs uppercase tracking-[0.2em] flex items-center justify-center gap-4 group">
                    <span>Entrar al Sistema</span>
                    <i class="ph-bold ph-arrow-right text-lg group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div class="mt-12 flex justify-between items-center">
                <div class="flex gap-4">
                    <i class="ph ph-facebook-logo text-slate-200 hover:text-cmd-yellow cursor-pointer text-xl transition-colors"></i>
                    <i class="ph ph-instagram-logo text-slate-200 hover:text-cmd-yellow cursor-pointer text-xl transition-colors"></i>
                    <i class="ph ph-linkedin-logo text-slate-200 hover:text-cmd-yellow cursor-pointer text-xl transition-colors"></i>
                </div>
                <div class="text-[9px] font-black text-slate-200 uppercase tracking-tighter">
                    v{{ $systemVersion }} • SIG • ARS CMD
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Decoration -->
    <div class="absolute bottom-10 left-10 text-white/10">
        <i class="ph ph-heart-beat text-9xl"></i>
    </div>
</body>
</html>
