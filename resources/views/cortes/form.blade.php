@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto flex flex-col gap-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('cortes.index') }}" class="text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-3xl">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold text-on-surface">{{ isset($corte) ? 'Editar Corte' : 'Nuevo Corte' }}</h2>
            <p class="text-on-surface-variant text-[0.875rem] mt-1">Complete los detalles del periodo de corte.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">
        <form action="{{ isset($corte) ? route('cortes.update', $corte) : route('cortes.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($corte))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del Corte <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $corte->nombre ?? '') }}" required class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha de Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $corte->fecha_inicio ?? '') }}" class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    @error('fecha_inicio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Fecha de Fin</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $corte->fecha_fin ?? '') }}" class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    @error('fecha_fin') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', $corte->activo ?? true) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300">
                <label for="activo" class="text-sm font-semibold text-slate-700">Corte Activo</label>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('cortes.index') }}" class="px-6 py-2.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-50 transition-colors border border-transparent">Cancelar</a>
                <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg font-semibold shadow-lg shadow-primary/20 hover:bg-blue-800 transition-colors">Guardar Corte</button>
            </div>
        </form>
    </div>
</div>
@endsection
