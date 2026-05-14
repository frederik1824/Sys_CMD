@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8 max-w-[1600px] mx-auto min-h-screen">
    <!-- COMPACT COMMAND CENTER HEADER -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 mb-8 bg-white/40 backdrop-blur-xl p-6 rounded-[2rem] border border-white/60 shadow-xl">
        <div class="flex items-center gap-5">
            <div class="relative">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 flex items-center justify-center text-white shadow-xl rotate-2">
                    <i class="ph-fill ph-tray text-2xl"></i>
                </div>
                <div class="absolute -bottom-1 -right-1 w-7 h-7 bg-indigo-600 rounded-xl border-2 border-white flex items-center justify-center text-white shadow-md">
                    <i class="ph-bold ph-lightning text-xs"></i>
                </div>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-pulse"></span>
                    <p class="text-[9px] font-black text-indigo-600 uppercase tracking-[0.3em]">Live Dashboard</p>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter leading-none">Bandeja Operativa</h1>
                <p class="text-slate-400 font-bold text-[10px] uppercase tracking-widest mt-1">Gestión de Afiliaciones</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="hidden md:flex flex-col items-end mr-4 pr-4 border-r border-slate-200">
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none">Sistema</span>
                <span class="text-[10px] font-black text-emerald-500 flex items-center gap-1.5 mt-1">
                    <i class="ph-fill ph-circle text-[6px]"></i> Latencia Óptima
                </span>
            </div>
            
            <div class="flex gap-2">
                @can('solicitudes_afiliacion.configurar')
                <a href="{{ route('afiliacion.config') }}" class="w-12 h-12 rounded-2xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:bg-slate-900 hover:text-white transition-all shadow-sm group">
                    <i class="ph ph-gear-six text-xl group-hover:rotate-90 transition-transform"></i>
                </a>
                @endcan
                <a href="{{ route('afiliacion.create') }}" class="flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all group">
                    <i class="ph-bold ph-plus-circle text-lg group-hover:scale-110 transition-transform"></i>
                    Nueva Solicitud
                </a>
            </div>
        </div>
    </div>

    <!-- COMPACT MINI-METRIC BAR (Operational Focus) -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-6 mb-10">
        @php
            $kpiSchema = [
                'pendientes' => ['label' => 'Pendientes', 'icon' => 'ph-hourglass-high', 'color' => 'amber'],
                'en_revision' => ['label' => 'Evaluación', 'icon' => 'ph-eye', 'color' => 'indigo'],
                'devueltas' => ['label' => 'Observadas', 'icon' => 'ph-warning-circle', 'color' => 'rose'],
                'aprobadas' => ['label' => 'Cierres', 'icon' => 'ph-check-square', 'color' => 'teal'],
                'completadas' => ['label' => 'Completadas', 'icon' => 'ph-check-circle', 'color' => 'emerald']
            ];
        @endphp

        @foreach($kpiSchema as $key => $kpi)
        <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-sm hover:shadow-xl transition-all duration-300 group flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-{{ $kpi['color'] }}-50 text-{{ $kpi['color'] }}-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <i class="ph-fill {{ $kpi['icon'] }} text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1.5">{{ $kpi['label'] }}</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl font-black text-slate-900 leading-none">{{ $stats[$key] ?? 0 }}</h3>
                    <span class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter">Items</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- ÁREA DE GESTIÓN OPERATIVA -->
    <!-- ÁREA DE GESTIÓN OPERATIVA -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/30 overflow-hidden">
        <!-- ADVANCED TOOLBAR -->
        <div class="px-8 py-6 border-b border-slate-50 flex flex-col xl:flex-row justify-between items-center gap-6 bg-slate-50/30">
            <div class="flex flex-col md:flex-row items-center gap-6 w-full xl:w-auto">
                <!-- VIEW SWITCHER -->
                <div class="flex p-1.5 bg-slate-200/50 rounded-3xl border border-slate-200/60 w-full md:w-auto">
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['view' => 'list'])) }}" 
                       class="flex-1 md:flex-none px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('view', 'list') == 'list' ? 'bg-white text-slate-900 shadow-lg' : 'text-slate-400 hover:text-slate-600' }}">
                        Lista
                    </a>
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['view' => 'kanban'])) }}" 
                       class="flex-1 md:flex-none px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('view') == 'kanban' ? 'bg-white text-indigo-600 shadow-lg' : 'text-slate-400 hover:text-slate-600' }}">
                        Tablero
                    </a>
                </div>

                @php 
                    $statusFilters = [
                        ['slug' => '', 'label' => 'Todas', 'color' => 'slate-900'],
                        ['slug' => 'Pendiente', 'label' => 'Pendientes', 'color' => 'amber-500'],
                        ['slug' => 'En revisión', 'label' => 'Evaluación', 'color' => 'indigo-600'],
                        ['slug' => 'Devuelta', 'label' => 'Devueltas', 'color' => 'rose-500'],
                        ['slug' => 'Aprobada', 'label' => 'Aprobadas', 'color' => 'teal-600'],
                        ['slug' => 'Completada', 'label' => 'Éxito', 'color' => 'emerald-600']
                    ];
                @endphp
                <!-- STATUS QUICK FILTERS -->
                <div class="flex flex-wrap items-center gap-2">
                    @foreach($statusFilters as $filter)
                    <a href="{{ route('afiliacion.index', array_merge(request()->all(), ['estado' => $filter['slug']])) }}" 
                       class="px-5 py-3 rounded-2xl text-[9px] font-black uppercase tracking-widest transition-all {{ request('estado') == $filter['slug'] ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/10' : 'bg-white text-slate-400 border border-slate-100 hover:border-slate-300' }}">
                        {{ $filter['label'] }}
                    </a>
                    @endforeach
                </div>
            </div>

            <!-- SMART SEARCH -->
            <form action="{{ route('afiliacion.index') }}" method="GET" class="relative w-full xl:w-[450px]">
                <input type="hidden" name="view" value="{{ request('view', 'list') }}">
                <input type="hidden" name="estado" value="{{ request('estado') }}">
                <i class="ph ph-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar expediente..." 
                       class="w-full pl-16 pr-8 py-5 bg-white border border-slate-200 rounded-3xl text-sm font-bold text-slate-700 placeholder-slate-300 focus:ring-4 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all">
            </form>
        </div>

        @if(request('view') == 'kanban')
            <!-- CYBER-KANBAN BOARD -->
            <div class="p-10 bg-slate-50/50 overflow-x-auto min-h-[600px]">
                <div class="flex gap-10 min-w-max pb-10">
                    @php
                        $kanbanStates = [
                            'Pendiente' => ['color' => 'amber', 'label' => 'Sin Asignar'],
                            'En revisión' => ['color' => 'indigo', 'label' => 'En Evaluación'],
                            'Devuelta' => ['color' => 'rose', 'label' => 'Rechazadas'],
                            'Aprobada' => ['color' => 'teal', 'label' => 'Pendiente Cierre'],
                            'Completada' => ['color' => 'emerald', 'label' => 'Finalizadas']
                        ];
                    @endphp

                    @foreach($kanbanStates as $estado => $meta)
                    <div class="w-[380px] flex flex-col gap-8">
                        <!-- Column Header -->
                        <div class="flex items-center justify-between px-4">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-{{ $meta['color'] }}-500 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-{{ $meta['color'] }}-100">
                                    <span class="text-sm font-black">{{ isset($tablero[$estado]) ? $tablero[$estado]->count() : 0 }}</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 tracking-tight">{{ $meta['label'] }}</h3>
                                    <p class="text-[8px] font-black text-slate-400 uppercase tracking-widest">{{ $estado }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Card List -->
                        <div class="flex flex-col gap-5">
                            @if(isset($tablero[$estado]))
                                @foreach($tablero[$estado] as $sol)
                                <div class="group bg-white p-6 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all cursor-default relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-{{ $meta['color'] }}-50 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    
                                    <div class="relative z-10">
                                        <div class="flex justify-between items-center mb-6">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-3 py-1 bg-slate-50 rounded-lg">{{ $sol->codigo_solicitud }}</span>
                                            <div class="w-3 h-3 rounded-full {{ $sol->priority_color_simple }} shadow-lg"></div>
                                        </div>

                                        <h4 class="text-sm font-black text-slate-900 mb-1 tracking-tight">{{ $sol->nombre_completo }}</h4>
                                        <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-6">{{ $sol->tipoSolicitud->nombre }}</p>

                                        <div class="flex flex-col gap-1 mb-6">
                                            <div class="flex items-center gap-2">
                                                <i class="ph ph-clock-countdown text-indigo-500 text-xs"></i>
                                                <span class="text-[10px] font-black text-slate-900">hace {{ $sol->created_at->diffForHumans(null, true) }}</span>
                                            </div>
                                            <div class="w-full h-1 bg-slate-50 rounded-full overflow-hidden">
                                                @php
                                                    $days = $sol->created_at->diffInDays(now());
                                                    $agingWidth = min(($days / 15) * 100, 100); // Gradiente visual basado en 15 días
                                                @endphp
                                                <div class="h-full {{ $days > 10 ? 'bg-rose-500' : ($days > 5 ? 'bg-amber-500' : 'bg-emerald-500') }} transition-all" style="width: {{ $agingWidth }}%"></div>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                                            <div class="flex -space-x-3">
                                                <img src="{{ $sol->solicitante->avatar_url }}" class="w-10 h-10 rounded-2xl border-4 border-white shadow-xl" title="Creado por {{ $sol->solicitante->name }}">
                                                @if($sol->asignado)
                                                    <img src="{{ $sol->asignado->avatar_url }}" class="w-10 h-10 rounded-2xl border-4 border-white shadow-xl object-cover" title="Asignado a {{ $sol->asignado->name }}">
                                                @endif
                                            </div>
                                            <a href="{{ route('afiliacion.show', $sol) }}" class="w-12 h-12 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-inner">
                                                <i class="ph ph-arrow-right text-xl"></i>
                                            </a>
                                        </div>
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
            <!-- RESTRUCTURED TACTICAL NEXUS (Folio & Priority Focus) -->
            <div class="px-6 py-6 bg-slate-50/40" x-data="{ selected: [] }">
                <!-- MASS ACTIONS OVERLAY (Hidden if no selection, though user asked to remove Sel, I'll keep the logic if needed for later but remove the checkbox) -->

                <!-- HEADERS -->
                <div class="grid grid-cols-12 gap-4 px-10 mb-4 opacity-30">
                    <div class="col-span-2 text-[8px] font-black uppercase tracking-widest"># Solicitud</div>
                    <div class="col-span-3 text-[8px] font-black uppercase tracking-widest">Información del Afiliado</div>
                    <div class="col-span-1 text-[8px] font-black uppercase tracking-widest text-center">Prioridad</div>
                    <div class="col-span-2 text-[8px] font-black uppercase tracking-widest text-center">Estado</div>
                    <div class="col-span-2 text-[8px] font-black uppercase tracking-widest">Responsable</div>
                    <div class="col-span-1 text-[8px] font-black uppercase tracking-widest">Antigüedad</div>
                    <div class="col-span-1 text-right text-[8px] font-black uppercase tracking-widest">Acc.</div>
                </div>

                <!-- CARD LIST CONTAINER -->
                <div class="space-y-2">
                    @forelse($solicitudes as $solicitud)
                    <div class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/30 transition-all duration-300 p-4 grid grid-cols-12 gap-4 items-center">
                        
                        <!-- FOLIO / CODIGO -->
                        <div class="col-span-2">
                            <div class="bg-slate-50 border border-slate-100 px-3 py-2 rounded-xl inline-flex flex-col">
                                <span class="text-[9px] font-black text-indigo-600 leading-none">#{{ $solicitud->codigo_solicitud }}</span>
                                <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter mt-1">Expediente</span>
                            </div>
                        </div>

                        <!-- TITLE & INFO -->
                        <div class="col-span-3 flex items-center gap-4">
                            <div class="shrink-0 relative">
                                <div class="w-10 h-10 rounded-xl overflow-hidden border border-slate-100 shadow-sm">
                                    <img src="{{ $solicitud->solicitante->avatar_url }}" class="w-full h-full object-cover">
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white shadow-sm"></div>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <h4 class="text-xs font-black text-slate-900 tracking-tight truncate leading-none mb-1">{{ $solicitud->nombre_completo }}</h4>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[9px] font-bold text-slate-400">{{ $solicitud->cedula }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-tighter truncate">{{ $solicitud->institucion_pension ?: ($solicitud->empresa ?: 'Particular') }}</span>
                                    <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                                    <span class="text-[8px] font-bold text-indigo-500 uppercase tracking-tighter">{{ $solicitud->tipoSolicitud->nombre }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- PRIORITY -->
                        <div class="col-span-1 flex justify-center">
                            <div class="flex flex-col items-center gap-1">
                                <div class="w-3 h-3 rounded-full {{ $solicitud->priority_color_simple }} shadow-lg ring-2 ring-white"></div>
                                <span class="text-[8px] font-black text-slate-400 uppercase">{{ $solicitud->prioridad }}</span>
                            </div>
                        </div>

                        <!-- STATUS BADGE -->
                        <div class="col-span-2 flex justify-center">
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border {{ $solicitud->status_color }} bg-white/50 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                <span class="text-[9px] font-black uppercase tracking-widest leading-none">{{ $solicitud->status_label }}</span>
                            </div>
                        </div>

                        <!-- ASSIGNEE -->
                        <div class="col-span-2">
                            @if($solicitud->asignado)
                            <div class="flex items-center gap-3">
                                <img src="{{ $solicitud->asignado->avatar_url }}" class="w-8 h-8 rounded-xl border-2 border-white shadow-md object-cover">
                                <div class="flex flex-col">
                                    <span class="text-[9px] font-black text-slate-700 uppercase leading-none">{{ explode(' ', $solicitud->asignado->name)[0] }}</span>
                                    <span class="text-[7px] font-black text-slate-400 uppercase tracking-tighter">Analista</span>
                                </div>
                            </div>
                            @else
                            <div class="flex items-center gap-2 text-slate-200">
                                <div class="w-8 h-8 rounded-xl border border-slate-100 border-dashed flex items-center justify-center">
                                    <i class="ph ph-user text-[10px]"></i>
                                </div>
                                <span class="text-[7px] font-black uppercase tracking-widest">Pendiente</span>
                            </div>
                            @endif
                        </div>

                        <!-- TIME AGING -->
                        <div class="col-span-1 flex flex-col gap-0.5">
                            <span class="text-[9px] font-black text-slate-900 uppercase">hace {{ $solicitud->created_at->diffForHumans(null, true) }}</span>
                            <div class="w-full h-0.5 bg-slate-100 rounded-full overflow-hidden">
                                @php
                                    $days = $solicitud->created_at->diffInDays(now());
                                    $agingWidth = min(($days / 15) * 100, 100);
                                @endphp
                                <div class="h-full {{ $days > 10 ? 'bg-rose-500' : ($days > 5 ? 'bg-amber-500' : 'bg-emerald-500') }} transition-all" style="width: {{ $agingWidth }}%"></div>
                            </div>
                        </div>

                        <!-- ACTION LINK -->
                        <div class="col-span-1 flex justify-end">
                            <a href="{{ route('afiliacion.show', $solicitud) }}" class="w-9 h-9 bg-slate-900 text-white rounded-xl flex items-center justify-center hover:bg-indigo-600 transition-all shadow-md group/link">
                                <i class="ph ph-arrow-right text-base group-hover:translate-x-0.5 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl border border-slate-100 p-16 text-center">
                        <p class="text-xs font-black text-slate-300 uppercase tracking-widest">Sin expedientes activos</p>
                    </div>
                    @endforelse
                </div>
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
    /* Animación de pulso para casos urgentes */
    @keyframes subtle-pulse {
        0% { box-shadow: inset 6px 0 0 #e11d48; }
        50% { box-shadow: inset 12px 0 20px -5px rgba(225,29,72,0.2); }
        100% { box-shadow: inset 6px 0 0 #e11d48; }
    }
    
    .animate-urgent {
        animation: subtle-pulse 2s infinite ease-in-out;
    }

    /* Personalización del sistema de paginación */
    .pagination {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
    }
    .page-item .page-link {
        border-radius: 18px;
        font-weight: 900;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.15em;
        border: 1px solid #f1f5f9;
        padding: 1rem 1.5rem;
        color: #64748b;
        background: white;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .page-item.active .page-link {
        background: #0f172a;
        color: white;
        border-color: #0f172a;
        shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .page-item:hover .page-link:not(.active) {
        background: #f8fafc;
        color: #1e293b;
        border-color: #e2e8f0;
        transform: translateY(-1px);
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

