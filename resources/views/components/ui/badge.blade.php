@props(['color' => 'slate', 'label' => null])

@php
    $colors = [
        'slate' => 'bg-slate-100 text-slate-700',
        'primary' => 'bg-blue-50 text-blue-700',
        'emerald' => 'bg-emerald-100 text-emerald-700',
        'amber' => 'bg-amber-100 text-amber-700',
        'rose' => 'bg-rose-100 text-rose-700',
        'indigo' => 'bg-indigo-100 text-indigo-700',
    ];
    $colorClass = $colors[$color] ?? $colors['slate'];
@endphp

<span {{ $attributes->merge(['class' => "text-[0.65rem] font-black uppercase tracking-widest px-2.5 py-1 rounded-full $colorClass"]) }}>
    {{ $label ?? $slot }}
</span>
