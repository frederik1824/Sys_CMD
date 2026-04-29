@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto flex flex-col gap-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('estados.index') }}" class="text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-3xl">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold text-on-surface">{{ isset($estado) ? 'Editar Estado' : 'Nuevo Estado' }}</h2>
            <p class="text-on-surface-variant text-[0.875rem] mt-1">Configura las etiquetas de estado para los afiliados.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">
        <form action="{{ isset($estado) ? route('estados.update', $estado) : route('estados.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($estado))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del Estado <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $estado->nombre ?? '') }}" required class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">{{ old('descripcion', $estado->descripcion ?? '') }}</textarea>
                @error('descripcion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('estados.index') }}" class="px-6 py-2.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-50 transition-colors border border-transparent">Cancelar</a>
                <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg font-semibold shadow-lg shadow-primary/20 hover:bg-blue-800 transition-colors">Guardar Estado</button>
            </div>
        </form>
    </div>
</div>
@endsection
