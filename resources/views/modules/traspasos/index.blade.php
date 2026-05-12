@extends('layouts.app')

@section('content')
    <script>
        function traspasosInbox() {
            return {
                showModal: false,
                showVerifyModal: false,
                selectedTraspaso: { id: null, nombre: '', cedula: '', fecha_efectivo: '', periodo_efectivo: '', cantidad_dependientes: 0, verificado: false, unipago_status: 'pendiente', unipago_observaciones: '' },
                motivosDynamic: @json($motivosRechazo),
                
                openEdit(traspaso) {
                    this.selectedTraspaso = {
                        id: traspaso.id,
                        nombre: traspaso.nombre,
                        cedula: traspaso.cedula || '',
                        fecha_efectivo: traspaso.fecha_efectivo_raw || '',
                        periodo_efectivo: traspaso.periodo_efectivo || '',
                        cantidad_dependientes: traspaso.cantidad_dependientes || 0
                    };
                    this.showModal = true;
                },

                openVerify(traspaso) {
                    this.selectedTraspaso = {
                        id: traspaso.id,
                        nombre: traspaso.nombre,
                        cedula: traspaso.cedula || '',
                        cantidad_dependientes: traspaso.cantidad_dependientes || 0,
                        verificado: traspaso.verificado || false,
                        unipago_status: traspaso.unipago_status || 'pendiente',
                        unipago_observaciones: traspaso.unipago_observaciones || ''
                    };
                    this.showVerifyModal = true;
                },

                async emitirCarnet(traspasoId) {
                    const result = await Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Deseas enviar este registro al módulo de carnetización?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0f172a',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Sí, enviar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-[32px]',
                            confirmButton: 'rounded-2xl px-6 py-3 uppercase text-[10px] font-black tracking-widest',
                            cancelButton: 'rounded-2xl px-6 py-3 uppercase text-[10px] font-black tracking-widest'
                        }
                    });

                    if (!result.isConfirmed) return;
                    
                    try {
                        const url = "{{ route('traspasos.emitir-carnet', ':id') }}".replace(':id', traspasoId);
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await Swal.fire({
                                title: '¡Completado!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#0f172a',
                                customClass: { popup: 'rounded-[32px]' }
                            });
                            window.location.reload();
                        } else {
                            Swal.fire({
                                title: 'Aviso',
                                text: data.message,
                                icon: 'warning',
                                confirmButtonColor: '#0f172a',
                                customClass: { popup: 'rounded-[32px]' }
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al procesar la solicitud.',
                            icon: 'error',
                            confirmButtonColor: '#0f172a',
                            customClass: { popup: 'rounded-[32px]' }
                        });
                    }
                },

                async rechazar(traspasoId) {
                    const motivosMap = {};
                    this.motivosDynamic.forEach(m => {
                        motivosMap[m.id] = `(${m.codigo_sisalril}) ${m.descripcion} [${m.codigo_unsigima}]`;
                    });
                    motivosMap['OTRO'] = 'OTRO (ESPECIFIQUE)';

                    const { value: motivoKey } = await Swal.fire({
                        title: 'Rechazar Traspaso',
                        input: 'select',
                        inputOptions: motivosMap,
                        inputPlaceholder: 'Busque por código (ej: 10657) o motivo...',
                        showCancelButton: true,
                        confirmButtonColor: '#e11d48',
                        confirmButtonText: 'Confirmar Rechazo',
                        cancelButtonText: 'Cancelar',
                        customClass: {
                            popup: 'rounded-[32px]',
                            confirmButton: 'rounded-2xl px-6 py-3 uppercase text-[10px] font-black tracking-widest',
                            cancelButton: 'rounded-2xl px-6 py-3 uppercase text-[10px] font-black tracking-widest'
                        },
                        inputValidator: (value) => {
                            if (!value) return 'Debes seleccionar un motivo.';
                        }
                    });

                    if (motivoKey) {
                        let motivoFinal = motivosMap[motivoKey];
                        if (motivoKey === 'OTRO') {
                            const { value: otroMotivo } = await Swal.fire({
                                title: 'Especifique el motivo',
                                input: 'textarea',
                                inputPlaceholder: 'Describa el motivo del rechazo...',
                                showCancelButton: true,
                                confirmButtonColor: '#0f172a',
                                customClass: { popup: 'rounded-[32px]' }
                            });
                            if (!otroMotivo) return;
                            motivoFinal = otroMotivo;
                        }

                        try {
                            const url = "{{ route('traspasos.rechazar', ':id') }}".replace(':id', traspasoId);
                            const response = await fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ 
                                    motivo_id: motivoKey === 'OTRO' ? null : motivoKey,
                                    motivos_estado: motivoFinal 
                                })
                            });
                            if (response.ok) window.location.reload();
                        } catch (error) {
                            Swal.fire('Error', 'Problema al procesar el rechazo.', 'error');
                        }
                    }
                },

                // Lógica de Historial
                showHistory: false,
                historyLoading: false,
                currentHistory: [],
                selectedForHistory: null,

                async fetchHistory(traspasoId, nombre, cedula) {
                    this.selectedForHistory = { nombre_afiliado: nombre, cedula_afiliado: cedula };
                    this.showHistory = true;
                    this.historyLoading = true;
                    this.currentHistory = [];

                    try {
                        const url = "{{ route('traspasos.history', ':id') }}".replace(':id', traspasoId);
                        const response = await fetch(url);
                        if (response.ok) {
                            this.currentHistory = await response.json();
                        }
                    } catch (error) {
                        console.error('Error fetching history:', error);
                    } finally {
                        this.historyLoading = false;
                    }
                },

                async saveEnrichment() {
                    try {
                        const response = await fetch(`/traspasos/${this.selectedTraspaso.id}/enriquecer`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                fecha_efectivo: this.selectedTraspaso.fecha_efectivo,
                                cantidad_dependientes: this.selectedTraspaso.cantidad_dependientes
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok && data.success) {
                            await Swal.fire({
                                title: '¡Guardado!',
                                text: 'Los datos de efectividad se han actualizado.',
                                icon: 'success',
                                confirmButtonColor: '#0f172a',
                                customClass: { popup: 'rounded-[32px]' }
                            });
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Error al validar los datos');
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error al Guardar',
                            text: error.message,
                            icon: 'error',
                            confirmButtonColor: '#0f172a',
                            customClass: { popup: 'rounded-[32px]' }
                        });
                    }
                },

                async saveVerification() {
                    try {
                        const url = "{{ route('traspasos.verificar', ':id') }}".replace(':id', this.selectedTraspaso.id);
                        const response = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                cantidad_dependientes: this.selectedTraspaso.cantidad_dependientes,
                                verificado: this.selectedTraspaso.verificado,
                                unipago_status: this.selectedTraspaso.unipago_status,
                                unipago_observaciones: this.selectedTraspaso.unipago_observaciones
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok && data.success) {
                            await Swal.fire({
                                title: '¡Actualizado!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#0f172a',
                                customClass: { popup: 'rounded-[32px]' }
                            });
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Error al procesar la verificación');
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: error.message,
                            icon: 'error',
                            confirmButtonColor: '#0f172a',
                            customClass: { popup: 'rounded-[32px]' }
                        });
                    }
                },


                // Lógica de búsqueda automática
                handleAutoSearch(event) {
                    const val = event.target.value.replace(/[^0-9]/g, '');
                    if (val.length === 11) {
                        event.target.form.submit();
                    }
                },

                // Lógica de Historial
                showHistory: false,
                historyLoading: false,
                currentHistory: [],
                selectedForHistory: null,

                async fetchHistory(traspasoId, nombre, cedula) {
                    this.selectedForHistory = { nombre_afiliado: nombre, cedula_afiliado: cedula };
                    this.showHistory = true;
                    this.historyLoading = true;
                    this.currentHistory = [];

                    try {
                        const url = "{{ route('traspasos.history', ':id') }}".replace(':id', traspasoId);
                        const response = await fetch(url);
                        if (response.ok) {
                            this.currentHistory = await response.json();
                        }
                    } catch (error) {
                        console.error('Error fetching history:', error);
                    } finally {
                        this.historyLoading = false;
                    }
                }
            }
        }
    </script>
