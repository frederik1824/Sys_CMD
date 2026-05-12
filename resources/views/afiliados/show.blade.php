@extends('layouts.app')
@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto min-h-screen bg-slate-50/50">
    <!-- Breadcrumbs & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <nav class="flex items-center gap-2">
            <a href="{{ $returnUrl }}" class="text-xs text-slate-500 hover:text-primary transition-colors uppercase tracking-wider font-bold">{{ $returnLabel }}</a>
            <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
            <span class="text-xs text-primary uppercase tracking-wider font-extrabold">Detalle de Perfil</span>
        </nav>
        <div class="flex gap-3">
            @if(strtolower($afiliado->estado?->nombre) === 'completado')
                <div class="px-5 py-2.5 bg-emerald-100 text-emerald-800 font-bold text-sm rounded-xl flex items-center gap-2 border border-emerald-200">
                    <span class="material-symbols-outlined text-lg">check_circle</span>
                    Expediente Completado
                </div>
            @endif

            <button type="button" onclick="document.getElementById('estadoModal').style.display='flex'" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">sync_alt</span>
                Actualizar Estado
            </button>
            <button type="button" onclick="document.getElementById('reassignModal').style.display='flex'" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">person_add</span>
                Reasignar
            </button>
            <a href="{{ route('carnetizacion.afiliados.edit', $afiliado) }}" class="px-5 py-2.5 bg-slate-900 text-white font-bold text-sm rounded-xl shadow-lg hover:bg-slate-800 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">edit</span>
                Editar Perfil
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
        <!-- Left Sidebar: Profile Overview -->
        <div class="xl:col-span-4 space-y-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 flex flex-col items-center relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-br from-primary/10 to-transparent"></div>
                
                <div class="w-28 h-28 bg-white p-1 rounded-full shadow-md z-10 mb-4 ring-4 ring-primary/5">
                    <div class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-4xl font-black text-slate-400">
                        {{ strtoupper(substr($afiliado->nombre_completo, 0, 1)) }}
                    </div>
                </div>

                <div class="z-10 text-center w-full">
                    <h2 class="text-2xl font-extrabold text-slate-800 mb-1">{{ $afiliado->nombre_completo }}</h2>
                    <p class="text-sm font-semibold text-slate-500 mb-4 flex justify-center items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">badge</span> {{ $afiliado->cedula }}
                    </p>
                    
                    <div class="inline-flex items-center px-4 py-2 border rounded-full {{ $afiliado->status_color_class }} shadow-sm">
                        <div class="w-1.5 h-1.5 rounded-full mr-2 
                            {{ (str_contains($afiliado->status_color_class, 'emerald') || str_contains($afiliado->status_color_class, 'blue')) ? 'bg-current' : 'bg-current animate-pulse' }}"></div>
                        <span class="text-[0.65rem] font-black uppercase tracking-widest">{{ $afiliado->estado?->nombre ?? 'Sin Estado' }}</span>
                    </div>

                    @if($afiliado->reasignado)
                        <div class="mt-3 inline-flex items-center px-3 py-1 bg-rose-50 text-rose-600 border border-rose-100 rounded-full shadow-sm mx-auto">
                            <span class="material-symbols-outlined text-[14px] mr-1.5">repeat</span>
                            <span class="text-[0.6rem] font-black uppercase tracking-widest">Reasignado para Auditoría</span>
                        </div>
                    @endif

                    {{-- Data Quality Diagnostic --}}
                    @php $quality = $afiliado->data_quality; @endphp
                    <div class="mt-6 p-5 rounded-3xl bg-{{ $quality->color }}-50/50 border border-{{ $quality->color }}-100/50 text-left w-full">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-{{ $quality->color }}-500 text-sm">health_metrics</span>
                                <span class="text-[0.65rem] font-black text-{{ $quality->color }}-600 uppercase tracking-widest">Salud: {{ $quality->score }}%</span>
                            </div>
                            @if($quality->is_ready)
                                <span class="px-2 py-0.5 bg-emerald-500 text-white text-[0.55rem] font-black uppercase tracking-widest rounded-md">Completo</span>
                            @else
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-600 text-[0.55rem] font-black uppercase tracking-widest rounded-md">Incompleto</span>
                            @endif
                        </div>
                        <div class="w-full bg-white h-1.5 rounded-full overflow-hidden mb-4">
                            <div class="h-full bg-{{ $quality->color }}-500" style="width: {{ $quality->score }}%"></div>
                        </div>
                        @if(count($quality->missing) > 0)
                            <p class="text-[0.55rem] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Campos requeridos faltantes:</p>
                            <div class="flex flex-wrap gap-1.5 px-1">
                                @foreach($quality->missing as $field)
                                    <span class="px-2 py-1 bg-white border border-{{ $quality->color }}-100/50 text-[0.55rem] font-bold text-slate-500 rounded-lg capitalize shadow-sm">
                                        {{ str_replace('_id', '', str_replace('_', ' ', $field)) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="w-full mt-8 pt-6 border-t border-slate-100 space-y-4 z-10">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                            <span class="material-symbols-outlined text-sm">phone</span>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Teléfono Móvil</p>
                            <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $afiliado->telefono ?? 'No registrado' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                            <span class="material-symbols-outlined text-sm">location_on</span>
                        </div>
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Ubicación</p>
                            <p class="text-sm font-semibold text-slate-700 mt-0.5">{{ $afiliado->provincia ?? 'N/D' }}, {{ $afiliado->municipio ?? 'N/D' }}</p>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $afiliado->direccion ?? 'Sin dirección' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Insurance & Contract Info -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">
                <h3 class="flex items-center gap-2 font-bold text-slate-800 text-lg mb-6">
                    <span class="material-symbols-outlined text-primary">verified</span> Datos de Cobertura
                </h3>
                
                <div class="space-y-5">
                    <div class="bg-slate-50 rounded-xl p-4 flex items-center justify-between border border-slate-100">
                        <div>
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Empresa / Cliente</p>
                            <p class="text-sm font-bold text-slate-700 mt-1">
                                @if($afiliado->empresa_id)
                                    <a href="{{ route('sistema.empresas.show', $afiliado->empresaModel) }}" class="text-primary hover:underline flex items-center gap-1">
                                        <i class="fa-solid fa-building text-xs"></i>
                                        {{ $afiliado->empresaModel->nombre ?? $afiliado->empresa }}
                                    </a>
                                @else
                                    {{ $afiliado->empresa ?? 'No aplica' }}
                                @endif
                            </p>
                        </div>
                        <span class="material-symbols-outlined text-slate-300">work</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Contrato</p>
                            <p class="text-sm font-bold text-slate-700 mt-1">{{ $afiliado->contrato ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest">Póliza</p>
                            <p class="text-sm font-bold text-slate-700 mt-1">{{ $afiliado->poliza ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bento Stats Summary (Mini) -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-primary-container p-4 rounded-2xl text-white shadow-sm border border-primary/20">
                    <p class="text-[0.6rem] font-black uppercase tracking-widest opacity-80 mb-1">Días en Proceso</p>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black">{{ $afiliado->dias_transcurridos }}</span>
                        <span class="text-[0.65rem] font-bold bg-white/20 px-2 py-0.5 rounded-full">SLA</span>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                    <p class="text-[0.6rem] font-black uppercase tracking-widest text-slate-400 mb-1">Tickets / Notas</p>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-black text-slate-800">{{ $afiliado->notas->count() }}</span>
                        <span class="material-symbols-outlined text-amber-500 text-lg">sticky_note_2</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Content: Tabs & Details -->
        <div class="xl:col-span-8 flex flex-col gap-8">
            
            <!-- Operations Flow -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 relative overflow-hidden">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="font-bold text-slate-800 text-lg">Progreso Operativo</h3>
                    <div class="flex items-center gap-2 bg-slate-50 p-1 pl-3 pr-1 rounded-full border border-slate-100 text-xs text-slate-500 font-semibold">
                        Responsable Actual:
                        @if($afiliado->responsable)
                            <span class="bg-white shadow-sm border border-slate-200 px-3 py-1 rounded-full text-slate-700 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-primary">account_circle</span>
                                {{ $afiliado->responsable?->nombre }}
                            </span>
                        @else
                            <span class="bg-white px-3 py-1 rounded-full text-slate-400">Sin asignar</span>
                        @endif
                    </div>
                </div>

                @php
                    $stepIndex = min(intval($afiliado->estado_id), 4); // Fake logic just for visual
                @endphp
                <div class="relative flex justify-between px-4 pb-4">
                    <div class="absolute top-5 left-10 right-10 h-1 bg-slate-100 rounded-full z-0"></div>
                    <div class="absolute top-5 left-10 h-1 bg-gradient-to-r from-primary to-primary-container rounded-full z-0 transition-all duration-1000" style="width: {{ ($stepIndex / 4) * 100 }}%"></div>
                    
                    <!-- Steps -->
                    @php 
                        $steps = ['Registrado', 'En Producción', 'Enviado', 'Entregado', 'Cierre'];
                    @endphp
                    @foreach($steps as $idx => $step)
                    <div class="relative z-10 flex flex-col items-center gap-3 w-20">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center border-4 border-white shadow-sm transition-colors duration-500 
                            {{ $idx <= $stepIndex ? 'bg-primary text-white' : 'bg-slate-50 text-slate-300' }}">
                            @if($idx < $stepIndex)
                                <span class="material-symbols-outlined text-sm font-bold">check</span>
                            @elseif($idx == $stepIndex)
                                <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                            @else
                                <span class="text-xs font-bold">{{ $idx + 1 }}</span>
                            @endif
                        </div>
                        <span class="text-[0.65rem] font-bold uppercase tracking-wider text-center {{ $idx <= $stepIndex ? 'text-slate-800' : 'text-slate-400' }}">{{ $step }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">folder_open</span> Documentos y Evidencias
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @php
                        $acuse = $afiliado->evidenciasAfiliado->where('tipo_documento', 'acuse_recibo')->first();
                        $formulario = $afiliado->evidenciasAfiliado->where('tipo_documento', 'formulario_firmado')->first();
                    @endphp

                    <!-- Card Acuse -->
                    <div class="group border {{ $acuse ? 'border-primary/20 bg-primary/5' : 'border-slate-100 bg-slate-50' }} rounded-2xl p-6 transition-all hover:shadow-md">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 {{ $acuse ? 'bg-primary text-white' : 'bg-white text-slate-300 shadow-sm' }} rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">fact_check</span>
                            </div>
                            @if($acuse)
                                <span class="px-3 py-1 bg-white text-primary rounded-full text-[0.65rem] font-bold uppercase tracking-wider shadow-sm border border-primary/10">Subido</span>
                            @else
                                <span class="px-3 py-1 bg-white text-slate-400 rounded-full text-[0.65rem] font-bold uppercase tracking-wider shadow-sm border border-slate-100">Faltante</span>
                            @endif
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1 text-sm">Acuse de Recibo</h4>
                        <p class="text-[0.7rem] text-slate-500 mb-6 leading-relaxed">Soporte físico de entrega en manos del afiliado.</p>
                        
                        @if($acuse)
                            <div class="space-y-3">
                                <div class="flex items-center justify-between bg-white/50 px-3 py-2 rounded-lg border border-primary/10">
                                    <span class="text-[0.6rem] font-bold uppercase text-slate-400">Estado Doc:</span>
                                    <span class="text-[0.65rem] font-black uppercase {{ $acuse->status === 'validado' ? 'text-emerald-600' : ($acuse->status === 'rechazado' ? 'text-rose-600' : 'text-amber-600') }}">
                                        {{ $acuse->status ?? 'Recibido' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $acuse->file_path) }}" target="_blank" class="flex-1 py-2.5 bg-white hover:bg-primary hover:text-white border border-primary/20 text-primary font-bold text-xs uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2 text-center">
                                        Ver Documento
                                    </a>
                                </div>
                                <form action="{{ route('evidencias.update_status', $acuse->id) }}" method="POST" class="flex gap-1">
                                    @csrf
                                    <input type="hidden" name="status" value="valido">
                                    <button type="submit" class="flex-1 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-[10px] font-bold uppercase tracking-tighter transition-colors">Validar</button>
                                </form>
                            </div>
                            <form action="{{ route('carnetizacion.afiliados.upload_evidencia', $afiliado) }}" method="POST" enctype="multipart/form-data" class="w-full flex gap-2 items-center">
                                @csrf
                                <input type="hidden" name="tipo_documento" value="acuse_recibo">
                                <div class="relative flex-1">
                                    <input type="file" name="file" required accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                    <div class="w-full py-2.5 bg-white border border-slate-200 text-slate-500 font-bold text-xs uppercase tracking-widest rounded-xl flex items-center justify-center gap-2 group-hover:border-primary/30 transition-colors">
                                        Seleccionar
                                    </div>
                                </div>
                                <button type="submit" class="w-10 h-10 bg-slate-800 hover:bg-primary text-white rounded-xl flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">publish</span>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Card Formulario -->
                    <div class="group border {{ $formulario ? 'border-primary/20 bg-primary/5' : 'border-slate-100 bg-slate-50' }} rounded-2xl p-6 transition-all hover:shadow-md">
                        <div class="flex justify-between items-start mb-4">
                            <div class="w-12 h-12 {{ $formulario ? 'bg-primary text-white' : 'bg-white text-slate-300 shadow-sm' }} rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">assignment_turned_in</span>
                            </div>
                            @if($formulario)
                                <span class="px-3 py-1 bg-white text-primary rounded-full text-[0.65rem] font-bold uppercase tracking-wider shadow-sm border border-primary/10">Subido</span>
                            @else
                                <span class="px-3 py-1 bg-white text-slate-400 rounded-full text-[0.65rem] font-bold uppercase tracking-wider shadow-sm border border-slate-100">Faltante</span>
                            @endif
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1 text-sm">Formulario Firmado</h4>
                        <p class="text-[0.7rem] text-slate-500 mb-6 leading-relaxed">Formulario de registro físico lleno y sellado/huellado.</p>
                        
                        @if($formulario)
                            <div class="space-y-3">
                                <div class="flex items-center justify-between bg-white/50 px-3 py-2 rounded-lg border border-primary/10">
                                    <span class="text-[0.6rem] font-bold uppercase text-slate-400">Estado Doc:</span>
                                    <span class="text-[0.65rem] font-black uppercase {{ $formulario->status === 'validado' ? 'text-emerald-600' : ($formulario->status === 'rechazado' ? 'text-rose-600' : 'text-amber-600') }}">
                                        {{ $formulario->status ?? 'Recibido' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $formulario->file_path) }}" target="_blank" class="flex-1 py-2.5 bg-white hover:bg-primary hover:text-white border border-primary/20 text-primary font-bold text-xs uppercase tracking-widest rounded-xl transition-all flex items-center justify-center gap-2 text-center">
                                        Ver Documento
                                    </a>
                                </div>
                                <form action="{{ route('evidencias.update_status', $formulario->id) }}" method="POST" class="flex gap-1">
                                    @csrf
                                    <input type="hidden" name="status" value="valido">
                                    <button type="submit" class="flex-1 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-[10px] font-bold uppercase tracking-tighter transition-colors">Validar</button>
                                </form>
                            </div>
                            <form action="{{ route('carnetizacion.afiliados.upload_evidencia', $afiliado) }}" method="POST" enctype="multipart/form-data" class="w-full flex gap-2 items-center">
                                @csrf
                                <input type="hidden" name="tipo_documento" value="formulario_firmado">
                                <div class="relative flex-1">
                                    <input type="file" name="file" required accept=".jpg,.jpeg,.png,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" />
                                    <div class="w-full py-2.5 bg-white border border-slate-200 text-slate-500 font-bold text-xs uppercase tracking-widest rounded-xl flex items-center justify-center gap-2 group-hover:border-primary/30 transition-colors">
                                        Seleccionar
                                    </div>
                                </div>
                                <button type="submit" class="w-10 h-10 bg-slate-800 hover:bg-primary text-white rounded-xl flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-[18px]">publish</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

             <!-- Combined Audit Timeline (Audit Trail & Notes) -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">history_edu</span> Auditoría e Historia del Expediente
                    </h3>
                    <button onclick="document.getElementById('noteSection').scrollIntoView({behavior: 'smooth'})" class="text-xs font-black uppercase tracking-widest text-primary hover:text-blue-700 flex items-center gap-1 transition-colors">
                        <span class="material-symbols-outlined text-sm">add_comment</span> Nueva Nota
                    </button>
                </div>

                <div class="space-y-8 relative before:absolute before:left-[17px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-50">
                    {{-- Combine notes, history and audits and sort --}}
                    @php
                        $timeline = collect();
                        foreach($afiliado->historialEstados as $h) {
                            $timeline->push(['type' => 'status', 'date' => $h->created_at, 'data' => $h]);
                        }
                        foreach($afiliado->notas as $n) {
                            $timeline->push(['type' => 'note', 'date' => $n->created_at, 'data' => $n]);
                        }
                        if (isset($auditLogs)) {
                            foreach($auditLogs as $a) {
                                $timeline->push(['type' => 'audit', 'date' => $a->created_at, 'data' => $a]);
                            }
                        }
                        $timeline = $timeline->sortByDesc('date');
                    @endphp

                    @forelse($timeline as $item)
                        <div class="relative pl-12 page-transition">
                            <!-- Timeline Node -->
                            <div class="absolute left-0 top-1.5 w-[36px] h-[36px] rounded-full bg-white border-2 border-slate-50 flex items-center justify-center z-10 shadow-sm
                                @if($item['type'] === 'status') border-primary/20 
                                @elseif($item['type'] === 'note') border-amber-200 
                                @else border-emerald-200 @endif">
                                <span class="material-symbols-outlined text-[18px] 
                                    @if($item['type'] === 'status') text-primary 
                                    @elseif($item['type'] === 'note') text-amber-500 
                                    @else text-emerald-500 @endif">
                                    {{ $item['type'] === 'status' ? 'sync_alt' : ($item['type'] === 'note' ? 'sticky_note_2' : 'cloud_sync') }}
                                </span>
                            </div>
                            
                            <div class="bg-white rounded-2xl p-5 border border-slate-100 hover:border-primary/20 hover:shadow-xl hover:shadow-primary/5 transition-all group">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400 mb-0.5">
                                            @if($item['type'] === 'status') Cambio de Estado
                                            @elseif($item['type'] === 'note') Nota de Seguimiento
                                            @else Sincronización Cloud @endif
                                        </p>
                                        <h4 class="text-sm font-bold text-slate-800">
                                            @if($item['type'] === 'status')
                                                <span class="text-slate-400 font-medium">{{ $item['data']->estadoAnterior?->nombre }}</span> 
                                                <span class="mx-1 text-slate-300">&rarr;</span> 
                                                <span class="text-primary">{{ $item['data']->estadoNuevo?->nombre }}</span>
                                            @elseif($item['type'] === 'note')
                                                Observación Registrada
                                            @else
                                                Actualización de Campo: <span class="text-emerald-600">{{ ucfirst(str_replace('_', ' ', $item['data']->field)) }}</span>
                                            @endif
                                        </h4>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[0.65rem] font-bold text-slate-400 uppercase">{{ $item['date']->format('d M, Y') }}</p>
                                        <p class="text-[0.6rem] font-medium text-slate-300">{{ $item['date']->format('h:i A') }}</p>
                                    </div>
                                </div>
                                
                                <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-50 group-hover:bg-white group-hover:border-primary/10 transition-colors">
                                    <p class="text-[0.85rem] text-slate-700 leading-relaxed font-medium">
                                        @if($item['type'] === 'status')
                                            {{ $item['data']->observacion ?: 'Sin detalles adicionales.' }}
                                        @elseif($item['type'] === 'note')
                                            {{ $item['data']->contenido }}
                                        @else
                                            <span class="text-slate-400">De:</span> <span class="line-through">{{ $item['data']->old_value ?: '(Vacío)' }}</span><br>
                                            <span class="text-slate-400">A:</span> <span class="font-bold text-emerald-700">{{ $item['data']->new_value ?: '(Vacío)' }}</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 mt-4">
                                    @if($item['type'] === 'audit')
                                        <div class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[0.55rem] font-black uppercase tracking-widest">
                                            Origen: {{ $item['data']->company_origin }}
                                        </div>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-slate-900 border-2 border-white flex items-center justify-center text-[8px] font-black text-white shadow-sm">
                                            {{ strtoupper(substr($item['data']->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <span class="text-[0.7rem] font-bold text-slate-500">{{ $item['data']->user->name ?? 'Sistema Automático' }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                            <span class="material-symbols-outlined text-slate-200 text-5xl mb-4">history_toggle_off</span>
                            <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">Aún no hay historia en este expediente</p>
                        </div>
                    @endforelse
                </div>

                <!-- Note Form Integrated at Bottom of Timeline -->
                <div id="noteSection" class="mt-12 pt-10 border-t border-slate-100">
                    <div class="bg-slate-50 rounded-3xl p-8 border border-slate-100">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined">add_comment</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Agregar Nueva Nota</h4>
                                <p class="text-xs text-slate-500">Los incidentes registrados ayudarán al equipo de soporte.</p>
                            </div>
                        </div>
                        <form action="{{ route('carnetizacion.notas.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="afiliado_id" value="{{ $afiliado->id }}">
                            <textarea name="contenido" rows="3" required placeholder="Describe lo que sucedió con este carnet..." 
                                class="w-full bg-white border border-slate-200 rounded-2xl p-4 text-sm font-medium focus:ring-4 focus:ring-primary/5 focus:border-primary outline-none transition-all resize-none shadow-sm"></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="px-8 py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-[0.7rem] font-black uppercase tracking-widest shadow-xl transition-all flex items-center gap-2 hover:scale-105 active:scale-95">
                                    Publicar Nota <span class="material-symbols-outlined text-sm">send</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Modal Cambio de Estado Individual -->
<div id="estadoModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
    <form method="POST" action="{{ route('carnetizacion.afiliados.update_status', $afiliado) }}" class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 scale-100 transition-all">
        @csrf
        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-2xl">sync</span>
        </div>
        
        <h3 class="text-xl font-extrabold mb-2 text-slate-800">Actualizar Progreso</h3>
        <p class="text-sm text-slate-500 mb-8 leading-relaxed">Cambia el estado actual de <strong class="text-slate-700">{{ $afiliado->nombre_completo }}</strong> para reflejar el avance en el proceso operativo.</p>
        
        <div class="space-y-5">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Nuevo Estado</label>
                <select name="estado_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3.5 text-sm font-semibold text-slate-700 transition-all outline-none">
                    <option value="">Seleccione el siguiente paso...</option>
                    @foreach($estados as $est)
                        @if($est->id != ($afiliado->estado_id ?? 0))
                            <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Acción Rápida (Opcional)</label>
                <select name="motivo_rapido" class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3.5 text-sm font-semibold text-slate-700 transition-all outline-none">
                    <option value="">-- Personalizar observación --</option>
                    <option value="Documentos recibidos físicamente">Documentos recibidos físicamente</option>
                </select>
            </div>

            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Observación Adicional (Opcional)</label>
                <textarea name="observacion" rows="3" placeholder="Notas adicionales sobre el cambio..." class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3.5 text-sm transition-all outline-none resize-none"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('estadoModal').style.display='none'" class="px-5 py-3 rounded-xl text-slate-500 font-bold text-sm hover:bg-slate-50 transition-colors">Cancelar</button>
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-primary/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">save</span> Guardar Estado
            </button>
        </div>
    </form>
</div>
<!-- Modal Reapertura -->
<div id="reopenModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
    <form method="POST" action="{{ route('carnetizacion.afiliados.reopen', $afiliado) }}" class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-md border border-slate-100 scale-100 transition-all">
        @csrf
        <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-2xl">history_toggle_off</span>
        </div>
        
        <h3 class="text-xl font-extrabold mb-2 text-slate-800 uppercase tracking-tighter">Solicitar Reapertura</h3>
        <p class="text-sm text-slate-500 mb-8 leading-relaxed">Estás intentando abrir un expediente que ya ha sido auditado y cerrado. **Debes justificar esta acción** para el registro de auditoría.</p>
        
        <div class="space-y-5">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Motivo de Auditoría</label>
                <textarea name="motivo" rows="4" required minlength="10" placeholder="Ej: Error en el RNC de la empresa, se requiere subir nueva evidencia..." class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 p-3.5 text-sm transition-all outline-none resize-none"></textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('reopenModal').style.display='none'" class="px-5 py-3 rounded-xl text-slate-500 font-bold text-sm hover:bg-slate-50 transition-colors">Cancelar</button>
            <button type="submit" class="px-6 py-3 bg-rose-600 text-white rounded-xl font-bold text-sm hover:bg-rose-700 transition-colors shadow-lg shadow-rose-600/20 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">lock_open</span> Confirmar Reapertura
            </button>
        </div>
    </form>
</div>

<!-- Modal Reasignar Responsable Individual -->
<div id="reassignModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
    <form method="POST" action="{{ route('carnetizacion.afiliados.reassign', $afiliado) }}" class="bg-white p-8 rounded-3xl shadow-2xl w-full max-w-sm border border-slate-100 scale-100 transition-all">
        @csrf
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-6">
            <span class="material-symbols-outlined text-2xl">person_add</span>
        </div>
        
        <h3 class="text-xl font-extrabold mb-2 text-slate-800">Cambiar Responsable</h3>
        <p class="text-sm text-slate-500 mb-8 leading-relaxed">Selecciona el nuevo responsable para <strong class="text-slate-700">{{ $afiliado->nombre_completo }}</strong>. Esto quedará registrado en el historial.</p>
        
        <div class="space-y-4">
            <div>
                <label class="block text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-2">Nuevo Responsable</label>
                <select name="responsable_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3.5 text-sm font-semibold text-slate-700 transition-all outline-none">
                    <option value="">Seleccione un responsable...</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ ($afiliado->responsable_id == $resp->id) ? 'selected' : '' }}>
                            {{ $resp->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
            <button type="button" onclick="document.getElementById('reassignModal').style.display='none'" class="px-5 py-3 rounded-xl text-slate-500 font-bold text-sm hover:bg-slate-50 transition-colors">Cancelar</button>
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-bold text-sm hover:opacity-90 transition-opacity shadow-lg shadow-primary/20 flex items-center gap-2">
                Actualizar Responsable
            </button>
        </div>
    </form>
</div>
@endsection
