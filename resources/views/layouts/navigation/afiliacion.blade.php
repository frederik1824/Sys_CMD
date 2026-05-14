<!-- MENU: AFILIACIÓN -->
<a class="{{ request()->routeIs('solicitudes-afiliacion.index') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.index') }}">
    <i class="ph ph-list-checks text-[22px] {{ request()->routeIs('solicitudes-afiliacion.index') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Bandeja Operativa</span>
</a>

<a class="{{ request()->routeIs('solicitudes-afiliacion.create') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.create') }}">
    <i class="ph ph-plus-circle text-[22px] {{ request()->routeIs('solicitudes-afiliacion.create') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Nueva Solicitud</span>
</a>

<a class="{{ request()->routeIs('afiliacion.analytics') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.analytics') }}">
    <i class="ph ph-chart-line-up text-[22px] {{ request()->routeIs('afiliacion.analytics') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Command Center (Sisalril)</span>
</a>

@hasanyrole('Admin|Supervisor|Supervisor de Afiliación|Supervisor de Autorizaciones|Supervisor de Cuentas Médicas|Supervisor de Servicio al Cliente')
<a class="{{ request()->routeIs('solicitudes-afiliacion.workload') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.workload') }}">
    <i class="ph ph-chart-bar text-[22px] {{ request()->routeIs('solicitudes-afiliacion.workload') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Balanceo de Carga</span>
</a>
@endhasanyrole

@hasanyrole('Admin')
<div class="pt-4 mt-4 border-t border-slate-100">
    <p class="px-6 text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Administración</p>
    <a class="{{ request()->routeIs('solicitudes-afiliacion.departamentos.*') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.departamentos.index') }}">
        <i class="ph ph-buildings text-[22px] {{ request()->routeIs('solicitudes-afiliacion.departamentos.*') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Estructura / Dptos</span>
    </a>
    <a class="{{ request()->routeIs('solicitudes-afiliacion.config') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('afiliacion.config') }}">
        <i class="ph ph-gear text-[22px] {{ request()->routeIs('solicitudes-afiliacion.config') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Configuración</span>
    </a>
</div>
@endhasanyrole
