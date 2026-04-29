@extends('layouts.app')

@section('content')
<div class="space-y-10 pb-12 max-w-4xl mx-auto font-body">
    <div class="flex items-center justify-between">
        <a href="{{ route('rutas.index') }}" class="group inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-100 rounded-xl text-slate-500 hover:text-primary transition-all text-sm font-bold">
            <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Listado de Rutas
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl overflow-hidden relative group">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors duration-700"></div>
        
        <div class="p-10 relative z-10">
            <h1 class="text-3xl font-black text-slate-900 font-headline flex items-center gap-3">
                <span class="w-12 h-12 bg-primary text-white rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined">{{ $ruta->id ? 'location_on' : 'add_location_alt' }}</span>
                </span>
                {{ $ruta->id ? 'Editar Ruta' : 'Agregar Nueva Ruta' }}
            </h1>
            <p class="mt-2 text-slate-500 font-medium ml-16">Estructura geográfica para la optimización de despachos.</p>

            <form action="{{ $ruta->id ? route('rutas.update', $ruta) : route('rutas.store') }}" 
                  method="POST" class="mt-12 space-y-10">
                @csrf
                @if($ruta->id) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                    {{-- Nombre --}}
                    <div class="space-y-4">
                        <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-primary">map</span>
                            Nombre de la Ruta
                        </label>
                        <input type="text" name="nombre" value="{{ old('nombre', $ruta->nombre) }}" required
                               class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                               placeholder="Ej. Ruta Norte - Santo Domingo">
                        @error('nombre') <p class="text-rose-500 text-xs font-bold pl-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Zona --}}
                    <div class="space-y-4">
                        <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-primary">explore</span>
                            Zona Geográfica
                        </label>
                        <select name="zona" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary/5 transition-all outline-none">
                            <option value="General" {{ old('zona', $ruta->zona) == 'General' ? 'selected' : '' }}>General</option>
                            <option value="Norte" {{ old('zona', $ruta->zona) == 'Norte' ? 'selected' : '' }}>Norte</option>
                            <option value="Sur" {{ old('zona', $ruta->zona) == 'Sur' ? 'selected' : '' }}>Sur</option>
                            <option value="Este" {{ old('zona', $ruta->zona) == 'Este' ? 'selected' : '' }}>Este</option>
                            <option value="Oeste" {{ old('zona', $ruta->zona) == 'Oeste' ? 'selected' : '' }}>Oeste</option>
                            <option value="Metropolitana" {{ old('zona', $ruta->zona) == 'Metropolitana' ? 'selected' : '' }}>Metropolitana</option>
                        </select>
                        @error('zona') <p class="text-rose-500 text-xs font-bold pl-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Descripción --}}
                    <div class="col-span-full space-y-4">
                        <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-primary">description</span>
                            Descripción y Alcance
                        </label>
                        <textarea name="descripcion" rows="3" 
                                  class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-medium text-slate-700 focus:ring-4 focus:ring-primary/5 transition-all outline-none resize-none"
                                  placeholder="Detalla los sectores o provincias que abarca esta ruta...">{{ old('descripcion', $ruta->descripcion) }}</textarea>
                    </div>

                    <div class="col-span-full flex items-center gap-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                        <div class="flex items-center gap-4 cursor-pointer group/stat">
                            <div class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="es_frecuente" value="0">
                                <input type="checkbox" name="es_frecuente" value="1" class="sr-only peer" {{ old('es_frecuente', $ruta->es_frecuente) ? 'checked' : '' }}>
                                <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-amber-500"></div>
                            </div>
                            <div>
                                <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none">Ruta Frecuente</p>
                                <p class="text-xs font-bold text-slate-700 mt-1">Marcar si esta ruta se realiza de forma periódica.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit" class="inline-flex items-center gap-3 px-10 py-5 bg-primary text-white rounded-[1.5rem] font-black text-sm uppercase tracking-widest hover:shadow-2xl hover:shadow-primary/30 transition-all active:scale-95 group">
                        <span class="material-symbols-outlined text-white group-hover:rotate-12 transition-transform">save</span>
                        {{ $ruta->id ? 'Guardar Cambios' : 'Registrar Ruta' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
