@props(['icon', 'label', 'value', 'color' => 'blue', 'trend' => null, 'trendType' => 'up'])

@php
    $colors = [
        'blue' => 'bg-blue-50 text-blue-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'slate' => 'bg-slate-100 text-slate-600',
    ];
    $colorClass = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm group hover:shadow-xl hover:shadow-slate-200/20 transition-all duration-300">
    <div class="flex items-start justify-between mb-4">
        <div class="w-14 h-14 rounded-2xl {{ $colorClass }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
            <span class="material-symbols-outlined text-2xl">{{ $icon }}</span>
        </div>
        @if($trend)
            <div @class([
                'flex items-center gap-1 px-2 py-1 rounded-full text-[0.65rem] font-bold',
                'bg-emerald-50 text-emerald-600' => $trendType === 'up',
                'bg-rose-50 text-rose-600' => $trendType === 'down'
            ])>
                <span class="material-symbols-outlined text-[0.8rem]">{{ $trendType === 'up' ? 'trending_up' : 'trending_down' }}</span>
                <span>{{ $trend }}</span>
            </div>
        @endif
    </div>
    <p class="text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.15em] mb-1">{{ $label }}</p>
    <h3 class="text-3xl font-black text-slate-900 tracking-tighter">{{ $value }}</h3>
</div>
