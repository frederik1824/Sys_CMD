<!-- MENU: PROGRAMA PYP -->
<a class="{{ request()->routeIs('pyp.dashboard') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('pyp.dashboard') }}">
    <i class="ph ph-activity text-[22px] {{ request()->routeIs('pyp.dashboard') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Dashboard Riesgo</span>
</a>

<a class="{{ request()->routeIs('pyp.afiliados.*') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('pyp.afiliados.index') }}">
    <i class="ph ph-users-three text-[22px] {{ request()->routeIs('pyp.afiliados.*') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Buscador Clínico</span>
</a>

<a class="{{ request()->routeIs('pyp.programas.*') ? 'flex items-center gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('pyp.programas.index') }}">
    <i class="ph ph-folder-notched text-[22px] {{ request()->routeIs('pyp.programas.*') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Programas Base</span>
</a>

<div class="px-6 pt-6 pb-2">
    <p class="text-[0.65rem] font-black uppercase tracking-[0.25em] text-slate-400">Acciones Médicas</p>
</div>

<a class="flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link mb-2" href="{{ route('pyp.afiliados.index') }}">
    <i class="ph ph-plus-circle text-[22px] group-hover/link:text-indigo-700 text-slate-400"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Nueva Evaluación</span>
</a>
