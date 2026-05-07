<div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden" x-data="{ script: 'inicio' }">
    <div class="p-8 border-b border-slate-50 bg-indigo-50/30 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                <i class="ph-bold ph-megaphone text-xl"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Scripts de Venta</h2>
        </div>
    </div>
    
    <div class="p-8 space-y-6">
        <!-- Selector de Script -->
        <div class="flex p-1 bg-slate-100 rounded-2xl">
            <button @click="script = 'inicio'" :class="script === 'inicio' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">Inicio</button>
            <button @click="script = 'seguimiento'" :class="script === 'seguimiento' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">Seguimiento</button>
            <button @click="script = 'cierre'" :class="script === 'cierre' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-500'" class="flex-1 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all">Cierre</button>
        </div>

        <!-- Contenido de los Scripts -->
        <div class="space-y-4">
            <div x-show="script === 'inicio'" class="animate-in slide-in-from-right-4 duration-300">
                <p class="text-sm font-bold text-slate-600 leading-relaxed mb-4">
                    "Buen día, le habla <strong>{{ auth()->user()->name }}</strong> de <strong>ARS CMD</strong>. Me comunico para validar sus datos de carnetización corporativa vinculados a la empresa <strong>{{ $registro->empresa_nombre }}</strong>..."
                </p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-[11px] font-bold text-indigo-600">
                        <i class="ph-bold ph-check-circle"></i> Confirmar nombre completo
                    </li>
                    <li class="flex items-center gap-2 text-[11px] font-bold text-indigo-600">
                        <i class="ph-bold ph-check-circle"></i> Validar dirección de entrega
                    </li>
                </ul>
            </div>

            <div x-show="script === 'seguimiento'" style="display: none;" class="animate-in slide-in-from-right-4 duration-300">
                <p class="text-sm font-bold text-slate-600 leading-relaxed mb-4">
                    "Hola <strong>{{ $registro->nombre }}</strong>, le contactamos nuevamente para dar seguimiento a la solicitud de sus documentos. ¿Ha podido completar el formulario firmado?"
                </p>
                <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100">
                    <p class="text-[10px] font-black text-indigo-700 uppercase mb-1">Tip de Calidad:</p>
                    <p class="text-xs font-medium text-indigo-600">Si el afiliado tiene dudas, recuérdele que este proceso es gratuito y obligatorio para su cobertura.</p>
                </div>
            </div>

            <div x-show="script === 'cierre'" style="display: none;" class="animate-in slide-in-from-right-4 duration-300">
                <p class="text-sm font-bold text-slate-600 leading-relaxed mb-4">
                    "Perfecto, hemos actualizado sus datos. En los próximos días recibirá su carnet en la dirección confirmada. ¿Hay algo más en lo que pueda ayudarle?"
                </p>
                <div class="flex items-center gap-2 text-[11px] font-black text-emerald-600 bg-emerald-50 px-3 py-2 rounded-xl border border-emerald-100">
                    <i class="ph-bold ph-rocket-launch"></i>
                    PROMOVER A CARNETIZACIÓN AHORA
                </div>
            </div>
        </div>
    </div>
</div>
