<!-- Modal de Gestión de Llamada -->
<div x-show="modalOpen" class="fixed inset-0 z-[120] flex items-center justify-center p-4" style="display: none;">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md" @click="closeModal" x-transition.opacity></div>
    
    <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl relative z-10 overflow-hidden border border-white transform transition-all" 
        x-transition:enter="ease-out duration-300" 
        x-transition:enter-start="opacity-0 scale-95 translate-y-10">
        
        <div class="p-10">
            <div class="flex items-center gap-5 mb-8">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg" :class="{
                    'bg-emerald-100 text-emerald-600': modalType == 'success',
                    'bg-amber-100 text-amber-600': modalType == 'warning',
                    'bg-red-100 text-red-600': modalType == 'danger',
                    'bg-blue-100 text-blue-600': modalType == 'info',
                }">
                    <span class="material-symbols-outlined text-2xl" x-text="modalType == 'success' ? 'check_circle' : 'call_log'"></span>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tighter leading-none" x-text="bulkMode ? 'Gestión Masiva' : selectedState"></h3>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mt-2" x-text="bulkMode ? 'Aplicar a todos los afiliados de la sesión' : 'Confirmar resultado de la llamada'"></p>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Toggle de Documento Recibido y Carga de Foto (Fase 3) -->
                <template x-if="selectedState === 'Cédula efectiva'">
                    <div class="space-y-6 animate-in fade-in duration-500">
                        <div class="p-5 bg-emerald-50 rounded-3xl border border-emerald-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-black text-emerald-900 uppercase tracking-widest">¿Documento Recibido?</span>
                                <p class="text-[9px] text-emerald-600 font-bold mt-1">Marcar si ya tienes el soporte físico/digital</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="documentoRecibido" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3 ml-1">Foto de Cédula (Opcional)</label>
                            <div class="relative group">
                                <input type="file" @change="handleFileUpload" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                <div class="w-full bg-slate-50 border-2 border-dashed border-slate-200 rounded-3xl p-8 flex flex-col items-center justify-center transition-all group-hover:border-blue-400 group-hover:bg-blue-50/30">
                                    <template x-if="!fotoPreview">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm mx-auto mb-4 group-hover:scale-110 transition-transform">
                                                <span class="material-symbols-outlined text-slate-300 group-hover:text-blue-500 transition-colors">add_a_photo</span>
                                            </div>
                                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Click o arrastrar foto</p>
                                        </div>
                                    </template>
                                    <template x-if="fotoPreview">
                                        <div class="relative w-full aspect-video">
                                            <img :src="fotoPreview" class="w-full h-full object-cover rounded-2xl shadow-xl border-4 border-white">
                                            <button @click.stop="fotoPreview = null; evidenciaFile = null" class="absolute -top-3 -right-3 bg-rose-500 text-white p-2 rounded-xl shadow-xl hover:bg-rose-600 transition-colors">
                                                <span class="material-symbols-outlined text-sm">delete</span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3 ml-1">Observaciones (Opcional)</label>
                    <textarea x-model="observacion" rows="3" class="w-full bg-slate-50 border-none rounded-2xl p-5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 transition-all placeholder:text-slate-300" placeholder="Ej: Se comunicó con el titular, enviará por WhatsApp..."></textarea>
                </div>

                <!-- Agendamiento de Seguimiento (Fase 2) -->
                <template x-if="['No contesta', 'Correo de voz', 'Fuera de servicio', 'Número equivocado'].includes(selectedState)">
                    <div class="animate-in slide-in-from-top-2 duration-300">
                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3 ml-1">Agendar Seguimiento</label>
                        <div class="relative">
                            <input type="date" x-model="proximoContacto" class="w-full bg-slate-50 border-none rounded-2xl p-5 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-blue-500/20 transition-all">
                            <div class="absolute right-5 top-1/2 -translate-y-1/2 text-slate-300">
                                <span class="material-symbols-outlined text-lg">event</span>
                            </div>
                        </div>
                        <p class="text-[9px] font-bold text-slate-400 mt-2 ml-1">Sugerencia: Programar para 24-48 horas hábiles</p>
                    </div>
                </template>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
                <button @click="closeModal" class="h-14 bg-slate-100 text-slate-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">Cancelar</button>
                <button @click="submitCall()" :disabled="isSubmitting" class="h-14 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 hover:bg-blue-700 transition-all disabled:opacity-50">
                    <span x-show="!isSubmitting">Guardar Gestión</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2 justify-center"><span class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span> Procesando</span>
                </button>
            </div>
        </div>
    </div>
</div>
