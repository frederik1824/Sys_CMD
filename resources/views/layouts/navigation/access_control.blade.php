@hasanyrole('Admin')
<!-- MENU: SEGURIDAD / ACCESOS -->
<div class="px-6 pt-2 pb-2">
    <p class="text-[0.65rem] font-black uppercase tracking-[0.25em] text-slate-400">Control Maestro</p>
</div>

<a class="{{ request()->routeIs('admin.access.users*') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.access.users') }}">
    <i class="ph ph-users-three text-[22px] {{ request()->routeIs('admin.access.users*') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Gestión de Nómina</span>
</a>

<a class="{{ request()->routeIs('admin.access.apps*') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.access.apps') }}">
    <i class="ph ph-squares-four text-[22px] {{ request()->routeIs('admin.access.apps*') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Módulos Instalados</span>
</a>

<a class="{{ request()->routeIs('admin.access.roles*') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.access.roles') }}">
    <i class="ph ph-shield-star text-[22px] {{ request()->routeIs('admin.access.roles*') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Diccionario de Roles</span>
</a>

<a class="{{ request()->routeIs('admin.access.index') ? 'flex items-center gap-4 px-6 py-3 text-slate-900 font-black bg-slate-100 border-l-[3px] border-slate-900 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-slate-900 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.access.index') }}">
    <i class="ph ph-shield-checkered text-[22px] {{ request()->routeIs('admin.access.index') ? 'text-slate-900' : 'group-hover/link:text-slate-900 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Matriz de Accesos</span>
</a>
@endhasanyrole
