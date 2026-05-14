<!-- MENU: ASISTENCIA -->
<a class="{{ request()->routeIs('asistencia.index') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('asistencia.index') }}">
    <i class="ph ph-clock text-[22px] {{ request()->routeIs('asistencia.index') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Reloj Checador</span>
</a>

<a class="{{ request()->routeIs('asistencia.permisos.*') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('asistencia.permisos.index') }}">
    <i class="ph ph-envelope-open text-[22px] {{ request()->routeIs('asistencia.permisos.*') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Mis Permisos</span>
</a>

<a class="{{ request()->routeIs('asistencia.historial') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('asistencia.historial') }}">
    <i class="ph ph-calendar-check text-[22px] {{ request()->routeIs('asistencia.historial') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Mi Historial</span>
</a>

@hasanyrole('Admin|Supervisor|Recursos Humanos')
    <div class="px-6 pt-6 pb-2">
        <p class="text-[0.65rem] font-black uppercase tracking-[0.25em] text-slate-400">Administración</p>
    </div>

    <a class="{{ request()->routeIs('asistencia.dashboard') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('asistencia.dashboard') }}">
        <i class="ph ph-chart-pie text-[22px] {{ request()->routeIs('asistencia.dashboard') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Dashboard Monitor</span>
    </a>

    <a class="{{ request()->routeIs('asistencia.permisos.bandeja') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('asistencia.permisos.bandeja') }}">
        <i class="ph ph-tray text-[22px] {{ request()->routeIs('asistencia.permisos.bandeja') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Bandeja Aprobación</span>
    </a>

    <a class="{{ request()->routeIs('asistencia.reportes.*') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('asistencia.reportes.index') }}">
        <i class="ph ph-file-csv text-[22px] {{ request()->routeIs('asistencia.reportes.*') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Reportes Analíticos</span>
    </a>

    <a class="{{ request()->routeIs('asistencia.configuracion.*') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('asistencia.configuracion.index') }}">
        <i class="ph ph-gear text-[22px] {{ request()->routeIs('asistencia.configuracion.*') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Configuración App</span>
    </a>
@endhasanyrole
