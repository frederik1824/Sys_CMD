<!-- Navigation Links (Suite Ejecutiva) -->
<a class="{{ request()->routeIs('executive.suite') ? 'flex items-center gap-4 px-6 py-3 text-blue-700 font-black bg-blue-50/50 border-l-[3px] border-blue-600 shadow-sm rounded-r-xl transition-all relative overflow-hidden' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-blue-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-4 mt-0.5" href="{{ route('reportes.executive.suite') }}">
    @if(request()->routeIs('executive.suite'))
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-transparent"></div>
    @endif
    <i class="ph ph-crown text-[22px] {{ request()->routeIs('executive.suite') ? 'text-blue-600' : 'group-hover/link:text-blue-700 text-slate-400' }} transition-colors relative z-10"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold relative z-10">Suite Ejecutiva</span>
</a>
