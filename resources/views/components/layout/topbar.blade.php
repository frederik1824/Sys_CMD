<header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md h-16 px-8 flex justify-between items-center shadow-sm">
    <div class="flex items-center gap-8">
        {{ $search ?? '' }}
        <nav class="flex gap-6">
            {{ $nav ?? '' }}
        </nav>
    </div>
    
    <div class="flex items-center gap-4">
        {{ $slot }}
    </div>
</header>
