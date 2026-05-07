@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1200px] mx-auto">
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tighter text-slate-900">Gestión de Agentes y Supervisores</h1>
            <p class="text-slate-500 font-medium mt-1">Define la jerarquía operativa para el seguimiento de traspasos.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('traspasos.config.motivos.index') }}" class="bg-blue-50 text-blue-700 border border-blue-100 rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-blue-100 transition-all flex items-center gap-2 shadow-sm">
                <i class="ph-bold ph-list-checks text-lg"></i> Gestionar Motivos
            </a>
            <a href="{{ route('traspasos.index') }}" class="bg-white text-slate-900 border border-slate-200 rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="ph ph-arrow-left text-lg"></i> Volver a la Bandeja
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- SECCIÓN: SUPERVISORES -->
        <div class="space-y-6">
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-8">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-6 flex items-center gap-2">
                    <i class="ph-fill ph-user-gear text-blue-500 text-lg"></i> Nuevo Supervisor
                </h3>
                <form action="{{ route('traspasos.config.supervisores.store') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="nombre" placeholder="Nombre del Supervisor..." required
                           class="flex-1 bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                    <button type="submit" class="bg-primary text-white rounded-2xl px-6 py-3.5 text-[10px] font-black uppercase tracking-widest hover:bg-secondary transition-all">
                        Añadir
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Supervisor</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($supervisores as $sup)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4 text-sm font-black text-slate-900">{{ $sup->nombre }}</td>
                                <td class="px-8 py-4 text-right">
                                    <form action="{{ route('traspasos.config.supervisores.toggle', $sup) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter
                                            {{ $sup->activo ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                                            {{ $sup->activo ? 'Activo' : 'Inactivo' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- SECCIÓN: AGENTES -->
        <div class="space-y-6">
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-8">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-6 flex items-center gap-2">
                    <i class="ph-fill ph-users text-amber-500 text-lg"></i> Nuevo Agente
                </h3>
                <form action="{{ route('traspasos.config.agentes.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="text" name="nombre" placeholder="Nombre del Agente..." required
                           class="w-full bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                    
                    <div class="flex gap-2">
                        <select name="supervisor_id" required
                                class="flex-1 bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                            <option value="">Seleccionar Supervisor...</option>
                            @foreach($supervisores->where('activo', true) as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->nombre }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-slate-900 text-white rounded-2xl px-6 py-3.5 text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all">
                            Añadir Agente
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden" x-data="{ openMetaModal: false, selectedAgente: null }">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Agente</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Supervisor</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Meta Actual</th>
                            <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($agentes as $agente)
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-4">
                                    <div class="text-sm font-black text-slate-900">{{ $agente->nombre }}</div>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="text-[10px] font-bold px-2 py-1 bg-slate-100 rounded-md text-slate-600 uppercase">{{ $agente->supervisor->nombre }}</span>
                                </td>
                                <td class="px-8 py-4">
                                    @php
                                        $meta = $agente->currentMeta;
                                    @endphp
                                    @if($meta)
                                        <span class="text-xs font-black text-blue-600">{{ $meta->meta_cantidad }} <span class="text-[9px] text-slate-400">/ {{ now()->translatedFormat('M') }}</span></span>
                                    @else
                                        <span class="text-[10px] font-bold text-slate-300 italic">Sin meta</span>
                                    @endif
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="selectedAgente = {{ json_encode(['id' => $agente->id, 'nombre' => $agente->nombre]) }}; openMetaModal = true"
                                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Asignar Meta">
                                            <i class="ph ph-target text-lg"></i>
                                        </button>
                                        <form action="{{ route('traspasos.config.agentes.toggle', $agente) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-tighter
                                                {{ $agente->activo ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-slate-50 text-slate-400 border border-slate-100' }}">
                                                {{ $agente->activo ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- MODAL PARA ASIGNAR METAS -->
                <template x-if="openMetaModal">
                    <div class="fixed inset-0 z-[100] flex items-center justify-center p-6">
                        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md" @click="openMetaModal = false"></div>
                        <div class="relative bg-white rounded-[40px] shadow-2xl w-full max-w-md p-10 animate-in zoom-in-95 duration-200">
                            <h2 class="text-2xl font-black text-slate-900 tracking-tighter mb-2">Asignar Meta Mensual</h2>
                            <p class="text-sm font-medium text-slate-500 mb-8">Agente: <span class="text-blue-600 font-bold" x-text="selectedAgente.nombre"></span></p>

                            <form action="{{ route('traspasos.config.metas.store') }}" method="POST" class="space-y-6">
                                @csrf
                                <input type="hidden" name="agente_id" :value="selectedAgente.id">
                                
                                <div>
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Periodo (Mes)</label>
                                    <input type="month" name="periodo" required value="{{ now()->format('Y-m') }}"
                                           class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500/20">
                                </div>

                                <div>
                                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Cantidad Objetivo (Traspasos)</label>
                                    <input type="number" name="meta_cantidad" required min="1" placeholder="Ej: 50"
                                           class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500/20">
                                </div>

                                <div class="flex gap-4 pt-4">
                                    <button type="button" @click="openMetaModal = false"
                                            class="flex-1 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:bg-slate-50 transition-all">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            class="flex-1 bg-slate-900 text-white px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-200">
                                        Guardar Meta
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection
