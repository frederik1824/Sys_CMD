@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-in fade-in duration-700 bg-slate-50/30 min-h-screen">
    <!-- Header de Contexto -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Bandeja de Gestión Operativa</h1>
            <p class="text-slate-500 font-medium italic">Gestión diaria de contactos, validación y seguimiento de prospectos.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('call-center.import') }}" class="inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                <i class="ph-bold ph-plus-circle text-lg"></i>
                Cargar Data
            </a>
            <a href="{{ route('call-center.stats') }}" class="inline-flex items-center gap-3 bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-sm active:scale-95">
                <i class="ph-bold ph-chart-bar text-lg"></i>
                Estadísticas
            </a>
        </div>
    </div>

    <!-- Dashboard de KPIs Rápidos (Compacto y Elegante) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $kpis = [
                ['label' => 'Asignados', 'value' => $registros->total(), 'color' => 'slate', 'icon' => 'users'],
                ['label' => 'Pendientes', 'value' => $registros->where('estado.nombre', 'Pendiente de gestión')->count(), 'color' => 'amber', 'icon' => 'clock-countdown'],
                ['label' => 'Contactados', 'value' => $registros->where('estado.nombre', 'Contactado')->count(), 'color' => 'emerald', 'icon' => 'phone-call'],
                ['label' => 'Promovidos', 'value' => $registros->where('estado.nombre', 'Enviado a carnetización')->count(), 'color' => 'indigo', 'icon' => 'rocket-launch'],
            ];
        @endphp
        @foreach($kpis as $kpi)
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden group hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-500">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 group-hover:opacity-[0.07] transition-all duration-700 text-{{ $kpi['color'] }}-600">
                <i class="ph-bold ph-{{ $kpi['icon'] }} text-9xl"></i>
            </div>
            <div class="relative z-10 flex items-center gap-5">
                <div class="w-14 h-14 bg-{{ $kpi['color'] }}-50 text-{{ $kpi['color'] }}-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:rotate-12 transition-transform">
                    <i class="ph-bold ph-{{ $kpi['icon'] }} text-2xl"></i>
                </div>
                <div>
                    <h6 class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">{{ $kpi['label'] }}</h6>
                    <p class="text-3xl font-black text-slate-900 leading-none tracking-tight">{{ $kpi['value'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Filtros Avanzados -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <div class="p-8 border-b border-slate-50 bg-slate-50/30">
            <form action="{{ route('call-center.worklist') }}" method="GET" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <!-- Búsqueda Principal -->
                    <div class="md:col-span-4 space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Buscar Afiliado</label>
                        <div class="relative group">
                            <i class="ph-bold ph-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors"></i>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   class="w-full bg-white border-slate-200 rounded-2xl py-4 pl-14 pr-6 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all placeholder:text-slate-300"
                                   placeholder="Nombre, Cédula o Póliza...">
                        </div>
                    </div>
                    
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Estado</label>
                        <select name="estado_id" class="w-full bg-white border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all appearance-none">
                            <option value="">Todos</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id }}" {{ request('estado_id') == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Fecha Desde</label>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full bg-white border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Fecha Hasta</label>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full bg-white border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                    </div>

                    <div class="md:col-span-2 flex items-end">
                        <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2">
                            <i class="ph-bold ph-funnel text-base"></i>Filtrar
                        </button>
                    </div>
                </div>
                
                @if(request()->anyFilled(['search', 'estado_id', 'fecha_desde', 'fecha_hasta']))
                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ route('call-center.worklist') }}" class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:underline flex items-center gap-1">
                        <i class="ph-bold ph-x-circle"></i> Limpiar todos los filtros
                    </a>
                </div>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Información del Prospecto</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Empresa y Origen</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado de Gestión</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Interacciones</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Seguimiento</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($registros as $reg)
                    @php $isUrgent = $reg->prioridad >= 100; @endphp
                    <tr class="transition-all group {{ $isUrgent ? 'bg-rose-50/80 hover:bg-rose-100 border-l-4 border-rose-500 shadow-sm relative z-10' : 'hover:bg-indigo-50/30' }}">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-[20px] bg-white border border-slate-100 text-slate-400 flex items-center justify-center font-black text-xl shadow-sm group-hover:border-emerald-200 group-hover:text-emerald-500 transition-all">
                                    {{ substr($reg->nombre, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="font-black {{ $isUrgent ? 'text-rose-900' : 'text-slate-900 group-hover:text-indigo-600' }} tracking-tight transition-colors flex items-center gap-2">
                                        {{ $reg->nombre }}
                                        @if($isUrgent)
                                            <span class="inline-flex items-center gap-1 text-[9px] font-black uppercase tracking-widest text-white bg-rose-500 px-2 py-1 rounded-lg animate-pulse shadow-md shadow-rose-500/20">
                                                <i class="ph-bold ph-siren"></i> Urgente
                                            </span>
                                        @endif
                                    </h4>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg uppercase tracking-tighter">{{ $reg->cedula }}</span>
                                        @if($reg->poliza)
                                        <span class="text-[10px] font-black text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg uppercase tracking-tighter border border-indigo-100">POL: {{ $reg->poliza }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="text-xs font-black text-slate-700 truncate max-w-[180px]">{{ $reg->empresa_nombre ?? 'Individual' }}</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-tight">
                                <i class="ph-bold ph-map-pin text-emerald-500"></i>
                                {{ $reg->provincia ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex justify-center">
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-[9px] font-black uppercase tracking-[0.1em]"
                                    style="background-color: {{ $reg->estado->color }}10; color: {{ $reg->estado->color }}; border: 1px solid {{ $reg->estado->color }}20;">
                                    <div class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $reg->estado->color }};"></div>
                                    {{ $reg->estado->nombre }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <div class="flex flex-col items-center gap-1">
                                <div class="flex -space-x-2">
                                    @for($i=0; $i < min($reg->intentos_llamada, 3); $i++)
                                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[8px] font-black">
                                            <i class="ph ph-phone-call"></i>
                                        </div>
                                    @endfor
                                    @if($reg->intentos_llamada > 3)
                                        <div class="w-6 h-6 rounded-full border-2 border-white bg-slate-800 text-white flex items-center justify-center text-[8px] font-black">
                                            +{{ $reg->intentos_llamada - 3 }}
                                        </div>
                                    @endif
                                </div>
                                <span class="text-[9px] font-black text-slate-400 uppercase">{{ $reg->intentos_llamada ?: 'Sin' }} Intentos</span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            @if($reg->fecha_proximo_contacto)
                                @php
                                    $isOverdue = $reg->fecha_proximo_contacto < now()->startOfDay();
                                    $isToday = $reg->fecha_proximo_contacto->isToday();
                                @endphp
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-[10px] font-black {{ $isOverdue ? 'text-rose-600' : ($isToday ? 'text-amber-600' : 'text-slate-600') }} uppercase">
                                        {{ $reg->fecha_proximo_contacto->format('d M, Y') }}
                                    </span>
                                    @if($isOverdue || $isToday)
                                        <div class="flex items-center gap-1 mt-1">
                                            <div class="w-1.5 h-1.5 rounded-full {{ $isOverdue ? 'bg-rose-500 animate-ping' : 'bg-amber-500 animate-pulse' }}"></div>
                                            <span class="text-[8px] font-black uppercase">{{ $isOverdue ? 'Vencido' : 'Hoy' }}</span>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 uppercase italic">No programado</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('call-center.manage', $reg->uuid) }}" 
                               class="inline-flex items-center justify-center w-12 h-12 bg-white border border-slate-100 text-slate-400 rounded-2xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm hover:shadow-lg group-hover:rotate-6">
                                <i class="ph-bold ph-headset text-xl"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-24 text-center">
                            <div class="max-w-xs mx-auto space-y-6">
                                <div class="w-24 h-24 bg-slate-50 rounded-[40px] border border-slate-100 flex items-center justify-center mx-auto">
                                    <i class="ph-bold ph-magnifying-glass text-5xl text-slate-200"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900">Sin coincidencias</h3>
                                    <p class="text-slate-400 font-medium italic mt-2">No encontramos registros con los filtros aplicados. Intenta ampliar tu búsqueda.</p>
                                </div>
                                <a href="{{ route('call-center.worklist') }}" class="inline-block bg-slate-900 text-white px-8 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest">Ver todo el listado</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($registros->hasPages())
        <div class="p-8 border-t border-slate-50 bg-slate-50/30">
            {{ $registros->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .bg-indigo-50\/30:hover { background-color: rgba(238, 242, 255, 0.4); }
</style>
@endsection
