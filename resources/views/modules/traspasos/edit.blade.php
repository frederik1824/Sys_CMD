@extends('layouts.app')

@section('content')
<div class="p-8 max-w-6xl mx-auto">
    <!-- Breadcrumbs & Navigation -->
    <nav class="flex items-center gap-4 mb-8 text-[11px] font-black uppercase tracking-widest text-slate-400">
        <a href="{{ route('traspasos.index') }}" class="hover:text-primary transition-colors flex items-center gap-2">
            <i class="ph-bold ph-arrow-left"></i> Volver a Bandeja
        </a>
        <span>/</span>
        <span class="text-slate-900">Detalle de Traspaso</span>
    </nav>

    <form action="{{ route('traspasos.update', $traspaso->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Primary Data -->
            <div class="lg:col-span-2 space-y-8">
                <div class="glass-card p-10 rounded-[3rem] shadow-xl bg-white border border-slate-100">
                    <div class="flex items-center gap-6 mb-10">
                        <div class="w-20 h-20 bg-slate-900 text-white rounded-[2rem] flex items-center justify-center text-3xl shadow-2xl shadow-slate-200">
                            <i class="ph-fill ph-user-focus"></i>
                        </div>
                        <div>
                            <span class="text-xs font-black uppercase tracking-[0.2em] text-primary mb-1 block">Expediente de Afiliado</span>
                            <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase">{{ $traspaso->nombre_afiliado }}</h1>
                            <p class="text-slate-400 font-bold mt-1 tracking-tight">{{ $traspaso->cedula_afiliado }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Nombre -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Nombre Completo</label>
                            <div class="relative">
                                <i class="ph ph-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                                <input type="text" name="nombre_afiliado" value="{{ old('nombre_afiliado', $traspaso->nombre_afiliado) }}" 
                                       class="w-full bg-slate-50 border-none rounded-3xl pl-14 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                            </div>
                        </div>

                        <!-- Cedula -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Cédula de Identidad</label>
                            <div class="relative">
                                <i class="ph ph-identification-card absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                                <input type="text" name="cedula_afiliado" value="{{ old('cedula_afiliado', $traspaso->cedula_afiliado) }}" 
                                       class="w-full bg-slate-50 border-none rounded-3xl pl-14 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                            </div>
                        </div>

                        <!-- Solicitud EPBD -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Número de Solicitud (EPBD)</label>
                            <div class="relative">
                                <i class="ph ph-hash absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                                <input type="text" name="numero_solicitud_epbd" value="{{ old('numero_solicitud_epbd', $traspaso->numero_solicitud_epbd) }}" 
                                       class="w-full bg-slate-50 border-none rounded-3xl pl-14 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all text-blue-600">
                            </div>
                        </div>

                        <!-- Cantidad Dependientes -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Cantidad de Dependientes</label>
                            <div class="relative flex items-center gap-4">
                                <div class="relative flex-1">
                                    <i class="ph ph-users absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                                    <input type="number" name="cantidad_dependientes" value="{{ old('cantidad_dependientes', $traspaso->cantidad_dependientes ?? 0) }}" 
                                           class="w-full bg-slate-50 border-none rounded-3xl pl-14 pr-6 py-4 text-sm font-black focus:ring-2 focus:ring-primary/20 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fechas de Proceso -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-10 pt-10 border-t border-slate-50">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Fecha de Solicitud</label>
                            <div class="relative">
                                <i class="ph ph-calendar absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                                <input type="date" name="fecha_solicitud" value="{{ old('fecha_solicitud', $traspaso->fecha_solicitud ? $traspaso->fecha_solicitud->format('Y-m-d') : '') }}" 
                                       class="w-full bg-slate-50 border-none rounded-3xl pl-14 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Fecha Envío EPBD</label>
                            <div class="relative">
                                <i class="ph ph-calendar-check absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                                <input type="date" name="fecha_envio_epbd" value="{{ old('fecha_envio_epbd', $traspaso->fecha_envio_epbd ? $traspaso->fecha_envio_epbd->format('Y-m-d') : '') }}" 
                                       class="w-full bg-slate-50 border-none rounded-3xl pl-14 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Solicitante (Si existe) -->
                <div class="glass-card p-10 rounded-[3rem] shadow-xl bg-white border border-slate-100">
                    <h3 class="text-sm font-black uppercase tracking-widest text-slate-900 mb-8 flex items-center gap-3">
                        <i class="ph-bold ph-users-three text-xl text-primary"></i>
                        Información del Solicitante
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Nombre Solicitante (Si aplica)</label>
                            <input type="text" name="nombre_solicitante" value="{{ old('nombre_solicitante', $traspaso->nombre_solicitante) }}" 
                                   class="w-full bg-slate-50 border-none rounded-3xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Cédula Solicitante</label>
                            <input type="text" name="cedula_solicitante" value="{{ old('cedula_solicitante', $traspaso->cedula_solicitante) }}" 
                                   class="w-full bg-slate-50 border-none rounded-3xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Status & Assignment -->
            <div class="space-y-8">
                <!-- Status Card -->
                <div class="glass-card p-8 rounded-[3rem] shadow-xl bg-slate-900 text-white border-none">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Estado de la Solicitud</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-3 block">Estado Actual</label>
                            <select name="estado_id" class="w-full bg-white/5 border-white/10 rounded-2xl px-6 py-4 text-sm font-black focus:ring-2 focus:ring-primary/20 text-white">
                                @foreach($estados as $est)
                                    <option value="{{ $est->id }}" {{ old('estado_id', $traspaso->estado_id) == $est->id ? 'selected' : '' }} class="text-slate-900">
                                        {{ $est->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-3 block">Agente Asignado</label>
                            <select name="agente_id" class="w-full bg-white/5 border-white/10 rounded-2xl px-6 py-4 text-sm font-black focus:ring-2 focus:ring-primary/20 text-white">
                                @foreach($agentes as $ag)
                                    <option value="{{ $ag->id }}" {{ old('agente_id', $traspaso->agente_id) == $ag->id ? 'selected' : '' }} class="text-slate-900 uppercase">
                                        {{ $ag->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-6 border-t border-white/10 space-y-4">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative w-12 h-6 bg-slate-700 rounded-full transition-colors group-hover:bg-slate-600">
                                    <input type="checkbox" name="verificado" value="1" {{ old('verificado', $traspaso->verificado) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-6 peer-checked:bg-emerald-400"></div>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-300">Verificado Unipago</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer group">
                                <div class="relative w-12 h-6 bg-slate-700 rounded-full transition-colors group-hover:bg-slate-600">
                                    <input type="checkbox" name="pendiente_carga_documento" value="1" {{ old('pendiente_carga_documento', $traspaso->pendiente_carga_documento) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-6 peer-checked:bg-amber-400"></div>
                                </div>
                                <span class="text-[10px] font-black uppercase tracking-widest text-slate-300">Pend. Carga Doc</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Efectividad Card -->
                <div class="glass-card p-8 rounded-[3rem] shadow-xl bg-white border border-slate-100">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-8">Datos de Efectividad</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-3 block">Fecha de Efectividad</label>
                            <input type="date" name="fecha_efectivo" value="{{ old('fecha_efectivo', $traspaso->fecha_efectivo ? $traspaso->fecha_efectivo->format('Y-m-d') : '') }}" 
                                   class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-black focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-3 block">Periodo (YYYY-MM)</label>
                            <input type="text" name="periodo_efectivo" value="{{ old('periodo_efectivo', $traspaso->periodo_efectivo) }}" placeholder="Ej: 2024-05"
                                   class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-black focus:ring-2 focus:ring-primary/20 font-mono">
                        </div>
                    </div>
                </div>

                <!-- Rechazo Card -->
                <div class="glass-card p-8 rounded-[3rem] shadow-xl bg-rose-50 border border-rose-100">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-600 mb-8">Información de Rechazo</h3>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-rose-400 mb-3 block">Motivo Catálogo</label>
                            <select name="motivo_rechazo_id" class="w-full bg-white border-rose-100 rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-rose-200">
                                <option value="">Sin motivo de catálogo</option>
                                @foreach($motivosRechazo as $mot)
                                    <option value="{{ $mot->id }}" {{ old('motivo_rechazo_id', $traspaso->motivo_rechazo_id) == $mot->id ? 'selected' : '' }}>
                                        ({{ $mot->codigo_unsigima }}) {{ $mot->descripcion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-rose-400 mb-3 block">Comentarios adicionales</label>
                            <textarea name="motivos_estado" rows="3" class="w-full bg-white border-rose-100 rounded-2xl px-6 py-4 text-xs font-bold focus:ring-2 focus:ring-rose-200">{{ old('motivos_estado', $traspaso->motivos_estado) }}</textarea>
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-rose-400 mb-3 block">Fecha de Rechazo</label>
                            <input type="date" name="fecha_rechazo" value="{{ old('fecha_rechazo', $traspaso->fecha_rechazo ? $traspaso->fecha_rechazo->format('Y-m-d') : '') }}" 
                                   class="w-full bg-white border-rose-100 rounded-2xl px-6 py-4 text-xs font-black focus:ring-2 focus:ring-rose-200">
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <button type="submit" class="w-full bg-primary text-white rounded-[2rem] py-6 text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    <i class="ph-bold ph-floppy-disk text-xl"></i>
                    Guardar Cambios
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
