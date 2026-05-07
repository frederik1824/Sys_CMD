@extends('layouts.app')

@section('content')
<div class="p-4 md:p-10 max-w-[1600px] mx-auto min-h-[calc(100vh-100px)]" x-data="{ 
    tipoSeleccionado: null,
    tipos: {{ $tipos->toJson() }},
    isDragging: false,
    fileName: '',
    
    get documentos() {
        if (!this.tipoSeleccionado) return [];
        let tipo = this.tipos.find(t => t.id == this.tipoSeleccionado);
        return tipo ? tipo.documentos_requeridos : [];
    },

    handleFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.fileName = file.name;
        }
    }
}">
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
            <!-- SECCIÓN 1: IDENTIDAD DEL TRÁMITE -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden relative group">
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-50/30 rounded-full -mr-32 -mt-32 blur-3xl group-hover:bg-indigo-100/40 transition-colors duration-1000"></div>
                
                <div class="p-8 md:p-12 relative z-10">
                    <div class="flex items-center gap-4 mb-10">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black">1</div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Identificación y Clasificación</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2 space-y-3">
                            <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Tipo de Solicitud Operativa</label>
                            <div class="relative">
                                <select name="tipo_solicitud_id" x-model="tipoSeleccionado" required
                                    class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] px-8 py-5 text-base font-bold text-slate-800 appearance-none transition-all cursor-pointer shadow-sm hover:shadow-md">
                                    <option value="">Clasifica este trámite...</option>
                                    @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <i class="ph-bold ph-caret-down"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3" x-data="{ 
                            formatCedula(e) {
                                let v = e.target.value.replace(/\D/g, '');
                                if (v.length > 11) v = v.substring(0, 11);
                                let formatted = '';
                                if (v.length > 0) formatted += v.substring(0, 3);
                                if (v.length > 3) formatted += '-' + v.substring(3, 10);
                                if (v.length > 10) formatted += '-' + v.substring(10, 11);
                                e.target.value = formatted;
                            }
                        }">
                            <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Cédula del Solicitante</label>
                            <div class="relative group">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="ph-bold ph-identification-card text-xl"></i>
                                </div>
                                <input type="text" name="cedula" required placeholder="000-0000000-0"
                                    @input="formatCedula"
                                    maxlength="13"
                                    class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Nombre Completo</label>
                            <div class="relative group">
                                <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                    <i class="ph-bold ph-user text-xl"></i>
                                </div>
                                <input type="text" name="nombre_completo" required placeholder="Ej: Juan Pérez"
                                    class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN 2: CONTACTO Y EMPRESA -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 md:p-12 space-y-10">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black">2</div>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Canales de Contacto y Origen</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Teléfono de Contacto</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="ph-bold ph-phone text-xl"></i>
                            </div>
                            <input type="text" name="telefono" placeholder="809-000-0000"
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Correo Electrónico</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="ph-bold ph-envelope-simple text-xl"></i>
                            </div>
                            <input type="email" name="correo" placeholder="usuario@empresa.com"
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Nombre de la Empresa</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="ph-bold ph-buildings text-xl"></i>
                            </div>
                            <input type="text" name="empresa" placeholder="Empresa vinculada..."
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">RNC / Identificador Fiscal</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="ph-bold ph-hash text-xl"></i>
                            </div>
                            <input type="text" name="rnc_empresa" placeholder="RNC de la empresa..."
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Notas u Observaciones del Solicitante</label>
                        <textarea name="observacion_solicitante" rows="4" placeholder="Cualquier detalle relevante para el analista..."
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[32px] px-8 py-6 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300 resize-none"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- DERECHA: EXPEDIENTE Y ACCIONES (4 COL) -->
        <div class="xl:col-span-4 space-y-10">
            <!-- CARD DE DOCUMENTOS -->
            <div class="bg-slate-900 rounded-[48px] p-1 shadow-2xl shadow-indigo-200/50">
                <div class="bg-white rounded-[44px] p-8 md:p-10">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Expediente Digital</h2>
                        <div class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[9px] font-black uppercase tracking-widest">PDF Unificado</div>
                    </div>

                    <!-- Checklist de Requisitos Dinámico -->
                    <div class="p-6 bg-slate-50 rounded-[32px] border border-slate-100 mb-8 overflow-hidden relative group">
                        <div class="absolute inset-0 bg-indigo-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500 opacity-[0.02]"></div>
                        
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <i class="ph-fill ph-check-square text-indigo-500 text-lg"></i> Requisitos del Proceso
                        </h3>
                        
                        <ul class="space-y-4">
                            <template x-if="!tipoSeleccionado">
                                <div class="flex flex-col items-center justify-center py-6 text-center">
                                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-slate-200 mb-3 shadow-sm">
                                        <i class="ph ph-file-search text-2xl"></i>
                                    </div>
                                    <p class="text-[11px] font-bold text-slate-400 leading-tight">Define el tipo de solicitud para <br> habilitar los requisitos</p>
                                </div>
                            </template>
                            <template x-for="doc in documentos" :key="doc.id">
                                <li class="flex items-start gap-4 p-3 bg-white/50 rounded-2xl border border-slate-50 hover:border-indigo-100 transition-colors">
                                    <div class="mt-1">
                                        <div class="w-4 h-4 rounded-md border-2 border-indigo-200 flex items-center justify-center">
                                            <div class="w-2 h-2 rounded-[2px] bg-indigo-500" x-show="doc.obligatorio"></div>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <span class="text-[11px] font-black text-slate-700 leading-tight block" x-text="doc.nombre_documento"></span>
                                        <span class="text-[8px] font-black uppercase tracking-widest" :class="doc.obligatorio ? 'text-rose-500' : 'text-slate-400'" x-text="doc.obligatorio ? 'Obligatorio' : 'Opcional'"></span>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Area de Carga -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2 block">Cargar Archivo Final</label>
                        
                        <div class="relative group" 
                             @dragover.prevent="isDragging = true" 
                             @dragleave.prevent="isDragging = false" 
                             @drop.prevent="isDragging = false; handleFile($event)">
                            
                            <input type="file" name="expediente_pdf" required accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   @change="handleFile($event)"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-50">
                            
                            <div class="w-full border-2 border-dashed rounded-[32px] p-8 transition-all flex flex-col items-center justify-center text-center gap-4 bg-slate-50/50"
                                 :class="isDragging ? 'border-indigo-500 bg-indigo-50/50 scale-95' : 'border-slate-200 hover:border-indigo-300 hover:bg-white shadow-sm'">
                                
                                <div class="w-16 h-16 rounded-3xl bg-white shadow-xl flex items-center justify-center text-indigo-600 transition-transform group-hover:scale-110 group-hover:rotate-6">
                                    <i class="ph-fill ph-file-arrow-up text-3xl"></i>
                                </div>
                                
                                <div class="space-y-1">
                                    <p class="text-sm font-black text-slate-900" x-text="fileName || 'Seleccionar Expediente'"></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">PDF, Word o Imágenes (Máx 20MB)</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                            <i class="ph ph-info text-indigo-500 text-xl"></i>
                            <p class="text-[9px] font-bold text-indigo-700 leading-tight">
                                Ahora puedes adjuntar el expediente en formatos PDF, Word o imágenes directamente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CARD DE ACCIONES -->
            <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 md:p-10 space-y-6">
                <div class="space-y-3">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Prioridad de Atención</label>
                    <div class="flex gap-2">
                        @foreach(['Normal' => 'ph-clock', 'Alta' => 'ph-lightning', 'Urgente' => 'ph-warning-octagon'] as $val => $icon)
                        <label class="flex-1 cursor-pointer group">
                            <input type="radio" name="prioridad" value="{{ $val }}" {{ $val == 'Normal' ? 'checked' : '' }} class="sr-only">
                            <div class="py-4 rounded-2xl border-2 border-slate-50 bg-slate-50 flex flex-col items-center justify-center gap-2 transition-all group-hover:bg-white group-hover:border-slate-200 radio-checked:border-indigo-600 radio-checked:bg-indigo-50 radio-checked:text-indigo-600">
                                <i class="ph {{ $icon }} text-xl"></i>
                                <span class="text-[9px] font-black uppercase tracking-widest">{{ $val }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-4 pt-4">
                    <button type="submit" name="save_as_draft" value="0" 
                        class="w-full bg-slate-900 text-white rounded-[24px] py-6 text-xs font-black uppercase tracking-[0.2em] hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 flex items-center justify-center gap-4 group">
                        <span class="group-hover:translate-x-1 transition-transform">Finalizar y Enviar Trámite</span>
                        <i class="ph-fill ph-paper-plane-tilt text-xl"></i>
                    </button>
                    
                    <button type="submit" name="save_as_draft" value="1" 
                        class="w-full bg-white text-slate-400 border border-slate-200 rounded-[24px] py-5 text-xs font-black uppercase tracking-[0.2em] hover:text-slate-900 hover:border-slate-900 hover:bg-slate-50 transition-all flex items-center justify-center gap-3">
                        <i class="ph ph-archive-tray text-lg"></i> Guardar como Borrador
                    </button>
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

