@extends('layouts.app')

@section('content')
<div class="p-8 page-transition">
    <!-- Header -->
    <div class="flex items-center gap-3 mb-10">
        <a href="{{ route('pyp.afiliados.index') }}" class="w-10 h-10 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all">
            <i class="ph ph-caret-left font-bold"></i>
        </a>
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Programa PyP</p>
            <h1 class="text-3xl font-black text-slate-900">Matriculación de Nuevo Afiliado</h1>
        </div>
    </div>

    <div class="max-w-4xl">
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl overflow-hidden">
            <div class="bg-slate-900 p-10 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-12 opacity-10">
                    <i class="ph-fill ph-user-plus text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <h3 class="text-2xl font-black mb-2">Datos de Identificación</h3>
                    <p class="text-slate-400 text-sm font-medium">Ingrese la información básica para abrir el expediente clínico preventivo.</p>
                </div>
            </div>

            <form action="{{ route('pyp.afiliados.store') }}" method="POST" class="p-12 space-y-8">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Cédula -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-widest text-slate-400 ml-4">Número de Cédula</label>
                        <div class="relative">
                            <i class="ph ph-identification-card absolute left-6 top-1/2 -translate-y-1/2 text-xl text-slate-300"></i>
                            <input type="text" name="cedula" required class="w-full pl-14 pr-6 py-5 bg-slate-50 border-none rounded-3xl font-black text-sm text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="000-0000000-0">
                        </div>
                        @error('cedula') <p class="text-[10px] font-bold text-rose-500 ml-4 italic">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nombre Completo -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-widest text-slate-400 ml-4">Nombre Completo</label>
                        <div class="relative">
                            <i class="ph ph-user absolute left-6 top-1/2 -translate-y-1/2 text-xl text-slate-300"></i>
                            <input type="text" name="nombre_completo" required class="w-full pl-14 pr-6 py-5 bg-slate-50 border-none rounded-3xl font-black text-sm text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="Ej. Juan Pérez">
                        </div>
                    </div>

                    <!-- Sexo -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-widest text-slate-400 ml-4">Sexo Biológico</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="sexo" value="M" required class="peer sr-only">
                                <div class="py-4 text-center bg-slate-50 border-2 border-transparent rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-400 peer-checked:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:text-indigo-600 transition-all">
                                    Masculino
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="sexo" value="F" required class="peer sr-only">
                                <div class="py-4 text-center bg-slate-50 border-2 border-transparent rounded-2xl font-black text-[11px] uppercase tracking-widest text-slate-400 peer-checked:bg-pink-50 peer-checked:border-pink-500 peer-checked:text-pink-600 transition-all">
                                    Femenino
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase tracking-widest text-slate-400 ml-4">Teléfono de Contacto</label>
                        <div class="relative">
                            <i class="ph ph-phone absolute left-6 top-1/2 -translate-y-1/2 text-xl text-slate-300"></i>
                            <input type="text" name="telefono" class="w-full pl-14 pr-6 py-5 bg-slate-50 border-none rounded-3xl font-black text-sm text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="809-000-0000">
                        </div>
                    </div>
                </div>

                <!-- Dirección -->
                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase tracking-widest text-slate-400 ml-4">Dirección Domiciliaria</label>
                    <div class="relative">
                        <i class="ph ph-map-pin absolute left-6 top-8 text-xl text-slate-300"></i>
                        <textarea name="direccion" rows="3" class="w-full pl-14 pr-6 py-6 bg-slate-50 border-none rounded-[2rem] font-medium text-xs text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none" placeholder="Calle, Número, Sector, Ciudad..."></textarea>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-6 bg-slate-900 text-white rounded-[2rem] text-[12px] font-black uppercase tracking-[0.2em] hover:bg-indigo-600 transition-all shadow-2xl shadow-slate-200 flex items-center justify-center gap-3">
                        <i class="ph ph-user-plus text-xl"></i> Finalizar Matriculación e Iniciar Expediente
                    </button>
                    <p class="text-center mt-6 text-[10px] font-bold text-slate-400 italic">Al finalizar, será redirigido automáticamente a la ficha clínica del paciente.</p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
