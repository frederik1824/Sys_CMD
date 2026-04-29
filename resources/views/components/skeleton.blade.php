@props(['type' => 'text', 'class' => ''])

@php
    $baseClasses = "shimmer-bg bg-slate-100 dark:bg-surface-container-high/40 rounded-3xl $class";
    
    $typeClasses = match ($type) {
        'avatar' => "w-14 h-14 rounded-2xl",
        'title' => "h-10 w-2/3 mb-6 rounded-2xl",
        'text' => "h-4 w-full mb-3 rounded-lg",
        'button' => "h-12 w-32 rounded-[2rem]",
        'card' => "h-64 w-full rounded-[3.5rem]",
        default => "h-4 w-full"
    };
@endphp

<div {{ $attributes->merge(['class' => "$baseClasses $typeClasses"]) }}></div>
