@extends('layouts.app')

@section('content')
<div class="p-8 page-transition" x-data="{ activeTab: 'timeline', showModal: false }">
    <!-- Breadcrumbs & Actions -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-3">
            <a href="{{ route('pyp.afiliados.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-900 hover:shadow-md transition-all">
                <i class="ph ph-caret-left font-bold"></i>
            </a>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Expediente Clínico</p>
                <h1 class="text-2xl font-black text-slate-900">{{ $afiliado->nombre_completo }}</h1>
            </div>
        </div>
        <div class="flex gap-3">
            <button @click="showModal = true" class="px-6 py-3 bg-white border border-slate-200 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-600 hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="ph ph-calendar-plus text-lg text-indigo-600"></i> Registrar Interacción
            </button>
            <a href="{{ route('pyp.evaluaciones.create', $afiliado->uuid) }}" class="px-6 py-3 bg-indigo-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-xl shadow-indigo-100">
                <i class="ph ph-stethoscope text-lg"></i> Iniciar Evaluación
            </a>
        </div>
    </div>

    <!-- FOLLOW UP MODAL -->
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div @click.away="showModal = false" class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-100">
            <div class="p-8 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-slate-900">Registrar Contacto</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bitácora de Seguimiento PyP</p>
                </div>
                <button @click="showModal = false" class="text-slate-400 hover:text-rose-500 transition-colors">
                    <i class="ph ph-x-circle text-3xl"></i>
                </button>
            </div>
            <form action="{{ route('pyp.seguimientos.store', $afiliado->uuid) }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Tipo de Contacto</label>
                        <select name="tipo_contacto" required class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl font-bold text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="Llamada Telefónica">Llamada Telefónica</option>
                            <option value="Visita Domiciliaria">Visita Domiciliaria</option>
                            <option value="Cita en Consultorio">Cita en Consultorio</option>
                            <option value="WhatsApp / Mensajería">WhatsApp / Mensajería</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Resultado</label>
                        <select name="resultado" required class="w-full px-4 py-3 bg-slate-50 border-none rounded-xl font-bold text-xs text-slate-700 outline-none focus:ring-2 focus:ring-indigo-500/20">
                            <option value="Contacto Exitoso">Contacto Exitoso</option>
                            <option value="No Contesta">No Contesta</option>
                            <option value="Cita Agendada">Cita Agendada</option>
                            <option value="Paciente Rechaza">Paciente Rechaza</option>
                            <option value="Número Equivocado">Número Equivocado</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Comentarios de la Gestión</label>
                    <textarea name="comentarios" rows="3" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-medium text-xs text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none" placeholder="Detalles de la interacción..."></textarea>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Próximo Seguimiento Sugerido</label>
                    <input type="date" name="proximo_contacto_at" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-xs text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                </div>

                <button type="submit" class="w-full py-5 bg-slate-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200">
                    Guardar Interacción
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar: Patient Identity -->
        <div class="space-y-8">
            <!-- Profile Card -->
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl overflow-hidden">
                @php
                    $exp = $afiliado->pypExpediente;
                    $riskColor = match($exp?->riesgo_nivel) {
                        'Alto' => 'rose',
                        'Moderado' => 'amber',
                        'Bajo' => 'emerald',
                        default => 'slate'
                    };
                @endphp
                <div class="bg-{{ $riskColor }}-500 p-8 flex flex-col items-center relative overflow-hidden transition-colors duration-500">
                    <div class="absolute top-0 right-0 p-4 opacity-20">
                        <i class="ph-fill ph-shield-check text-6xl text-white"></i>
                    </div>
                    <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-[2rem] flex items-center justify-center text-white mb-4 border border-white/30 shadow-2xl">
                        <i class="ph-fill ph-user-circle text-6xl"></i>
                    </div>
                    <h2 class="text-white font-black text-lg text-center mb-1">{{ $afiliado->nombre_completo }}</h2>
                    <p class="text-white/70 text-[10px] font-bold uppercase tracking-widest">{{ $afiliado->cedula }}</p>
                </div>

                <div class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Puntaje Riesgo</p>
                            <p class="text-xl font-black text-slate-900">{{ number_format($exp?->riesgo_score ?? 0, 1) }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Estado</p>
                            <p class="text-xs font-black text-{{ $riskColor }}-600 uppercase">{{ $exp?->riesgo_nivel ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-slate-50">
                            <span class="text-xs font-bold text-slate-400 italic">Edad</span>
                            <span class="text-xs font-black text-slate-700">34 Años</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-50">
                            <span class="text-xs font-bold text-slate-400 italic">Sexo</span>
                            <span class="text-xs font-black text-slate-700">Masculino</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-slate-50">
                            <span class="text-xs font-bold text-slate-400 italic">Condición</span>
                            <span class="text-xs font-black text-slate-700">{{ $exp?->estado_clinico ?? 'Pendiente' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Tags -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Comorbilidades Identificadas</p>
                <div class="flex flex-wrap gap-2">
                    @php
                        $tags = $exp?->enfermedades_json ?? ['Sin Comorbilidades Reportadas'];
                    @endphp
                    @foreach($tags as $tag)
                        <span class="px-3 py-1.5 bg-rose-50 text-rose-600 text-[10px] font-black rounded-lg border border-rose-100 italic">
                            # {{ $tag }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content: Tabs & Data -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Summary Tab Navigation -->
            <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xl flex gap-2 overflow-hidden">
                <button 
                    @click="activeTab = 'timeline'" 
                    :class="activeTab === 'timeline' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-400 hover:text-slate-900'"
                    class="flex-1 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                >
                    Línea de Tiempo
                </button>
                <button 
                    @click="activeTab = 'evaluations'" 
                    :class="activeTab === 'evaluations' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-400 hover:text-slate-900'"
                    class="flex-1 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                >
                    Evaluaciones Médicas
                </button>
                <button 
                    @click="activeTab = 'history'" 
                    :class="activeTab === 'history' ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'text-slate-400 hover:text-slate-900'"
                    class="flex-1 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all"
                >
                    Historial de Riesgo
                </button>
            </div>

            <!-- Tab: Timeline (Clinical Journey) -->
            <div x-show="activeTab === 'timeline'" x-transition class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl relative min-h-[500px]">
                <h4 class="text-xl font-black text-slate-900 mb-10 flex items-center gap-3">
                    <i class="ph-fill ph-clock-counter-clockwise text-indigo-600 text-2xl"></i> Clinical Journey
                </h4>

                <div class="relative space-y-12">
                    <div class="absolute left-6 top-0 bottom-0 w-1 bg-slate-50 rounded-full"></div>
                    @forelse($exp?->seguimientos ?? [] as $seg)
                        <div class="relative flex items-start gap-8 group">
                            <div class="w-12 h-12 bg-white border-4 border-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all z-10 shadow-sm">
                                <i class="ph ph-phone-call text-xl"></i>
                            </div>
                            <div class="flex-1 bg-slate-50 p-6 rounded-3xl group-hover:bg-white group-hover:shadow-2xl transition-all border border-transparent group-hover:border-slate-100">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600 mb-1">{{ $seg->tipo_contacto }}</p>
                                        <h5 class="text-sm font-black text-slate-900">{{ $seg->resultado }}</h5>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 italic bg-white px-3 py-1 rounded-full shadow-sm">{{ $seg->created_at->format('d M, Y') }}</span>
                                </div>
                                <p class="text-xs text-slate-500 leading-relaxed font-medium">{{ $seg->comentarios }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center opacity-30">
                            <i class="ph ph-tray text-6xl mb-4"></i>
                            <p class="text-sm font-bold">Sin seguimientos registrados.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tab: Evaluations List -->
            <div x-show="activeTab === 'evaluations'" x-transition class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl min-h-[500px]">
                <h4 class="text-xl font-black text-slate-900 mb-10 flex items-center gap-3">
                    <i class="ph-fill ph-stethoscope text-indigo-600 text-2xl"></i> Historial de Evaluaciones
                </h4>

                <div class="space-y-4">
                    @forelse($exp?->evaluaciones ?? [] as $eval)
                        <div class="p-6 bg-slate-50 rounded-[1.5rem] border border-slate-100 hover:bg-white hover:shadow-xl transition-all">
                            <div class="flex justify-between items-start">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                                        <i class="ph ph-clipboard-text text-xl"></i>
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-black text-slate-900">Evaluación Médica #{{ $loop->iteration }}</h5>
                                        <p class="text-[10px] font-bold text-slate-400 italic">Médico: {{ $eval->medico->name ?? 'Sistema' }}</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-slate-400 italic">{{ $eval->created_at->format('d/m/Y • h:i A') }}</span>
                            </div>
                            <div class="mt-4 grid grid-cols-4 gap-4">
                                <div class="p-3 bg-white rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black uppercase text-slate-300">P. Sistólica</p>
                                    <p class="text-xs font-black text-slate-700">{{ $eval->datos_evaluacion_json['presion_sistolica'] ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black uppercase text-slate-300">Glucosa</p>
                                    <p class="text-xs font-black text-slate-700">{{ $eval->datos_evaluacion_json['glucosa'] ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black uppercase text-slate-300">Peso (kg)</p>
                                    <p class="text-xs font-black text-slate-700">{{ $eval->datos_evaluacion_json['peso'] ?? '-' }}</p>
                                </div>
                                <div class="p-3 bg-white rounded-xl border border-slate-100">
                                    <p class="text-[8px] font-black uppercase text-slate-300">IMC</p>
                                    <p class="text-xs font-black text-indigo-600">
                                        @php
                                            $p = $eval->datos_evaluacion_json['peso'] ?? 0;
                                            $t = $eval->datos_evaluacion_json['talla'] ?? 0;
                                            echo ($p && $t) ? round($p / (($t/100) * ($t/100)), 1) : '-';
                                        @endphp
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 p-4 bg-indigo-50/50 rounded-xl border border-indigo-100">
                                <p class="text-[9px] font-black uppercase text-indigo-400 mb-1">Diagnóstico</p>
                                <p class="text-xs font-medium text-slate-600 italic leading-relaxed">{{ $eval->diagnostico }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-20 text-center opacity-30">
                            <p class="text-sm font-bold">No hay evaluaciones registradas.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tab: Risk History -->
            <div x-show="activeTab === 'history'" x-transition class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-xl min-h-[500px]">
                <h4 class="text-xl font-black text-slate-900 mb-10 flex items-center gap-3">
                    <i class="ph-fill ph-chart-line-up text-indigo-600 text-2xl"></i> Historial de Estratificación
                </h4>
                <div class="flex flex-col items-center justify-center py-20 text-center opacity-30">
                    <i class="ph ph-graph text-6xl mb-4"></i>
                    <p class="text-sm font-bold">Análisis de tendencia en desarrollo.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
