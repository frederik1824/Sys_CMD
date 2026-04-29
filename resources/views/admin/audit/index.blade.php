@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-slate-900/20">
                <span class="material-symbols-outlined">history</span>
            </div>
            <div>
                <h2 class="font-bold text-2xl text-slate-800 leading-tight">Auditoría del Sistema</h2>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Historial de cambios y actividad global</p>
            </div>
        </div>

        {{-- Filtros Rápidos --}}
        <form action="{{ route('admin.audit.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="user_id" class="bg-slate-50 border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:ring-primary/20 focus:border-primary px-4 py-2 opacity-80 hover:opacity-100 transition-opacity">
                <option value="">Todos los Usuarios</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            
            <select name="event" class="bg-slate-50 border-slate-200 rounded-xl text-xs font-bold text-slate-600 focus:ring-primary/20 focus:border-primary px-4 py-2 opacity-80 hover:opacity-100 transition-opacity">
                <option value="">Eventos</option>
                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Creado</option>
                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Editado</option>
                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Eliminado</option>
            </select>

            <button type="submit" class="p-2 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-md">
                <span class="material-symbols-outlined text-sm">search</span>
            </button>
            
            @if(request()->anyFilled(['user_id', 'event', 'model']))
                <a href="{{ route('admin.audit.index') }}" class="p-2 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-colors">
                    <span class="material-symbols-outlined text-sm">close</span>
                </a>
            @endif
        </form>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-slate-100">
        <a href="{{ route('usuarios.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-all border-b-2 border-transparent">
            Usuarios
        </a>
        <a href="{{ route('roles.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-all border-b-2 border-transparent">
            Roles & Permisos
        </a>
        <a href="{{ route('admin.audit.index') }}" class="px-6 py-3 text-sm font-bold border-b-2 border-primary text-primary transition-all">
            Auditoría
        </a>
    </div>

    {{-- Timeline Content --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden" x-data="{ openModal: false, selectedLog: null }">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Sujeto / Acción</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Modelo Afectado</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Cambios Realizados</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black text-slate-400 uppercase tracking-[0.2em]">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 overflow-hidden border-2 border-white shadow-sm">
                                        @if($log->user?->avatar)
                                            <img src="{{ Storage::url($log->user->avatar) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="font-bold text-xs uppercase">{{ substr($log->user?->name ?? '?', 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700 leading-tight">{{ $log->user?->name ?? 'Usuario Desconocido' }}</span>
                                        <span class="flex items-center gap-1.5 mt-1">
                                            @php
                                                $badgeClass = match($log->event) {
                                                    'created' => 'bg-emerald-100 text-emerald-700 font-black',
                                                    'updated' => 'bg-amber-100 text-amber-700 font-black',
                                                    'deleted' => 'bg-rose-100 text-rose-700 font-black',
                                                    default => 'bg-slate-100 text-slate-700 font-black'
                                                };
                                                $eventLabel = match($log->event) {
                                                    'created' => 'CREACIÓN',
                                                    'updated' => 'EDICIÓN',
                                                    'deleted' => 'ELIMINACIÓN',
                                                    default => strtoupper($log->event)
                                                };
                                            @endphp
                                            <span class="text-[0.6rem] px-2 py-0.5 rounded-full {{ $badgeClass }} tracking-wider">
                                                {{ $eventLabel }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ class_basename($log->model_type) }}</span>
                                    <span class="text-[0.7rem] font-bold text-primary mt-1 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">fingerprint</span> ID: {{ $log->model_id }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="max-w-md">
                                    @if($log->event === 'updated')
                                        <div class="flex flex-col gap-2">
                                            @php 
                                                $visibleChanges = collect($log->formatted_changes)->where('is_technical', false);
                                            @endphp

                                            @forelse($visibleChanges->take(3) as $change)
                                                <div class="text-[0.7rem] leading-relaxed">
                                                    <span class="font-black text-slate-500 uppercase tracking-tighter mr-1">{{ $change['label'] }}:</span>
                                                    <span class="line-through text-slate-400 opacity-60">{{ Str::limit($change['old'], 20) }}</span>
                                                    <span class="mx-1 text-primary">→</span>
                                                    <span class="font-bold text-slate-800">{{ Str::limit($change['new'], 30) }}</span>
                                                </div>
                                            @empty
                                                <div class="text-[0.7rem] text-slate-400 italic flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[10px]">settings</span>
                                                    Solo cambios técnicos de sistema
                                                </div>
                                            @endforelse

                                            @if($visibleChanges->count() > 3)
                                                <span class="text-[0.6rem] text-slate-400 italic">+ {{ $visibleChanges->count() - 3 }} cambios más</span>
                                            @endif
                                        </div>
                                    @elseif($log->event === 'created')
                                        <div class="text-[0.7rem] text-emerald-600 font-bold italic">Registro inicial creado.</div>
                                    @else
                                        <div class="text-[0.7rem] text-rose-600 font-bold italic">Eliminación de registro.</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-end gap-2">
                                    <button @click="selectedLog = {{ json_encode($log) }}; openModal = true" class="p-2 bg-slate-50 text-slate-500 rounded-lg hover:bg-primary hover:text-white transition-all">
                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                    </button>
                                    <span class="text-[0.6rem] font-bold text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 opacity-40">
                                    <span class="material-symbols-outlined text-5xl">inventory_2</span>
                                    <p class="text-sm font-bold uppercase tracking-[0.2em] text-slate-500">No se encontraron logs</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-6 py-4 bg-slate-50/50 border-t border-slate-50">
                {{ $logs->links() }}
            </div>
        @endif

        {{-- Modal de Detalles --}}
        <div x-show="openModal" class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div @click.away="openModal = false" class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in-95 duration-200">
                <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Detalles de Interacción</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Inspección granular de datos</p>
                    </div>
                    <button @click="openModal = false" class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-slate-400 hover:text-rose-500 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                
                <div class="p-8 space-y-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1">Usuario</p>
                            <p class="text-sm font-bold text-slate-700" x-text="selectedLog?.user?.name || 'Sistema'"></p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest mb-1">Fecha de Interacción</p>
                            <p class="text-sm font-bold text-slate-700" x-text="selectedLog?.created_at"></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary"></span>
                            Inspección de Cambios
                        </h4>
                        
                        <div class="space-y-3">
                            <template x-for="change in selectedLog?.formatted_changes" :key="change.label">
                                <div class="p-4 rounded-2xl border border-slate-100 flex flex-col gap-2" :class="change.is_technical ? 'bg-slate-50/50 opacity-60' : 'bg-white shadow-sm'">
                                    <div class="flex justify-between items-center">
                                        <span class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest" x-text="change.label"></span>
                                        <template x-if="change.is_technical">
                                            <span class="text-[8px] bg-slate-200 text-slate-500 px-1.5 py-0.5 rounded font-bold uppercase tracking-tighter">Técnico</span>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1">
                                            <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Anterior</p>
                                            <p class="text-xs font-medium text-slate-500 line-through truncate" x-text="change.old"></p>
                                        </div>
                                        <div class="text-primary opacity-30">
                                            <span class="material-symbols-outlined">double_arrow</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] text-primary font-bold uppercase mb-1">Nuevo</p>
                                            <p class="text-sm font-black text-slate-800" x-text="change.new"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-slate-50/50 border-t border-slate-100 flex justify-end">
                    <button @click="openModal = false" class="px-8 py-3 bg-slate-800 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-slate-700 transition-all">
                        Cerrar Inspección
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
