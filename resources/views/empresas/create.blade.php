@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-fade-in pb-20">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <a href="{{ route('sistema.empresas.index') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all shadow-sm">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <nav class="flex items-center gap-2 mb-1 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                    <a href="{{ route('sistema.empresas.index') }}" class="hover:text-primary transition-colors">Empresas</a>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="text-primary">Registro</span>
                </nav>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Nueva Entidad Corporativa</h2>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
        {{-- Side Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-2xl">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl opacity-50"></div>
                <h4 class="text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    Guía de Registro
                </h4>
                <div class="space-y-6 relative z-10">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-primary text-xs font-black">01</div>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed">Complete la identidad legal y el RNC de la empresa.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-primary text-xs font-black">02</div>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed">Defina el referente ejecutivo principal de contacto.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-lg bg-white/5 flex items-center justify-center text-primary text-xs font-black">03</div>
                        <p class="text-xs text-slate-300 font-medium leading-relaxed">Establezca el esquema de comisiones y responsables.</p>
                    </div>
                </div>
            </div>

            <div class="bg-primary/5 rounded-[2rem] p-6 border border-primary/10">
                <p class="text-[0.6rem] font-bold text-primary uppercase tracking-widest mb-2">Nota Importante</p>
                <p class="text-xs text-slate-500 italic">Los campos marcados con (*) son obligatorios para la trazabilidad del sistema.</p>
            </div>
        </div>

        {{-- Form Area --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 lg:p-12 relative overflow-hidden">
                <form action="{{ route('sistema.empresas.store') }}" method="POST" id="empresaForm">
                    @csrf
                    
                    @include('empresas.form')

                    <div class="mt-12 flex flex-col md:flex-row justify-end gap-4 border-t border-slate-50 pt-10">
                        <a href="{{ route('sistema.empresas.index') }}" class="px-10 py-4 text-center bg-white border border-slate-200 text-slate-500 rounded-[1.5rem] font-black text-[0.65rem] uppercase tracking-widest hover:bg-slate-50 transition-all">
                            Descartar
                        </a>
                        <button type="submit" class="px-12 py-4 bg-slate-900 text-white rounded-[1.5rem] font-black text-[0.65rem] uppercase tracking-[0.2em] hover:bg-primary transition-all hover:shadow-xl shadow-lg hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 group">
                            Finalizar Registro
                            <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">send</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
