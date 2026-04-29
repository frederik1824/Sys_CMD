<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servidor de Base de Datos Inactivo</title>
    <style>
        :root {
            --bg-color: #0f172a;
            --card-bg: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #38bdf8;
            --accent-hover: #0284c7;
            --error: #ef4444;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }

        /* Animated background grid */
        .grid-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 200vw;
            height: 200vh;
            background-image: 
                linear-gradient(to right, rgba(255,255,255,0.05) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255,255,255,0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            transform: perspective(500px) rotateX(60deg) translateY(-100px) translateZ(-200px);
            animation: grid-move 20s linear infinite;
            z-index: 0;
        }

        @keyframes grid-move {
            0% { transform: perspective(500px) rotateX(60deg) translateY(0) translateZ(-200px); }
            100% { transform: perspective(500px) rotateX(60deg) translateY(50px) translateZ(-200px); }
        }

        .container {
            background-color: var(--card-bg);
            border-radius: 24px;
            padding: 3rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            transform: translateY(20px);
            opacity: 0;
            animation: fade-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes fade-up {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .icon-container {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            position: relative;
        }

        .pulse-ring {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 3px solid var(--error);
            animation: pulse 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
            opacity: 0;
        }

        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.8; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        .database-icon {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border: 2px solid rgba(255,255,255,0.05);
        }

        .database-icon svg {
            width: 50px;
            height: 50px;
            color: var(--error);
            animation: shake 4s ease-in-out infinite;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-3px); }
            20%, 40%, 60%, 80% { transform: translateX(3px); }
        }

        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .instructions {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: left;
            margin-bottom: 2rem;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .instructions h3 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .instructions ul {
            list-style-type: none;
        }

        .instructions li {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 0.8rem;
            color: var(--text-muted);
        }

        .instructions li::before {
            content: '→';
            position: absolute;
            left: 0;
            color: var(--accent);
            font-weight: bold;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background-color: var(--accent);
            color: #0f172a;
            padding: 1rem 2rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px 0 rgba(56, 189, 248, 0.39);
        }

        .btn:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px 0 rgba(56, 189, 248, 0.5);
        }

        .btn svg {
            width: 20px;
            height: 20px;
            transition: transform 0.3s ease;
        }

        .btn:hover svg {
            transform: rotate(180deg);
        }

        .error-code {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(239, 68, 68, 0.1);
            color: var(--error);
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

    </style>
</head>
<body>
    <div class="grid-bg"></div>

    <div class="container">
        <div class="error-code">ERR_CONNECTION_REFUSED</div>
        
        <div class="icon-container">
            <div class="pulse-ring"></div>
            <div class="database-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                    <!-- Un rayo o X para denotar error -->
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 9l-6 6m0-6l6 6" stroke="#ef4444" />
                </svg>
            </div>
        </div>

        <h1>¡Ups! Sin Conexión a la Base de Datos</h1>
        <p>No pudimos establecer una conexión con el servidor MySQL. Parece que el servicio de base de datos no está corriendo en este momento.</p>

        <div class="instructions">
            <h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                ¿Cómo solucionarlo?
            </h3>
            <ul>
                <li>Si usas <strong>XAMPP</strong>, abre el panel de control y haz clic en "Start" junto a MySQL.</li>
                <li>Si usas <strong>Laragon</strong>, haz clic en el botón "Iniciar Todo".</li>
                <li>Si usas <strong>Docker</strong>, verifica que tu contenedor de base de datos esté activo con <code>docker-compose up -d</code>.</li>
                <li>Verifica que los datos en tu archivo <code>.env</code> sean correctos (DB_PORT, DB_HOST).</li>
            </ul>
        </div>

        <a href="javascript:window.location.reload()" class="btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Reintentar Conexión
        </a>
    </div>

    <script>
        // Pequeño efecto 3D al mover el ratón sobre la tarjeta
        document.addEventListener('mousemove', (e) => {
            const container = document.querySelector('.container');
            const xAxis = (window.innerWidth / 2 - e.pageX) / 50;
            const yAxis = (window.innerHeight / 2 - e.pageY) / 50;
            container.style.transform = `perspective(1000px) rotateY(${xAxis}deg) rotateX(${yAxis}deg) translateY(0)`;
        });

        // Resetear la rotación cuando el ratón sale
        document.addEventListener('mouseleave', () => {
            const container = document.querySelector('.container');
            container.style.transform = `perspective(1000px) rotateY(0deg) rotateX(0deg) translateY(0)`;
            container.style.transition = 'transform 0.5s ease';
        });

        // Eliminar transición al entrar para que el seguimiento sea fluido
        document.addEventListener('mouseenter', () => {
            const container = document.querySelector('.container');
            container.style.transition = 'none';
        });
    </script>
</body>
</html>
