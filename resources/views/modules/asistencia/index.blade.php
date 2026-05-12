@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-80px)] bg-slate-50 flex items-center justify-center p-6 lg:p-12 overflow-hidden relative">
    
    <!-- Capa de Diseño Abstracto (Elegancia Visual) -->
    <div class="absolute top-0 left-0 w-full h-full pointer-events-none opacity-40">
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-emerald-100 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[400px] h-[400px] bg-blue-100 rounded-full blur-[120px]"></div>
    </div>

    <div class="w-full max-w-5xl grid grid-cols-12 gap-8 relative z-10">
        
        <!-- Columna Izquierda: Perfil y Estado -->
        <div class="col-span-12 lg:col-span-4 flex flex-col gap-6">
            <!-- Perfil Premium -->
            <div class="bg-white/70 backdrop-blur-2xl border border-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50">
                <div class="flex flex-col items-center text-center">
                    <div class="relative mb-6">
                        <div class="w-28 h-28 bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2rem] flex items-center justify-center text-white text-4xl font-black shadow-2xl rotate-3">
                            {{ substr($empleado->nombre_completo, 0, 1) }}
                        </div>
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 border-4 border-white rounded-full flex items-center justify-center text-white shadow-lg animate-bounce">
                            <i class="ph-fill ph-check text-sm"></i>
                        </div>
                    </div>
                    
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight leading-tight uppercase">{{ $empleado->nombre_completo }}</h2>
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mt-2">{{ $empleado->cargo->nombre }}</p>
                    
                    <div class="mt-8 w-full space-y-3">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[9px] font-black text-slate-400 uppercase">ID Empleado</span>
                            <span class="text-xs font-black text-slate-900">{{ $empleado->codigo_empleado }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[9px] font-black text-slate-400 uppercase">Tu Supervisor</span>
                            <span class="text-xs font-black text-slate-900">{{ $empleado->supervisor->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Horario Elegante -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-32 h-32 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2 blur-2xl group-hover:bg-white/10 transition-colors"></div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] opacity-40 mb-6">Jornada Asignada</h3>
                
                <div class="flex justify-between items-center">
                    <div class="flex flex-col gap-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Entrada</span>
                        <span class="text-xl font-black">{{ \Carbon\Carbon::parse($empleado->turno->entrada_esperada)->format('h:i A') }}</span>
                    </div>
                    <div class="w-px h-10 bg-white/10"></div>
                    <div class="flex flex-col gap-1 text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Salida</span>
                        <span class="text-xl font-black">{{ \Carbon\Carbon::parse($empleado->turno->salida_esperada)->format('h:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Reloj y Acciones -->
        <div class="col-span-12 lg:col-span-8 flex flex-col gap-8">
            <!-- Consola de Reloj Central -->
            <div class="bg-white/40 backdrop-blur-3xl border border-white/50 rounded-[3.5rem] p-12 shadow-2xl shadow-slate-200/50 flex flex-col items-center justify-center relative overflow-hidden h-full">
                
                <div class="text-center relative z-10">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.5em] mb-4" id="clock-label">
                        {{ $registro && $registro->inicio_almuerzo && !$registro->fin_almuerzo ? 'Tiempo Restante de Almuerzo' : now()->translatedFormat('l, d F Y') }}
                    </p>
                    
                    <div class="flex items-baseline justify-center gap-2">
                        <h1 class="text-8xl md:text-9xl font-[900] text-slate-900 tracking-tighter" id="current-time">00:00</h1>
                        <span class="text-3xl font-black text-emerald-500" id="current-seconds">:00</span>
                    </div>
                    
                    @if($registro && $registro->hora_entrada && !$registro->hora_salida)
                        @if($registro->inicio_almuerzo && !$registro->fin_almuerzo)
                            <div class="mt-8 inline-flex items-center gap-4 px-6 py-3 bg-blue-50 border border-blue-100 rounded-2xl animate-pulse">
                                <i class="ph-bold ph-timer text-blue-600"></i>
                                <span class="text-xs font-black text-blue-700 uppercase tracking-widest">Buen provecho, {{ explode(' ', $empleado->nombre_completo)[0] }}</span>
                            </div>
                        @else
                            <div class="mt-8 inline-flex items-center gap-4 px-6 py-3 bg-emerald-50 border border-emerald-100 rounded-2xl">
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <span class="text-xs font-black text-emerald-700 uppercase tracking-widest">En Turno: {{ floor($registro->minutos_trabajados_actuales / 60) }}h {{ $registro->minutos_trabajados_actuales % 60 }}m acumulados</span>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Grid de Acciones Minimalista -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16 w-full relative z-10">
                    <!-- ENTRADA -->
                    <form action="{{ route('asistencia.marcar') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="tipo" value="entrada">
                        <button type="submit" 
                                {{ $registro && $registro->hora_entrada ? 'disabled' : '' }}
                                class="w-full aspect-square rounded-[2rem] flex flex-col items-center justify-center gap-4 transition-all duration-500 {{ $registro && $registro->hora_entrada ? 'bg-slate-50 text-slate-300' : 'bg-white text-slate-900 shadow-xl shadow-slate-200/50 hover:bg-slate-900 hover:text-white hover:-translate-y-2 group' }}">
                            <i class="ph ph-sign-in text-4xl group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Entrada</span>
                            @if($registro && $registro->hora_entrada)
                            <span class="text-[9px] font-bold opacity-40">{{ $registro->hora_entrada->format('h:i A') }}</span>
                            @endif
                        </button>
                    </form>

                    <!-- ALMUERZO -->
                    <form action="{{ route('asistencia.marcar') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="tipo" value="inicio_almuerzo">
                        <button type="submit" 
                                {{ ($registro && $registro->inicio_almuerzo) || (!$registro || !$registro->hora_entrada) || ($registro && $registro->hora_salida) ? 'disabled' : '' }}
                                class="w-full aspect-square rounded-[2rem] flex flex-col items-center justify-center gap-4 transition-all duration-500 {{ ($registro && $registro->inicio_almuerzo) || (!$registro || !$registro->hora_entrada) || ($registro && $registro->hora_salida) ? 'bg-slate-50 text-slate-300' : 'bg-white text-slate-900 shadow-xl shadow-slate-200/50 hover:bg-blue-600 hover:text-white hover:-translate-y-2 group' }}">
                            <i class="ph ph-fork-knife text-4xl group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Almuerzo</span>
                            @if($registro && $registro->inicio_almuerzo)
                            <span class="text-[9px] font-bold opacity-40">{{ $registro->inicio_almuerzo->format('h:i A') }}</span>
                            @endif
                        </button>
                    </form>

                    <!-- FIN ALMUERZO -->
                    <form action="{{ route('asistencia.marcar') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="tipo" value="fin_almuerzo">
                        <button type="submit" 
                                {{ ($registro && $registro->fin_almuerzo) || (!$registro || !$registro->inicio_almuerzo) || ($registro && $registro->hora_salida) ? 'disabled' : '' }}
                                class="w-full aspect-square rounded-[2rem] flex flex-col items-center justify-center gap-4 transition-all duration-500 {{ ($registro && $registro->fin_almuerzo) || (!$registro || !$registro->inicio_almuerzo) || ($registro && $registro->hora_salida) ? 'bg-slate-50 text-slate-300' : 'bg-white text-slate-900 shadow-xl shadow-slate-200/50 hover:bg-emerald-600 hover:text-white hover:-translate-y-2 group' }}">
                            <i class="ph ph-bowl-food text-4xl group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Fin Almuerzo</span>
                        </button>
                    </form>

                    <!-- SALIDA -->
                    <form action="{{ route('asistencia.marcar') }}" method="POST" class="h-full">
                        @csrf
                        <input type="hidden" name="tipo" value="salida">
                        <button type="submit" 
                                {{ ($registro && $registro->hora_salida) || (!$registro || !$registro->hora_entrada) ? 'disabled' : '' }}
                                class="w-full aspect-square rounded-[2rem] flex flex-col items-center justify-center gap-4 transition-all duration-500 {{ ($registro && $registro->hora_salida) || (!$registro || !$registro->hora_entrada) ? 'bg-slate-50 text-slate-300' : 'bg-white text-rose-600 shadow-xl shadow-slate-200/50 hover:bg-rose-600 hover:text-white hover:-translate-y-2 group' }}">
                            <i class="ph ph-sign-out text-4xl group-hover:scale-110 transition-transform"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Salida</span>
                            @if($registro && $registro->hora_salida)
                            <span class="text-[9px] font-bold opacity-40">{{ $registro->hora_salida->format('h:i A') }}</span>
                            @endif
                        </button>
                    </form>
                </div>

                <!-- Historial Link -->
                <div class="mt-12 flex items-center justify-center w-full">
                    <a href="{{ route('asistencia.historial') }}" class="group flex items-center gap-3 text-slate-400 hover:text-slate-900 transition-colors">
                        <span class="text-[10px] font-black uppercase tracking-[0.3em]">Consultar Bitácora</span>
                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center group-hover:bg-slate-900 group-hover:text-white transition-all">
                            <i class="ph-bold ph-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: JUSTIFICACIÓN DE TURNO OLVIDADO (Mantenemos Funcionalidad) -->
    @if(isset($pendienteJustificar))
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xl">
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-lg p-10 relative overflow-hidden animate-in zoom-in duration-300">
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-amber-50 rounded-full blur-3xl"></div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center mb-6">
                    <i class="ph ph-warning-octagon text-3xl"></i>
                </div>
                
                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Turno Incompleto</h3>
                <p class="text-sm text-slate-500 font-medium leading-relaxed mb-8">
                    Se detectó un registro abierto el <span class="font-black text-slate-900">{{ $pendienteJustificar->fecha->format('d/m/Y') }}</span>. 
                    Por favor, completa la información para continuar.
                </p>

                <form action="{{ route('asistencia.justificar', $pendienteJustificar->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Hora de Salida Real</label>
                        <input type="time" name="hora_salida_estimada" required
                               class="w-full bg-slate-50 border-slate-100 rounded-2xl p-4 font-bold text-slate-900 focus:ring-amber-500 focus:border-amber-500">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Motivo / Justificación</label>
                        <textarea name="justificacion" rows="3" required placeholder="Describe brevemente lo ocurrido..."
                                  class="w-full bg-slate-50 border-slate-100 rounded-2xl p-4 text-sm font-medium focus:ring-amber-500 focus:border-amber-500"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white p-5 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-200">
                        Regularizar Turno
                    </button>
                </form>
            </div>
        </div>
    </div>
    <style>body { overflow: hidden; }</style>
    @endif
</div>

<script>
    // Configuración del Almuerzo
    const enAlmuerzo = @json($registro && $registro->inicio_almuerzo && !$registro->fin_almuerzo);
    const inicioAlmuerzo = @json($registro && $registro->inicio_almuerzo ? $registro->inicio_almuerzo->toIso8601String() : null);
    const minutosAlmuerzo = @json($empleado->turno->minutos_almuerzo ?? 60);

    function updateClock() {
        const now = new Date();
        
        if (enAlmuerzo && inicioAlmuerzo) {
            const inicio = new Date(inicioAlmuerzo);
            const fin = new Date(inicio.getTime() + (minutosAlmuerzo * 60 * 1000));
            const diff = fin - now;
            
            if (diff > 0) {
                const h = Math.floor(diff / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);
                
                document.getElementById('current-time').textContent = (h > 0 ? h + ':' : '') + m.toString().padStart(2, '0');
                document.getElementById('current-seconds').innerHTML = `<span class="text-xl opacity-40 ml-1">:${s.toString().padStart(2, '0')}</span><span class="text-2xl ml-2 font-black text-blue-600">RESTANTE</span>`;
                return;
            } else {
                // Tiempo excedido
                const overdue = Math.abs(diff);
                const m = Math.floor(overdue / (1000 * 60));
                const s = Math.floor((overdue % (1000 * 60)) / 1000);
                document.getElementById('current-time').textContent = m.toString().padStart(2, '0');
                document.getElementById('current-seconds').innerHTML = `<span class="text-xl opacity-40 ml-1">:${s.toString().padStart(2, '0')}</span><span class="text-2xl ml-2 font-black text-rose-600">EXCEDIDO</span>`;
                document.getElementById('clock-label').textContent = 'TIEMPO DE ALMUERZO EXCEDIDO';
                return;
            }
        }

        // Formato 12 horas con AM/PM (Reloj normal)
        let hours = now.getHours();
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12;
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = ':' + now.getSeconds().toString().padStart(2, '0');
        
        document.getElementById('current-time').textContent = hours.toString().padStart(2, '0') + ':' + minutes;
        document.getElementById('current-seconds').innerHTML = `<span class="text-xl opacity-40 ml-1">${seconds}</span><span class="text-2xl ml-2 font-black">${ampm}</span>`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Auditoría UX: Evitar doble submit y mostrar loading
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            if(btn) {
                btn.disabled = true;
                btn.innerHTML = `
                    <div class="flex flex-col items-center gap-2">
                        <i class="ph-bold ph-circle-notch animate-spin text-2xl"></i>
                    </div>
                `;
            }
        });
    });
</script>
@endsection
