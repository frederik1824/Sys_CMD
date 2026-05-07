<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mantenimiento Programado | SysCarnet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-mesh {
            background-color: #0f172a;
            background-image: 
                radial-gradient(at 0% 0%, hsla(210,100%,20%,1) 0, transparent 50%), 
                radial-gradient(at 100% 100%, hsla(220,100%,15%,1) 0, transparent 50%);
        }
        @keyframes scan {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100%); }
        }
        .scanner {
            height: 100%;
            width: 100%;
            background: linear-gradient(to bottom, transparent, rgba(59, 130, 246, 0.2), transparent);
            animation: scan 3s linear infinite;
        }
    </style>
</head>
<body class="bg-mesh min-h-screen flex items-center justify-center p-6 overflow-hidden relative">
    
    <!-- Capa de Decoración -->
    <div class="absolute inset-0 opacity-20">
        <div class="scanner"></div>
    </div>

    <div class="max-w-2xl w-full relative z-10">
        <!-- Logo -->
        <div class="flex justify-center mb-12 animate-in fade-in zoom-in duration-1000">
            <img src="{{ asset('img/Logo.png') }}" alt="Logo" class="h-20 w-auto brightness-0 invert opacity-80">
        </div>

        <!-- Card de Estado -->
        <div class="bg-white/5 backdrop-blur-3xl border border-white/10 rounded-[40px] p-10 lg:p-16 shadow-2xl text-center space-y-10">
            
            <div class="flex justify-center">
                <div class="w-24 h-24 bg-blue-500/10 text-blue-400 rounded-3xl flex items-center justify-center animate-pulse">
                    <i class="ph ph-arrows-clockwise text-5xl"></i>
                </div>
            </div>

            <div class="space-y-4">
                <h1 class="text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                    Actualizando <br>
                    <span class="text-blue-400">Infraestructura</span>
                </h1>
                <p class="text-slate-400 font-medium text-lg lg:px-10">
                    Estamos aplicando mejoras críticas para optimizar tu experiencia operativa. El acceso se restaurará en unos minutos.
                </p>
            </div>

            <!-- Barra de Progreso Simulada/Real -->
            <div class="space-y-4">
                <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 px-2">
                    <span>Sincronizando Archivos</span>
                    <span id="progress-text">75%</span>
                </div>
                <div class="w-full h-3 bg-white/5 rounded-full overflow-hidden border border-white/5">
                    <div id="progress-bar" class="h-full bg-gradient-to-r from-blue-600 to-indigo-500 shadow-[0_0_20px_rgba(37,99,235,0.5)] transition-all duration-1000" style="width: 75%"></div>
                </div>
            </div>

            <div class="pt-6 flex flex-col md:flex-row items-center justify-center gap-6">
                <div class="flex items-center gap-3 px-6 py-3 bg-white/5 rounded-2xl border border-white/5">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Base de Datos Protegida</span>
                </div>
                <div class="flex items-center gap-3 px-6 py-3 bg-white/5 rounded-2xl border border-white/5">
                    <i class="ph ph-shield-check text-blue-400"></i>
                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Rollback Disponible</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-12 text-center text-slate-500 text-xs font-bold uppercase tracking-[0.3em]">
            &copy; {{ date('Y') }} ARS CMD • Ecosistema SysCarnet Master
        </p>
    </div>

    <script>
        // Lógica simple para simular/animar la barra si no hay conexión real
        let progress = 75;
        const bar = document.getElementById('progress-bar');
        const text = document.getElementById('progress-text');
        
        setInterval(() => {
            if (progress < 98) {
                progress += Math.random() * 2;
                bar.style.width = progress + '%';
                text.innerText = Math.floor(progress) + '%';
            }
        }, 5000);

        // Auto-refresh para detectar cuando termina el mantenimiento
        setInterval(() => {
            fetch(window.location.href, { method: 'HEAD' })
                .then(response => {
                    if (response.status === 200) window.location.reload();
                })
                .catch(() => {});
        }, 30000);
    </script>
</body>
</html>
