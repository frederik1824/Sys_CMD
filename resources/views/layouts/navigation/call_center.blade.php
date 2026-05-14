<!-- MENU: CALL CENTER V2 -->
@php
    $enlacesPendientes = \App\Models\CallCenterRegistro::where('prioridad', '>=', 100)
        ->whereNull('operador_id')
        ->count();
@endphp

<a class="{{ request()->routeIs('call-center.enlaces') ? 'flex items-center justify-between gap-4 px-6 py-3 text-indigo-700 font-black bg-indigo-50/50 border-l-[3px] border-indigo-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center justify-between gap-4 px-6 py-3 text-slate-500 hover:text-indigo-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-4" href="{{ route('call-center.enlaces') }}">
    <div class="flex items-center gap-4">
        <i class="ph-bold ph-link text-[22px] {{ request()->routeIs('call-center.enlaces') ? 'text-indigo-600' : 'group-hover/link:text-indigo-700 text-slate-400' }}"></i>
        <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Enlaces Directos</span>
    </div>
    @if($enlacesPendientes > 0)
        <span class="flex items-center justify-center min-w-[20px] h-5 px-1 bg-rose-500 text-white text-[9px] font-black rounded-full animate-pulse shadow-lg shadow-rose-500/20 border border-white">
            {{ $enlacesPendientes }}
        </span>
    @endif
</a>

<div class="h-px bg-slate-100 mx-6 mb-4"></div>

<a class="{{ request()->routeIs('call-center.worklist') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('call-center.worklist') }}">
    <i class="ph ph-headset text-[22px] {{ request()->routeIs('call-center.worklist') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Mi Bandeja de Trabajo</span>
</a>

<a class="{{ request()->routeIs('call-center.import') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('call-center.import') }}">
    <i class="ph ph-upload-simple text-[22px] {{ request()->routeIs('call-center.import') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Importar Prospectos</span>
</a>

<a class="{{ request()->routeIs('call-center.index') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('call-center.index') }}">
    <i class="ph ph-list-numbers text-[22px] {{ request()->routeIs('call-center.index') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Histórico de Cargas</span>
</a>

<a class="{{ request()->routeIs('call-center.stats') ? 'flex items-center gap-4 px-6 py-3 text-emerald-700 font-black bg-emerald-50/50 border-l-[3px] border-emerald-600 shadow-sm rounded-r-xl transition-all' : 'flex items-center gap-4 px-6 py-3 text-slate-500 hover:text-emerald-700 hover:bg-slate-50 rounded-r-xl transition-all group/link' }} mb-2" href="{{ route('call-center.stats') }}">
    <i class="ph ph-chart-pie-slice text-[22px] {{ request()->routeIs('call-center.stats') ? 'text-emerald-600' : 'group-hover/link:text-emerald-700 text-slate-400' }}"></i>
    <span class="text-[0.75rem] tracking-widest uppercase font-extrabold">Estadísticas y KPIs</span>
</a>
