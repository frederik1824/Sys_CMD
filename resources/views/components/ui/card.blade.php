@props(['title' => null, 'description' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden']) }}>
    @if($title || $description || isset($header))
        <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30">
            @if(isset($header))
                {{ $header }}
            @else
                <h3 class="font-bold text-slate-800 leading-tight">{{ $title }}</h3>
                @if($description)
                    <p class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $description }}</p>
                @endif
            @endif
        </div>
    @endif

    <div @class(['p-8' => $padding])>
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-8 py-4 bg-slate-50/50 border-t border-slate-50">
            {{ $footer }}
        </div>
    @endif
</div>
