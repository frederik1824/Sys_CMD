<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Restringido - CMD</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(209, 213, 219, 0.3);
        }
        .bg-mesh {
            background-color: #ffffff;
            background-image: 
                radial-gradient(at 0% 0%, hsla(225,39%,30%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,20%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(225,39%,10%,1) 0, transparent 50%);
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-6 overflow-hidden">
    <!-- Partículas sutiles de fondo -->
    <div class="absolute inset-0 pointer-events-none opacity-20">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-indigo-500 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-blue-400 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <div class="glass max-w-2xl w-full rounded-[60px] p-16 text-center relative z-10 shadow-2xl shadow-indigo-900/40 border border-white/20 transform transition-all hover:scale-[1.01] duration-500">
        
        <!-- ICONO DINÁMICO -->
        <div class="relative w-40 h-40 mx-auto mb-10">
            <div class="absolute inset-0 bg-rose-500/10 rounded-full blur-2xl animate-ping"></div>
            <div class="relative w-full h-full bg-gradient-to-tr from-rose-600 to-rose-400 rounded-full flex items-center justify-center shadow-2xl shadow-rose-500/30">
                <i class="ph-fill ph-shield-warning text-7xl text-white"></i>
            </div>
        </div>

        <div class="space-y-6 mb-12">
            <span class="inline-block px-6 py-2 bg-rose-50 text-rose-600 rounded-full text-[10px] font-black uppercase tracking-[0.3em] border border-rose-100">
                Error de Autorización 403
            </span>
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter leading-none">
                Acceso Restringido
            </h1>
            <p class="text-slate-500 text-xl font-medium max-w-md mx-auto leading-relaxed">
                Lo sentimos, su usuario <span class="text-indigo-600 font-bold">no tiene los permisos requeridos</span> para acceder a este módulo del sistema.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('portal') }}" class="group bg-slate-900 text-white rounded-[32px] px-12 py-6 text-sm font-black uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center justify-center gap-4 shadow-2xl shadow-slate-900/20 active:scale-95">
                <i class="ph-bold ph-house text-xl transition-transform group-hover:-translate-y-1"></i>
                Volver al Inicio
            </a>
            
            <a href="javascript:history.back()" class="group bg-white/50 hover:bg-white text-slate-600 rounded-[32px] px-12 py-6 text-sm font-black uppercase tracking-widest transition-all flex items-center justify-center gap-4 border border-slate-200 active:scale-95">
                <i class="ph-bold ph-arrow-left text-xl transition-transform group-hover:-translate-x-1"></i>
                Regresar
            </a>
        </div>

        <p class="mt-12 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            Si cree que esto es un error, contacte al administrador de sistemas (IT).
        </p>
    </div>

    <!-- Script para efecto 3D sutil -->
    <script>
        document.addEventListener('mousemove', (e) => {
            const container = document.querySelector('.glass');
            const xAxis = (window.innerWidth / 2 - e.pageX) / 100;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 100;
            container.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
        });
    </script>
</body>
</html>
