@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#F8FAFC] pb-20" x-data="worklistData()">
    <!-- Header Minimalista -->
    <div class="bg-white border-b border-slate-200 py-6 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                        <span class="material-symbols-outlined text-white">headset_mic</span>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight leading-none">Mi Lista de Trabajo</h1>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Gestión Operativa Diaria</p>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <a href="{{ route('callcenter.management') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">fact_check</span>
                        Validación de Documentos
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Tabs Compactas -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-2xl mb-8 w-fit border border-slate-200">
            <template x-for="tab in tabs">
                <button @click="activeTab = tab.id" 
                    :class="activeTab === tab.id ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-900'"
                    class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm" x-text="tab.icon"></span>
                    <span x-text="tab.name"></span>
                    <span class="ml-1 px-1.5 py-0.5 bg-slate-100 text-slate-400 rounded-md text-[8px]" x-text="tab.count"></span>
                </button>
            </template>
        </div>

        <!-- Contenedor de Listas Agrupadas -->
        <div class="space-y-12">
            <template x-for="(list, empresa) in groupedAfiliados" :key="empresa">
                <div class="animate-in fade-in slide-in-from-top-4 duration-500">
                    <!-- Cabecera de Empresa -->
                    <div class="flex items-center gap-4 mb-6 sticky top-[104px] bg-surface/90 backdrop-blur-md py-3 z-30 px-2 rounded-2xl group/header">
                        <div class="w-1.5 h-8 bg-blue-600 rounded-full shadow-lg shadow-blue-500/20 transition-all group-hover/header:h-10"></div>
                        <div class="flex flex-col">
                            <h2 class="text-sm font-black text-slate-800 tracking-tight uppercase" x-text="empresa"></h2>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest"><span x-text="list.length"></span> afiliados pendientes</p>
                        </div>
                        <div class="flex-1 h-[1px] bg-slate-100 ml-4"></div>
                        <button @click="startSession(empresa, list)" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all flex items-center gap-2 shadow-lg shadow-blue-500/20 active:scale-95">
                            <span class="material-symbols-outlined text-sm">bolt</span>
                            Gestionar Empresa
                        </button>
                    </div>

                    <!-- Grid de Afiliados de esta Empresa -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="afiliado in list" :key="afiliado.uuid">
                            <div :id="'row-' + afiliado.uuid" 
                                class="bg-white border border-slate-200 rounded-[32px] p-6 hover:border-blue-400 hover:shadow-2xl hover:shadow-blue-500/10 transition-all group relative overflow-hidden">
                                
                                <!-- Badge de Estado -->
                                <div class="flex items-start justify-between mb-5">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500 shadow-inner">
                                        <span class="material-symbols-outlined text-2xl">person</span>
                                    </div>
                                    <div class="flex flex-col items-end">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5" x-text="afiliado.cedula"></span>
                                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter shadow-sm"
                                            :class="getStatusClass(afiliado.estado_nombre)"
                                            x-text="afiliado.estado_nombre"></span>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <h3 class="text-base font-black text-slate-900 leading-tight group-hover:text-blue-600 transition-colors truncate" x-text="afiliado.nombre"></h3>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="w-1 h-1 rounded-full bg-blue-400"></span>
                                        <p class="text-[11px] font-bold text-slate-400 uppercase truncate" x-text="afiliado.empresa"></p>
                                    </div>
                                    <!-- SLA Alert -->
                                    <template x-if="afiliado.last_call_days > 0">
                                        <div class="flex items-center gap-1.5 mt-2">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="afiliado.last_call_days > 3 ? 'bg-rose-500 animate-pulse' : (afiliado.last_call_days > 1 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                            <span class="text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="'Última gestión: hace ' + afiliado.last_call_days + ' días'"></span>
                                        </div>
                                    </template>
                                </div>

                                <div class="flex items-center justify-between bg-slate-50 rounded-[24px] px-5 py-4 mb-6 border border-slate-100 shadow-inner">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-blue-600 shadow-sm">
                                            <span class="material-symbols-outlined text-lg">call</span>
                                        </div>
                                        <span class="text-sm font-black text-slate-700 tracking-tighter" x-text="afiliado.telefono || '--- --- ----'"></span>
                                    </div>
                                    <button @click="showHistory(afiliado.uuid)" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-blue-600 hover:bg-white rounded-full transition-all">
                                        <span class="material-symbols-outlined text-lg">history</span>
                                    </button>
                                    <button @click="openWhatsApp(afiliado.telefono, afiliado.nombre)" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-emerald-500 hover:bg-white rounded-full transition-all">
                                        <span class="material-symbols-outlined text-lg">chat</span>
                                    </button>
                                </div>

                                <!-- Acciones Compactas -->
                                <div class="flex flex-wrap gap-2">
                                    <template x-if="activeTab === 'nuevos' || activeTab === 'reintentos'">
                                        <div class="flex w-full gap-2">
                                            <button @click="openModal(afiliado.uuid, 'Cédula efectiva', 'success')" class="flex-1 h-12 bg-emerald-600 text-white rounded-[18px] text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-500/20 active:scale-95">Efectiva</button>
                                            <button @click="openModal(afiliado.uuid, 'No contesta', 'warning')" class="px-4 h-12 bg-slate-100 text-slate-600 rounded-[18px] text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all active:scale-95">No Contesta</button>
                                            <button @click="openMore(afiliado.uuid)" class="w-12 h-12 bg-slate-100 text-slate-400 rounded-[18px] hover:bg-slate-900 hover:text-white transition-all active:scale-95 flex items-center justify-center">
                                                <span class="material-symbols-outlined text-xl">more_vert</span>
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="activeTab === 'documentacion'">
                                        <button @click="openModal(afiliado.uuid, 'Cédula efectiva', 'success', true)" class="w-full h-12 bg-indigo-600 text-white rounded-[18px] text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all flex items-center justify-center gap-3 shadow-lg shadow-indigo-500/20 active:scale-95">
                                            <span class="material-symbols-outlined text-lg">verified</span>
                                            Validar Documento
                                        </button>
                                    </template>

                                    <template x-if="activeTab === 'confirmados'">
                                        <button @click="markAsOnRoute(afiliado.uuid)" class="w-full h-12 bg-slate-900 text-white rounded-[18px] text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all flex items-center justify-center gap-3 shadow-lg shadow-slate-900/20 active:scale-95">
                                            <span class="material-symbols-outlined text-lg">local_shipping</span>
                                            Despachar a Ruta
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="filteredAfiliados.length === 0" class="py-32 text-center bg-white rounded-[40px] border border-slate-100 mt-4">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-200">
                <span class="material-symbols-outlined text-4xl">inventory_2</span>
            </div>
            <h3 class="text-xl font-black text-slate-900 tracking-tight">Sin gestiones en esta categoría</h3>
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-2">Excelente trabajo, todo al día</p>
        </div>
    </div>

    <!-- Modales (Inyectados dinámicamente o fijos) -->
    @include('admin.callcenter.partials.worklist_modals')
    @include('admin.callcenter.partials.session_modal')
