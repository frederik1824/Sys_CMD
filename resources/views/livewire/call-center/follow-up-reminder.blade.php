<div wire:poll.30s="refreshReminders" class="flex items-center">
    @if($pendingCount > 0)
        <a href="{{ route('call-center.worklist') }}" 
           class="group relative inline-flex items-center gap-3 bg-white hover:bg-rose-50 text-rose-600 px-6 py-3 rounded-2xl border border-rose-100 shadow-xl shadow-rose-500/10 transition-all active:scale-95 animate-in fade-in zoom-in duration-500">
            
            <!-- Icono con Pulso -->
            <div class="relative">
                <i class="ph-bold ph-calendar-check text-2xl group-hover:rotate-12 transition-transform"></i>
                <span class="absolute top-0 right-0 w-3 h-3 bg-rose-500 rounded-full border-2 border-white animate-ping"></span>
                <span class="absolute top-0 right-0 w-3 h-3 bg-rose-500 rounded-full border-2 border-white"></span>
            </div>

            <!-- Texto Informativo -->
            <div class="flex flex-col items-start leading-tight">
                <span class="text-[10px] font-black uppercase tracking-widest text-rose-400">Seguimientos</span>
                <span class="text-sm font-black tracking-tight">{{ $pendingCount }} Pendientes</span>
            </div>

            <!-- Indicador de Urgencia -->
            <div class="ml-2 w-8 h-8 bg-rose-100 text-rose-600 rounded-lg flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-all">
                <i class="ph-bold ph-caret-right"></i>
            </div>
        </a>
    @endif
</div>
