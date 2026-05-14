<!-- MENU: CONTROL DE DISPERSION UNIFICADO -->
<div class="px-6 mb-4">
    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Núcleo Estratégico</p>
    
    <a class="{{ request()->routeIs('dispersion.index') && !request()->has('view') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.index') }}">
        <i class="ph ph-chart-pie-slice text-[22px] {{ request()->routeIs('dispersion.index') && !request()->has('view') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Dashboard Mensual</span>
    </a>

    <a class="{{ request()->query('view') === 'history' ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.index') }}?view=history">
        <i class="ph ph-calendar-check text-[22px] {{ request()->query('view') === 'history' ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Histórico de Periodos</span>
    </a>

    <a class="{{ request()->query('view') === 'cartera' ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.index') }}?view=cartera">
        <i class="ph ph-users-three text-[22px] {{ request()->query('view') === 'cartera' ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Cartera de Pensionados</span>
    </a>

    <a class="{{ request()->query('view') === 'reports' ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.index') }}?view=reports">
        <i class="ph ph-file-pdf text-[22px] {{ request()->query('view') === 'reports' ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Reportes Ejecutivos</span>
    </a>

    <a class="{{ request()->query('view') === 'config' ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('dispersion.index') }}?view=config">
        <i class="ph ph-gear-six text-[22px] {{ request()->query('view') === 'config' ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Configuración</span>
    </a>
</div>

<!-- SECCION UNIFICADA: PENSIONADOS TSS (ÁREA SEÑALADA) -->
<div class="px-6 mb-4 mt-8 pt-6 border-t border-slate-100">
    <div class="flex items-center justify-between mb-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Dispersión TSS</p>
        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-black uppercase rounded-md tracking-tighter">Motor de Descifrado</span>
    </div>
    
    <a class="{{ request()->routeIs('dispersion.pensionados.*') ? 'flex items-center gap-4 px-6 py-4 text-emerald-700 font-black bg-emerald-50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-4 text-slate-500 hover:text-emerald-700 hover:bg-emerald-50/50 rounded-r-xl transition-all group/pension' }} mb-2" href="{{ route('dispersion.pensionados.index') }}">
        <div class="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center border border-slate-100 group-hover/pension:border-emerald-200 transition-all">
            <i class="ph ph-bank text-[20px] {{ request()->routeIs('dispersion.pensionados.*') ? 'text-emerald-600' : 'group-hover/pension:text-emerald-600 text-slate-400' }}"></i>
        </div>
        <div class="flex flex-col">
            <span class="text-[0.7rem] tracking-tight uppercase font-black leading-none mb-1">Pensionados</span>
            <span class="text-[0.6rem] font-bold text-slate-400 leading-none">Descifrado de Archivos</span>
        </div>
    </a>

    <a class="flex items-center gap-4 px-6 py-3 text-slate-400 hover:text-slate-600 rounded-r-xl transition-all group/link mb-2" href="{{ route('dispersion.pensionados.history') }}">
        <i class="ph ph-clock-counter-clockwise text-[18px] group-hover/link:text-slate-600"></i>
        <span class="text-[0.65rem] tracking-widest uppercase font-extrabold">Historial TSS</span>
    </a>
</div>