</div>

<script>
    function worklistData() {
        return {
            activeTab: 'nuevos',
            empresasInfo: @json($empresasInfo),
            sessionContacto: null,
            init() {
                window.addEventListener('open-call-modal', (e) => {
                    Swal.close();
                    this.openModal(e.detail.uuid, e.detail.state, e.detail.type);
                });
            },
            tabs: [
                { id: 'nuevos', name: 'Llamando', icon: 'call', count: {{ $nuevos->count() }} },
                { id: 'reintentos', name: 'No Comunica', icon: 'replay', count: {{ $reintentos->count() }} },
                { id: 'documentacion', name: 'Documentación', icon: 'file_present', count: {{ $documentacion->count() }} },
                { id: 'confirmados', name: 'Confirmados', icon: 'task_alt', count: {{ $confirmados->count() }} },
            ],
            afiliados: [
                @foreach($afiliadosAsignados as $a)
                {
                    uuid: '{{ $a->uuid }}',
                    nombre: '{{ addslashes($a->nombre_completo) }}',
                    cedula: '{{ $a->cedula }}',
                    empresa: '{{ addslashes($a->empresaModel->nombre ?? $a->empresa ?? "Individual") }}',
                    telefono: '{{ $a->telefono }}',
                    estado_nombre: '{{ $a->estado?->nombre }}',
                    category: '{{ $a->work_category }}',
                    last_call_days: {{ $a->llamadas->first()?->fecha_llamada ? floor($a->llamadas->first()->fecha_llamada->diffInDays()) : 0 }}
                },
                @endforeach
            ],
            
            get filteredAfiliados() {
                return this.afiliados.filter(a => a.category === this.activeTab);
            },

            get groupedAfiliados() {
                const groups = {};
                this.filteredAfiliados.forEach(a => {
                    const empresa = a.empresa || 'Individuales';
                    if (!groups[empresa]) groups[empresa] = [];
                    groups[empresa].push(a);
                });
                return groups;
            },

            getStatusClass(name) {
                if (name === 'Cédula Recibida') return 'bg-emerald-100 text-emerald-600';
                if (name === 'Cédula Pendiente') return 'bg-amber-100 text-amber-600';
                return 'bg-blue-100 text-blue-600';
            },

            modalOpen: false,
            historyOpen: false,
            afiliadoId: null,
            selectedState: '',
            modalType: 'info',
            observacion: '',
            documentoRecibido: false,
            proximoContacto: '',
            evidenciaFile: null,
            fotoPreview: null,
            isSubmitting: false,
            bulkMode: false,

            // Sesión de Trabajo por Empresa
            sessionOpen: false,
            sessionEmpresa: '',
            sessionAfiliados: [],

            startSession(empresa, list) {
                this.sessionEmpresa = empresa;
                this.sessionAfiliados = JSON.parse(JSON.stringify(list));
                this.sessionContacto = this.empresasInfo[empresa] || null;
                this.sessionOpen = true;
                // Prevenir scroll del body cuando la sesión está abierta
                document.body.style.overflow = 'hidden';
            },

            closeSession() {
                this.sessionOpen = false;
                document.body.style.overflow = 'auto';
            },

            openModal(uuid, state, type, docOnly = false) {
                this.bulkMode = false;
                this.afiliadoId = uuid;
                this.selectedState = state;
                this.modalType = type;
                this.observacion = '';
                this.documentoRecibido = docOnly || false;
                this.proximoContacto = '';
                this.evidenciaFile = null;
                this.fotoPreview = null;
                this.modalOpen = true;
            },

            openBulkModal() {
                this.bulkMode = true;
                this.afiliadoId = null;
                this.selectedState = 'Cédula efectiva';
                this.modalType = 'success';
                this.observacion = '';
                this.documentoRecibido = false;
                this.modalOpen = true;
            },

            closeModal() {
                this.modalOpen = false;
                this.bulkMode = false;
            },

            handleFileUpload(e) {
                const file = e.target.files[0];
                if (file) {
                    this.evidenciaFile = file;
                    this.fotoPreview = URL.createObjectURL(file);
                }
            },

            submitCall() {
                this.isSubmitting = true;
                
                const url = this.bulkMode ? '/callcenter/calls/bulk' : `/callcenter/calls/${this.afiliadoId}`;
                const formData = new FormData();
                
                if (this.bulkMode) {
                    this.sessionAfiliados.forEach(a => formData.append('uuids[]', a.uuid));
                }
                
                formData.append('estado_llamada', this.selectedState);
                formData.append('observacion', this.observacion);
                formData.append('documento_recibido', this.documentoRecibido ? 1 : 0);
                if (this.proximoContacto) formData.append('proximo_contacto', this.proximoContacto);
                if (this.evidenciaFile) formData.append('evidencia_foto', this.evidenciaFile);

                fetch(url, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    this.isSubmitting = false;
                    this.modalOpen = false;
                    if (res.success) {
                        if (this.bulkMode) {
                            // Copiar UUIDs antes de vaciar la lista para evitar problemas de iteración
                            const uuidsToRemove = this.sessionAfiliados.map(a => a.uuid);
                            uuidsToRemove.forEach(uuid => this.removeAfiliado(uuid));
                            this.bulkMode = false;
                        } else {
                            this.removeAfiliado(this.afiliadoId);
                        }
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: res.message || 'Gestión guardada',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            },

            showHistory(uuid) {
                Swal.fire({
                    title: 'Historial de Gestión',
                    html: '<div class="flex justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>',
                    showConfirmButton: false,
                    customClass: { 
                        popup: 'rounded-[40px] p-8',
                        title: 'text-xl font-black text-slate-900 uppercase tracking-tight',
                        confirmButton: 'px-8 py-3 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest'
                    },
                    didOpen: () => {
                        fetch(`/callcenter/history/${uuid}`)
                        .then(r => r.json())
                        .then(history => {
                            if (history.length === 0) {
                                Swal.update({ 
                                    html: '<div class="py-10 text-center"><span class="material-symbols-outlined text-4xl text-slate-200">history</span><p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-4">No hay gestiones registradas aún</p></div>',
                                    showConfirmButton: true,
                                    confirmButtonText: 'Cerrar'
                                });
                                return;
                            }
                            
                            let html = `
                                <div class="max-h-[450px] overflow-y-auto custom-scrollbar pr-2 mt-4">
                                    <div class="space-y-3">
                            `;
                            
                            history.forEach(item => {
                                const statusClass = item.estado === 'Cédula efectiva' ? 'text-emerald-600' : 'text-blue-600';
                                html += `
                                    <div class="text-left p-5 bg-slate-50 border border-slate-100 rounded-[28px] transition-all hover:bg-white hover:border-blue-200 hover:shadow-xl hover:shadow-blue-500/5 group">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">${item.fecha}</span>
                                            </div>
                                            <span class="px-2 py-0.5 bg-white border border-slate-200 rounded-lg text-[8px] font-black text-slate-400 uppercase">${item.usuario}</span>
                                        </div>
                                        <h5 class="text-[11px] font-black ${statusClass} uppercase tracking-tight mb-2">${item.estado}</h5>
                                        <div class="bg-white/50 p-3 rounded-2xl border border-slate-50">
                                            <p class="text-[11px] text-slate-600 font-medium leading-relaxed italic">"${item.observacion}"</p>
                                        </div>
                                        ${item.foto ? `
                                            <div class="mt-4">
                                                <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest mb-2">Evidencia Adjunta</p>
                                                <a href="${item.foto}" target="_blank" class="block relative group/img overflow-hidden rounded-2xl border-2 border-white shadow-md">
                                                    <img src="${item.foto}" class="w-full h-24 object-cover transition-transform duration-500 group-hover/img:scale-110">
                                                    <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-white text-sm">visibility</span>
                                                    </div>
                                                </a>
                                            </div>
                                        ` : ''}
                                    </div>
                                `;
                            });
                            
                            html += '</div></div>';
                            Swal.update({ html: html, showConfirmButton: true, confirmButtonText: 'Entendido' });
                        });
                    }
                });
            },

            openMore(uuid) {
                Swal.fire({
                    title: 'Otras Gestiones',
                    icon: 'info',
                    showConfirmButton: false,
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    customClass: { popup: 'rounded-[40px]' },
                    html: `
                        <div class="grid grid-cols-1 gap-2 mt-4">
                            <button onclick="window.dispatchEvent(new CustomEvent('open-call-modal', {detail: {uuid: '${uuid}', state: 'Correo de voz', type: 'warning'}}))" class="w-full py-4 px-6 bg-slate-50 hover:bg-amber-50 text-slate-700 rounded-2xl text-xs font-bold transition-all border border-slate-100 text-left flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-amber-400"></span> Correo de voz
                            </button>
                            <button onclick="window.dispatchEvent(new CustomEvent('open-call-modal', {detail: {uuid: '${uuid}', state: 'Fuera de servicio', type: 'danger'}}))" class="w-full py-4 px-6 bg-slate-50 hover:bg-red-50 text-slate-700 rounded-2xl text-xs font-bold transition-all border border-slate-100 text-left flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span> Fuera de servicio
                            </button>
                            <button onclick="window.dispatchEvent(new CustomEvent('open-call-modal', {detail: {uuid: '${uuid}', state: 'Número equivocado', type: 'danger'}}))" class="w-full py-4 px-6 bg-slate-50 hover:bg-red-50 text-slate-700 rounded-2xl text-xs font-bold transition-all border border-slate-100 text-left flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-red-400"></span> Número equivocado
                            </button>
                            <button onclick="window.dispatchEvent(new CustomEvent('open-call-modal', {detail: {uuid: '${uuid}', state: 'No desea carnet', type: 'danger'}}))" class="w-full py-4 px-6 bg-slate-50 hover:bg-slate-900 hover:text-white text-slate-700 rounded-2xl text-xs font-bold transition-all border border-slate-100 text-left flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span> No desea carnet
                            </button>
                        </div>
                    `
                });
            },

            markAsOnRoute(uuid) {
                Swal.fire({
                    title: '¿Mover a Ruta?',
                    text: "Este afiliado se marcará como 'En ruta' para entrega de carnet.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, despachar',
                    customClass: { popup: 'rounded-[32px]' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/callcenter/documents/${uuid}/status`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ status: 'recibida' }) // Esto ya lo marca como Completado/En Ruta en el controlador
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                this.removeAfiliado(uuid);
                                Swal.fire('¡En Ruta!', 'Registro actualizado para logística.', 'success');
                            }
                        });
                    }
                });
            },

            removeAfiliado(uuid) {
                this.afiliados = this.afiliados.filter(a => a.uuid !== uuid);
                
                // Actualizar la lista de la sesión si está abierta
                if (this.sessionOpen) {
                    this.sessionAfiliados = this.sessionAfiliados.filter(a => a.uuid !== uuid);
                    // Si se vacía la sesión, cerrarla con éxito
                    if (this.sessionAfiliados.length === 0) {
                        setTimeout(() => {
                            this.closeSession();
                            Swal.fire({
                                title: '¡Empresa Completada!',
                                text: 'Has gestionado a todos los afiliados de esta entidad.',
                                icon: 'success',
                                customClass: { popup: 'rounded-[32px]' }
                            });
                        }, 500);
                    }
                }

                // Actualizar contadores
                this.tabs.forEach(t => {
                    t.count = this.afiliados.filter(a => a.category === t.id).length;
                });
            },

            openWhatsApp(phone, name) {
                if (!phone) return;
                const cleanPhone = phone.replace(/\D/g, '');
                const message = encodeURIComponent(`Hola ${name}, te contactamos de ARS CMD para coordinar la entrega de tu carnet. Por favor confírmanos tu disponibilidad.`);
                window.open(`https://wa.me/1${cleanPhone}?text=${message}`, '_blank');
            }
        }
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection
