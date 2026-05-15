<div class="bg-white/80 backdrop-blur-md p-6 rounded-[2rem] border border-white shadow-[0_15px_35px_-12px_rgba(0,0,0,0.05)] hover:shadow-[0_25px_50px_-15px_rgba(0,0,0,0.08)] hover:-translate-y-1.5 transition-all duration-500 group relative overflow-hidden">
    <!-- Subtle Gradient Glow -->
    <div class="absolute -top-16 -right-16 w-32 h-32 bg-{{ $color }}-500/10 rounded-full blur-[40px] group-hover:bg-{{ $color }}-500/15 transition-colors duration-700"></div>
    
    <div class="relative z-10">
        <div class="flex justify-between items-start mb-6">
            <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-slate-100 group-hover:scale-105 group-hover:rotate-2 transition-all duration-500">
                <i class="ph-duotone {{ $icon }} text-xl text-{{ $color }}-600"></i>
            </div>
            
            @if(isset($variation))
                <div class="text-right">
                    @if($variation > 0)
                        <div class="inline-flex items-center gap-1 text-emerald-600 font-bold bg-emerald-50 px-2.5 py-1 rounded-xl border border-emerald-100/50 text-[9px] shadow-sm">
                            <i class="ph-bold ph-trend-up"></i> +{{ $variation }}%
                        </div>
                    @elseif($variation < 0)
                        <div class="inline-flex items-center gap-1 text-rose-600 font-bold bg-rose-50 px-2.5 py-1 rounded-xl border border-rose-100/50 text-[9px] shadow-sm">
                            <i class="ph-bold ph-trend-down"></i> {{ $variation }}%
                        </div>
                    @else
                        <div class="inline-flex items-center gap-1 text-slate-400 font-bold bg-slate-50 px-2.5 py-1 rounded-xl border border-slate-100 text-[9px]">
                            0%
                        </div>
                    @endif
                </div>
            @endif
        </div>
        
        <div class="space-y-0.5">
            <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ $title }}</h4>
            <div class="text-3xl font-heading font-black text-slate-900 tracking-tight leading-none flex items-baseline gap-1">
                {{ $value }}
                @if(isset($unit))
                    <span class="text-sm text-slate-400 font-bold uppercase tracking-widest">{{ $unit }}</span>
                @endif
            </div>
        </div>

        <!-- Sparkline Animation (More subtle) -->
        <div class="mt-6 flex items-end justify-between gap-1 h-8 opacity-60 group-hover:opacity-100 transition-opacity">
            @foreach(range(1, 15) as $i)
                <div class="w-full bg-{{ $color }}-100 rounded-full group-hover:bg-{{ $color }}-500/20 transition-all duration-700" 
                     style="height: {{ rand(20, 100) }}%; transition-delay: {{ $i * 20 }}ms"></div>
            @endforeach
        </div>
    </div>
</div>
