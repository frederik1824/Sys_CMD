@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl hover:shadow-primary/5">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight font-headline">Rutas Logísticas</h1>
            <p class="text-slate-500 font-medium mt-1">Planifica y organiza las áreas de distribución.</p>
        </div>
        <a href="{{ route('rutas.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-bold hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
            <span class="material-symbols-outlined">add_location_alt</span>
            Nueva Ruta
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rutas as $ruta)
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden group hover:border-primary/20 transition-all relative">
                <div class="p-8">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-14 h-14 rounded-2xl bg-primary/5 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-500">
                            <span class="material-symbols-outlined text-2xl">map</span>
                        </div>
                        <div class="flex gap-1">
                             <a href="{{ route('rutas.edit', $ruta) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-colors">
                                <span class="material-symbols-outlined text-[20px]">edit</span>
                            </a>
                            <form action="{{ route('rutas.destroy', $ruta) }}" method="POST" onsubmit="return confirm('¿Eliminar ruta?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <h3 class="text-xl font-black text-slate-900 truncate mb-1">{{ $ruta->nombre }}</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-[0.6rem] font-black uppercase text-primary tracking-widest bg-primary/5 px-2 py-0.5 rounded border border-primary/10">ZONA {{ $ruta->zona }}</span>
                        @if($ruta->es_frecuente)
                             <span class="text-[0.6rem] font-black uppercase text-amber-600 tracking-widest bg-amber-50 px-2 py-0.5 rounded border border-amber-500/10">Recurrente</span>
                        @endif
                    </div>

                    <p class="text-sm text-slate-500 font-medium line-clamp-2 min-h-[2.5rem] leading-relaxed italic">
                        {{ $ruta->descripcion ?: 'Sin descripción detallada para esta ruta.' }}
                    </p>
                </div>
                <div class="px-8 py-5 bg-slate-50 border-t border-slate-100 flex justify-between items-center group-hover:bg-primary/5 transition-colors">
                    <div class="flex items-center gap-4">
                         <div class="flex -space-x-2">
                             {{-- Placeholder para futuros indicadores de carga --}}
                             <div class="w-7 h-7 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[0.6rem] font-black text-slate-500 italic">...</div>
                         </div>
                    </div>
                </div>
            </div>
        @empty
             <div class="col-span-full py-20 text-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100">
                <span class="material-symbols-outlined text-6xl text-slate-100 mb-4">map</span>
                <p class="text-slate-400 font-medium">No hay rutas configuradas.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $rutas->links() }}
    </div>
</div>
@endsection
