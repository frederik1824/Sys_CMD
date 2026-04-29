<div class="space-y-10">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12 animate-fade-in">
        <div class="max-w-2xl">
            <h2 class="text-4xl font-black text-on-surface dark:text-white tracking-tight font-headline">Centro de Noticias ARS CMD</h2>
            <p class="text-[0.7rem] text-outline dark:text-outline-variant font-black uppercase tracking-[0.2em] mt-3">Comunicación Institucional, Eventos y Beneficios Corporativos</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <div class="relative flex-1 sm:w-64">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant text-[1.2rem]">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar noticia..." class="w-full bg-white dark:bg-surface-container-low border border-outline-variant/10 dark:border-white/5 rounded-2xl py-3.5 pl-12 pr-4 text-sm shadow-sm focus:ring-2 focus:ring-primary/20 transition-all font-bold text-on-surface dark:text-white">
            </div>
            
            <select wire:model.live="category" class="bg-white dark:bg-surface-container-low border border-outline-variant/10 dark:border-white/5 rounded-2xl py-3.5 px-6 text-sm shadow-sm focus:ring-2 focus:ring-primary/20 transition-all font-black uppercase tracking-widest cursor-pointer min-w-[180px] text-on-surface dark:text-white">
                <option value="">Todas las Categorías</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>
    </div>

    @if($news->count() > 0)
        <!-- Urgent Banner -->
        @php $urgent = $news->where('is_urgent', true)->first(); @endphp
        @if($urgent && $news->onFirstPage())
            <div class="relative overflow-hidden bg-white dark:bg-surface-container-low rounded-[4rem] shadow-2xl border border-error/10 dark:border-white/5 mb-16 group transition-all">
                <div class="flex flex-col lg:flex-row animate-fade-in">
                    <div class="lg:w-2/5 h-80 lg:h-auto overflow-hidden relative">
                        <img src="{{ $urgent->image_url ?? '/storage/news/news1.png' }}" alt="{{ $urgent->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-1000">
                        <div class="absolute inset-0 bg-gradient-to-r from-error/40 to-transparent"></div>
                        <div class="absolute top-8 left-8 px-5 py-2 bg-error text-white rounded-2xl text-[0.65rem] font-black uppercase tracking-widest shadow-2xl animate-pulse">Comunicado Urgente</div>
                    </div>
                    <div class="lg:w-3/5 p-16 flex flex-col justify-center">
                        <span class="text-[0.6rem] font-black text-error uppercase tracking-[0.3em] mb-4">COMUNICACIÓN OFICIAL • {{ $urgent->published_at->format('d M, Y') }}</span>
                        <h3 class="text-4xl font-black text-on-surface dark:text-white leading-tight mb-8 group-hover:text-error transition-colors">{{ $urgent->title }}</h3>
                        <p class="text-[0.85rem] text-outline dark:text-outline-variant leading-relaxed line-clamp-4 mb-10 font-medium">
                            {{ $urgent->content }}
                        </p>
                        <div class="flex items-center gap-6">
                            <button class="px-10 py-4 bg-error text-white font-black text-[0.7rem] uppercase tracking-widest rounded-2xl shadow-xl shadow-error/20 hover:scale-105 active:scale-95 transition-all">Prioridad Institucional</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="relative min-h-[400px]">
            <div wire:loading.remove wire:target="search, category">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    @foreach($news as $item)
                        @if($urgent && $urgent->id == $item->id) @continue @endif
                        <div class="bg-white dark:bg-surface-container-low rounded-[3.5rem] overflow-hidden border border-outline-variant/10 dark:border-white/5 shadow-sm hover:shadow-2xl hover:-translate-y-3 transition-all duration-500 h-full flex flex-col group">
                            <div class="h-64 relative overflow-hidden">
                                <img src="{{ $item->image_url ?? '/storage/news/news1.png' }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                <div class="absolute top-8 right-8 px-4 py-1.5 bg-white/95 dark:bg-surface-container-high/95 backdrop-blur-md rounded-xl text-[0.55rem] font-black uppercase tracking-widest text-primary dark:text-white shadow-lg">
                                    {{ $item->category }}
                                </div>
                            </div>
                            <div class="p-12 flex-1 flex flex-col">
                                <span class="text-[0.6rem] font-black text-outline dark:text-outline-variant uppercase tracking-[0.25em] mb-6 block">{{ $item->published_at->format('d M, Y') }}</span>
                                <h4 class="text-2xl font-black text-on-surface dark:text-white leading-tight mb-6 group-hover:text-primary transition-colors">{{ $item->title }}</h4>
                                <p class="text-[0.8rem] text-outline dark:text-outline-variant leading-relaxed line-clamp-4 mb-10 flex-1 font-medium italic">
                                    "{{ $item->content }}"
                                </p>
                                <div class="pt-8 border-t border-outline-variant/10 mt-auto">
                                    <button wire:click="viewNews({{ $item->id }})" class="text-[0.65rem] font-black text-primary dark:text-outline-variant dark:hover:text-white uppercase tracking-[0.25em] flex items-center gap-3 group/link hover:underline">
                                        Leer Completo <span class="material-symbols-outlined text-lg group-hover/link:translate-x-2 transition-transform">east</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-24">
                    {{ $news->links() }}
                </div>
            </div>

            <!-- Skeleton Loader Feed -->
            <div wire:loading wire:target="search, category" class="absolute inset-0 z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                    @for($i=0; $i<6; $i++)
                        <x-skeleton type="card" class="h-[500px] rounded-[3.5rem] bg-white dark:bg-surface-container-low" />
                    @endfor
                </div>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <img src="{{ asset('brain/895837ba-5cc8-42db-8d8c-0839e111acbc/empty_news_illustration_1775656276034.png') }}" class="w-64 mb-12 opacity-90" alt="Sin noticias">
            <h3 class="text-2xl font-black text-on-surface dark:text-white font-headline uppercase tracking-tight mb-4">Mesa de Redacción Vacía</h3>
            <p class="text-[0.75rem] text-outline font-black uppercase tracking-[0.3em] italic">No encontramos novedades que coincidan con tu búsqueda.</p>
        </div>
    @endif

    <!-- News Portrait Modal -->
    @if($selectedNews)
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-6 md:p-12 animate-fade-in">
            <div class="absolute inset-0 bg-surface/80 dark:bg-black/80 backdrop-blur-2xl" wire:click="closeNews"></div>
            
            <div class="relative bg-white dark:bg-surface-container-low w-full max-w-6xl max-h-[90vh] rounded-[4rem] shadow-[0_40px_100px_rgba(0,0,0,0.3)] border border-outline-variant/10 dark:border-white/5 overflow-hidden flex flex-col md:flex-row animate-scale-in">
                <div class="md:w-1/2 h-64 md:h-auto overflow-hidden relative">
                    <img src="{{ $selectedNews->image_url ?? '/storage/news/news1.png' }}" class="w-full h-full object-cover" alt="{{ $selectedNews->title }}">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    <div class="absolute bottom-12 left-12">
                        <span class="px-4 py-1.5 bg-primary text-white rounded-xl text-[0.6rem] font-black uppercase tracking-widest shadow-2xl mb-4 inline-block">{{ $selectedNews->category }}</span>
                        <h3 class="text-3xl font-black text-white leading-tight">{{ $selectedNews->title }}</h3>
                    </div>
                </div>
                
                <div class="md:w-1/2 p-12 md:p-20 overflow-y-auto no-scrollbar flex flex-col">
                    <div class="flex justify-between items-center mb-12">
                        <span class="text-[0.65rem] font-black text-outline uppercase tracking-[0.3em]">COMUNICADO • {{ $selectedNews->published_at->format('d M, Y') }}</span>
                        <button wire:click="closeNews" class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-surface-container hover:bg-error/10 hover:text-error transition-all flex items-center justify-center">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <div class="prose dark:prose-invert max-w-none">
                        <p class="text-lg text-on-surface dark:text-white leading-relaxed font-medium mb-8">
                            {{ $selectedNews->content }}
                        </p>
                        <!-- Space for more detailed content if needed in the future -->
                    </div>

                    <div class="mt-auto pt-12 flex items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">share</span>
                            </div>
                            <span class="text-[0.6rem] font-bold text-outline uppercase tracking-widest">Compartir Noticia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
