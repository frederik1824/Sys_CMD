@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 p-6 lg:p-12">
    <!-- Header -->
    <div class="max-w-7xl mx-auto mb-12 flex justify-between items-end">
        <div>
            <span class="text-[10px] font-black text-primary uppercase tracking-[0.3em] mb-2 block">Administración Central</span>
            <h1 class="text-4xl font-[900] text-slate-900 tracking-tighter">Configuración de Asistencia</h1>
        </div>
        <div class="flex gap-4">
            <button onclick="openModal('modalTurno')" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-primary transition-all shadow-xl shadow-slate-200">
                Nuevo Turno
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-12 gap-8">
        
        <!-- Tabs / Navegación Lateral -->
        <div class="col-span-12 lg:col-span-3">
            <div class="bg-white rounded-[2.5rem] p-4 shadow-xl shadow-slate-200/50 sticky top-32">
                <nav class="flex flex-col gap-2" id="config-tabs">
                    <button onclick="switchTab('turnos')" id="btn-turnos" class="tab-btn flex items-center gap-4 p-4 rounded-2xl bg-slate-900 text-white font-black text-xs uppercase tracking-widest transition-all">
                        <i class="ph ph-clock-user text-xl"></i>
                        Gestión de Turnos
                    </button>
                    <button onclick="switchTab('personal')" id="btn-personal" class="tab-btn flex items-center gap-4 p-4 rounded-2xl text-slate-400 hover:bg-slate-50 font-black text-xs uppercase tracking-widest transition-all">
                        <i class="ph ph-users text-xl"></i>
                        Asignación Personal
                    </button>
                    <button onclick="switchTab('politicas')" id="btn-politicas" class="tab-btn flex items-center gap-4 p-4 rounded-2xl text-slate-400 hover:bg-slate-50 font-black text-xs uppercase tracking-widest transition-all">
                        <i class="ph ph-shield-check text-xl"></i>
                        Políticas Globales
                    </button>
                </nav>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="col-span-12 lg:col-span-9 space-y-8">
            
            <!-- SECCIÓN: TURNOS -->
            <div id="tab-turnos" class="tab-content space-y-8">
                <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-white">
                    <div class="flex justify-between items-center mb-10">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Turnos Laborales</h2>
                        <p class="text-xs text-slate-400 font-medium italic">Define los horarios y días de operación.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($turnos as $turno)
                        <div class="group bg-slate-50 rounded-[2.5rem] p-8 border border-slate-100 hover:border-primary hover:bg-white transition-all cursor-pointer relative overflow-hidden" onclick="editTurno({{ json_encode($turno) }})">
                            <div class="absolute right-6 top-6 opacity-10 group-hover:opacity-100 transition-opacity">
                                <i class="ph ph-pencil-simple-line text-2xl text-primary"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-4">{{ $turno->nombre }}</h3>
                            <div class="flex items-center gap-6 mb-6">
                                <div class="flex flex-col"><span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Entrada</span><span class="text-base font-black text-slate-900">{{ \Carbon\Carbon::parse($turno->entrada_esperada)->format('h:i A') }}</span></div>
                                <div class="w-px h-8 bg-slate-200"></div>
                                <div class="flex flex-col"><span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Salida</span><span class="text-base font-black text-slate-900">{{ \Carbon\Carbon::parse($turno->salida_esperada)->format('h:i A') }}</span></div>
                            </div>
                            <div class="flex gap-1.5 mt-auto">
                                @php $diasKeys = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']; @endphp
                                @foreach(['L', 'M', 'X', 'J', 'V', 'S', 'D'] as $index => $label)
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-black {{ $turno->{$diasKeys[$index]} ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-400' }}">{{ $label }}</div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: ASIGNACIÓN PERSONAL -->
            <div id="tab-personal" class="tab-content hidden space-y-8">
                <div class="bg-white rounded-[3rem] p-10 shadow-xl shadow-slate-200/50 border border-white">
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight mb-8">Asignación de Horarios Individuales</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left border-b border-slate-100">
                                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Representante</th>
                                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cargo</th>
                                    <th class="pb-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Turno Asignado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($empleados as $emp)
                                <tr>
                                    <td class="py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center font-black text-slate-500">{{ substr($emp->nombre_completo, 0, 1) }}</div>
                                            <div>
                                                <div class="text-sm font-black text-slate-900">{{ $emp->nombre_completo }}</div>
                                                <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $emp->codigo_empleado }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-6">
                                        <span class="px-3 py-1 bg-slate-50 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-wider">{{ $emp->cargo->nombre }}</span>
                                    </td>
                                    <td class="py-6">
                                        <select onchange="updateEmpleadoTurno({{ $emp->id }}, this.value)" 
                                                class="bg-slate-50 border-none rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-primary transition-all">
                                            @foreach($turnos as $t)
                                            <option value="{{ $t->id }}" {{ $emp->turno_id == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN: POLÍTICAS -->
            <div id="tab-politicas" class="tab-content hidden space-y-8">
                <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden">
                    <div class="absolute right-[-10%] top-[-20%] w-64 h-64 bg-primary/20 rounded-full blur-[100px]"></div>
                    <h2 class="text-2xl font-black mb-8 relative z-10">Políticas Globales de Control</h2>
                    <form action="{{ route('asistencia.configuracion.global') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Tolerancia (Minutos)</label>
                            <input type="number" name="tolerancia" value="{{ $configs['tolerancia'] }}" class="bg-white/10 border-white/20 rounded-2xl p-4 text-white font-black focus:ring-primary">
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Almuerzo Estándar</label>
                            <input type="number" name="almuerzo_defecto" value="{{ $configs['almuerzo_defecto'] }}" class="bg-white/10 border-white/20 rounded-2xl p-4 text-white font-black focus:ring-primary">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-primary text-white p-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:brightness-110 transition-all">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- MODAL: GESTIÓN DE TURNO -->
<div id="modalTurno" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-2xl p-10 relative overflow-hidden">
        <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-8" id="modalTitle">Nuevo Turno Laboral</h3>
        <form action="{{ route('asistencia.configuracion.save_turno') }}" method="POST" id="formTurno">
            @csrf
            <input type="hidden" name="id" id="turno_id">
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="col-span-2"><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Nombre del Turno</label><input type="text" name="nombre" id="turno_nombre" required class="w-full bg-slate-50 border-slate-100 rounded-2xl p-4 font-bold text-slate-900"></div>
                <div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Hora de Entrada</label><input type="time" name="entrada_esperada" id="turno_entrada" required class="w-full bg-slate-50 border-slate-100 rounded-2xl p-4 font-bold text-slate-900"></div>
                <div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Hora de Salida</label><input type="time" name="salida_esperada" id="turno_salida" required class="w-full bg-slate-50 border-slate-100 rounded-2xl p-4 font-bold text-slate-900"></div>
                <div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Tolerancia (Min)</label><input type="number" name="tolerancia_minutos" id="turno_tolerancia" required class="w-full bg-slate-50 border-slate-100 rounded-2xl p-4 font-bold text-slate-900"></div>
                <div><label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 block">Almuerzo (Min)</label><input type="number" name="minutos_almuerzo" id="turno_almuerzo" required class="w-full bg-slate-50 border-slate-100 rounded-2xl p-4 font-bold text-slate-900"></div>
            </div>
            <div class="mb-10">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 block">Días Laborales</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'] as $dia)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="{{ $dia }}" id="check_{{ $dia }}" value="1" class="hidden peer">
                        <div class="px-5 py-3 rounded-xl border border-slate-100 bg-slate-50 text-[10px] font-black uppercase peer-checked:bg-slate-900 peer-checked:text-white transition-all">{{ substr($dia, 0, 3) }}</div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="closeModal('modalTurno')" class="flex-1 bg-slate-100 text-slate-400 p-5 rounded-2xl font-black uppercase tracking-widest">Cancelar</button>
                <button type="submit" class="flex-[2] bg-slate-900 text-white p-5 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-slate-200">Guardar Turno</button>
            </div>
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.getElementById('tab-' + tab).classList.remove('hidden');
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('bg-slate-900', 'text-white');
            el.classList.add('text-slate-400', 'hover:bg-slate-50');
        });
        document.getElementById('btn-' + tab).classList.remove('text-slate-400', 'hover:bg-slate-50');
        document.getElementById('btn-' + tab).classList.add('bg-slate-900', 'text-white');
    }

    function updateEmpleadoTurno(empId, turnoId) {
        fetch("{{ route('asistencia.configuracion.asignar_turno') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ empleado_id: empId, turno_id: turnoId })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Notificación sutil (Toast)
                console.log(data.message);
            }
        });
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
        document.getElementById('formTurno').reset();
    }

    function editTurno(turno) {
        document.getElementById('modalTitle').textContent = 'Editar Turno: ' + turno.nombre;
        document.getElementById('turno_id').value = turno.id;
        document.getElementById('turno_nombre').value = turno.nombre;
        document.getElementById('turno_entrada').value = turno.entrada_esperada;
        document.getElementById('turno_salida').value = turno.salida_esperada;
        document.getElementById('turno_tolerancia').value = turno.tolerancia_minutos;
        document.getElementById('turno_almuerzo').value = turno.minutos_almuerzo;
        const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        dias.forEach(dia => { document.getElementById('check_' + dia).checked = !!turno[dia]; });
        openModal('modalTurno');
    }
</script>
@endsection
