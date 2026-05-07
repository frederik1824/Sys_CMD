@extends('layouts.app')

@section('content')
<div class="p-4 md:p-10 max-w-[1600px] mx-auto min-h-[calc(100vh-100px)]" x-data="{ 
    tipoSeleccionado: {{ $solicitud->tipo_solicitud_id }},
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
                <div class="absolute -left-6 top-1/2 -translate-y-1/2 w-1.5 h-12 bg-amber-500 rounded-full hidden md:block"></div>
                <h1 class="text-5xl font-black tracking-tighter text-slate-900 leading-tight">
                    Corregir <span class="text-amber-600">Trámite</span>
                </h1>
                <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed mt-2">
                    Ajusta la información solicitada o actualiza el expediente para re-enviar el caso a revisión técnica.
                </p>
            </div>
        </div>
        
        <div class="flex items-center gap-4 bg-white p-3 rounded-[32px] border border-slate-100 shadow-sm">
            <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center">
                <i class="ph-bold ph-pencil-simple text-2xl"></i>
            </div>
            <div class="pr-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] leading-none mb-1">Código de Referencia</p>
                <p class="text-sm font-black text-slate-900 leading-none">{{ $solicitud->codigo_solicitud }}</p>
            </div>
        </div>
    </div>

    @if($solicitud->estado == 'Devuelta' && $solicitud->motivo_devolucion)
    <div class="mb-10 p-8 bg-orange-50 border-2 border-orange-100 rounded-[40px] flex items-start gap-6 relative overflow-hidden group">
        <div class="absolute top-0 right-0 w-32 h-32 bg-orange-200/20 rounded-full -mr-16 -mt-16 blur-2xl"></div>
        <div class="w-14 h-14 bg-orange-500 text-white rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-orange-200">
            <i class="ph-fill ph-warning-circle text-2xl"></i>
        </div>
        <div>
            <h3 class="text-lg font-black text-orange-900 mb-1 uppercase tracking-tight">Motivo de la Devolución</h3>
            <p class="text-orange-700 font-medium leading-relaxed">
                {{ $solicitud->motivo_devolucion }}
            </p>
        </div>
    </div>
    @endif

    <form action="{{ route('afiliacion.update', $solicitud) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 xl:grid-cols-12 gap-10 items-start">
        @csrf
        @method('PATCH')
        
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
                                <input type="text" name="cedula" required value="{{ $solicitud->cedula }}" placeholder="000-0000000-0"
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
                                <input type="text" name="nombre_completo" required value="{{ $solicitud->nombre_completo }}" placeholder="Ej: Juan Pérez"
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
                            <input type="text" name="telefono" value="{{ $solicitud->telefono }}" placeholder="809-000-0000"
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Correo Electrónico</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="ph-bold ph-envelope-simple text-xl"></i>
                            </div>
                            <input type="email" name="correo" value="{{ $solicitud->correo }}" placeholder="usuario@empresa.com"
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Nombre de la Empresa</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="ph-bold ph-buildings text-xl"></i>
                            </div>
                            <input type="text" name="empresa" value="{{ $solicitud->empresa }}" placeholder="Empresa vinculada..."
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">RNC / Identificador Fiscal</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors">
                                <i class="ph-bold ph-hash text-xl"></i>
                            </div>
                            <input type="text" name="rnc_empresa" value="{{ $solicitud->rnc_empresa }}" placeholder="RNC de la empresa..."
                                class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] pl-16 pr-8 py-5 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300">
                        </div>
                    </div>

                    <div class="md:col-span-2 space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] ml-2 block">Notas u Observaciones del Solicitante</label>
                        <textarea name="observacion_solicitante" rows="4" placeholder="Cualquier detalle relevante para el analista..."
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[32px] px-8 py-6 text-base font-bold text-slate-800 transition-all placeholder:text-slate-300 resize-none">{{ $solicitud->observacion_solicitante }}</textarea>
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

                    <div class="p-6 bg-slate-50 rounded-[32px] border border-slate-100 mb-8">
                        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                            <i class="ph-bold ph-file-pdf text-indigo-500 text-lg"></i> Archivo Actual
                        </h3>
                        @php $docPrincipal = $solicitud->documentos->first(); @endphp
                        @if($docPrincipal)
                        <div class="flex items-center justify-between p-3 bg-white rounded-2xl border border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                    <i class="ph-bold ph-file-pdf text-xl"></i>
                                </div>
                                <div class="truncate max-w-[150px]">
                                    <p class="text-[10px] font-black text-slate-800 truncate">{{ $docPrincipal->nombre_original }}</p>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Registrado</p>
                                </div>
                            </div>
                            <a href="{{ route('afiliacion.documentos.view', $docPrincipal) }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-colors">
                                <i class="ph-bold ph-eye"></i>
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Area de Carga -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2 block">Actualizar Expediente (Opcional)</label>
                        
                        <div class="relative group" 
                             @dragover.prevent="isDragging = true" 
                             @dragleave.prevent="isDragging = false" 
                             @drop.prevent="isDragging = false; handleFile($event)">
                            
                            <input type="file" name="expediente_pdf" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   @change="handleFile($event)"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-50">
                            
                            <div class="w-full border-2 border-dashed rounded-[32px] p-8 transition-all flex flex-col items-center justify-center text-center gap-4 bg-slate-50/50"
                                 :class="isDragging ? 'border-indigo-500 bg-indigo-50/50 scale-95' : 'border-slate-200 hover:border-indigo-300 hover:bg-white shadow-sm'">
                                
                                <div class="w-16 h-16 rounded-3xl bg-white shadow-xl flex items-center justify-center text-indigo-600 transition-transform group-hover:scale-110 group-hover:rotate-6">
                                    <i class="ph-fill ph-file-arrow-up text-3xl"></i>
                                </div>
                                
                                <div class="space-y-1">
                                    <p class="text-sm font-black text-slate-900" x-text="fileName || 'Subir nueva versión'"></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">PDF, Word o Imágenes (Máx 20MB)</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-amber-50 rounded-2xl border border-amber-100">
                            <i class="ph ph-info text-amber-500 text-xl"></i>
                            <p class="text-[9px] font-bold text-amber-700 leading-tight">
                                Solo sube un archivo si necesitas reemplazar el expediente actual por correcciones.
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
                            <input type="radio" name="prioridad" value="{{ $val }}" {{ $solicitud->prioridad == $val ? 'checked' : '' }} class="sr-only">
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
                        class="w-full bg-emerald-600 text-white rounded-[24px] py-6 text-xs font-black uppercase tracking-[0.2em] hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-900/20 flex items-center justify-center gap-4 group">
                        <span class="group-hover:translate-x-1 transition-transform">Re-enviar para Revisión</span>
                        <i class="ph-fill ph-paper-plane-tilt text-xl"></i>
                    </button>
                    
                    <button type="submit" name="save_as_draft" value="1" 
                        class="w-full bg-white text-slate-400 border border-slate-200 rounded-[24px] py-5 text-xs font-black uppercase tracking-[0.2em] hover:text-slate-900 hover:border-slate-900 hover:bg-slate-50 transition-all flex items-center justify-center gap-3">
                        <i class="ph ph-archive-tray text-lg"></i> Guardar Cambios Localmente
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    /* Estilo para los radio buttons personalizados */
    input[type="radio"]:checked + div {
        border-color: #059669 !important; /* emerald-600 */
        background-color: #ecfdf5 !important; /* emerald-50 */
        color: #059669 !important;
        box-shadow: 0 10px 15px -3px rgba(5, 150, 105, 0.1);
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
            fetch(`{{ route('afiliacion.search-afiliado') }}?cedula=${this.value}`)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('nombre_completo').value = data.nombre_completo || '';
                        document.getElementById('telefono').value = data.telefono || '';
                        document.getElementById('empresa').value = data.empresa || '';
                        document.getElementById('rnc_empresa').value = data.rnc_empresa || '';
                        
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Datos de afiliado actualizados'
                        });
                    }
                });
        }
    });
</script>
@endpush
@endsection
