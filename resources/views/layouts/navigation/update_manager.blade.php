<!-- MENU: UPDATE MANAGER -->
<a class="{{ request()->routeIs('admin.updates.index') && !request()->has('anchor') ? 'flex items-center gap-4 px-6 py-3 text-blue-700 font-black bg-blue-50/50 border-l-[3px] border-blue-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('admin.updates.index') }}">
    <i class="ph ph-rocket-launch text-[22px] {{ request()->routeIs('admin.updates.index') ? 'text-blue-600' : 'group-hover/link:text-blue-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Historial de Releases</span>
</a>

<a class="flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link mb-2" href="{{ route('admin.updates.index') }}#health">
    <i class="ph ph-activity text-[22px] group-hover/link:text-blue-700 text-slate-400"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Monitor de Salud</span>
</a>

<a class="flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link mb-2" href="{{ route('admin.updates.index') }}#backups">
    <i class="ph ph-database text-[22px] group-hover/link:text-blue-700 text-slate-400"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Snapshots & Backups</span>
</a>

<a class="flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link mb-2" href="{{ route('admin.updates.index') }}#packer">
    <i class="ph ph-package text-[22px] group-hover/link:text-blue-700 text-slate-400"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Generador de Patches</span>
</a>
