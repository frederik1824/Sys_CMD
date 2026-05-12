@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-12" x-data="{ 
    search: '', 
    activeCategory: 'ALL',
    categories: ['ALL', 'ADMIN', 'AFILIACION', 'OPERACIONES', 'FINANZAS', 'CALLCENTER']
}">
    <form action="{{ route('admin.access.roles.store') }}" method="POST">
        @csrf
        
        <!-- Header & Main Controls -->
        <div class="mb-12 flex flex-col lg:flex-row lg:items-end justify-between gap-8 sticky top-0 z-50 bg-slate-50/90 backdrop-blur-xl py-6 -mt-6">
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-6">
                    <a href="{{ route('admin.access.roles') }}" class="w-12 h-12 bg-white text-slate-400 rounded-2xl flex items-center justify-center hover:bg-slate-100 transition-all shadow-sm border border-slate-100">
                        <i class="ph ph-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-black text-slate-900 tracking-tighter">Crear Nuevo Perfil</h1>
                        <p class="text-sm font-medium text-slate-400 italic">Define capacidades mediante acciones directas y módulos</p>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-4 items-center">
                    <div class="w-full md:w-80">
                        <input type="text" name="name" required
                               class="w-full px-6 py-4 bg-white border-none rounded-2xl text-lg font-black shadow-xl shadow-slate-200/40 focus:ring-4 focus:ring-amber-500/10 transition-all"
                               placeholder="Ej. Operador de Call Center">
                    </div>
                    
                    <div class="flex-1 min-w-[300px]">
                        <div class="relative">
                            <i class="ph ph-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                            <input type="text" x-model="search"
                                   class="w-full px-14 py-4 bg-white border-none rounded-2xl text-sm font-bold shadow-xl shadow-slate-200/40 focus:ring-4 focus:ring-amber-500/10 transition-all"
                                   placeholder="Filtrar por módulo o acción...">
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="px-10 py-5 bg-slate-900 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-amber-600 hover:shadow-2xl hover:shadow-amber-600/40 transition-all active:scale-95 shadow-xl shadow-slate-900/20">
                Crear Perfil Maestro
            </button>
        </div>

        <!-- Category Tabs -->
        <div class="flex flex-wrap gap-2 mb-10 overflow-x-auto pb-4 no-scrollbar">
            <template x-for="cat in categories" :key="cat">
                <button type="button" 
                        @click="activeCategory = cat"
                        :class="activeCategory === cat ? 'bg-amber-500 text-white shadow-lg shadow-amber-500/30' : 'bg-white text-slate-400 hover:bg-slate-50'"
                        class="px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all whitespace-nowrap border border-transparent"
                        x-text="cat === 'ALL' ? 'Todas las Áreas' : cat">
                </button>
            </template>
        </div>

        <!-- Matrix of Grouped Permissions -->
        @php
            $groupedByModule = collect($permissions)->flatten()->groupBy(function($p) use ($permissionMap) {
                return $permissionMap[$p->name]['module'] ?? 'Otros Módulos';
            });
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($groupedByModule as $moduleName => $items)
            @php 
                $firstPermission = $items->first();
                $category = $permissionMap[$firstPermission->name]['category'] ?? 'OTROS';
            @endphp
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/30 overflow-hidden flex flex-col h-full transition-all hover:shadow-2xl"
                 x-show="(activeCategory === 'ALL' || activeCategory === '{{ $category }}') && (search === '' || '{{ strtolower($moduleName) }}'.includes(search.toLowerCase()) || {{ json_encode($items->pluck('name')->map(fn($n) => strtolower($n))) }}.some(n => n.includes(search.toLowerCase())))">
                
                <!-- Module Header -->
                <div class="px-8 py-6 bg-slate-50/50 border-b border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 @if($category == 'ADMIN') bg-slate-900 @elseif($category == 'AFILIACION') bg-indigo-600 @elseif($category == 'FINANZAS') bg-emerald-600 @else bg-amber-500 @endif rounded-2xl flex items-center justify-center text-white shadow-lg">
                            <i class="ph ph-@if($category == 'ADMIN') shield-star @elseif($category == 'AFILIACION') users-three @elseif($category == 'FINANZAS') credit-card @else package @endif text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-900">{{ $moduleName }}</h3>
                            <span class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em]">{{ $category }}</span>
                        </div>
                    </div>
                    
                    <button type="button" 
                            @click="$el.closest('.bg-white').querySelectorAll('input[type=checkbox]').forEach(el => el.checked = !el.checked)"
                            class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-400 hover:text-amber-500 transition-all">
                        <i class="ph ph-arrows-counter-clockwise font-bold"></i>
                    </button>
                </div>
                
                <!-- Semantic Actions List -->
                <div class="p-8 space-y-3 flex-1">
                    @foreach($items as $permission)
                    @php $mapped = $permissionMap[$permission->name] ?? null; @endphp
                    <label class="flex items-center justify-between p-4 bg-slate-50/50 rounded-2xl border-2 border-transparent hover:border-amber-100 hover:bg-amber-50/30 transition-all cursor-pointer group">
                        <div class="flex-1">
                            <p class="text-[11px] font-black text-slate-700 leading-tight group-hover:text-amber-700 transition-colors">
                                {{ $mapped ? $mapped['verb'] : strtoupper($permission->name) }}
                            </p>
                            @if($mapped)
                            <p class="text-[9px] font-medium text-slate-400 mt-0.5 leading-tight">{{ $mapped['desc'] }}</p>
                            @endif
                        </div>

                        <div class="ml-4">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                   class="w-6 h-6 rounded-lg border-2 border-slate-200 text-amber-500 focus:ring-amber-500/20 transition-all">
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </form>
</div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
