@extends('layouts.app')

@section('content')
<div class="p-4 md:p-10 max-w-[1600px] mx-auto min-h-[calc(100vh-100px)]" x-data="{ 
    tipoSeleccionado: null,
    tipos: {{ $tipos->toJson() }},
    isDragging: false,
    selectedFiles: [],
    
    get documentos() {
        if (!this.tipoSeleccionado) return [];
        let tipo = this.tipos.find(t => t.id == this.tipoSeleccionado);
        return tipo ? tipo.documentos_requeridos : [];
    },

    handleFile(event, docId = null) {
        let files = event.target.files;
        if (!files || files.length === 0) {
            if (event.dataTransfer && event.dataTransfer.files) {
                files = event.dataTransfer.files;
            } else {
                return;
            }
        }
        
        for(let i=0; i<files.length; i++) {
            this.selectedFiles.push({
                file: files[i],
                docId: docId,
                name: files[i].name,
                size: (files[i].size / 1024 / 1024).toFixed(2) + ' MB'
            });
        }
        
        this.updateRealInput();
        event.target.value = '';
    },
    
    removeFile(index) {
        this.selectedFiles.splice(index, 1);
        this.updateRealInput();
    },
    
    updateRealInput() {
        // Para simplificar el envío, mantendremos el input múltiple original
        // Pero el controlador deberá manejar la asociación si es necesario.
        // O mejor, usaremos inputs dinámicos con el ID del documento.
        this.$nextTick(() => {
            const container = document.getElementById('dynamic_inputs_container');
            container.innerHTML = '';
            this.selectedFiles.forEach((f, idx) => {
                const input = document.createElement('input');
                input.type = 'file';
                input.name = f.docId ? `expediente_doc[${f.docId}][]` : 'expediente_pdf[]';
                input.className = 'hidden';
                
                // Creamos un DataTransfer para asignar el archivo al input
                const dt = new DataTransfer();
                dt.items.add(f.file);
                input.files = dt.files;
                
                container.appendChild(input);
            });
        });
    }
}">
    <!-- ESPACIADO SUPERIOR -->
    <div class="mb-10"></div>
    <!-- HEADER INTEGRADO -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
        <div class="space-y-4">
            <a href="{{ route('afiliacion.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-500 hover:text-indigo-600 hover:border-indigo-100 hover:bg-indigo-50/30 transition-all text-[10px] font-black uppercase tracking-widest shadow-sm group">
                <i class="ph-bold ph-arrow-left group-hover:-translate-x-1 transition-transform"></i> 
                Volver al Panel Principal
            </a>
            <div class="relative">
                <div class="absolute -left-6 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-indigo-600 rounded-full hidden md:block"></div>
                <h1 class="text-5xl font-black tracking-tighter text-slate-900 leading-tight">
                    Apertura de <span class="text-indigo-600">Nuevo Trámite</span>
                </h1>
                <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed mt-2">
                    Inicia un proceso de afiliación capturando los datos esenciales y consolidando el expediente documental en un único flujo optimizado.
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-4 bg-white p-3 rounded-[32px] border border-slate-100 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                <i class="ph-fill ph-user-circle-plus text-2xl"></i>
            </div>
            <div class="pr-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-1">Responsable</p>
                <p class="text-sm font-black text-slate-900 leading-none">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('afiliacion.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-12 gap-10 items-start">
        @csrf
        
        <!-- IZQUIERDA: FORMULARIO PRINCIPAL (8 COL) -->
        <div class="xl:col-span-8 space-y-10">
            
            <!-- SECCIÓN 1: ESTRATEGIA -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 md:p-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/30 rounded-full -mr-32 -mt-32 blur-3xl"></div>
                
                <div class="flex items-center gap-4 mb-10 relative z-10">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-lg shadow-indigo-200">
                        <i class="ph-bold ph-strategy text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">1. Estrategia del Trámite</h2>
                        <p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest mt-0.5">Clasificación y Urgencia</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Tipo de Solicitud Operativa</label>
                        <div class="relative group">
                            <select name="tipo_solicitud_id" x-model="tipoSeleccionado" required
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] px-8 py-5 text-base font-bold text-slate-800 appearance-none transition-all cursor-pointer shadow-sm">
                                <option value="">Seleccione el tipo de trámite...</option>
                                @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400 group-focus-within:text-indigo-600">
                                <i class="ph-bold ph-caret-down text-xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-4">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Prioridad de Atención</label>
                        <div class="flex gap-4">
                            @foreach(['Normal' => ['icon' => 'ph-clock', 'color' => 'slate'], 'Alta' => ['icon' => 'ph-lightning', 'color' => 'amber'], 'Urgente' => ['icon' => 'ph-warning-octagon', 'color' => 'rose']] as $val => $meta)
                            <label class="flex-1 cursor-pointer group">
                                <input type="radio" name="prioridad" value="{{ $val }}" {{ $val == 'Normal' ? 'checked' : '' }} class="sr-only">
                                <div class="py-6 rounded-[28px] border-2 border-slate-50 bg-slate-50 flex flex-col items-center justify-center gap-3 transition-all group-hover:border-{{ $meta['color'] }}-200 group-hover:bg-white radio-checked:border-{{ $meta['color'] }}-600 radio-checked:bg-{{ $meta['color'] }}-50 radio-checked:text-{{ $meta['color'] }}-600 shadow-sm radio-checked:shadow-{{ $meta['color'] }}-100/50">
                                    <i class="ph-bold {{ $meta['icon'] }} text-2xl"></i>
                                    <span class="text-[10px] font-black uppercase tracking-widest">{{ $val }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- BLOQUE DINÁMICO PENSIONADOS -->
                    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 p-8 bg-indigo-50/50 rounded-[32px] border border-indigo-100/50 mt-4" 
                         x-show="tipoSeleccionado == 6" x-transition x-cloak>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] ml-2 block">Resolución Nº</label>
                            <input type="text" name="numero_resolucion" placeholder="Ex: 2024-XXX" class="w-full bg-white border-2 border-transparent focus:border-indigo-500 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 transition-all shadow-sm">
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] ml-2 block">Tipo de Pensión</label>
                            <select name="tipo_pension" class="w-full bg-white border-2 border-transparent focus:border-indigo-500 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 appearance-none shadow-sm">
                                <option value="">Seleccionar...</option>
                                <option value="Hacienda">Hacienda</option>
                                <option value="IDSS">IDSS</option>
                                <option value="Jubilación">Jubilación</option>
                            </select>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] ml-2 block">Institución</label>
                            <input type="text" name="institucion_pension" placeholder="Ej: DGJP" class="w-full bg-white border-2 border-transparent focus:border-indigo-500 rounded-[20px] px-6 py-4 text-sm font-bold text-slate-800 transition-all shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: IDENTIDAD -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 md:p-12 relative overflow-hidden">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-lg shadow-indigo-200">
                        <i class="ph-bold ph-user-focus text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">2. Identidad y Contacto</h2>
                        <p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest mt-0.5">Información del Solicitante</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Cédula del Solicitante</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="ph-bold ph-identification-card text-xl"></i>
                            </div>
                            <input type="text" name="cedula" id="cedula" required placeholder="000-0000000-0"
                                maxlength="13"
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all shadow-sm">
                        </div>
                        <p class="text-[9px] font-bold text-indigo-500 uppercase tracking-widest ml-4">El sistema autocompletará datos conocidos al ingresar la cédula</p>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Nombre Completo</label>
                        <input type="text" name="nombre_completo" id="nombre_completo" required placeholder="Juan Pérez"
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] px-8 py-5 text-base font-bold text-slate-800 transition-all shadow-sm">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Teléfono de Contacto</label>
                        <input type="text" name="telefono" id="telefono" placeholder="809-000-0000"
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] px-8 py-5 text-base font-bold text-slate-800 transition-all shadow-sm">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Correo Electrónico</label>
                        <input type="email" name="correo" id="correo" placeholder="usuario@mail.com"
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] px-8 py-5 text-base font-bold text-slate-800 transition-all shadow-sm">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Empresa vinculada</label>
                        <input type="text" name="empresa" id="empresa" placeholder="Nombre de la empresa..."
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] px-8 py-5 text-base font-bold text-slate-800 transition-all shadow-sm">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">RNC Empresa</label>
                        <input type="text" name="rnc_empresa" id="rnc_empresa" placeholder="RNC..."
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] px-8 py-5 text-base font-bold text-slate-800 transition-all shadow-sm">
                    </div>

                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Observaciones del Trámite</label>
                        <textarea name="observacion_solicitante" rows="3" placeholder="Detalles adicionales para el analista..." class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 rounded-[32px] px-8 py-6 text-base font-bold text-slate-800 transition-all resize-none shadow-sm"></textarea>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 3: CONSOLIDACIÓN -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 md:p-12 relative overflow-hidden">
                <div class="flex items-center gap-4 mb-10">
                    <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-black shadow-lg shadow-indigo-200">
                        <i class="ph-bold ph-files text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">3. Expediente Documental</h2>
                        <p class="text-slate-400 font-bold text-[11px] uppercase tracking-widest mt-0.5">Carga de Requisitos</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <div class="lg:col-span-7 space-y-8">
                        <div class="space-y-4" x-show="selectedFiles.length > 0" x-transition>
                            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-2">Archivos Seleccionados</h4>
                            <template x-for="(f, index) in selectedFiles" :key="index">
                                <div class="flex items-center justify-between p-4 bg-white rounded-[24px] border border-slate-100 shadow-sm group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                            <i class="ph-fill ph-file text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-slate-900 truncate max-w-[200px]" x-text="f.name"></p>
                                            <div class="flex items-center gap-2">
                                                <span class="text-[9px] font-bold text-slate-400 uppercase" x-text="f.size"></span>
                                                <span x-show="f.docId" class="text-[9px] font-black text-indigo-500 uppercase px-2 py-0.5 bg-indigo-50 rounded-full" 
                                                      x-text="'Vinculado a: ' + (documentos.find(d => d.id == f.docId)?.nombre_documento || 'ID/Cédula')"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeFile(index)" class="w-10 h-10 rounded-full hover:bg-rose-50 text-rose-500 flex items-center justify-center transition-colors">
                                        <i class="ph-bold ph-trash text-lg"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <div class="relative group" 
                             @dragover.prevent="isDragging = true" 
                             @dragleave.prevent="isDragging = false" 
                             @drop.prevent="isDragging = false; handleFile($event)">
                            <input type="file" multiple @change="handleFile($event)" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-50">
                            <div class="w-full border-2 border-dashed border-slate-200 rounded-[40px] p-10 text-center transition-all bg-slate-50/50 group-hover:border-indigo-400 group-hover:bg-white"
                                 :class="isDragging ? 'border-indigo-600 bg-indigo-50' : ''">
                                <div class="w-16 h-16 bg-white rounded-2xl shadow-lg flex items-center justify-center mx-auto mb-4 text-indigo-600 group-hover:scale-110 transition-transform">
                                    <i class="ph-fill ph-file-arrow-up text-3xl"></i>
                                </div>
                                <h3 class="text-base font-black text-slate-900 mb-1">Carga General</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Arrastra cualquier otro documento aquí</p>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-5">
                        <div class="p-8 bg-slate-900 rounded-[40px] text-white relative overflow-hidden h-full">
                            <div class="absolute -right-4 -top-4 w-32 h-32 bg-indigo-500 opacity-20 blur-3xl"></div>
                            <h4 class="text-[11px] font-black uppercase tracking-[0.2em] mb-6 text-indigo-400">Requisitos para <span x-text="tipos.find(t => t.id == tipoSeleccionado)?.nombre || 'este tipo'"></span></h4>
                            
                            <div x-show="!tipoSeleccionado" class="text-center py-10 opacity-40">
                                <i class="ph ph-selection-all text-4xl mb-4 block"></i>
                                <p class="text-[10px] font-bold uppercase tracking-widest leading-relaxed">Selecciona un tipo de solicitud para ver los documentos requeridos</p>
                            </div>

                            <ul class="space-y-4" x-show="tipoSeleccionado">
                                <!-- Requisito Especial: Cédula/ID -->
                                <li class="p-4 bg-white/10 rounded-2xl border border-white/20 group relative overflow-hidden">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="flex items-start gap-3">
                                            <div class="mt-1 w-6 h-6 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                                <i class="ph ph-identification-card"></i>
                                            </div>
                                            <div>
                                                <p class="text-[11px] font-black leading-tight">Cédula de Identidad (ID)</p>
                                                <span class="text-[8px] font-black uppercase tracking-widest text-rose-400">Obligatorio</span>
                                            </div>
                                        </div>
                                        <label class="cursor-pointer">
                                            <input type="file" @change="handleFile($event, 'cedula')" class="hidden">
                                            <div class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded-lg text-[9px] font-black uppercase tracking-widest transition-colors flex items-center gap-1">
                                                <i class="ph-bold ph-upload"></i> Subir
                                            </div>
                                        </label>
                                    </div>
                                </li>

                                <template x-for="doc in documentos" :key="doc.id">
                                    <li class="p-4 bg-white/5 rounded-2xl border border-white/5 group hover:border-white/20 transition-all">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex items-start gap-3">
                                                <div class="mt-1 w-6 h-6 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                                    <i class="ph ph-check-square"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[11px] font-black leading-tight" x-text="doc.nombre_documento"></p>
                                                    <span class="text-[8px] font-black uppercase tracking-widest" 
                                                          :class="doc.obligatorio ? 'text-rose-400' : 'text-indigo-400'" 
                                                          x-text="doc.obligatorio ? 'Obligatorio' : 'Opcional'"></span>
                                                </div>
                                            </div>
                                            <label class="cursor-pointer">
                                                <input type="file" @change="handleFile($event, doc.id)" class="hidden">
                                                <div class="px-3 py-1 bg-white/10 hover:bg-white/20 rounded-lg text-[9px] font-black uppercase tracking-widest transition-colors flex items-center gap-1">
                                                    <i class="ph-bold ph-upload"></i> Subir
                                                </div>
                                            </label>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="dynamic_inputs_container" class="hidden"></div>
        </div>

        <!-- DERECHA: TIPS Y ACCIONES (4 COL) -->
        <div class="xl:col-span-4 sticky top-10 space-y-8">
            <!-- CARD DE ACCIONES -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 md:p-10 space-y-8">
                <div class="flex flex-col gap-4">
                    <button type="submit" name="save_as_draft" value="0" 
                        class="w-full bg-slate-900 text-white rounded-[24px] py-6 text-xs font-black uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-xl shadow-indigo-900/20 flex items-center justify-center gap-4 group">
                        <span class="group-hover:translate-x-1 transition-transform">Finalizar y Enviar</span>
                        <i class="ph-fill ph-paper-plane-tilt text-xl"></i>
                    </button>
                    
                    <button type="submit" name="save_as_draft" value="1" 
                        class="w-full bg-white text-slate-400 border border-slate-200 rounded-[24px] py-5 text-xs font-black uppercase tracking-[0.2em] hover:text-slate-900 hover:border-slate-900 hover:bg-slate-50 transition-all flex items-center justify-center gap-3">
                        <i class="ph ph-archive-tray text-lg"></i> Guardar Borrador
                    </button>
                </div>

                <div class="pt-6 border-t border-slate-50">
                    <div class="bg-indigo-50/50 p-6 rounded-3xl border border-indigo-100/50">
                        <h4 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i class="ph-bold ph-info"></i> Resumen del Trámite
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between text-[10px] font-bold">
                                <span class="text-slate-400">Archivos:</span>
                                <span class="text-slate-900" x-text="selectedFiles.length">0</span>
                            </div>
                            <div class="flex justify-between text-[10px] font-bold">
                                <span class="text-slate-400">Tipo:</span>
                                <span class="text-indigo-600" x-text="tipos.find(t => t.id == tipoSeleccionado)?.nombre || 'No seleccionado'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[40px] p-8 border border-slate-100 shadow-xl shadow-slate-200/20">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-3">
                    <i class="ph ph-lightbulb text-indigo-500 text-xl"></i> Soporte Operativo
                </h3>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0 font-black text-xs">1</div>
                        <p class="text-[11px] font-medium text-slate-500 leading-relaxed">Asegúrate de que la cédula sea válida antes de continuar.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0 font-black text-xs">2</div>
                        <p class="text-[11px] font-medium text-slate-500 leading-relaxed">El expediente debe incluir todos los documentos obligatorios marcados en negro en el checklist.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    /* Estilo para los radio buttons personalizados */
    input[type="radio"]:checked + div {
        border-color: #4f46e5 !important; /* indigo-600 */
        background-color: #f5f3ff !important; /* indigo-50 */
        color: #4f46e5 !important;
        box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.1);
    }
</style>
@push('js')
<script>
    function formatCedula(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length > 11) value = value.slice(0, 11);
        
        let formatted = '';
        if (value.length > 0) {
            formatted = value.slice(0, 3);
            if (value.length > 3) {
                formatted += '-' + value.slice(3, 10);
                if (value.length > 10) {
                    formatted += '-' + value.slice(10, 11);
                }
            }
        }
        input.value = formatted;
    }

    document.getElementById('cedula').addEventListener('input', function() {
        formatCedula(this);
        
        let cleanValue = this.value.replace(/\D/g, '');
        if (cleanValue.length === 11) {
            // Intentar autocompletar si la cédula está completa
            fetch(`{{ route('afiliacion.search-afiliado') }}?cedula=${this.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('nombre_completo').value = data.nombre_completo || '';
                        document.getElementById('telefono').value = data.telefono || '';
                        document.getElementById('empresa').value = data.empresa || '';
                        document.getElementById('rnc_empresa').value = data.rnc_empresa || '';
                        
                        // Notificar al usuario
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Datos de afiliado cargados automáticamente'
                        });
                    }
                });
        }
    });
</script>
@endpush
@endsection

