<!-- MENU: TRASPASOS -->
<a href="{{ route('traspasos.dashboard') }}" class="flex items-center gap-4 px-6 py-3 rounded-r-xl transition-all {{ request()->routeIs('traspasos.dashboard') ? 'text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm' : 'text-slate-500 hover:text-amber-700 hover:bg-slate-50 group/link' }} mb-2">
    <i class="ph ph-chart-line-up text-[22px] {{ request()->routeIs('traspasos.dashboard') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Executive Hub</span>
</a>

<a href="{{ route('reportes.produccion_traspasos') }}" class="flex items-center gap-4 px-6 py-3 rounded-r-xl transition-all {{ request()->routeIs('reportes.produccion_traspasos') ? 'text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm' : 'text-slate-500 hover:text-amber-700 hover:bg-slate-50 group/link' }} mb-2">
    <i class="ph ph-presentation-chart text-[22px] {{ request()->routeIs('reportes.produccion_traspasos') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Reporte Producción</span>
</a>

<a class="{{ request()->routeIs('traspasos.index') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.index') }}">
    <i class="ph ph-list-bullets text-[22px] {{ request()->routeIs('traspasos.index') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Bandeja Global</span>
</a>

<a class="{{ request()->routeIs('traspasos.import') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.import') }}">
    <i class="ph ph-upload-simple text-[22px] {{ request()->routeIs('traspasos.import') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Importar Unipago</span>
</a>

<a class="{{ request()->routeIs('traspasos.bulk.effective') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.bulk.effective') }}">
    <i class="ph ph-lightning text-[22px] {{ request()->routeIs('traspasos.bulk.effective') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Efectividad Masiva</span>
</a>

@hasanyrole('Admin|Supervisor de Traspasos')
    <div class="px-6 pt-6 pb-2">
        <p class="text-[0.65rem] font-black uppercase tracking-[0.25em] text-slate-400">Administración</p>
    </div>

    <a class="{{ request()->routeIs('traspasos.config.afiliacion-procesos.*') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.config.afiliacion-procesos.index') }}">
        <i class="ph ph-notebook text-[22px] {{ request()->routeIs('traspasos.config.afiliacion-procesos.*') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Requisitos Afiliación</span>
    </a>

    <a class="{{ request()->routeIs('traspasos.config.motivos.*') ? 'flex items-center gap-4 px-6 py-3 text-amber-700 font-black bg-amber-50/50 border-l-[3px] border-amber-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-amber-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('traspasos.config.motivos.index') }}">
        <i class="ph ph-list-checks text-[22px] {{ request()->routeIs('traspasos.config.motivos.*') ? 'text-amber-600' : 'group-hover/link:text-amber-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Catálogo de Motivos</span>
    </a>
@endhasanyrole
