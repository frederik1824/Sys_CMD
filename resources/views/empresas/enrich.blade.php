@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Saneamiento de Datos Maestros
    </h2>
@endsection

@section('content')
<div class="py-12 px-4 max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100">
        <div class="p-10 text-center bg-slate-50/50 border-b border-slate-100">
            <div class="w-20 h-20 bg-primary/10 text-primary rounded-3xl flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl">auto_fix_high</span>
            </div>
            <h3 class="text-2xl font-black text-slate-800 uppercase tracking-tighter">Enriquecimiento de Empresas</h3>
            <p class="text-slate-500 max-w-md mx-auto mt-2">
                Detectamos que tienes <span class="font-black text-rose-500">{{ $incomplete }}</span> empresas sin ubicación definida (Provincia/Municipio).
            </p>
        </div>

        <div class="p-10 space-y-8">
            <div class="flex gap-6 items-start bg-emerald-50 p-6 rounded-2xl border border-emerald-100">
                <span class="material-symbols-outlined text-emerald-600 text-3xl">info</span>
                <div>
                    <h4 class="font-black text-emerald-800 uppercase text-xs tracking-widest mb-1">¿Cómo funciona?</h4>
                    <p class="text-sm text-emerald-700/80 leading-relaxed">
                        El sistema analizará los carnets (afiliados) que pertenecen a estas empresas. Si un afiliado ya tiene grabada una provincia y municipio, el sistema los copiará automáticamente a la ficha de la empresa principal.
                    </p>
                </div>
            </div>

            <div class="space-y-4">
                <h5 class="text-[0.65rem] font-black uppercase text-slate-400 tracking-widest">Estado Actual</h5>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50 p-4 rounded-2xl">
                        <span class="text-xl font-black text-slate-800">{{ $incomplete }}</span>
                        <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Sin Ubicación</p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-2xl">
                        <span class="text-xl font-black text-emerald-600">{{ $enriched }}</span>
                        <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Saneadas</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('sistema.empresas.processEnrich') }}" method="POST" class="pt-6">
                @csrf
                <button type="submit" class="w-full py-5 bg-primary text-white font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-3">
                    <span class="material-symbols-outlined">magic_button</span>
                    Iniciar Enriquecimiento Automático
                </button>
            </form>
            
            <div class="text-center">
                <a href="{{ route('sistema.empresas.index') }}" class="text-xs font-black uppercase text-slate-400 hover:text-slate-600 transition-colors">
                    Volver al listado de empresas
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
