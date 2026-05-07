@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto flex flex-col gap-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('sistema.responsables.index') }}" class="text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-3xl">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold text-on-surface">{{ isset($responsable) ? 'Editar Responsable' : 'Nuevo Responsable' }}</h2>
            <p class="text-on-surface-variant text-[0.875rem] mt-1">Complete los detalles de la empresa/entidad responsable.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">
        <form action="{{ isset($responsable) ? route('sistema.responsables.update', $responsable) : route('sistema.responsables.store') }}" method="POST" class="space-y-6">
            @csrf
            @if(isset($responsable))
                @method('PUT')
            @endif

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre Comercial <span class="text-red-500">*</span></label>
                <input type="text" name="nombre" value="{{ old('nombre', $responsable->nombre ?? '') }}" required class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">{{ old('descripcion', $responsable->descripcion ?? '') }}</textarea>
                @error('descripcion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Usuario Asociado (Opcional)</label>
                <select name="user_id" class="w-full bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Ninguno</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', $responsable->user_id ?? '') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[0.65rem] text-slate-400 mt-1 font-medium italic">Permite vincular este responsable a un usuario del sistema para trazabilidad.</p>
                @error('user_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1">Costo de Entrega Base ($)</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <span class="text-slate-400 group-focus-within:text-primary transition-colors text-sm font-black">$</span>
                    </div>
                    <input type="number" step="0.01" name="precio_entrega" value="{{ old('precio_entrega', $responsable->precio_entrega ?? '') }}" placeholder="0.00" class="w-full pl-8 pr-4 py-3 bg-surface-container-lowest border border-slate-200 rounded-lg focus:ring-2 focus:ring-primary text-sm shadow-inner transition-all">
                </div>
                <p class="text-[0.65rem] text-slate-400 mt-1 font-medium italic">Este costo se aplicará a los carnets completados por este responsable si no hay un proveedor asignado.</p>
                @error('precio_entrega') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="p-5 bg-blue-50/50 rounded-xl border border-blue-100">
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    <span class="material-symbols-outlined text-blue-500 text-sm align-middle mr-1">location_on</span>
                    Provincias Delegadas (Opcional)
                </label>
                <p class="text-[0.7rem] text-slate-500 mb-3">Las importaciones y sincronizaciones de estas provincias se asignarán automáticamente a este responsable.</p>
                
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-2 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    @php
                        $selectedProvincias = old('provincias', isset($responsable) ? $responsable->provincias->pluck('id')->toArray() : []);
                    @endphp
                    @foreach($provincias as $prov)
                        <label class="flex items-center gap-2 p-2 bg-white border border-slate-200 rounded-lg cursor-pointer hover:border-primary transition-colors">
                            <input type="checkbox" name="provincias[]" value="{{ $prov->id }}" 
                                {{ in_array($prov->id, $selectedProvincias) ? 'checked' : '' }} 
                                class="rounded text-primary focus:ring-primary w-4 h-4 border-slate-300">
                            <span class="text-[0.8rem] text-slate-600 font-medium truncate" title="{{ $prov->nombre }}">
                                {{ $prov->nombre }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('provincias') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', $responsable->activo ?? true) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary w-5 h-5 border-slate-300">
                <label for="activo" class="text-sm font-semibold text-slate-700">Responsable Activo</label>
            </div>

            <div class="pt-6 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('sistema.responsables.index') }}" class="px-6 py-2.5 rounded-lg font-semibold text-slate-600 hover:bg-slate-50 transition-colors border border-transparent">Cancelar</a>
                <button type="submit" class="bg-primary text-white px-6 py-2.5 rounded-lg font-semibold shadow-lg shadow-primary/20 hover:bg-blue-800 transition-colors">Guardar Responsable</button>
            </div>
        </form>
    </div>
</div>
@endsection
