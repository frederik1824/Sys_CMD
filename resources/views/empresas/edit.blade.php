@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-fade-in pb-20">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <a href="{{ route('empresas.show', $empresa) }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all shadow-sm">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <nav class="flex items-center gap-2 mb-1 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                    <a href="{{ route('empresas.index') }}" class="hover:text-primary transition-colors">Empresas</a>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="text-primary">Edición de Perfil</span>
                </nav>
                <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none italic">{{ $empresa->nombre }}</h2>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
        {{-- Side Info --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-amber-500 rounded-[2.5rem] p-8 text-white relative overflow-hidden shadow-xl shadow-amber-500/20">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/20 rounded-full blur-3xl opacity-50"></div>
                <h4 class="text-[0.65rem] font-black text-amber-100 uppercase tracking-[0.3em] mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">edit_note</span>
                    Modo Edición
                </h4>
                <p class="text-xs text-amber-50 font-medium leading-relaxed relative z-10">
                    Está modificando los datos maestros de la entidad. Los cambios afectarán la facturación y la trazabilidad de los afiliados vinculados.
                </p>
                
                <div class="mt-8 pt-6 border-t border-white/10 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white">fingerprint</span>
                    </div>
                    <div>
                        <p class="text-[0.5rem] font-black text-amber-100 uppercase tracking-widest">RNC Actual</p>
                        <p class="text-xs font-bold text-white">{{ $empresa->rnc ?? 'No definido' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 rounded-[2rem] p-6 border border-slate-100">
                <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest mb-4">Última Actualización</p>
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-slate-400 text-lg">history</span>
                    <span class="text-xs text-slate-600 font-bold">{{ $empresa->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        {{-- Form Area --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-8 lg:p-12">
                <form action="{{ route('empresas.update', $empresa) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="last_updated_at" value="{{ $empresa->updated_at }}">
                    
                    @include('empresas.form', ['empresa' => $empresa])

                    <div class="mt-12 flex flex-col md:flex-row justify-end gap-4 border-t border-slate-50 pt-10">
                        <a href="{{ route('empresas.show', $empresa) }}" class="px-10 py-4 text-center bg-white border border-slate-200 text-slate-500 rounded-[1.5rem] font-black text-[0.65rem] uppercase tracking-widest hover:bg-slate-50 transition-all">
                            Cancelar
                        </a>
                        <button type="submit" class="px-12 py-4 bg-amber-500 text-white rounded-[1.5rem] font-black text-[0.65rem] uppercase tracking-[0.2em] hover:bg-amber-600 transition-all hover:shadow-xl shadow-lg hover:-translate-y-1 active:scale-95 flex items-center justify-center gap-3 group">
                            Actualizar Entidad
                            <span class="material-symbols-outlined text-lg group-hover:rotate-12 transition-transform">update</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
