<!-- Modal de Sesión de Trabajo Enfocada (Batch Management) -->
<div x-show="sessionOpen" class="fixed inset-0 z-[110] flex items-center justify-center p-0 md:p-8" style="display: none;">
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-900/80 backdrop-blur-xl" @click="closeSession" x-transition.opacity></div>
    
    <!-- Modal Container -->
    <div class="bg-white w-full max-w-6xl h-full md:h-[85vh] rounded-none md:rounded-[48px] shadow-2xl relative z-10 overflow-hidden border border-white/20 flex flex-col transform transition-all"
         x-transition:enter="ease-out duration-500" 
         x-transition:enter-start="opacity-0 scale-95 translate-y-20">
        
        <!-- Header de la Sesión -->
        <div class="px-10 py-8 bg-slate-50/50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 rounded-[24px] bg-blue-600 flex items-center justify-center text-white shadow-xl shadow-blue-500/30">
                    <span class="material-symbols-outlined text-3xl animate-pulse">bolt</span>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-blue-600 mb-1">Sesión de Trabajo Enfocada</p>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tighter" x-text="sessionEmpresa"></h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button @click="openBulkModal()" class="px-6 py-3 bg-slate-900 text-white rounded-[20px] text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all flex items-center gap-2 shadow-xl shadow-slate-900/10 active:scale-95 group">
                    <span class="material-symbols-outlined text-base group-hover:animate-bounce">done_all</span>
                    Gestión Masiva
                </button>
                
                <button @click="closeSession" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-100 hover:bg-red-50 transition-all group">
                    <span class="material-symbols-outlined transition-transform group-hover:rotate-90">close</span>
                </button>
            </div>
        </div>

        <!-- Body: Lista de Afiliados de la Empresa -->
        <div class="flex-1 overflow-y-auto custom-scrollbar p-10 bg-slate-50/30">
            <!-- Info de Contacto RRHH (Sugerencia 2) -->
            <template x-if="sessionContacto && sessionContacto.contacto">
                <div class="mb-8 p-6 bg-white border border-slate-100 rounded-[32px] shadow-sm flex items-center justify-between animate-in fade-in zoom-in duration-500">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner">
                            <span class="material-symbols-outlined text-3xl">contact_phone</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Enlace de RRHH / Empresa</p>
                            <h4 class="text-lg font-black text-slate-900 tracking-tight leading-none mt-1" x-text="sessionContacto.contacto"></h4>
                            <p class="text-[10px] font-bold text-blue-600 uppercase mt-1.5" x-text="sessionContacto.puesto || 'Gestor de Carnets'"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <template x-if="sessionContacto.telefono">
                            <a :href="'tel:' + sessionContacto.telefono" class="flex items-center gap-3 px-6 h-12 bg-slate-900 text-white rounded-2xl hover:bg-blue-600 transition-all group shadow-lg shadow-slate-900/10">
                                <span class="material-symbols-outlined text-sm group-hover:animate-shake">call</span>
                                <span class="text-[11px] font-black tracking-widest uppercase" x-text="sessionContacto.telefono"></span>
                            </a>
                        </template>
                        <template x-if="sessionContacto.email">
                            <a :href="'mailto:' + sessionContacto.email" class="w-12 h-12 flex items-center justify-center bg-white border border-slate-200 text-slate-400 rounded-2xl hover:text-blue-600 hover:border-blue-100 transition-all">
                                <span class="material-symbols-outlined text-xl">mail</span>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <div class="grid grid-cols-1 gap-4">
                <template x-for="afiliado in sessionAfiliados" :key="afiliado.uuid">
                    <div class="group/item flex items-center gap-6 p-6 bg-slate-50/50 border border-slate-100 rounded-[32px] hover:bg-white hover:border-blue-200 hover:shadow-xl hover:shadow-blue-500/5 transition-all">
                        
                        <!-- Info del Afiliado -->
                        <div class="flex-1 flex items-center gap-6">
                            <div class="w-14 h-14 rounded-2xl bg-white flex items-center justify-center text-slate-300 group-hover/item:bg-blue-50 group-hover/item:text-blue-600 transition-all shadow-inner">
                                <span class="material-symbols-outlined text-2xl">person</span>
                            </div>
                            <div class="flex flex-col">
                                <h4 class="text-base font-black text-slate-800 tracking-tight" x-text="afiliado.nombre"></h4>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest" x-text="afiliado.cedula"></span>
                                    <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                    <span class="text-[11px] font-bold text-blue-600" x-text="afiliado.telefono || 'Sin teléfono'"></span>
                                </div>
                                <!-- SLA Alert in Session -->
                                <template x-if="afiliado.last_call_days > 0">
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="afiliado.last_call_days > 3 ? 'bg-rose-500 animate-pulse' : (afiliado.last_call_days > 1 ? 'bg-amber-500' : 'bg-emerald-500')"></span>
                                        <span class="text-[8px] font-black uppercase tracking-widest text-slate-400" x-text="'Hace ' + afiliado.last_call_days + ' días'"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Acciones Rápidas -->
                        <div class="flex items-center gap-3">
                            <template x-if="activeTab === 'nuevos' || activeTab === 'reintentos'">
                                <div class="flex gap-2">
                                    <button @click="openModal(afiliado.uuid, 'Cédula efectiva', 'success')" 
                                            class="h-12 px-6 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition-all flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                        Efectiva
                                    </button>
                                    <button @click="openModal(afiliado.uuid, 'No contesta', 'warning')" 
                                            class="h-12 px-6 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 hover:text-white transition-all flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm">call_missed</span>
                                        No Contesta
                                    </button>
                                    <button @click="openMore(afiliado.uuid)" 
                                            class="h-12 w-12 bg-slate-100 text-slate-400 rounded-2xl hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center">
                                        <span class="material-symbols-outlined text-xl">more_vert</span>
                                    </button>
                                </div>
                            </template>

                            <template x-if="activeTab === 'documentacion'">
                                <button @click="openModal(afiliado.uuid, 'Cédula efectiva', 'success', true)" 
                                        class="h-12 px-8 bg-indigo-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all flex items-center gap-3">
                                    <span class="material-symbols-outlined text-lg">verified</span>
                                    Validar Documento
                                </button>
                            </template>

                            <template x-if="activeTab === 'confirmados'">
                                <button @click="markAsOnRoute(afiliado.uuid)" 
                                        class="h-12 px-8 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all flex items-center gap-3">
                                    <span class="material-symbols-outlined text-lg">local_shipping</span>
                                    Despachar
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Footer Informativo -->
        <div class="px-10 py-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                Modo de enfoque activo: Gestionando <span class="text-blue-600" x-text="sessionAfiliados.length"></span> registros de esta entidad.
            </p>
            <div class="flex items-center gap-4">
                <span class="text-[9px] font-black text-slate-400 uppercase">Progreso de la sesión:</span>
                <div class="w-32 h-2 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600 transition-all" :style="'width: ' + (100 - (sessionAfiliados.length / (filteredAfiliados.filter(a => a.empresa === sessionEmpresa).length || 1) * 100)) + '%'"></div>
                </div>
            </div>
        </div>
    </div>
</div>