<div class="p-8 max-w-[1600px] mx-auto" x-data="traspasosInbox()">

    <!-- HEADER -->
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tighter text-slate-900">Bandeja de Traspasos</h1>
            <p class="text-slate-500 font-medium mt-1 text-lg">Gestión operativa de expedientes y solicitudes.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('traspasos.dashboard') }}" class="bg-blue-50 text-blue-700 border border-blue-100 rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-blue-100 transition-all flex items-center gap-2 shadow-sm">
                <i class="ph ph-chart-line-up text-lg"></i> Ver Dashboard
            </a>
            <a href="{{ route('traspasos.export', request()->all()) }}" class="bg-white text-slate-900 border border-slate-200 rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="ph ph-download-simple text-lg"></i> Exportar
            </a>
            <a href="{{ route('traspasos.import') }}" class="bg-white text-slate-900 border border-slate-200 rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="ph ph-upload-simple text-lg"></i> Importar
            </a>
            <a href="{{ route('traspasos.sync.unipago') }}" class="bg-slate-900 text-white rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center gap-2 shadow-xl shadow-slate-200">
                <i class="ph ph-arrows-counter-clockwise text-lg"></i> Sincronizar Unipago
            </a>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm mb-8">
        <form method="GET" action="{{ route('traspasos.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       @input="handleAutoSearch($event)"
                       placeholder="Escriba Cédula para buscar..." 
                       class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
            </div>
            
            <select name="supervisor" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                <option value="all">Todos los Supervisores</option>
                @foreach($supervisores as $sup)
                    <option value="{{ $sup->id }}" {{ request('supervisor') == $sup->id ? 'selected' : '' }}>{{ $sup->nombre }}</option>
                @endforeach
            </select>

            <select name="agente" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                <option value="all">Todos los Agentes</option>
                @foreach($agentes as $ag)
                    <option value="{{ $ag->id }}" {{ request('agente') == $ag->id ? 'selected' : '' }}>{{ $ag->nombre }}</option>
                @endforeach
            </select>

            <select name="estado" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                <option value="all">Todos los Estados</option>
                @foreach($estados as $est)
                    <option value="{{ $est->id }}" {{ request('estado') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                @endforeach
            </select>

            <select name="efectividad" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                <option value="all">Condición Efectiva</option>
                <option value="efectivas" {{ request('efectividad') == 'efectivas' ? 'selected' : '' }}>Solo Efectivas</option>
                <option value="no_efectivas" {{ request('efectividad') == 'no_efectivas' ? 'selected' : '' }}>Pendientes</option>
            </select>

            <select name="verificado" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                <option value="all">Estado Verificación</option>
                <option value="si" {{ request('verificado') == 'si' ? 'selected' : '' }}>Verificados</option>
                <option value="no" {{ request('verificado') == 'no' ? 'selected' : '' }}>No Verificados</option>
            </select>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden mb-10">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 w-10">#</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Afiliado</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Solicitud Unipago</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Agente / Equipo</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Estado Unipago</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 text-center">Dep.</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Enriquecimiento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($traspasos as $index => $traspaso)
                    <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="px-8 py-6 text-[10px] font-black text-slate-300">
                            {{ ($traspasos->currentPage() - 1) * $traspasos->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <a href="{{ route('traspasos.edit', $traspaso->id) }}" class="text-sm font-black text-slate-900 hover:text-primary transition-colors uppercase decoration-primary/30 decoration-2 underline-offset-4">
                                    {{ $traspaso->nombre_afiliado }}
                                </a>
                                <span class="text-[11px] font-bold text-slate-400">{{ $traspaso->cedula_afiliado }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-blue-600">{{ $traspaso->numero_solicitud_epbd }}</span>
                                <span class="text-[11px] font-bold text-slate-400">{{ $traspaso->fecha_solicitud ? $traspaso->fecha_solicitud->format('d/m/Y') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-slate-700 uppercase">{{ $traspaso->agenteRel->nombre ?? 'N/A' }}</span>
                                @if($traspaso->agenteRel && $traspaso->agenteRel->supervisor)
                                    <span class="text-[10px] font-bold text-slate-400 uppercase italic">Eq. {{ $traspaso->agenteRel->supervisor->nombre }}</span>
                                @endif
                            </div>
                                    <td class="px-8 py-6">
                            <div class="flex flex-col gap-1">
                                @if($traspaso->estadoRel)
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter w-fit bg-{{ $traspaso->estadoRel->color }}-50 text-{{ $traspaso->estadoRel->color }}-600 border border-{{ $traspaso->estadoRel->color }}-100">
                                        {{ $traspaso->estadoRel->nombre }}
                                    </span>
                                @endif
                                @if($traspaso->motivoRechazoRel)
                                    <span class="text-[10px] font-bold text-rose-600 uppercase tracking-tight leading-none mt-1">
                                        {{ $traspaso->motivoRechazoRel->codigo_unsigima }} - {{ $traspaso->motivoRechazoRel->descripcion }}
                                    </span>
                                @endif
                                @if($traspaso->motivos_estado && !$traspaso->motivoRechazoRel)
                                    <span class="text-[10px] font-medium text-slate-400 italic max-w-[200px] leading-tight">{{ $traspaso->motivos_estado }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php 
                                $isClosed = in_array($traspaso->estadoRel->slug ?? '', ['rechazado', 'efectivo', 'emitido']);
                            @endphp
                            @if(is_null($traspaso->cantidad_dependientes))
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-[9px] font-black text-amber-600 uppercase tracking-[0.1em] bg-amber-100/50 px-2 py-1 rounded-md border border-amber-200">Pendiente</span>
                                </div>
                            @else
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl {{ $traspaso->cantidad_dependientes > 0 ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-200' : 'bg-slate-900 text-white shadow-lg shadow-slate-200' }} text-sm font-black transition-all hover:scale-105">
                                    {{ $traspaso->cantidad_dependientes }}
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Efectivo:</span>
                                    <span class="text-xs font-black {{ $traspaso->fecha_efectivo ? 'text-emerald-600' : 'text-slate-300 italic' }}">
                                        {{ $traspaso->fecha_efectivo ? $traspaso->fecha_efectivo->format('d/m/Y') : 'Pendiente' }}
                                    </span>
                                </div>

                                <div class="flex flex-col border-l border-slate-100 pl-4">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Unipago / Auditoría:</span>
                                    <div class="flex flex-col gap-1 mt-1">
                                        @if($traspaso->verificado)
                                            <span class="text-[10px] font-black text-emerald-600 uppercase flex items-center gap-1">
                                                <i class="ph-fill ph-check-circle"></i> Verificado
                                            </span>
                                            <span class="text-[9px] text-slate-400 font-bold" title="Verificado por {{ $traspaso->verificadoPor->name ?? 'Sistema' }}">
                                                Cerrado: {{ $traspaso->verificado_at->format('d/m/y') }}
                                            </span>
                                        @else
                                            @php
                                                $revisadoHoy = $traspaso->unipago_revisado_at && $traspaso->unipago_revisado_at->isToday();
                                                $statusColors = [
                                                    'pendiente' => 'slate',
                                                    'en_revision' => 'amber',
                                                    'rechazo_visto' => 'rose',
                                                    'verificado' => 'emerald'
                                                ];
                                                $statusColor = $statusColors[$traspaso->unipago_status] ?? 'slate';
                                            @endphp
                                            
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 rounded-md text-[8px] font-black uppercase bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 border border-{{ $statusColor }}-200">
                                                    {{ str_replace('_', ' ', $traspaso->unipago_status) }}
                                                </span>
                                                @if($revisadoHoy)
                                                    <span class="text-[9px] font-black text-blue-600 flex items-center gap-0.5">
                                                        <i class="ph-fill ph-eye"></i> Visto hoy
                                                    </span>
                                                @endif
                                            </div>

                                            <button @click="openVerify({
                                                id: {{ $traspaso->id }},
                                                nombre: '{{ addslashes($traspaso->nombre_afiliado) }}',
                                                cedula: '{{ $traspaso->cedula_afiliado }}',
                                                cantidad_dependientes: {{ $traspaso->cantidad_dependientes ?? 0 }},
                                                verificado: false,
                                                unipago_status: '{{ $traspaso->unipago_status }}',
                                                unipago_observaciones: '{{ addslashes($traspaso->unipago_observaciones) }}'
                                            })" class="mt-1 px-3 py-1 {{ $revisadoHoy ? 'bg-slate-50 text-slate-400 border-slate-100' : 'bg-amber-50 text-amber-600 border-amber-100 shadow-sm' }} border rounded-lg text-[9px] font-black uppercase hover:bg-amber-100 transition-all flex items-center gap-1 w-fit">
                                                <i class="ph-bold ph-magnifying-glass"></i> {{ $revisadoHoy ? 'Validar de nuevo' : 'Validar' }}
                                            </button>

                                            @if($traspaso->unipago_revisado_at)
                                                <span class="text-[8px] text-slate-400 font-bold mt-1">
                                                    Última vez: {{ $traspaso->unipago_revisado_at->diffForHumans() }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 border-l border-slate-100 pl-4">
                                    @if($traspaso->fecha_efectivo)
                                        @if($traspaso->es_emitido)
                                            <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase rounded-md flex items-center gap-1 border border-blue-100">
                                                <i class="ph-fill ph-check-circle"></i> Emitido
                                            </span>
                                        @else
                                            <button @click="emitirCarnet({{ $traspaso->id }})" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                                <i class="ph ph-identification-card text-lg"></i>
                                            </button>
                                        @endif
                                    @endif
                                    
                                    <a href="{{ route('traspasos.edit', $traspaso->id) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Ver Detalle y Editar">
                                        <i class="ph ph-pencil-simple-line text-lg"></i>
                                    </a>

                                    <button @click="fetchHistory({{ $traspaso->id }}, '{{ addslashes($traspaso->nombre_afiliado) }}', '{{ $traspaso->cedula_afiliado }}')" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Ver Historial">
                                        <i class="ph ph-clock-counter-clockwise text-lg"></i>
                                    </button>

                                    @if(!$isClosed)
                                    <button @click="rechazar({{ $traspaso->id }})" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-all" title="Rechazar">
                                        <i class="ph ph-x-circle text-lg"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $traspasos->links() }}
    </div>

    <!-- MODAL ENRIQUECIMIENTO (Mantener igual que antes) -->
    <div x-show="showModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-cloak>
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showModal = false"></div>
        <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl relative z-10 overflow-hidden p-8">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-blue-600 mb-1">Enriquecer Registro</h3>
                    <p class="text-2xl font-black text-slate-900 tracking-tighter uppercase" x-text="selectedTraspaso.nombre"></p>
                </div>
                <button @click="showModal = false" class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400">
                    <i class="ph ph-x text-xl font-bold"></i>
                </button>
            </div>
            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Fecha de Efectividad</label>
                    <div class="relative">
                        <i class="ph ph-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="date" x-model="selectedTraspaso.fecha_efectivo" 
                               class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Cantidad de Dependientes</label>
                    <div class="relative">
                        <i class="ph ph-users absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="number" x-model="selectedTraspaso.cantidad_dependientes" min="0"
                               class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500/20">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-10">
                <button @click="showModal = false" class="bg-slate-100 text-slate-600 rounded-2xl py-4 text-[10px] font-black uppercase">Cancelar</button>
                <button @click="saveEnrichment()" class="bg-blue-600 text-white rounded-2xl py-4 text-[10px] font-black uppercase shadow-xl">Guardar Datos</button>
            </div>
        </div>
    </div>

    <!-- MODAL VERIFICACIÓN UNIPAGO (AUDITORÍA OPERATIVA) -->
    <div x-show="showVerifyModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-cloak>
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showVerifyModal = false"></div>
        <div class="bg-white w-full max-w-xl rounded-[40px] shadow-2xl relative z-10 overflow-hidden">
            <!-- Header con Estilo Premium -->
            <div class="p-8 bg-slate-900 text-white relative">
                <div class="absolute top-0 right-0 p-8">
                    <button @click="showVerifyModal = false" class="w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center transition-all">
                        <i class="ph ph-x text-xl font-bold"></i>
                    </button>
                </div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                        <i class="ph-fill ph-shield-check text-2xl text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-amber-400">Auditoría Unipago</h3>
                        <p class="text-xl font-black tracking-tighter uppercase" x-text="selectedTraspaso.nombre"></p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="px-4 py-2 bg-white/5 rounded-xl border border-white/10">
                        <span class="text-[9px] font-black text-slate-400 uppercase block mb-1">Cédula Afiliado</span>
                        <span class="text-xs font-bold font-mono" x-text="selectedTraspaso.cedula"></span>
                    </div>
                </div>
            </div>

            <div class="p-8 space-y-6">
                <!-- Estado en Unipago -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Estado en Portal</label>
                        <select x-model="selectedTraspaso.unipago_status" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-amber-500/20">
                            <option value="pendiente">Pendiente de Revisión</option>
                            <option value="en_revision">En Proceso / Visto hoy</option>
                            <option value="rechazo_visto">Rechazo Detectado</option>
                            <option value="verificado">Efectivo / OK</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Dependientes Hallados</label>
                        <div class="flex items-center gap-4">
                            <div class="relative flex-1">
                                <i class="ph ph-users absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input type="number" x-model="selectedTraspaso.cantidad_dependientes" min="0" class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-4 text-sm font-bold focus:ring-2 focus:ring-amber-500/20">
                            </div>
                            <button @click="selectedTraspaso.cantidad_dependientes++" class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center font-black text-slate-600">+</button>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Observaciones de Auditoría</label>
                    <textarea x-model="selectedTraspaso.unipago_observaciones" placeholder="Escriba detalles relevantes (ej: Falta firma, rechazo por cédula...)" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-medium focus:ring-2 focus:ring-amber-500/20 h-24"></textarea>
                </div>

                <!-- Toggle de Verificación Final -->
                <div class="bg-amber-50 p-6 rounded-3xl border border-amber-100 flex items-center justify-between">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                            <i class="ph-fill ph-flag-checkered text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-amber-900 uppercase">¿Finalizar Verificación?</p>
                            <p class="text-[10px] font-medium text-amber-700">Marque si el proceso en Unipago está concluido.</p>
                        </div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" x-model="selectedTraspaso.verificado" class="sr-only peer">
                        <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>

                <!-- Footer Acciones -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                    <button @click="showVerifyModal = false" class="bg-slate-100 text-slate-600 rounded-2xl py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
                    <button @click="saveVerification()" class="bg-slate-900 text-white rounded-2xl py-4 text-[10px] font-black uppercase tracking-widest shadow-xl hover:bg-slate-800 transition-all">Guardar Seguimiento</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- PANEL LATERAL: HISTORIAL / TIMELINE -->
    <div x-show="showHistory" 
         class="fixed inset-0 z-[100] overflow-hidden" 
         x-cloak>
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" 
             @click="showHistory = false"
             x-show="showHistory"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <div class="fixed inset-y-0 right-0 max-w-full flex">
            <div class="w-screen max-w-md"
                 x-show="showHistory"
                 x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                
                <div class="h-full flex flex-col bg-white shadow-2xl overflow-y-scroll rounded-l-[40px]">
                    <!-- Header -->
                    <div class="p-8 bg-slate-900 text-white">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-black uppercase tracking-tighter">Trazabilidad</h2>
                            <button @click="showHistory = false" class="p-2 hover:bg-white/10 rounded-xl transition-all">
                                <i class="ph ph-x text-2xl"></i>
                            </button>
                        </div>
                        <template x-if="selectedForHistory">
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1" x-text="selectedForHistory.cedula_afiliado"></p>
                                <h3 class="text-lg font-black leading-tight" x-text="selectedForHistory.nombre_afiliado"></h3>
                            </div>
                        </template>
                    </div>

                    <!-- Timeline Content -->
                    <div class="flex-1 relative p-8">
                        <div x-show="historyLoading" class="flex flex-col items-center justify-center h-40 gap-4">
                            <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                            <p class="text-[10px] font-black text-slate-400 uppercase">Cargando historial...</p>
                        </div>

                        <div x-show="!historyLoading && currentHistory.length > 0" class="relative">
                            <!-- Línea Vertical Conectora -->
                            <div class="absolute left-4 top-2 bottom-0 w-0.5 bg-slate-100"></div>

                            <div class="space-y-10">
                                <template x-for="log in currentHistory" :key="log.id">
                                    <div class="relative pl-12">
                                        <!-- Punto -->
                                        <div class="absolute left-0 top-1.5 w-8 h-8 rounded-full border-4 border-white shadow-sm flex items-center justify-center transition-all group"
                                             :class="{
                                                 'bg-emerald-500': log.event === 'created',
                                                 'bg-blue-600': log.event === 'updated',
                                                 'bg-rose-500': log.event === 'deleted'
                                             }">
                                             <i class="ph-fill text-white text-sm" 
                                                :class="{
                                                    'ph-plus-circle': log.event === 'created',
                                                    'ph-pencil-simple': log.event === 'updated',
                                                    'ph-trash': log.event === 'deleted'
                                                }"></i>
                                        </div>

                                        <div class="flex flex-col">
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-[11px] font-black text-slate-900" x-text="log.user"></span>
                                                <span class="text-[9px] font-bold text-slate-400 uppercase" x-text="log.relative_date"></span>
                                            </div>
                                            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                                <p class="text-xs text-slate-600 leading-relaxed" x-html="log.description"></p>
                                            </div>
                                            <span class="mt-2 text-[9px] font-bold text-slate-400 uppercase tracking-tighter" x-text="log.date"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div x-show="!historyLoading && currentHistory.length === 0" class="flex flex-col items-center justify-center h-40 text-slate-400">
                            <i class="ph ph-ghost text-4xl mb-4 opacity-20"></i>
                            <p class="text-[10px] font-black uppercase">No hay registros de cambios</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
