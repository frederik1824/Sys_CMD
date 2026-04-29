@props(['route', 'icon', 'label', 'query' => null])

@php
    $fullRoute = $query ? route($route) . '?' . $query : route($route);
    $isActive = request()->routeIs($route) || (request()->fullUrl() == $fullRoute);
@endphp

<a href="{{ $fullRoute }}" 
   class="{{ $isActive ? 'text-blue-700 font-black bg-blue-50/80 border-l-[3px] border-blue-600 shadow-sm' : 'text-slate-500 hover:text-blue-700 hover:bg-slate-50' }} flex items-center gap-4 px-4 py-2.5 rounded-r-xl transition-all group/link mt-0.5 relative overflow-hidden">
    @if($isActive)
        <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 to-transparent"></div>
    @endif
    <i class="{{ $icon }} text-[20px] {{ $isActive ? 'text-blue-600' : 'text-slate-400 group-hover/link:text-blue-600' }} transition-colors relative z-10"></i>
    <span class="text-[0.7rem] tracking-wider uppercase font-extrabold relative z-10">{{ $label }}</span>
</a>
