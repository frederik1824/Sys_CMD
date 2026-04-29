@extends('layouts.app')

@section('content')
<div class="space-y-10 pb-12 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <a href="{{ route('mensajeros.index') }}" class="group inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-100 rounded-xl text-slate-500 hover:text-primary transition-all text-sm font-bold">
            <span class="material-symbols-outlined text-lg group-hover:-translate-x-1 transition-transform">arrow_back</span>
            Listado de Mensajeros
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl overflow-hidden relative group">
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-colors duration-700"></div>
        
        <div class="p-10 relative z-10">
            <h1 class="text-3xl font-black text-slate-900 tracking-tight font-headline flex items-center gap-3">
                <span class="w-12 h-12 bg-primary text-white rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined">{{ $mensajero->id ? 'edit' : 'person_add' }}</span>
                </span>
                {{ $mensajero->id ? 'Editar Mensajero' : 'Registrar Nuevo Mensajero' }}
            </h1>
            <p class="mt-2 text-slate-500 font-medium ml-16">Información personal y de transporte para logística.</p>

            <form action="{{ $mensajero->id ? route('mensajeros.update', $mensajero) : route('mensajeros.store') }}" 
                  method="POST" class="mt-12 space-y-10">
                @csrf
                @if($mensajero->id) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                    {{-- Nombre --}}
                    <div class="space-y-4">
                        <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-primary">person</span>
                            Nombre Completo
                        </label>
                        <input type="text" name="nombre" value="{{ old('nombre', $mensajero->nombre) }}" required
                               class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                               placeholder="Ej. Juan Pérez">
                        @error('nombre') <p class="text-rose-500 text-xs font-bold pl-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Cédula --}}
                    <div class="space-y-4">
                        <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-primary">badge</span>
                            Cédula (ID)
                        </label>
                        <input type="text" name="cedula" value="{{ old('cedula', $mensajero->cedula) }}" required
                               class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                               placeholder="000-0000000-0">
                        @error('cedula') <p class="text-rose-500 text-xs font-bold pl-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Teléfono --}}
                    <div class="space-y-4">
                        <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-primary">phone</span>
                            Número Telefónico
                        </label>
                        <input type="text" name="telefono" value="{{ old('telefono', $mensajero->telefono) }}"
                               class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                               placeholder="Ej. 809-555-0000">
                        @error('telefono') <p class="text-rose-500 text-xs font-bold pl-2">{{ $message }}</p> @enderror
                    </div>

                    {{-- Color --}}
                    <div class="space-y-4">
                        <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm text-primary">palette</span>
                            Color Distintivo
                        </label>
                        <input type="color" name="color" value="{{ old('color', $mensajero->color ?? '#3b82f6') }}"
                               class="w-full h-14 bg-slate-50 border-none rounded-2xl px-2 py-2 outline-none cursor-pointer">
                        @error('color') <p class="text-rose-500 text-xs font-bold pl-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-full border-t border-slate-100 flex flex-col pt-4">
                        <h4 class="text-[0.65rem] font-black text-primary uppercase tracking-[0.3em] mb-8">Información del Transporte</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                             {{-- Tipo Vehículo --}}
                            <div class="space-y-4">
                                <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">commute</span>
                                    Tipo de Vehículo
                                </label>
                                <select name="vehiculo_tipo" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary/5 transition-all outline-none">
                                    <option value="Motor" {{ old('vehiculo_tipo', $mensajero->vehiculo_tipo) == 'Motor' ? 'selected' : '' }}>Motor</option>
                                    <option value="Carro" {{ old('vehiculo_tipo', $mensajero->vehiculo_tipo) == 'Carro' ? 'selected' : '' }}>Carro</option>
                                    <option value="Camioneta" {{ old('vehiculo_tipo', $mensajero->vehiculo_tipo) == 'Camioneta' ? 'selected' : '' }}>Camioneta</option>
                                </select>
                                @error('vehiculo_tipo') <p class="text-rose-500 text-xs font-bold pl-2">{{ $message }}</p> @enderror
                            </div>

                            {{-- Placa --}}
                            <div class="space-y-4">
                                <label class="px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-[0.2em] flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">list_alt</span>
                                    Placa / Matricula
                                </label>
                                <input type="text" name="vehiculo_placa" value="{{ old('vehiculo_placa', $mensajero->vehiculo_placa) }}"
                                       class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-primary/5 transition-all outline-none uppercase"
                                       placeholder="Ej. K012345">
                                @error('vehiculo_placa') <p class="text-rose-500 text-xs font-bold pl-2">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <div class="flex items-center gap-4 cursor-pointer group/stat">
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" value="1" class="sr-only peer" {{ old('activo', $mensajero->activo ?? true) ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                        </div>
                        <div>
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none">Estado de Servicio</p>
                            <p class="text-xs font-bold text-slate-700 mt-1">El mensajero está disponible para rutas.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit" class="inline-flex items-center gap-3 px-10 py-5 bg-primary text-white rounded-[1.5rem] font-black text-sm uppercase tracking-widest hover:shadow-2xl hover:shadow-primary/30 transition-all active:scale-95 group">
                        <span class="material-symbols-outlined text-white group-hover:rotate-12 transition-transform">save</span>
                        {{ $mensajero->id ? 'Guardar Cambios' : 'Confirmar Registro' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
