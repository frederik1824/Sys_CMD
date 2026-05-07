@extends('layouts.app')

@section('content')
<div class="p-4 md:p-10 max-w-[1600px] mx-auto min-h-[calc(100vh-100px)]">
    <!-- HEADER ESTRATÉGICO -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-end gap-8 mb-12">
        <div class="space-y-2">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-2 h-8 bg-indigo-600 rounded-full"></div>
                <h1 class="text-5xl font-black tracking-tighter text-slate-900 leading-none">Bandeja Operativa</h1>
            </div>
            <p class="text-slate-500 font-medium text-xl max-w-2xl leading-relaxed">Gestión centralizada de expedientes de afiliación y procesos de validación corporativa.</p>
        </div>
        <div class="flex flex-wrap gap-4">
            @can('solicitudes_afiliacion.configurar')
            <a href="{{ route('afiliacion.config') }}" 
               class="bg-white text-slate-900 border border-slate-200 rounded-[20px] px-8 py-5 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-slate-50 hover:border-slate-300 transition-all flex items-center gap-3 shadow-sm group">
                <i class="ph ph-sliders-horizontal text-xl group-hover:rotate-180 transition-transform duration-500"></i> Parámetros
            </a>
            @endcan
            <a href="{{ route('afiliacion.create') }}" 
               class="bg-slate-900 text-white rounded-[20px] px-10 py-5 text-[10px] font-black uppercase tracking-[0.2em] hover:bg-slate-800 transition-all flex items-center gap-3 shadow-2xl shadow-slate-200 group">
                <i class="ph ph-plus-circle-fill text-xl group-hover:scale-110 transition-transform"></i> Nueva Solicitud
            </a>
        </div>
    </div>

    <!-- DASHBOARD DE INDICADORES -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <!-- PENDIENTES -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full blur-2xl group-hover:bg-amber-100 transition-colors"></div>
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-14 h-14 bg-amber-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-amber-100 group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-hourglass-high text-2xl"></i>
                </div>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-lg">Fase 1</span>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-black text-slate-900 tracking-tighter mb-1">{{ $stats['pendientes'] }}</div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em]">Pendientes de Asignación</div>
            </div>
        </div>

        <!-- EN REVISIÓN -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full blur-2xl group-hover:bg-indigo-100 transition-colors"></div>
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-14 h-14 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-100 group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-eye text-2xl"></i>
                </div>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-lg">Fase 2</span>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-black text-slate-900 tracking-tighter mb-1">{{ $stats['en_revision'] }}</div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em]">En Evaluación Técnica</div>
            </div>
        </div>

        <!-- DEVUELTAS -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl shadow-slate-200/40 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-50 rounded-full blur-2xl group-hover:bg-orange-100 transition-colors"></div>
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-14 h-14 bg-orange-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-orange-100 group-hover:scale-110 transition-transform">
                    <i class="ph-bold ph-warning-circle text-2xl"></i>
                </div>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-3 py-1.5 rounded-lg">Fase X</span>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-black text-slate-900 tracking-tighter mb-1">{{ $stats['devueltas'] }}</div>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-[0.1em]">Observadas / Enmienda</div>
            </div>
        </div>

        <!-- APROBADAS -->
        <div class="bg-slate-900 p-8 rounded-[40px] border border-slate-800 shadow-2xl shadow-indigo-200 relative overflow-hidden group">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-3xl"></div>
            <div class="flex justify-between items-start mb-6 relative z-10">
                <div class="w-14 h-14 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-900/20 group-hover:rotate-12 transition-transform">
                    <i class="ph-bold ph-check-circle text-2xl"></i>
                </div>
                <span class="text-[9px] font-black text-white/40 uppercase tracking-widest bg-white/5 px-3 py-1.5 rounded-lg border border-white/10">Histórico</span>
            </div>
            <div class="relative z-10">
                <div class="text-4xl font-black text-white tracking-tighter mb-1">{{ $stats['aprobadas'] }}</div>
                <div class="text-[10px] font-black text-white/50 uppercase tracking-[0.1em]">Total Expedientes Visados</div>
            </div>
        </div>
    </div>

    <!-- ÁREA DE GESTIÓN OPERATIVA -->
    <div class="bg-white rounded-[48px] border border-slate-100 shadow-2xl shadow-slate-200/30 overflow-hidden">
        <!-- TOOLBAR DE FILTROS -->
        <div class="p-8 md:p-10 border-b border-slate-50 bg-slate-50/30 flex flex-col xl:flex-row justify-between items-center gap-8">
            <div class="flex flex-col md:flex-row items-center gap-6 w-full xl:w-auto">
                <!-- SWITCHER DE VISTA -->
                <div class="flex items-center gap-1 p-1 bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['view' => 'list'])) }}" 
                       class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('view', 'list') == 'list' ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600' }}">
                        <i class="ph ph-rows-fill mr-2"></i> Lista
                    </a>
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['view' => 'kanban'])) }}" 
                       class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('view') == 'kanban' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-400 hover:text-slate-600' }}">
                        <i class="ph ph-kanban-fill mr-2"></i> Tablero
                    </a>
                </div>

                <div class="h-8 w-px bg-slate-200 hidden md:block mx-2"></div>

                <div class="flex items-center gap-3 px-6 py-2 bg-white border border-slate-200 rounded-[24px] shadow-sm overflow-hidden p-1">
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['estado' => ''])) }}" 
                       class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all {{ !request('estado') ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:text-slate-600' }}">
                        Todas
                    </a>
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['estado' => 'Pendiente'])) }}" 
                       class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('estado') == 'Pendiente' ? 'bg-amber-500 text-white shadow-lg shadow-amber-100' : 'text-slate-400 hover:text-slate-600' }}">
                        Pendientes
                    </a>
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['estado' => 'En revisión'])) }}" 
                       class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('estado') == 'En revisión' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-400 hover:text-slate-600' }}">
                        En Revisión
                    </a>
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['estado' => 'Devuelta'])) }}" 
                       class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('estado') == 'Devuelta' ? 'bg-orange-500 text-white shadow-lg shadow-orange-100' : 'text-slate-400 hover:text-slate-600' }}">
                        Devueltas
                    </a>
                </div>
            </div>
            
            <form action="{{ route('afiliacion.index') }}" method="GET" class="relative w-full xl:w-[500px] group">
                <input type="hidden" name="view" value="{{ request('view', 'list') }}">
                <input type="hidden" name="estado" value="{{ request('estado') }}">
                <i class="ph-bold ph-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 text-xl group-focus-within:text-indigo-600 transition-colors"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar por cédula, nombre o folio..." 
                       class="w-full pl-16 pr-8 py-5 bg-white border-2 border-slate-100 rounded-[28px] text-sm font-black text-slate-800 placeholder-slate-300 focus:ring-8 focus:ring-indigo-50 focus:border-indigo-200 focus:bg-white transition-all shadow-sm">
                @if(request('search') || request('estado'))
                <a href="{{ route('afiliacion.index') }}" class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase text-rose-500 hover:text-rose-600 transition-colors">Limpiar</a>
                @endif
            </form>
        </div>

        @if(request('view') == 'kanban')
            <!-- VISTA KANBAN (Tablero) -->
            <div class="p-10 bg-slate-50 overflow-x-auto">
                <div class="flex gap-8 min-w-max pb-4">
                    @foreach(['Pendiente', 'En revisión', 'Devuelta', 'Aprobada'] as $estado)
                    <div class="w-96 flex flex-col gap-6">
                        <div class="flex items-center justify-between px-2">
                            <div class="flex items-center gap-3">
                                <span class="w-3 h-3 rounded-full {{ $estado == 'Pendiente' ? 'bg-amber-500' : ($estado == 'En revisión' ? 'bg-indigo-600' : ($estado == 'Devuelta' ? 'bg-orange-500' : 'bg-emerald-500')) }}"></span>
                                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.15em]">{{ $estado }}</h3>
                            </div>
                            <span class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-black text-slate-500">
                                {{ isset($tablero[$estado]) ? $tablero[$estado]->count() : 0 }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            @if(isset($tablero[$estado]))
                                @foreach($tablero[$estado] as $sol)
                                <div class="bg-white p-6 rounded-[32px] border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                                    <div class="flex justify-between items-start mb-4">
                                        <span class="text-[9px] font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg uppercase tracking-widest">{{ $sol->codigo_solicitud }}</span>
                                        <div class="flex gap-1">
                                            <div class="w-2 h-2 rounded-full {{ $sol->priority_color_simple }}"></div>
                                        </div>
                                    </div>
                                    
                                    <h4 class="text-sm font-black text-slate-900 mb-1 leading-tight">{{ $sol->nombre_completo }}</h4>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">{{ $sol->tipoSolicitud->nombre }}</p>

                                    @if(!in_array($sol->estado, ['Aprobada', 'Rechazada', 'Cerrada']))
                                    <div class="mb-4">
                                        <div class="flex justify-between items-center text-[8px] font-black uppercase tracking-widest mb-1">
                                            <span class="text-slate-400">Progreso SLA</span>
                                            <span class="{{ $sol->sla_percentage > 80 ? 'text-rose-500 font-bold' : ($sol->sla_percentage > 50 ? 'text-amber-500' : 'text-emerald-500') }}">
                                                {{ $sol->sla_percentage }}%
                                            </span>
                                        </div>
                                        <div class="w-full h-1 bg-slate-50 rounded-full overflow-hidden">
                                            <div class="h-full {{ $sol->sla_percentage > 80 ? 'bg-rose-500' : ($sol->sla_percentage > 50 ? 'bg-amber-500' : 'bg-emerald-500') }} transition-all duration-1000" style="width: {{ $sol->sla_percentage }}%"></div>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                        <div class="flex -space-x-2">
                                            <img src="{{ $sol->solicitante->avatar_url }}" class="w-8 h-8 rounded-full border-2 border-white shadow-sm" title="Solicitante: {{ $sol->solicitante->name }}">
                                            @if($sol->asignado)
                                                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-black border-2 border-white shadow-sm" title="Asignado a: {{ $sol->asignado->name }}">
                                                    {{ substr($sol->asignado->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <a href="{{ route('afiliacion.show', $sol) }}" class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-900 hover:text-white transition-all">
                                            <i class="ph ph-arrow-right font-bold"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- TABLA DE ALTA DENSIDAD -->
            <div class="overflow-x-auto" x-data="{ selected: [] }">
                <!-- ACCIONES MASIVAS -->
                <div x-show="selected.length > 0" x-transition class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-slate-900 text-white px-10 py-6 rounded-[32px] shadow-2xl z-[100] flex items-center gap-8 border border-white/10 backdrop-blur-md">
                    <div class="flex items-center gap-4 pr-8 border-r border-white/10">
                        <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-black" x-text="selected.length"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Seleccionadas</span>
                    </div>

                    <form action="{{ route('afiliacion.bulk-assign') }}" method="POST" class="flex items-center gap-4">
                        @csrf
                        <input type="hidden" name="ids" :value="selected.join(',')">
                        <select name="user_id" required class="bg-white/10 border-none rounded-xl text-[10px] font-black uppercase tracking-widest focus:ring-0 px-6 py-3">
                            <option value="" class="text-slate-900">Asignar a Analista...</option>
                            @foreach($usuarios as $u)
                                <option value="{{ $u->id }}" class="text-slate-900">{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-500 transition-all">Ejecutar Asignación</button>
                    </form>

                    <button @click="selected = []" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-white transition-colors">Cancelar</button>
                </div>

                <table class="w-full text-left border-collapse table-fixed">
                    <thead>
                        <tr class="bg-white border-b border-slate-100">
                            <th class="w-16 px-6 py-6 text-center">
                                <input type="checkbox" @change="selected = $event.target.checked ? {{ $solicitudes->pluck('id')->toJson() }} : []" :checked="selected.length === {{ $solicitudes->count() }}" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </th>
                            <th class="w-1/4 px-6 py-6 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Folio / Tipo de Trámite</th>
                            <th class="w-32 px-4 py-6 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Prioridad</th>
                            <th class="w-1/5 px-6 py-6 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Afiliado</th>
                            <th class="w-1/5 px-6 py-6 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em]">Origen / Gestión</th>
                            <th class="w-40 px-6 py-6 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Estado / SLA</th>
                            <th class="w-32 px-6 py-6 text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50/60">
                        @forelse($solicitudes as $solicitud)
                        @php
                            $isFinished = in_array($solicitud->estado, ['Aprobada', 'Rechazada', 'Cerrada', 'Cancelada']);
                            $rowStyle = match($solicitud->prioridad) {
                                'Urgente' => 'bg-gradient-to-r from-rose-100/40 via-white to-white border-l-[8px] border-rose-600 ' . ($isFinished ? '' : 'animate-pulse'),
                                'Alta' => 'bg-gradient-to-r from-amber-100/30 via-white to-white border-l-[8px] border-amber-500',
                                default => 'hover:bg-slate-50/50'
                            };
                            
                            if ($isFinished) {
                                $rowStyle = str_replace('border-l-[8px]', 'border-l-[4px] opacity-75 grayscale-[0.2]', $rowStyle);
                            }
                        @endphp
                        <tr class="{{ $rowStyle }} transition-all group relative border-b border-slate-50">
                            <td class="px-6 py-8 text-center">
                                <input type="checkbox" value="{{ $solicitud->id }}" x-model="selected" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs font-black text-slate-900 leading-none">{{ $solicitud->codigo_solicitud }}</span>
                                    <span class="text-[10px] font-bold text-indigo-600 truncate max-w-[180px]">{{ $solicitud->tipoSolicitud->nombre }}</span>
                                    <span class="text-[8px] font-medium text-slate-400 uppercase tracking-tighter">{{ $solicitud->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-5">
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border {{ $solicitud->priority_color }}">
                                    <i class="{{ $solicitud->priority_icon }} text-xs"></i>
                                    <span class="text-[9px] font-black uppercase tracking-widest">{{ $solicitud->prioridad }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-900 truncate max-w-[150px]">{{ $solicitud->nombre_completo }}</span>
                                    <span class="text-[9px] font-black text-slate-400 tracking-widest">{{ $solicitud->cedula }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-3">
                                    <!-- SOLICITANTE -->
                                    <div class="flex items-center gap-2 opacity-80 scale-90 origin-left">
                                        <div class="w-6 h-6 rounded-lg bg-slate-100 flex items-center justify-center text-[8px] font-black text-slate-500 overflow-hidden">
                                            <img src="{{ $solicitud->solicitante->avatar_url }}" class="w-full h-full object-cover">
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-600 truncate">{{ explode(' ', $solicitud->solicitante->name)[0] }}</span>
                                    </div>
                                    <!-- ASIGNADO -->
                                    @if($solicitud->asignado)
                                    <div class="flex items-center gap-2 scale-90 origin-left">
                                        <div class="w-6 h-6 rounded-lg bg-indigo-600 flex items-center justify-center text-[8px] font-black text-white shadow-sm">
                                            {{ substr($solicitud->asignado->name, 0, 2) }}
                                        </div>
                                        <span class="text-[10px] font-black text-indigo-700 truncate">{{ explode(' ', $solicitud->asignado->name)[0] }}</span>
                                    </div>
                                    @else
                                    <div class="flex items-center gap-2 text-slate-300 scale-90 origin-left">
                                        <i class="ph ph-user-circle-plus text-lg"></i>
                                        <span class="text-[8px] font-black uppercase italic">Sin asignar</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-[0.1em] {{ $solicitud->status_color }} border border-black/5">
                                        {{ $solicitud->estado }}
                                    </span>
                                    @if(!in_array($solicitud->estado, ['Aprobada', 'Rechazada', 'Cerrada']))
                                    <div class="w-24 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $solicitud->sla_percentage > 80 ? 'bg-rose-500' : ($solicitud->sla_percentage > 50 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $solicitud->sla_percentage }}%"></div>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-end gap-2 scale-90 origin-right">
                                    @if(in_array($solicitud->estado, ['Borrador', 'Devuelta']) && $solicitud->solicitante_user_id == auth()->id())
                                    <a href="{{ route('afiliacion.edit', $solicitud) }}" class="w-10 h-10 flex items-center justify-center text-amber-500 hover:text-white hover:bg-amber-500 rounded-xl transition-all">
                                        <i class="ph-bold ph-pencil-simple text-xl"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('afiliacion.show', $solicitud) }}" class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">
                                        <i class="ph-bold ph-arrow-square-out text-xl"></i>
                                    </a>
                                    @if($solicitud->estado == 'Pendiente' && auth()->user()->can('solicitudes_afiliacion.asignarse'))
                                    <form action="{{ route('afiliacion.assign', $solicitud) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 flex items-center justify-center text-emerald-500 hover:text-white hover:bg-emerald-500 rounded-xl transition-all">
                                            <i class="ph-bold ph-hand-pointing text-xl"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-10 py-32 text-center">
                                <div class="flex flex-col items-center max-w-sm mx-auto">
                                    <div class="w-24 h-24 bg-slate-50 rounded-[32px] flex items-center justify-center mb-8 border border-slate-100 shadow-inner">
                                        <i class="ph ph-folder-dotted text-5xl text-slate-200"></i>
                                    </div>
                                    <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-3">Sin resultados técnicos</h3>
                                    <p class="text-slate-400 font-medium text-base leading-relaxed">No hemos encontrado expedientes que coincidan con los criterios de búsqueda actuales.</p>
                                    @if(request('search') || request('estado'))
                                    <a href="{{ route('afiliacion.index') }}" class="mt-8 text-[11px] font-black uppercase tracking-widest text-indigo-600 hover:text-indigo-700 underline decoration-indigo-200 underline-offset-8 transition-all">Limpiar todos los filtros</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
        
        <!-- PAGINACIÓN PREMIUM -->
        @if($solicitudes->hasPages())
        <div class="p-10 border-t border-slate-50 bg-slate-50/20">
            {{ $solicitudes->links() }}
        </div>
        @endif
    </div>

    <!-- FOOTER DE APOYO -->
    <div class="mt-12 flex flex-col md:flex-row justify-between items-center gap-6 px-10">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Sistema de Control Operativo v4.0.2</p>
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Servicios Online</span>
            </div>
            <div class="flex items-center gap-2">
                <i class="ph ph-shield-check-fill text-indigo-500"></i>
                <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Protocolo Seguro</span>
            </div>
        </div>
    </div>
</div>

<style>
    /* Suavizado de transiciones para toda la página */
    * {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    /* Personalización del sistema de paginación para que coincida con el diseño */
    .pagination {
        display: flex;
        gap: 0.5rem;
    }
    .page-item .page-link {
        border-radius: 12px;
        font-weight: 900;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 0.1em;
        border: none;
        padding: 0.75rem 1.25rem;
        color: #64748b;
        background: white;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        transition: all 0.2s ease;
    }
    .page-item.active .page-link {
        background: #0f172a;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }
    .page-item:hover .page-link:not(.active) {
        background: #f8fafc;
        color: #1e293b;
    }
</style>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentPendientes = {{ $stats['pendientes'] }};
        const checkInterval = 30000; // 30 segundos

        setInterval(() => {
            fetch("{{ route('afiliacion.check-stats') }}")
                .then(response => response.json())
                .then(data => {
                    if (data.pendientes > currentPendientes) {
                        Swal.fire({
                            title: 'Nueva Solicitud Recibida',
                            text: 'Se ha detectado un nuevo expediente en la bandeja operativa.',
                            icon: 'info',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: true,
                            confirmButtonText: 'Actualizar Lista',
                            timer: 10000,
                            timerProgressBar: true,
                            showCancelButton: true,
                            cancelButtonText: 'Cerrar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                        currentPendientes = data.pendientes;
                    } else if (data.pendientes < currentPendientes) {
                        // Alguien más tomó una solicitud o fue procesada
                        currentPendientes = data.pendientes;
                    }
                })
                .catch(err => console.error('Error verificando estados:', err));
        }, checkInterval);
    });
</script>
@endpush
@endsection

