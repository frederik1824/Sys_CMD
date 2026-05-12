@extends('layouts.app')

@section('content')
<div class="p-4 md:p-10 max-w-[1600px] mx-auto min-h-[calc(100vh-100px)]" x-data="{ 
    showRejectionModal: false, 
    showReturnModal: false,
    showEscalationModal: false,
    validating: false,
    showVisor: false,
    activeDocUrl: '',
    activeDocId: null,
    
    openVisor(url, id) {
        this.activeDocUrl = url;
        this.activeDocId = id;
        this.showVisor = true;
    },

    validateDoc(docId, estado) {
        this.validating = true;
        fetch(`/solicitudes-afiliacion/{{ $solicitud->id }}/documentos/${docId}/validar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ estado: estado })
        })
        .then(r => r.json())
        .then(data => {
            if(data.success) {
                // Si estamos en el visor, actualizar el estado local antes de recargar
                if(this.showVisor) {
                    window.location.reload();
                } else {
                    window.location.reload();
                }
            }
        });
    },

    confirmApprove(event) {
        event.preventDefault();
        Swal.fire({
            title: '¿Aprobación Técnica?',
            text: 'El expediente será aprobado técnicamente, pero quedará abierto para su cierre definitivo en los próximos días.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, Aprobar',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-[32px]',
                confirmButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-[10px]',
                cancelButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-[10px]'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    },

    confirmComplete(event) {
        event.preventDefault();
        Swal.fire({
            title: '¿Cierre Definitivo?',
            text: '¿Estás seguro de finalizar completamente este trámite? Ya no podrá ser editado.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Cerrar Definitivamente',
            cancelButtonText: 'Cancelar',
            customClass: {
                popup: 'rounded-[32px]',
                confirmButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-[10px]',
                cancelButton: 'rounded-xl px-6 py-3 font-black uppercase tracking-widest text-[10px]'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                event.target.submit();
            }
        });
    }
}">
    <!-- HEADER INMERSIVO -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-8 mb-12">
        <div class="space-y-4">
            <a href="{{ route('afiliacion.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50/30 transition-all text-[10px] font-black uppercase tracking-widest shadow-sm group">
                <i class="ph-bold ph-arrow-left group-hover:-translate-x-1 transition-transform"></i> 
                Volver a la Bandeja Operativa
            </a>
            <div class="flex flex-col md:flex-row md:items-center gap-6">
                <div class="relative">
                    <div class="absolute -left-6 top-1/2 -translate-y-1/2 w-1.5 h-14 bg-indigo-600 rounded-full hidden md:block"></div>
                <h1 class="text-6xl font-black tracking-tighter text-slate-900 leading-none">
                    {{ $solicitud->codigo_solicitud }}
                </h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="px-8 py-3 rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] {{ $solicitud->status_color }} shadow-xl shadow-slate-100 border border-black/5">
                    {{ $solicitud->status_label }}
                </span>
                <div class="px-6 py-3 bg-slate-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] flex items-center gap-3 shadow-2xl shadow-slate-200">
                    <i class="ph-fill ph-lightning text-amber-400 text-lg"></i> Prioridad {{ $solicitud->prioridad }}
                </div>
            </div>
        </div>
        <p class="text-slate-400 font-medium text-2xl leading-relaxed">
            <span class="text-slate-900 font-black">{{ $solicitud->tipoSolicitud->nombre }}</span> 
            <span class="mx-3 text-slate-200">/</span>
            {{ $solicitud->nombre_completo }} <span class="text-indigo-600 font-black ml-2">[{{ $solicitud->cedula }}]</span>
        </p>
            
            {{-- $afiliadoMaestro pre-cargado en el controlador --}}

            @if($afiliadoMaestro)
            <div class="pt-2">
                <a href="{{ route('carnetizacion.afiliados.show', $afiliadoMaestro) }}" target="_blank"
                    class="inline-flex items-center gap-3 px-6 py-3 bg-emerald-50 border border-emerald-100 rounded-[18px] text-[10px] font-black uppercase tracking-widest text-emerald-700 hover:bg-emerald-100 transition-all shadow-sm group">
                    <i class="ph ph-user-focus text-xl"></i>
                    Ver Historial Maestro del Afiliado
                    <i class="ph ph-arrow-square-out text-base opacity-0 group-hover:opacity-100 transition-opacity"></i>
                </a>
            </div>
            @endif
        </div>

        <div class="flex flex-wrap gap-4 items-center" id="action-bar-container">
            <div class="flex flex-wrap gap-4 items-center transition-all duration-500" 
                 :class="{ 'fixed top-4 right-10 z-[150] bg-white/80 backdrop-blur-xl p-4 rounded-[32px] shadow-2xl border border-white/20 scale-90 origin-right': $store.scroll > 200 }">
                
                @if($solicitud->estado == 'Pendiente' && auth()->user()->can('solicitudes_afiliacion.asignarse'))
                <form action="{{ route('afiliacion.assign', $solicitud) }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white rounded-[24px] px-10 py-6 text-xs font-black uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all flex items-center gap-4 shadow-xl shadow-indigo-200">
                        <i class="ph-fill ph-hand-pointing text-2xl"></i> Asignarme
                    </button>
                </form>
                @endif

                @if($solicitud->asignado_user_id == auth()->id() && $solicitud->estado == 'En revisión')
                <div class="flex flex-wrap gap-3 p-3 bg-white border border-slate-100 rounded-[32px] shadow-sm">
                    <button @click="showReturnModal = true" class="px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-orange-600 bg-orange-50 hover:bg-orange-100 transition-all">
                        Devolver
                    </button>
                    <button @click="showRejectionModal = true" class="px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-rose-600 bg-rose-50 hover:bg-rose-100 transition-all">
                        Rechazar
                    </button>
                    @can('solicitudes_afiliacion.escalar')
                    <button @click="showEscalationModal = true" class="px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-purple-600 bg-purple-50 hover:bg-purple-100 transition-all">
                        Escalar
                    </button>
                    @endcan
                    <div class="w-px h-10 bg-slate-100 mx-2 hidden md:block"></div>
                    <form action="{{ route('afiliacion.approve', $solicitud) }}" method="POST" @submit="confirmApprove($event)">
                        @csrf
                        <button type="submit" class="bg-emerald-600 text-white rounded-2xl px-10 py-4 text-xs font-black uppercase tracking-[0.15em] hover:bg-emerald-700 transition-all flex items-center gap-3 shadow-lg shadow-emerald-100">
                            <i class="ph-bold ph-check-circle text-xl"></i> Aprobar
                        </button>
                    </form>
                </div>
                @endif

                @if($solicitud->asignado_user_id == auth()->id() && $solicitud->estado == 'Aprobada')
                <div class="flex flex-wrap gap-3 p-3 bg-white border border-slate-100 rounded-[32px] shadow-sm">
                    <form action="{{ route('afiliacion.complete', $solicitud) }}" method="POST" @submit="confirmComplete($event)">
                        @csrf
                        <button type="submit" class="bg-indigo-600 text-white rounded-2xl px-10 py-4 text-xs font-black uppercase tracking-[0.15em] hover:bg-indigo-700 transition-all flex items-center gap-3 shadow-lg shadow-indigo-100">
                            <i class="ph-bold ph-lock-key text-xl"></i> Cierre Definitivo
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- IZQUIERDA: DETALLES Y DOCUMENTOS (8 col) -->
        <div class="lg:col-span-8 space-y-10">
            <!-- TARJETA TÉCNICA -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden relative group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-slate-50/50 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-indigo-50/30 transition-colors duration-1000"></div>
                
                <div class="p-8 md:p-12 relative z-10">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                            <i class="ph-bold ph-info text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Detalles Técnicos</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                        <div class="space-y-4">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] block">Originador del Caso</label>
                            <div class="flex items-center gap-5 p-2 pr-6 bg-slate-50/50 rounded-[28px] border border-slate-100/50">
                                <img src="{{ $solicitud->solicitante->avatar_url }}" class="w-14 h-14 rounded-2xl border-4 border-white shadow-xl">
                                <div class="overflow-hidden">
                                    <p class="text-[13px] font-black text-slate-900 leading-tight mb-0.5 truncate">{{ $solicitud->solicitante->name }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest truncate">{{ explode(' ', $solicitud->solicitante->roles->first()?->name ?? 'Usuario')[0] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] block">Analista Responsable</label>
                            @if($solicitud->asignado)
                            <div class="flex items-center gap-5 p-2 pr-6 bg-indigo-50/30 rounded-[28px] border border-indigo-100/50 group/analyst">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-xl shadow-indigo-100 group-hover/analyst:rotate-3 transition-transform">
                                    {{ substr($solicitud->asignado->name, 0, 1) }}
                                </div>
                                <div class="overflow-hidden">
                                    <p class="text-[13px] font-black text-indigo-900 leading-tight mb-0.5 truncate">{{ $solicitud->asignado->name }}</p>
                                    <p class="text-[9px] font-bold text-indigo-400 uppercase tracking-widest truncate">Desde {{ $solicitud->fecha_asignacion->diffForHumans() }}</p>
                                </div>
                            </div>
                            @else
                            <div class="flex items-center gap-5 p-2 pr-6 border-2 border-dashed border-slate-100 rounded-[28px] bg-slate-50/30">
                                <div class="w-14 h-14 rounded-2xl border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-300">
                                    <i class="ph ph-user-circle-plus text-2xl"></i>
                                </div>
                                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Por Asignar</span>
                            </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] block">Tiempo de Respuesta (SLA)</label>
                            @php 
                                $isDelayed = now()->isAfter($solicitud->sla_fecha_limite);
                                $slaColor = $isDelayed ? 'rose' : 'blue';
                            @endphp
                            <div class="inline-flex items-center gap-4 p-2 pr-8 rounded-[28px] bg-{{ $slaColor }}-50 border border-{{ $slaColor }}-100 shadow-sm relative overflow-hidden group/sla">
                                <div class="absolute inset-0 bg-gradient-to-r from-{{ $slaColor }}-100/50 to-transparent opacity-0 group-hover/sla:opacity-100 transition-opacity"></div>
                                <div class="w-14 h-14 rounded-2xl bg-{{ $slaColor }}-600 text-white flex items-center justify-center relative z-10 shadow-lg shadow-{{ $slaColor }}-100">
                                    <i class="ph-bold ph-clock-countdown text-2xl {{ $isDelayed ? 'animate-pulse' : '' }}"></i>
                                </div>
                                <div class="relative z-10">
                                    <p class="text-[13px] font-black text-{{ $slaColor }}-900 leading-tight mb-0.5">{{ $solicitud->sla_fecha_limite->format('d M, Y') }}</p>
                                    <p class="text-[9px] font-black text-{{ $slaColor }}-500 uppercase tracking-widest">{{ $solicitud->sla_fecha_limite->format('h:i A') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-slate-50">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block">Entidad Corporativa</label>
                                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-3xl border border-slate-100">
                                    <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center text-slate-400">
                                        <i class="ph-bold ph-buildings text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-800">{{ $solicitud->empresa ?? 'Solicitud Individual / Personal' }}</p>
                                        @if($solicitud->rnc_empresa)
                                        <p class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest">RNC: {{ $solicitud->rnc_empresa }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="md:col-span-2 space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block">Canales de Respuesta</label>
                                <div class="flex gap-4">
                                    <div class="flex-1 p-4 bg-slate-50 rounded-3xl border border-slate-100 flex items-center gap-3">
                                        <i class="ph ph-phone-call text-indigo-500"></i>
                                        <span class="text-[11px] font-black text-slate-700">{{ $solicitud->telefono ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex-1 p-4 bg-slate-50 rounded-3xl border border-slate-100 flex items-center gap-3">
                                        <i class="ph ph-envelope-open text-indigo-500"></i>
                                        <span class="text-[11px] font-black text-slate-700 truncate max-w-[120px]">{{ $solicitud->correo ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            @if($solicitud->tipo_solicitud_id == 6)
                            <div class="md:col-span-3 space-y-4 pt-6 mt-4 border-t border-indigo-100/50">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center">
                                        <i class="ph ph-bank-fill"></i>
                                    </div>
                                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Información de Pensión / Jubilación</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div class="p-5 bg-indigo-50/50 rounded-3xl border border-indigo-100/50 flex flex-col gap-1">
                                        <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Resolución Nº</span>
                                        <span class="text-sm font-black text-indigo-900">{{ $solicitud->numero_resolucion ?? 'No provista' }}</span>
                                    </div>
                                    <div class="p-5 bg-indigo-50/50 rounded-3xl border border-indigo-100/50 flex flex-col gap-1">
                                        <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Tipo de Pensión</span>
                                        <span class="text-sm font-black text-indigo-900">{{ $solicitud->tipo_pension ?? 'N/A' }}</span>
                                    </div>
                                    <div class="p-5 bg-indigo-50/50 rounded-3xl border border-indigo-100/50 flex flex-col gap-1">
                                        <span class="text-[9px] font-black text-indigo-400 uppercase tracking-widest">Institución</span>
                                        <span class="text-sm font-black text-indigo-900">{{ $solicitud->institucion_pension ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <div class="md:col-span-2 space-y-3">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block">Exposición del Caso</label>
                                <div class="p-8 bg-indigo-50/30 rounded-[32px] border border-indigo-100 relative overflow-hidden group">
                                    <i class="ph ph-quotes text-6xl absolute -right-4 -bottom-4 text-indigo-500/10 group-hover:scale-110 transition-transform"></i>
                                    <p class="text-slate-700 font-medium leading-relaxed italic text-xl relative z-10">
                                        "{{ $solicitud->observacion_solicitante ?? 'El solicitante no ha provisto información adicional para este caso.' }}"
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN DOCUMENTAL -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 md:p-12 space-y-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                            <i class="ph-bold ph-folder-open text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Expediente Documental</h2>
                    </div>
                    <div class="flex items-center gap-2 px-4 py-2 bg-slate-50 rounded-xl">
                        <span class="text-[10px] font-black text-slate-900">{{ $solicitud->documentos->count() }}</span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Archivos</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($solicitud->documentos as $doc)
                    <div class="p-8 rounded-[40px] border relative transition-all group {{ $doc->validacion_estado == 'Válido' ? 'border-emerald-100 bg-emerald-50/10' : ($doc->validacion_estado == 'Inválido' ? 'border-rose-100 bg-rose-50/10' : 'border-slate-100 bg-white shadow-sm hover:shadow-md') }}">
                        <div class="flex justify-between items-start mb-6">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-500 group-hover:bg-slate-900 group-hover:text-white transition-all shadow-sm">
                                    <i class="ph-fill ph-file-pdf text-3xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight mb-1">
                                        {{ $doc->requerimiento->nombre_documento ?? 'Expediente Consolidado' }}
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-bold text-slate-400 truncate max-w-[150px]">{{ $doc->nombre_original }}</span>
                                        <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                        <span class="text-[9px] font-black text-indigo-500 uppercase tracking-tighter">Verificado</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="openVisor('{{ route('afiliacion.documentos.view', $doc) }}', {{ $doc->id }})" 
                                        class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center hover:scale-110 transition-all shadow-lg shadow-slate-200">
                                     <i class="ph-bold ph-eye text-xl"></i>
                                </button>
                                <a href="{{ route('afiliacion.documentos.view', $doc) }}" target="_blank" 
                                   class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-all shadow-sm">
                                    <i class="ph-bold ph-arrow-square-out text-xl"></i>
                                </a>
                            </div>
                        </div>

                        @if($solicitud->asignado_user_id == auth()->id() && $solicitud->estado == 'En revisión' && !$doc->validacion_estado)
                        <div class="flex gap-3 mt-6 pt-6 border-t border-slate-100/50">
                            <button @click="validateDoc({{ $doc->id }}, 'Válido')" 
                                    class="flex-1 py-4 rounded-2xl bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100 flex items-center justify-center gap-2">
                                <i class="ph-bold ph-check"></i> Válido
                            </button>
                            <button @click="validateDoc({{ $doc->id }}, 'Inválido')" 
                                    class="flex-1 py-4 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 text-[10px] font-black uppercase tracking-widest hover:bg-rose-100 transition-all flex items-center justify-center gap-2">
                                <i class="ph-bold ph-x"></i> Inválido
                            </button>
                        </div>
                        @else
                        <div class="mt-6 pt-6 border-t border-slate-100/50 flex justify-between items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $doc->validacion_estado == 'Válido' ? 'bg-emerald-500' : ($doc->validacion_estado == 'Inválido' ? 'bg-rose-500' : 'bg-slate-300') }}"></div>
                                <span class="text-[10px] font-black uppercase tracking-widest {{ $doc->validacion_estado == 'Válido' ? 'text-emerald-600' : ($doc->validacion_estado == 'Inválido' ? 'text-rose-600' : 'text-slate-400') }}">
                                    {{ $doc->validacion_estado ?? 'Pendiente de Revisión' }}
                                </span>
                            </div>
                            @if($doc->validated_at)
                            <div class="text-right">
                                <p class="text-[9px] font-bold text-slate-400 leading-none mb-1">Auditado por</p>
                                <p class="text-[10px] font-black text-slate-700 leading-none">{{ $doc->validator->name }}</p>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- DERECHA: AUDITORÍA Y TRAZABILIDAD (4 col) -->
        <div class="lg:col-span-4 h-full">
            <div class="bg-slate-900 rounded-[48px] p-1 shadow-2xl shadow-indigo-200/40 h-full">
                <div class="bg-white rounded-[44px] h-full flex flex-col overflow-hidden">
                    <div class="p-10 border-b border-slate-50">
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Trazabilidad</h2>
                        <p class="text-slate-400 font-bold text-xs uppercase tracking-widest mt-1">Registro de Auditoría</p>
                    </div>
                    
                    <div class="p-8 md:p-10 flex-1 overflow-y-auto space-y-12 relative min-h-[600px]">
                        <div class="absolute left-[59px] top-12 bottom-12 w-0.5 bg-slate-100 rounded-full"></div>
                        
                        @foreach($solicitud->historial->sortByDesc('created_at') as $h)
                        <div class="relative pl-20 group">
                            <!-- ICONO ESTADO -->
                            <div class="absolute left-0 top-0 w-14 h-14 rounded-2xl bg-white border-2 {{ $h->estado_nuevo == 'Aprobada' ? 'border-emerald-100 shadow-emerald-50' : ($h->estado_nuevo == 'Rechazada' ? 'border-rose-100 shadow-rose-50' : 'border-slate-50 shadow-slate-50') }} flex items-center justify-center z-10 shadow-lg transition-transform group-hover:scale-110">
                                @php
                                    $icon = match($h->accion) {
                                        'Aprobación' => 'ph-fill ph-check-circle text-emerald-500',
                                        'Rechazo' => 'ph-fill ph-x-circle text-rose-500',
                                        'Devolución' => 'ph-fill ph-arrow-u-up-left text-orange-500',
                                        'Escalamiento' => 'ph-fill ph-arrows-out-cardinal text-purple-500',
                                        'Asignación' => 'ph-fill ph-user-focus text-indigo-500',
                                        default => 'ph-fill ph-dot-outline text-slate-300'
                                    };
                                @endphp
                                <i class="ph {{ $icon }} text-2xl"></i>
                            </div>

                            <div class="space-y-2">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight leading-none mb-1">{{ $h->accion }}</h4>
                                        <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">{{ $h->user->name }}</p>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 whitespace-nowrap">{{ $h->created_at->diffForHumans() }}</span>
                                </div>
                                
                                
                                @if($h->detalles)
                                <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 mb-3">
                                    <p class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-2">Cambios en campos críticos:</p>
                                    <div class="space-y-1">
                                        @foreach($h->detalles as $campo => $valores)
                                        <div class="flex items-center gap-2 text-[10px] font-medium">
                                            <span class="text-slate-500">{{ ucfirst(str_replace('_', ' ', $campo)) }}:</span>
                                            <span class="text-slate-400 line-through">{{ is_array($valores['anterior']) ? json_encode($valores['anterior']) : $valores['anterior'] }}</span>
                                            <i class="ph ph-arrow-right text-[8px] text-slate-400"></i>
                                            <span class="text-indigo-700 font-bold">{{ is_array($valores['nuevo']) ? json_encode($valores['nuevo']) : $valores['nuevo'] }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($h->comentario)
                                <div class="p-5 bg-slate-50 rounded-2xl text-xs font-medium text-slate-600 border border-slate-100 relative group-hover:bg-slate-100 transition-colors">
                                    {{ $h->comentario }}
                                </div>
                                @endif
                                
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[9px] font-black text-slate-400 uppercase">Estado:</span>
                                    <span class="text-[9px] font-black text-slate-900 uppercase">{{ $h->estado_nuevo }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL RECHAZO -->
    <div x-show="showRejectionModal" class="fixed inset-0 bg-slate-900/90 backdrop-blur-md z-[100] flex items-center justify-center p-6" x-cloak>
        <div class="bg-white rounded-[56px] w-full max-w-xl p-12 shadow-2xl relative overflow-hidden" @click.away="showRejectionModal = false">
            <div class="absolute top-0 right-0 w-32 h-32 bg-rose-50 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter mb-2">Rechazo Definitivo</h3>
            <p class="text-slate-500 font-medium mb-10 text-base leading-relaxed">¿Estás seguro de cerrar este caso con un rechazo? Por favor, provee una justificación técnica clara para el solicitante.</p>
            
            <form action="{{ route('afiliacion.reject', $solicitud) }}" method="POST">
                @csrf
                <textarea name="motivo" rows="5" required placeholder="Describe la causa técnica del rechazo..." 
                    class="w-full bg-slate-50 border-2 border-transparent focus:border-rose-500 focus:bg-white rounded-[32px] p-8 text-base font-bold text-slate-800 transition-all mb-8 shadow-sm resize-none"></textarea>
                
                <div class="flex gap-4">
                    <button type="button" @click="showRejectionModal = false" class="flex-1 py-6 rounded-[24px] text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] py-6 rounded-[24px] bg-rose-600 text-white text-xs font-black uppercase tracking-[0.2em] hover:bg-rose-700 shadow-xl shadow-rose-200 transition-all flex items-center justify-center gap-3">
                        Confirmar Cierre <i class="ph ph-prohibit text-xl"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DEVOLUCION -->
    <div x-show="showReturnModal" class="fixed inset-0 bg-slate-900/90 backdrop-blur-md z-[100] flex items-center justify-center p-6" x-cloak>
        <div class="bg-white rounded-[56px] w-full max-w-xl p-12 shadow-2xl relative overflow-hidden" @click.away="showReturnModal = false">
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter mb-2">Devolver para Enmienda</h3>
            <p class="text-slate-500 font-medium mb-10 text-base leading-relaxed">Indica al representante qué campos o documentos requieren corrección para que el caso pueda re-procesarse.</p>
            
            <form action="{{ route('afiliacion.return', $solicitud) }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4 mb-2 block">Respuestas Rápidas</label>
                    <select onchange="document.getElementById('motivo_textarea').value = this.value" class="w-full bg-slate-50 border-none rounded-[20px] px-6 py-4 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-orange-500/10 transition-all">
                        <option value="">-- Selecciona una plantilla --</option>
                        <option value="Cédula de identidad ilegible o vencida. Por favor cargue una imagen clara.">Cédula Ilegible/Vencida</option>
                        <option value="Falta firma del afiliado en el formulario de solicitud.">Falta Firma</option>
                        <option value="Discrepancia entre los datos del formulario PDF y los ingresados en el sistema.">Error de Captura</option>
                        <option value="Documentación incompleta para este tipo de trámite. Favor revisar requisitos.">Documentación Incompleta</option>
                        <option value="El sello de la empresa no es visible o es incorrecto.">Sello Incorrecto</option>
                    </select>
                </div>

                <textarea name="motivo" id="motivo_textarea" rows="5" required placeholder="Instrucciones precisas para la corrección..." 
                    class="w-full bg-slate-50 border-2 border-transparent focus:border-orange-500 focus:bg-white rounded-[32px] p-8 text-base font-bold text-slate-800 transition-all mb-8 shadow-sm resize-none"></textarea>
                
                <div class="flex gap-4">
                    <button type="button" @click="showReturnModal = false" class="flex-1 py-6 rounded-[24px] text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] py-6 rounded-[24px] bg-orange-500 text-white text-xs font-black uppercase tracking-[0.2em] hover:bg-orange-600 shadow-xl shadow-orange-200 transition-all flex items-center justify-center gap-3">
                        Enviar a Enmienda <i class="ph ph-arrow-u-up-left text-xl"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL ESCALAMIENTO -->
    <div x-show="showEscalationModal" class="fixed inset-0 bg-slate-900/90 backdrop-blur-md z-[100] flex items-center justify-center p-6" x-cloak>
        <div class="bg-white rounded-[56px] w-full max-w-2xl p-12 shadow-2xl relative overflow-hidden" @click.away="showEscalationModal = false">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter mb-2">Escalar Expediente</h3>
            <p class="text-slate-500 font-medium mb-10 text-base leading-relaxed">Transfiere este caso a una unidad especializada para su resolución técnica.</p>
            
            <form action="{{ route('afiliacion.escalate', $solicitud) }}" method="POST">
                @csrf
                <div class="space-y-8 mb-10">
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-4 block">Unidad / Departamento Destino</label>
                        <select name="departamento_id" required class="w-full bg-slate-50 border-2 border-transparent focus:border-purple-500 focus:bg-white rounded-[24px] px-8 py-5 text-base font-bold text-slate-800 transition-all shadow-sm">
                            <option value="">Selecciona unidad especializada...</option>
                            @foreach($departamentos as $dep)
                                @if($dep->id != $solicitud->departamento_id)
                                <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-4 block">Justificación del Escalamiento</label>
                        <textarea name="motivo" rows="4" required placeholder="Explica por qué este caso requiere atención de otra unidad..." 
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-purple-500 focus:bg-white rounded-[32px] p-8 text-base font-bold text-slate-800 transition-all shadow-sm resize-none"></textarea>
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <button type="button" @click="showEscalationModal = false" class="flex-1 py-6 rounded-[24px] text-xs font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all">Cancelar Operación</button>
                    <button type="submit" class="flex-[2] py-6 rounded-[24px] bg-purple-600 text-white text-xs font-black uppercase tracking-[0.2em] hover:bg-purple-700 shadow-xl shadow-purple-200 transition-all flex items-center justify-center gap-3">
                        Confirmar Escalamiento <i class="ph ph-arrows-out-cardinal text-xl"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- VISOR DE DOCUMENTOS SPLIT-SCREEN -->
    <div x-show="showVisor" class="fixed inset-0 bg-slate-900/95 backdrop-blur-xl z-[200] flex flex-col" x-cloak x-transition>
        <div class="h-20 bg-white/5 border-b border-white/10 flex items-center justify-between px-10">
            <div class="flex items-center gap-6">
                <button @click="showVisor = false" class="text-white/40 hover:text-white transition-colors">
                    <i class="ph-bold ph-x text-2xl"></i>
                </button>
                <div class="h-8 w-px bg-white/10"></div>
                <h3 class="text-white font-black tracking-tight">Visor Inmersivo de Expediente</h3>
            </div>
            
            <div class="flex items-center gap-4">
                <a :href="activeDocUrl" target="_blank" class="px-6 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                    Abrir en pestaña nueva <i class="ph ph-arrow-square-out ml-2"></i>
                </a>
            </div>
        </div>

        <div class="flex-1 flex overflow-hidden">
            <!-- PANEL PDF (Izquierda) -->
            <div class="flex-1 bg-slate-800 p-6 md:p-10 relative">
                <iframe :src="activeDocUrl" class="w-full h-full rounded-3xl shadow-2xl border border-white/5" frameborder="0"></iframe>
            </div>

            <!-- PANEL ACCIONES (Derecha) -->
            <div class="w-[450px] bg-white h-full flex flex-col border-l border-slate-100">
                <div class="p-10 border-b border-slate-50">
                    <span class="text-[10px] font-black text-indigo-500 uppercase tracking-[0.2em] block mb-2">Acciones Rápidas</span>
                    <h4 class="text-2xl font-black text-slate-900 tracking-tighter">Validación del Documento</h4>
                </div>
                
                <div class="p-10 space-y-10 flex-1 overflow-y-auto">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Criterio Técnico</label>
                        <div class="p-6 bg-slate-50 rounded-[32px] border border-slate-100">
                            <p class="text-slate-600 font-bold text-sm leading-relaxed">
                                Verifica que el documento sea legible, vigente y corresponda a los datos del afiliado (Cédula: {{ $solicitud->cedula }}).
                            </p>
                        </div>
                    </div>

                    @if($solicitud->asignado_user_id == auth()->id() && $solicitud->estado == 'En revisión')
                    <div class="grid grid-cols-1 gap-4">
                        <button @click="validateDoc(activeDocId, 'Válido')" 
                                class="w-full py-6 rounded-[24px] bg-emerald-600 text-white font-black uppercase tracking-[0.2em] hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-3">
                            <i class="ph-bold ph-check-circle text-2xl"></i> Marcar como Válido
                        </button>
                        <button @click="validateDoc(activeDocId, 'Inválido')" 
                                class="w-full py-6 rounded-[24px] bg-rose-50 text-rose-600 border-2 border-rose-100 font-black uppercase tracking-[0.2em] hover:bg-rose-100 transition-all flex items-center justify-center gap-3">
                            <i class="ph-bold ph-x-circle text-2xl"></i> Marcar como Inválido
                        </button>
                    </div>
                    @else
                    <div class="p-8 bg-indigo-50 rounded-[32px] border border-indigo-100 text-center">
                        <i class="ph ph-lock-key text-4xl text-indigo-300 mb-4"></i>
                        <p class="text-indigo-600 font-black text-sm">Estado de Solo Lectura</p>
                        <p class="text-indigo-400 text-xs font-bold mt-2">Para validar, debes ser el analista asignado.</p>
                    </div>
                    @endif
                </div>

                <div class="p-10 bg-slate-50 border-t border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Referencia: {{ $solicitud->codigo_solicitud }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    
    /* Personalización del scrollbar para el historial */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: #f1f5f9;
        border-radius: 10px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #e2e8f0;
    }
</style>
@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('scroll', 0);
    });
    
    window.addEventListener('scroll', () => {
        Alpine.store('scroll', window.scrollY);
    });
</script>
@endpush
@endsection

