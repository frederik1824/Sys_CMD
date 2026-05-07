@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1200px] mx-auto" x-data="{ showCreate: false, selectedMotivo: null }">
    <!-- HEADER -->
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tighter text-slate-900 uppercase">Gestión de Motivos</h1>
            <p class="text-slate-500 font-medium mt-1 text-lg italic">Configuración dinámica de rechazos Unipago / SISALRIL.</p>
        </div>
        <button @click="showCreate = true" class="bg-slate-900 text-white rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center gap-2 shadow-xl">
            <i class="ph-bold ph-plus text-lg"></i> Nuevo Motivo
        </button>
    </div>

    <!-- LISTADO -->
    <div class="grid grid-cols-1 gap-4">
        @foreach($motivos as $motivo)
            <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-all">
                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center {{ $motivo->activo ? 'bg-blue-50 text-blue-600' : 'bg-slate-50 text-slate-300' }}">
                        <span class="text-[10px] font-black leading-none">{{ $motivo->codigo_sisalril ?? '--' }}</span>
                        <div class="w-full h-[1px] bg-current opacity-20 my-1"></div>
                        <span class="text-[10px] font-bold leading-none opacity-60">{{ $motivo->codigo_unsigima ?? '--' }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter">{{ $motivo->descripcion }}</h3>
                        <div class="flex items-center gap-4 mt-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1">
                                <i class="ph ph-fingerprint text-xs"></i> SISALRIL: {{ $motivo->codigo_sisalril ?? 'N/A' }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1">
                                <i class="ph ph-hash text-xs"></i> UNSIGIMA: {{ $motivo->codigo_unsigima ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <form action="{{ route('traspasos.config.motivos.toggle', $motivo) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all {{ $motivo->activo ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                            {{ $motivo->activo ? 'Activo' : 'Inactivo' }}
                        </button>
                    </form>
                    
                    <button @click="selectedMotivo = @json($motivo)" class="p-3 bg-slate-50 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                        <i class="ph ph-pencil-simple text-xl"></i>
                    </button>

                    <form action="{{ route('traspasos.config.motivos.destroy', $motivo) }}" method="POST" onsubmit="return confirm('¿Eliminar este motivo?')">
                        @csrf @method('DELETE')
                        <button class="p-3 bg-slate-50 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                            <i class="ph ph-trash text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <!-- MODAL CREATE -->
    <div x-show="showCreate" class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showCreate = false"></div>
        <form action="{{ route('traspasos.config.motivos.store') }}" method="POST" class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl relative z-10 p-8">
            @csrf
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter uppercase mb-8">Nuevo Motivo</h3>
            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Cód. SISALRIL</label>
                        <input type="text" name="codigo_sisalril" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-slate-900/10">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Cód. UNSIGIMA</label>
                        <input type="text" name="codigo_unsigima" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-slate-900/10">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Descripción del Motivo</label>
                    <textarea name="descripcion" required rows="3" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-slate-900/10"></textarea>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-10">
                <button type="button" @click="showCreate = false" class="bg-slate-100 text-slate-600 rounded-2xl py-4 text-[10px] font-black uppercase">Cancelar</button>
                <button type="submit" class="bg-slate-900 text-white rounded-2xl py-4 text-[10px] font-black uppercase shadow-xl">Guardar Motivo</button>
            </div>
        </form>
    </div>

    <!-- MODAL EDIT -->
    <template x-if="selectedMotivo">
        <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="selectedMotivo = null"></div>
            <form :action="'{{ url('traspasos/configuracion/motivos') }}/' + selectedMotivo.id" method="POST" class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl relative z-10 p-8">
                @csrf @method('PATCH')
                <h3 class="text-2xl font-black text-slate-900 tracking-tighter uppercase mb-8">Editar Motivo</h3>
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Cód. SISALRIL</label>
                            <input type="text" name="codigo_sisalril" x-model="selectedMotivo.codigo_sisalril" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-slate-900/10">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Cód. UNSIGIMA</label>
                            <input type="text" name="codigo_unsigima" x-model="selectedMotivo.codigo_unsigima" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-slate-900/10">
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Descripción del Motivo</label>
                        <textarea name="descripcion" x-model="selectedMotivo.descripcion" required rows="3" class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-slate-900/10"></textarea>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-10">
                    <button type="button" @click="selectedMotivo = null" class="bg-slate-100 text-slate-600 rounded-2xl py-4 text-[10px] font-black uppercase">Cancelar</button>
                    <button type="submit" class="bg-slate-900 text-white rounded-2xl py-4 text-[10px] font-black uppercase shadow-xl">Actualizar</button>
                </div>
            </form>
        </div>
    </template>
</div>
@endsection
