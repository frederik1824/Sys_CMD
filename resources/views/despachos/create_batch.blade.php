@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ 
    selectedItems: new Set(),
    searchTerm: '',
    filterProvince: '',
    isLoading: false,
    
    toggleItem(id) {
        if (this.selectedItems.has(id)) {
            this.selectedItems.delete(id);
        } else {
            this.selectedItems.add(id);
        }
    },
    
    async fetchResults(url = '{{ route('despachos.create_batch') }}') {
        this.isLoading = true;
        const params = new URLSearchParams();
        if (this.searchTerm) params.append('searchTerm', this.searchTerm);
        if (this.filterProvince) params.append('filterProvince', this.filterProvince);
        
        // Mantener la página si la URL tiene una
        const finalUrl = new URL(url);
        if (params.toString()) {
            params.forEach((v, k) => finalUrl.searchParams.set(k, v));
        }

        try {
            const response = await fetch(finalUrl, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const html = await response.text();
            document.getElementById('table-wrapper').innerHTML = html;
            this.bindPagination();
        } catch (e) {
            console.error('Error fetching:', e);
        } finally {
            this.isLoading = false;
        }
    },

    bindPagination() {
        const links = document.querySelectorAll('#selection-pagination a');
        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                this.fetchResults(link.href);
            });
        });
    },

    init() {
        this.$watch('searchTerm', () => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.fetchResults(), 400);
        });
        this.$watch('filterProvince', () => this.fetchResults());
        this.bindPagination();
    }
}">
    {{-- Header --}}
    <div class="flex items-center justify-between bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="relative z-10">
            <h1 class="text-3xl font-black text-slate-900 font-headline tracking-tight">Centro de Despacho</h1>
            <p class="text-slate-500 font-medium mt-1">Selecciona los carnets listos y asígnalos a una ruta de entrega.</p>
        </div>
        <div class="relative z-10 flex gap-4">
             <div class="bg-primary/5 px-6 py-3 rounded-2xl border border-primary/10 text-center">
                 <p class="text-[0.6rem] font-black uppercase text-primary tracking-widest leading-none">Total Disponibles</p>
                 <p class="text-2xl font-black text-primary mt-1">{{ $afiliados->total() }}</p>
             </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Fleet & Selection (Left) --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Integrated Filters --}}
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex flex-wrap gap-4 items-center">
                <div class="flex-1 relative group">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors">search</span>
                    <input type="text" x-model="searchTerm" placeholder="Buscar por nombre, cédula o empresa..." 
                           class="w-full pl-12 pr-6 py-3 bg-slate-50 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary/5 outline-none transition-all">
                </div>
                
                <select x-model="filterProvince" class="bg-slate-50 border-none rounded-xl px-6 py-3 text-sm font-bold text-slate-600 focus:ring-4 focus:ring-primary/5 h-[46px]">
                    <option value="">Todas las Provincias</option>
                    @foreach($provincias as $prov)
                        <option value="{{ $prov }}">{{ $prov }}</option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button @click="selectedItems.clear()" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all">Limpiar Selección</button>
                    <div x-show="isLoading" class="flex items-center ml-2">
                        <div class="w-5 h-5 border-2 border-primary border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>
            </div>

            {{-- Affiliates Grid/List --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden" id="table-wrapper">
                @include('despachos.partials.selection_table')
            </div>
        </div>

        {{-- Dispatch Panel (Right) --}}
        <div class="lg:col-span-4">
            <div class="sticky top-24 bg-slate-900 rounded-[2.5rem] p-8 shadow-2xl text-white overflow-hidden group">
                {{-- Decoration --}}
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-colors"></div>
                
                <h2 class="text-xl font-black font-headline mb-8 flex items-center gap-3">
                    <span class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-primary-container">
                        <span class="material-symbols-outlined">rocket_launch</span>
                    </span>
                    Iniciar Despacho
                </h2>

                <form action="{{ route('despachos.process_batch') }}" method="POST" class="space-y-8 relative z-10">
                    @csrf
                    
                    {{-- Hidden selected IDs --}}
                    <template x-for="id in Array.from(selectedItems)">
                        <input type="hidden" name="afiliado_ids[]" :value="id">
                    </template>

                    {{-- Counter --}}
                    <div class="p-6 bg-white/5 rounded-3xl border border-white/10 text-center">
                        <p class="text-[0.6rem] font-black uppercase text-white/40 tracking-[0.2em]">Carnets Seleccionados</p>
                        <div class="text-6xl font-black mt-2 tracking-tighter" x-text="selectedItems.size">0</div>
                    </div>

                    {{-- Assignments --}}
                    <div class="space-y-6">
                        <div class="space-y-3">
                            <label class="text-[0.6rem] font-black uppercase text-white/30 tracking-widest pl-2">Asignar Mensajero</label>
                            <select name="mensajero_id" required
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-4 focus:ring-primary/20 outline-none transition-all appearance-none cursor-pointer">
                                <option value="" class="text-slate-900">Seleccionar mensajero...</option>
                                @foreach($mensajeros as $m)
                                    <option value="{{ $m->id }}" class="text-slate-900">{{ $m->nombre }} ({{ $m->vehiculo_tipo }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label class="text-[0.6rem] font-black uppercase text-white/30 tracking-widest pl-2">Ruta Sugerida</label>
                            <select name="ruta_id" 
                                    class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-4 focus:ring-primary/20 outline-none transition-all appearance-none cursor-pointer">
                                <option value="" class="text-slate-900">Ruta libre (sin asignar zona)</option>
                                @foreach($rutas as $r)
                                    <option value="{{ $r->id }}" class="text-slate-900">{{ $r->nombre }} - {{ $r->zona }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3">
                             <label class="text-[0.6rem] font-black uppercase text-white/30 tracking-widest pl-2">Notas de Salida</label>
                             <textarea name="observaciones" rows="2" 
                                       class="w-full bg-white/5 border border-white/10 rounded-2xl px-6 py-4 text-sm font-medium text-white focus:ring-4 focus:ring-primary/20 outline-none transition-all resize-none placeholder:text-white/20"
                                       placeholder="Instrucciones adicionales..."></textarea>
                        </div>
                    </div>

                    <button type="submit" 
                            :disabled="selectedItems.size === 0"
                            class="w-full py-5 bg-primary text-white rounded-[1.8rem] font-black text-xs uppercase tracking-[0.2em] shadow-2xl shadow-primary/40 hover:scale-[1.02] active:scale-95 transition-all disabled:opacity-30 disabled:grayscale disabled:pointer-events-none">
                        Confirmar Salida a Ruta
                    </button>
                </form>

                <p class="mt-8 text-[0.6rem] text-white/20 text-center italic font-medium leading-relaxed">
                    Al confirmar, los carnets pasarán a estatus <span class="text-white/50">"En Ruta"</span> y se generará una auditoría de salida.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
