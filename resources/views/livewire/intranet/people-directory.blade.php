<div class="space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12 animate-fade-in">
        <div>
            <h2 class="text-4xl font-black text-on-surface dark:text-white tracking-tight font-headline">Directorio de Colaboradores</h2>
            <p class="text-[0.7rem] text-outline dark:text-outline-variant font-black uppercase tracking-[0.2em] mt-3">Conecta con el Talento Humano de ARS CMD</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
            <div class="relative flex-1 sm:w-80">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant text-[1.2rem]">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre o puesto..." class="w-full bg-white dark:bg-surface-container-low border border-outline-variant/10 dark:border-white/5 rounded-2xl py-3.5 pl-12 pr-4 text-sm shadow-sm focus:ring-2 focus:ring-primary/20 transition-all font-bold text-on-surface dark:text-white">
            </div>
            
            <select wire:model.live="filterDepartment" class="bg-white dark:bg-surface-container-low border border-outline-variant/10 dark:border-white/5 rounded-2xl py-3.5 px-6 text-sm shadow-sm focus:ring-2 focus:ring-primary/20 transition-all font-black uppercase tracking-widest cursor-pointer min-w-[200px] text-on-surface dark:text-white">
                <option value="">Todos los Departamentos</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Data Display Section -->
    <div class="relative min-h-[400px]">
        <div wire:loading.remove wire:target="search, filterDepartment">
            @if($people->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($people as $person)
                        <div class="bg-white dark:bg-surface-container-low rounded-[3rem] p-10 border border-outline-variant/5 dark:border-white/5 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 relative overflow-hidden flex flex-col items-center group">
                            <!-- Background Decoration -->
                            <div class="absolute -right-10 -top-10 w-32 h-32 bg-primary/5 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
                            
                            <div class="relative mb-8">
                                <div class="w-28 h-28 rounded-[2.5rem] bg-slate-100 dark:bg-surface-container overflow-hidden border-4 border-white dark:border-surface-container-high shadow-xl group-hover:scale-105 transition-transform">
                                    @if($person->avatar_url)
                                        <img src="{{ $person->avatar_url }}" alt="{{ $person->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-primary text-white font-black text-3xl">
                                            {{ substr($person->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 {{ $person->isOnline() ? 'bg-success' : 'bg-slate-300 dark:bg-outline' }} rounded-full border-4 border-white dark:border-surface-container-high shadow-sm" title="{{ $person->isOnline() ? 'Conectado' : 'Desconectado (Últ. vez: ' . ($person->last_activity_at?->diffForHumans() ?? 'Nunca') . ')' }}"></div>
                            </div>

                            <div class="text-center mb-10 flex-1">
                                <h4 class="text-xl font-black text-on-surface dark:text-white tracking-tight mb-2">{{ $person->name }}</h4>
                                <p class="text-[0.65rem] font-black text-primary uppercase tracking-widest mb-4 italic">{{ $person->job_title ?? 'Colaborador' }}</p>
                                <span class="inline-block px-4 py-1.5 bg-slate-50 dark:bg-white/5 text-[0.6rem] font-black text-outline dark:text-outline-variant rounded-xl uppercase tracking-widest">
                                    {{ $person->department->name ?? 'Institucional' }}
                                </span>
                            </div>

                            <div class="w-full space-y-4 pt-8 border-t border-outline-variant/10">
                                <a href="mailto:{{ $person->email }}" class="flex items-center gap-3 text-outline hover:text-primary transition-all group/link truncate">
                                    <span class="material-symbols-outlined text-[1.2rem] group-hover/link:translate-x-1 transition-transform">mail</span>
                                    <span class="text-[0.75rem] font-bold truncate">{{ $person->email }}</span>
                                </a>
                                @if($person->phone_extension)
                                    <div class="flex items-center gap-3 text-outline hover:text-primary transition-all">
                                        <span class="material-symbols-outlined text-[1.2rem]">phone_forwarded</span>
                                        <span class="text-[0.75rem] font-bold">Extensión: <span class="text-on-surface dark:text-white">{{ $person->phone_extension }}</span></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-16">
                    {{ $people->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <img src="{{ asset('brain/895837ba-5cc8-42db-8d8c-0839e111acbc/people_search_illustration_1775656102061.png') }}" class="w-64 mb-10 opacity-90" alt="Sin hallazgos">
                    <h3 class="text-2xl font-black text-on-surface dark:text-white font-headline uppercase tracking-tight mb-4">Misión no cumplida</h3>
                    <p class="text-[0.75rem] text-outline font-black uppercase tracking-[0.25em] italic">No encontramos al colaborar que buscas.</p>
                </div>
            @endif
        </div>

        <!-- Skeleton Loader -->
        <div wire:loading wire:target="search, filterDepartment" class="absolute inset-0 z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @for($i=0; $i<8; $i++)
                    <x-skeleton type="card" class="h-96 rounded-[3rem] bg-white dark:bg-surface-container-low" />
                @endfor
            </div>
        </div>
    </div>
</div>
