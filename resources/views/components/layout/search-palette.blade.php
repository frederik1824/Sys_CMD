<div class="relative w-80 group">
    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg group-hover:text-primary transition-colors">search</span>
    <input id="navbar-search" class="w-full bg-slate-100 border-none rounded-2xl pl-10 pr-16 py-2.5 text-[0.875rem] font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 outline-none transition-all placeholder:font-medium shadow-inner" placeholder="Buscar..." type="text" autocomplete="off"/>
    <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1 px-1.5 py-1 bg-white rounded-lg border border-slate-200 shadow-sm pointer-events-none opacity-60 group-hover:opacity-100 transition-opacity">
        <span class="text-[0.6rem] font-black text-slate-400">CTRL</span>
        <span class="text-[0.6rem] font-black text-slate-400">K</span>
    </div>
    
    <!-- Search Results Dropdown -->
    <div id="search-results" class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden hidden animate-in fade-in slide-in-from-top-2 duration-200 z-50">
        <div class="p-3 border-b border-slate-50 bg-slate-50/50">
            <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">Panel de Comandos</span>
        </div>
        <div id="results-container" class="max-h-[400px] overflow-y-auto custom-scrollbar divide-y divide-slate-50">
            <!-- JS Will fill this -->
        </div>
        <div id="search-empty" class="p-8 text-center hidden">
            <span class="material-symbols-outlined text-slate-200 text-4xl mb-2">person_search</span>
            <p class="text-xs font-bold text-slate-400">No se encontraron coincidencias.</p>
        </div>
    </div>
</div>
