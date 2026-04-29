@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto flex flex-col gap-6 mt-8">
    <div>
        <h2 class="text-2xl font-bold text-on-surface">Editar Proveedor</h2>
        <p class="text-on-surface-variant text-sm mt-1">Actualice la información de {{ $proveedor->nombre }}.</p>
    </div>

    <form action="{{ route('proveedores.update', $proveedor) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 flex flex-col gap-6">
        @csrf
        @method('PUT')
        
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Nombre del Proveedor</label>
            <input type="text" name="nombre" required value="{{ $proveedor->nombre }}" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 font-bold text-slate-700 focus:ring-2 focus:ring-primary/20">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Precio Base (RD$)</label>
            <input type="number" step="0.01" name="precio_base" required value="{{ $proveedor->precio_base }}" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 font-bold text-slate-700 focus:ring-2 focus:ring-primary/20">
        </div>

        <div class="flex items-center gap-3 mt-2">
            <input type="checkbox" name="activo" id="activo" value="1" {{ $proveedor->activo ? 'checked' : '' }} class="w-5 h-5 text-primary bg-slate-100 border-none rounded focus:ring-primary">
            <label for="activo" class="text-sm font-bold text-slate-700">Proveedor Activo</label>
        </div>

        <div class="pt-4 flex gap-4 border-t border-slate-100">
            <a href="{{ route('proveedores.index') }}" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancelar</a>
            <button type="submit" class="px-6 py-3 bg-primary text-white rounded-xl font-bold hover:bg-blue-800 transition-colors shadow-lg shadow-primary/20">Actualizar Proveedor</button>
        </div>
    </form>
</div>
@endsection
