@extends('layouts.app')
@section('content')
    <div class="p-8 space-y-6">
        <!-- Page Header & Bulk Actions -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface">
                    @if(!$segment)
                        Módulo de Asignaciones (Pendientes)
                    @elseif($segment === 'CMD')
                        Afiliados CMD (Asignados)
                    @else
                        Afiliados Otras Empresas (Asignados)
                    @endif
                </h1>
                <p class="text-on-surface-variant text-[0.875rem] mt-1">Gestión y seguimiento del proceso de carnetización.</p>
            </div>
            <div class="flex items-center gap-3">
                <div id="bulk-actions-wrapper" class="hidden animate-in fade-in zoom-in duration-300">
                    <div class="flex items-center bg-primary/5 p-1 rounded-xl border border-primary/20 shadow-sm">
                        <button type="button" onclick="openAssignModal()" class="px-4 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white rounded-lg transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">person_add</span> Asignar
                        </button>
                        <div class="w-[1px] h-4 bg-primary/20 mx-1"></div>
                        <button type="button" onclick="openStatusModal()" class="px-4 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white rounded-lg transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">sync</span> Estado
                        </button>
                        <div class="w-[1px] h-4 bg-primary/20 mx-1"></div>
                        <button type="button" onclick="openCompanyModal()" class="px-4 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white rounded-lg transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">domain</span> Empresa
                        </button>
                    </div>
                </div>
                <form action="{{ route('carnetizacion.afiliados.sanitize') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-500 font-bold text-xs rounded-xl transition-all shadow-sm flex items-center gap-2" title="Normaliza abreviaturas en direcciones (C/ -> Calle, No. -> #)">
                        <span class="material-symbols-outlined text-sm">cleaning_services</span>
                        Normalizar Direcciones
                    </button>
                </form>
                <a href="{{ route('carnetizacion.afiliados.export', request()->all()) }}" class="px-5 py-2.5 bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg hover:bg-slate-800 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Exportar XLSX
                </a>
                <button type="button" onclick="confirmGlobalSync()" class="bg-primary/10 text-primary border border-primary/20 px-5 py-2.5 rounded-xl text-sm font-semibold flex items-center gap-2 hover:bg-primary hover:text-white transition-all shadow-sm">
                    <span class="material-symbols-outlined text-lg">sync</span>
                    Sincronizar
                </button>
                <form id="globalSyncForm" action="{{ route('carnetizacion.afiliados.sync_firebase') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <a href="{{ route('carnetizacion.afiliados.create', ['segment' => $segment]) }}" class="bg-primary text-white px-5 py-2.5 rounded-xl shadow-lg shadow-primary/20 text-sm font-semibold flex items-center gap-2 hover:bg-blue-800 transition-colors">
                    <span class="material-symbols-outlined text-lg">add</span> Nuevo
                </a>
            </div>
        </div>

        @if(isset($statsPorPeriodo) && $statsPorPeriodo->count() > 0)
        <!-- Panel de Avance por Periodo -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($statsPorPeriodo as $stat)
            <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-[0.6rem] font-black uppercase text-slate-400 tracking-wider">{{ $stat->nombre }}</span>
                    <span class="text-xs font-bold text-primary">{{ $stat->porcentaje }}%</span>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-lg font-black text-slate-800">{{ $stat->total }}</p>
                        <p class="text-[0.6rem] text-slate-400 font-bold uppercase">Total</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-amber-600">{{ $stat->pendiente }}</p>
                        <p class="text-[0.6rem] text-slate-400 font-bold uppercase">Pendiente</p>
                    </div>
                </div>
                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: {{ $stat->porcentaje }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Quick Filters (NUEVO SEMANA 2) -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-px">
            <a href="{{ route(Route::currentRouteName()) }}" 
               class="px-6 py-3 text-xs font-bold uppercase tracking-wider transition-all border-b-2 {{ !request('estado_id') && !request('sla_critical') ? 'border-blue-600 text-blue-700 bg-blue-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                Todos
            </a>
            <a href="{{ request()->fullUrlWithQuery(['estado_id' => 7, 'sla_critical' => null]) }}" 
               class="px-6 py-3 text-xs font-bold uppercase tracking-wider transition-all border-b-2 {{ request('estado_id') == 7 ? 'border-amber-500 text-amber-700 bg-amber-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                Pendientes Validación
            </a>
            <a href="{{ request()->fullUrlWithQuery(['estado_id' => null, 'sla_critical' => 1]) }}" 
               class="px-6 py-3 text-xs font-bold uppercase tracking-wider transition-all border-b-2 {{ request('sla_critical') == 1 ? 'border-rose-500 text-rose-700 bg-rose-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                SLA Crítico
            </a>
            <a href="{{ request()->fullUrlWithQuery(['estado_id' => 8, 'sla_critical' => null]) }}" 
               class="px-6 py-3 text-xs font-bold uppercase tracking-wider transition-all border-b-2 {{ request('estado_id') == 8 ? 'border-emerald-500 text-emerald-700 bg-emerald-50/50' : 'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-50' }}">
                Completados
            </a>
        </div>

        <!-- Filter Bar -->
        <form id="filterForm" method="GET" action="{{ route(Route::currentRouteName()) }}" class="bg-surface-container-low p-4 rounded-xl flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px] relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por Nombre / Cédula" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 focus:ring-2 ring-blue-500/10">
            </div>

            <div class="flex-1 min-w-[200px] relative">
                <select name="estado_id" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Estado: Todos</option>
                    @foreach($estados as $est)
                        <option value="{{ $est->id }}" {{ request('estado_id') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <div class="flex-1 min-w-[200px] relative">
                <select name="corte_id" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Corte: Todos</option>
                    @foreach($cortes as $c)
                        <option value="{{ $c->id }}" {{ request('corte_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>


            <div class="flex-1 min-w-[200px] relative">
                <input type="text" name="rnc_empresa" value="{{ request('rnc_empresa') }}" list="empresas_filter_list" placeholder="Empresa / RNC" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 focus:ring-2 ring-blue-500/10">
                <datalist id="empresas_filter_list">
                    @foreach($empresas as $emp)
                        <option value="{{ $emp->rnc }}">{{ $emp->nombre }}</option>
                    @endforeach
                </datalist>
            </div>

            <div class="flex-1 min-w-[120px] relative">
                <select name="sexo" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Sexo: Todos</option>
                    <option value="M" {{ request('sexo') == 'M' ? 'selected' : '' }}>Masculino</option>
                    <option value="F" {{ request('sexo') == 'F' ? 'selected' : '' }}>Femenino</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <div class="flex-1 min-w-[200px] relative">
                <select name="lote_id" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Lote: Todos</option>
                    @foreach($lotes as $lote)
                        <option value="{{ $lote->id }}" {{ request('lote_id') == $lote->id ? 'selected' : '' }}>{{ $lote->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <div class="flex-1 min-w-[150px] relative">
                <select name="reasignado" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Auditoría: Todos</option>
                    <option value="1" {{ request('reasignado') == '1' ? 'selected' : '' }}>Reasignados</option>
                    <option value="0" {{ request('reasignado') == '0' ? 'selected' : '' }}>Originales</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">repeat</span>
            </div>

            <div class="flex-1 min-w-[150px] relative">
                <select name="company_status" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10">
                    <option value="">Empresa: Todos</option>
                    <option value="none" {{ request('company_status') == 'none' ? 'selected' : '' }}>Sin Empresa</option>
                    <option value="assigned" {{ request('company_status') == 'assigned' ? 'selected' : '' }}>Con Empresa</option>
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">domain_disabled</span>
            </div>

            <button type="submit" class="bg-primary text-white p-2.5 rounded-lg hover:bg-primary-container transition-colors">
                <span class="material-symbols-outlined text-xl">search</span>
            </button>
            <a href="{{ route(Route::currentRouteName()) }}" class="bg-surface-container-high text-on-surface-variant p-2.5 rounded-lg hover:bg-slate-200 transition-colors">
                <span class="material-symbols-outlined text-xl">clear_all</span>
            </a>
        </form>

        <!-- Table Container -->
        <div id="tableContainer" class="bg-white rounded-xl overflow-hidden shadow-sm border border-slate-200 transition-opacity duration-300">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50">
                            <th class="py-3 px-6 border-b border-slate-200 dark:border-slate-700">
                                <input id="selectAll" class="rounded text-blue-600 focus:ring-blue-500 border-slate-300 w-4 h-4 cursor-pointer" type="checkbox"/>
                            </th>
                            <th class="py-3 px-2 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nombre', 'direction' => request('sort') === 'nombre' && request('direction') === 'asc' ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-blue-700 transition-colors">
                                    Afiliado @if(request('sort') === 'nombre') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                                </a>
                            </th>
                            <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500">
                                Contrato
                            </th>
                            <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500 w-32">RNC</th>
                            <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500">
                                Empresa
                            </th>
                            <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500">Corte</th>
                            <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500">
                                Responsable
                            </th>
                            <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500 text-center">
                                Entrega
                            </th>
                            <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500 text-center">Docs</th>
                            <th class="py-3 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500">
                                Estado
                            </th>
                            <th class="py-3 px-6 border-b border-slate-200 dark:border-slate-700 text-[0.65rem] font-bold tracking-wider uppercase text-slate-500 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-50 dark:divide-slate-800/50 transition-opacity duration-300">
                        <!-- Skeleton Rows (Hidden by default) -->
                        <template id="skeleton-row">
                            <tr class="animate-pulse border-b border-slate-100">
                                <td class="py-2 px-6"><div class="w-4 h-4 bg-slate-100 rounded"></div></td>
                                <td class="py-2 px-2">
                                    <div class="space-y-1">
                                        <div class="h-3 bg-slate-100 rounded w-3/4 skeleton"></div>
                                        <div class="h-2 bg-slate-50 rounded w-1/2 skeleton"></div>
                                    </div>
                                </td>
                                <td class="py-2 px-4"><div class="h-3 bg-slate-50 rounded w-2/3 skeleton"></div></td>
                                <td class="py-2 px-4"><div class="h-3 bg-slate-50 rounded w-24 skeleton"></div></td>
                                <td class="py-2 px-4"><div class="h-3 bg-slate-50 rounded w-32 skeleton"></div></td>
                                <td class="py-2 px-4"><div class="h-3 bg-slate-50 rounded w-16 skeleton"></div></td>
                                <td class="py-2 px-4"><div class="h-3 bg-slate-50 rounded w-24 skeleton"></div></td>
                                <td class="py-2 px-4"><div class="h-3 bg-slate-50 rounded w-20 skeleton"></div></td>
                                <td class="py-2 px-4"><div class="h-3 bg-slate-50 rounded w-12 skeleton"></div></td>
                                <td class="py-2 px-4"><div class="h-5 bg-slate-50 rounded-full w-20 skeleton"></div></td>
                                <td class="py-2 px-6 text-right"><div class="h-6 bg-slate-50 rounded-lg w-16 ml-auto skeleton"></div></td>
                            </tr>
                        </template>

                        @forelse($afiliados as $afiliado)
                        <tr class="hover:bg-blue-50/30 transition-all border-b border-slate-100 last:border-0">
                            <td class="py-2 px-6">
                                <input name="selected[]" value="{{ $afiliado->uuid }}" class="rounded text-blue-600 focus:ring-blue-500 border-slate-300 w-4 h-4 cursor-pointer affiliate-checkbox" type="checkbox"/>
                            </td>
                            <td class="py-2 px-2">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col">
                                        <a href="{{ route('carnetizacion.afiliados.show', $afiliado) }}" class="flex items-center gap-1 group/name">
                                            <span class="text-[0.8rem] font-bold text-slate-800 group-hover:text-blue-700 transition-colors">{{ $afiliado->nombre_completo }}</span>
                                            @if($afiliado->sexo)
                                                <span class="material-symbols-outlined text-[12px] {{ $afiliado->sexo === 'M' ? 'text-blue-500' : 'text-pink-500' }}">
                                                    {{ $afiliado->sexo === 'M' ? 'male' : 'female' }}
                                                </span>
                                            @endif
                                        </a>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[0.75rem] text-slate-500">{{ $afiliado->cedula }}</span>
                                            @if($afiliado->reasignado)
                                                <span class="px-1.5 py-0.5 bg-rose-50 text-rose-500 rounded text-[9px] font-black uppercase tracking-tighter border border-rose-100 flex items-center gap-0.5 shadow-sm" title="Registro Reasignado">
                                                    <span class="material-symbols-outlined text-[11px]">repeat</span> R
                                                </span>
                                            @endif
                                            
                                            {{-- Data Quality Mini Bar --}}
                                            @php $quality = $afiliado->data_quality; @endphp
                                            <div class="flex items-center gap-1 min-w-[60px] ml-2" title="Calidad del Expediente: {{ $quality->score }}%">
                                                <div class="flex-1 h-1 bg-slate-100 rounded-full overflow-hidden border border-slate-200/50">
                                                    <div class="h-full bg-{{ $quality->color }}-500" style="width: {{ $quality->score }}%"></div>
                                                </div>
                                                <span class="text-[0.5rem] font-black text-{{ $quality->color }}-600 uppercase tracking-tighter">{{ $quality->score }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-2 px-4">
                                <span class="text-[0.75rem] font-bold text-slate-800">{{ $afiliado->contrato ?? 'N/A' }}</span>
                            </td>
                            <td class="py-2 px-4 w-32">
                                <span class="text-[0.65rem] font-mono font-bold text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200">{{ $afiliado->rnc_empresa ?? '----------' }}</span>
                            </td>
                            <td class="py-2 px-4">
                                @if($afiliado->empresa_id)
                                    <a href="{{ route('sistema.empresas.show', $afiliado->empresaModel) }}" class="text-[0.75rem] font-bold text-blue-700 hover:underline">
                                        {{ Str::limit($afiliado->empresaModel->nombre ?? $afiliado->empresa, 25) }}
                                    </a>
                                @else
                                    <span class="text-[0.75rem] font-medium text-slate-600">{{ Str::limit($afiliado->empresa ?? 'N/A', 25) }}</span>
                                @endif
                            </td>
                            <td class="py-2 px-4">
                                <span class="text-[0.7rem] font-bold text-slate-500">{{ $afiliado->corte->nombre ?? 'N/A' }}</span>
                            </td>
                            <td class="py-2 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-[0.7rem] text-slate-600 font-bold">{{ Str::limit($afiliado->responsable->nombre ?? 'Sin asignar', 15) }}</span>
                                </div>
                            </td>
                            <td class="py-2 px-4">
                                <div class="flex flex-col items-center justify-center">
                                    @if($afiliado->fecha_entrega_safesure)
                                        <div class="flex items-center gap-1.5">
                                            @php
                                                $sla = $afiliado->sla_status;
                                            @endphp
                                            <div class="w-2 h-2 rounded-full bg-{{ $sla->color() }}-500"></div>
                                            <span class="text-[0.7rem] font-bold text-on-surface" title="{{ $sla->label() }}">{{ $afiliado->fecha_entrega_safesure->format('d/m/y') }}</span>
                                        </div>
                                        <span class="text-[0.6rem] text-slate-500 uppercase font-bold tracking-tighter">{{ $afiliado->dias_transcurridos }} Días</span>
                                    @else
                                        <span class="text-[0.65rem] text-slate-400 italic">No entregado</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @php
                                        $docs = $afiliado->evidenciasAfiliado;
                                        $hasAcuse = $docs->where('tipo_documento', 'acuse_recibo')->count() > 0;
                                        $hasForm = $docs->where('tipo_documento', 'formulario_firmado')->count() > 0;
                                    @endphp
                                    <span class="material-symbols-outlined text-[1.2rem] {{ $hasAcuse ? 'text-primary' : 'text-slate-200' }}" title="Acuse">description</span>
                                    <span class="material-symbols-outlined text-[1.2rem] {{ $hasForm ? 'text-primary' : 'text-slate-200' }}" title="Formulario">assignment_turned_in</span>
                                </div>
                            </td>
                            <td class="py-2 px-4">
                                <span class="status-badge px-2.5 py-1 rounded-full text-[0.6875rem] font-bold border {{ $afiliado->status_color_class }} uppercase transition-all">
                                    {{ $afiliado->estado->nombre ?? 'Pendiente' }}
                                </span>
                            </td>
                            <td class="py-2 px-6 text-right">
                                <div class="action-buttons flex items-center justify-end gap-1 opacity-20 group-hover:opacity-100 transition-opacity">
                                    <button type="button" onclick="syncSingle('{{ $afiliado->uuid }}', this)" class="p-1.5 text-slate-400 hover:text-blue-600 transition-all" title="Sincronizar">
                                        <span class="material-symbols-outlined text-[1.1rem]">sync</span>
                                    </button>
                                    @if(strtolower($afiliado->estado?->nombre) !== 'completado')
                                    <button type="button" onclick="confirmAcuse('{{ $afiliado->uuid }}', '{{ $afiliado->nombre_completo }}', this)" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Acuse">
                                        <span class="material-symbols-outlined text-[1.1rem]">assignment_late</span>
                                    </button>
                                    @endif
                                    @if($afiliado->estado?->nombre !== 'Completado' && $afiliado->estado_id != 7)
                                    <button type="button" onclick="quickComplete('{{ $afiliado->uuid }}', '{{ $afiliado->nombre_completo }}', this)" class="p-1.5 text-slate-400 hover:text-emerald-600 transition-colors" title="Completar">
                                        <span class="material-symbols-outlined text-[1.1rem]">check_circle</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">
                                No se encontraron afiliados según los filtros aplicados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 flex flex-col justify-between border-t border-slate-50 dark:border-slate-800 bg-surface-container-low/50 dark:bg-slate-800/30">
                {{ $afiliados->links() }}
            </div>
        </div>
        
        <!-- Tablero de Resumen (Bento) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Summary Card -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm flex flex-col justify-between border border-slate-100 dark:border-slate-800">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[0.6875rem] font-bold tracking-widest uppercase text-slate-400">Completado Global</span>
                        <span class="material-symbols-outlined text-primary-container">fact_check</span>
                    </div>
                    {{-- TODO Lógica de Progreso Dashboard --}}
                    <h3 class="text-3xl font-bold text-primary">0%</h3>
                    <p class="text-xs text-on-surface-variant mt-1">Afiliados con documentación validada.</p>
                </div>
                <div class="mt-6 w-full bg-surface-container-high h-1.5 rounded-full overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: 0%;"></div>
                </div>
            </div>

            <!-- Assignment Pulse -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[0.6875rem] font-bold tracking-widest uppercase text-slate-400">Pendientes</span>
                    <span class="material-symbols-outlined text-amber-500">bolt</span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-medium text-on-surface dark:text-slate-300">Asignados Activos</span>
                        </div>
                        <span class="text-xs font-bold text-on-surface dark:text-slate-200">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-medium text-on-surface dark:text-slate-300">Sin Asignar</span>
                        </div>
                        <span class="text-xs font-bold text-on-surface dark:text-slate-200">0</span>
                    </div>
                </div>
            </div>

            <!-- Quick Action CTA Card -->
            <div class="bg-primary-container p-6 rounded-2xl shadow-sm text-white relative overflow-hidden group">
                <div class="relative z-10">
                    <h4 class="text-lg font-bold mb-2">Cierre de Documentos</h4>
                    <p class="text-blue-100 text-xs mb-6">Módulo para carga y validación de acuses y formularios físicos.</p>
                    <a href="{{ route('cierre.index') }}" class="bg-white text-primary px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-800 hover:text-white transition-all inline-block">Ir a Módulo</a>
                </div>
                <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-white/10 text-8xl group-hover:scale-110 transition-transform">folder_zip</span>
            </div>
        </div>

    </div>

    <!-- Modal para Asignar Responsable -->
    <div id="assignModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form method="POST" action="{{ route('carnetizacion.afiliados.bulk_assign') }}" id="assignForm" class="bg-surface-container-lowest p-6 rounded-2xl shadow-lg w-full max-w-md border border-slate-100 dark:border-slate-800">
            @csrf
            @if(isset($segment))
                <input type="hidden" name="segment" value="{{ $segment }}">
            @endif
            <h3 class="text-xl font-bold mb-4 text-on-surface">Asignar Responsable</h3>
            <p class="text-sm text-slate-500 mb-6">Seleccione el responsable a asignar para los afiliados seleccionados (<span id="selectedCountDisplay">0</span>).</p>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Responsable</label>
                <select name="responsable_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Seleccione uno...</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}">{{ $resp->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Aquí se inyectarán los inputs hidden con los IDs seleccionados -->
            <div id="hiddenSelectedInputs"></div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 hover:bg-slate-100 rounded-lg text-slate-600 font-semibold text-sm transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-container transition-colors shadow-sm">Confirmar Asignación</button>
            </div>
        </form>
    </div>

    <!-- Modal para Cambiar Estado Masivo -->
    <div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form method="POST" action="{{ route('carnetizacion.afiliados.bulk_status') }}" id="statusForm" class="bg-surface-container-lowest p-6 rounded-2xl shadow-lg w-full max-w-md border border-slate-100 dark:border-slate-800">
            @csrf
            @if(isset($segment))
                <input type="hidden" name="segment" value="{{ $segment }}">
            @endif
            <h3 class="text-xl font-bold mb-4 text-on-surface">Cambiar Estado</h3>
            <p class="text-sm text-slate-500 mb-6">Seleccione el nuevo estado para los afiliados seleccionados (<span id="statusCountDisplay">0</span>).</p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nuevo Estado</label>
                <select name="estado_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Seleccione uno...</option>
                    @foreach($estados as $est)
                        <option value="{{ $est->id }}">{{ $est->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Acción Rápida (Opcional)</label>
                <select name="motivo_rapido" class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">-- Personalizar observación --</option>
                    <option value="Documentos recibidos físicamente">Documentos recibidos físicamente</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Observación Adicional (Opcional)</label>
                <textarea name="observacion" rows="2" placeholder="Notas adicionales..." class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm"></textarea>
            </div>

            <div id="hiddenStatusSelectedInputs"></div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 hover:bg-slate-100 rounded-lg text-slate-600 font-semibold text-sm transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-container transition-colors shadow-sm">Confirmar Cambio</button>
            </div>
        </form>
    </div>

    <!-- Modal para Asignar Empresa Masivo -->
    <div id="companyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form method="POST" action="{{ route('carnetizacion.afiliados.bulk_company') }}" id="companyForm" class="bg-surface-container-lowest p-6 rounded-2xl shadow-lg w-full max-w-md border border-slate-100 dark:border-slate-800">
            @csrf
            @if(isset($segment))
                <input type="hidden" name="segment" value="{{ $segment }}">
            @endif
            <h3 class="text-xl font-bold mb-4 text-on-surface">Asignar Empresa</h3>
            <p class="text-sm text-slate-500 mb-6">Seleccione la empresa a asignar para los afiliados seleccionados (<span id="companyCountDisplay">0</span>).</p>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Empresa</label>
                <select name="empresa_id" id="bulk_empresa_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Seleccione una...</option>
                    @foreach($empresas as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nombre }} (RNC: {{ $emp->rnc }})</option>
                    @endforeach
                </select>
            </div>

            <div id="hiddenCompanySelectedInputs"></div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeCompanyModal()" class="px-4 py-2 hover:bg-slate-100 rounded-lg text-slate-600 font-semibold text-sm transition-colors">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-container transition-colors shadow-sm">Confirmar Empresa</button>
            </div>
        </form>
    </div>

    <script>
        // --- Persistencia de Selección (Global) ---
        const selectedIds = new Set();
        let bulkActionsWrapper;

        function updateBulkActionsVisibility() {
            if (!bulkActionsWrapper) bulkActionsWrapper = document.getElementById('bulk-actions-wrapper');
            if (selectedIds.size > 0) {
                bulkActionsWrapper.classList.remove('hidden');
            } else {
                bulkActionsWrapper.classList.add('hidden');
            }
        }

        function syncCheckboxes() {
            const checkboxes = document.querySelectorAll('.affiliate-checkbox');
            let allCheckedInView = checkboxes.length > 0;
            
            checkboxes.forEach(cb => {
                const id = cb.value;
                if (selectedIds.has(id)) {
                    cb.checked = true;
                } else {
                    cb.checked = false;
                    allCheckedInView = false;
                }
            });

            const selectAll = document.getElementById('selectAll');
            if (selectAll) selectAll.checked = allCheckedInView && checkboxes.length > 0;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('filterForm');
            const tableBody = document.getElementById('tableBody');
            const tableContainer = document.getElementById('tableContainer');
            bulkActionsWrapper = document.getElementById('bulk-actions-wrapper');
            let timeout = null;

            const ESTADO_COMPLETADO_ID = {{ $estados->where('nombre', 'Completado')->first()->id ?? 9 }};

            function showSkeletons() {
                tableBody.style.opacity = '0';
                setTimeout(() => {
                    tableBody.innerHTML = '';
                    for (let i = 0; i < 8; i++) {
                        tableBody.appendChild(skeletonTemplate.content.cloneNode(true));
                    }
                    tableBody.style.opacity = '1';
                }, 150);
            }

            function fetchResults(urlParam = null) {
                let url;
                if(urlParam) {
                    url = new URL(urlParam);
                } else {
                    url = new URL(form.action);
                    const formData = new FormData(form);
                    const searchParams = new URLSearchParams(formData);
                    url.search = searchParams.toString();
                }

                showSkeletons();

                fetch(url, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest' 
                    }
                })
                .then(response => response.text())
                .then(html => {
                    setTimeout(() => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContent = doc.getElementById('tableContainer').innerHTML;
                        
                        tableContainer.style.opacity = '0';
                        setTimeout(() => {
                            tableContainer.innerHTML = newContent;
                            tableContainer.style.opacity = '1';
                            // Re-apply transitions class to new content
                            document.querySelector('#tableBody')?.classList.add('page-transition');
                            window.history.replaceState({}, '', url);
                            syncCheckboxes();
                        }, 150);
                    }, 300); // Artificial delay for smooth skeleton visibility
                });
            }

            form.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => fetchResults(), 500); 
            });

            form.addEventListener('change', (e) => {
                if(e.target.tagName === 'SELECT') {
                    clearTimeout(timeout);
                    fetchResults();
                }
            });

            // Handle pagination dynamically
            tableContainer.addEventListener('click', function(e) {
                const link = e.target.closest('nav[role="navigation"] a, .pagination a');
                if (link) {
                    e.preventDefault();
                    fetchResults(link.href);
                }
            });

            // Delegación de eventos para checkboxes (Atrapa cambios en tabla dinámica)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.id === 'selectAll') {
                    const checkboxes = document.querySelectorAll('.affiliate-checkbox');
                    checkboxes.forEach(cb => {
                        cb.checked = e.target.checked;
                        if (cb.checked) selectedIds.add(cb.value);
                        else selectedIds.delete(cb.value);
                    });
                    updateBulkActionsVisibility();
                }

                if (e.target && e.target.classList.contains('affiliate-checkbox')) {
                    const id = e.target.value;
                    if (e.target.checked) selectedIds.add(id);
                    else selectedIds.delete(id);
                    
                    syncCheckboxes();
                    updateBulkActionsVisibility();
                }
            });

            // Initialize Tom Select for Bulk Company Modal
            new TomSelect('#bulk_empresa_id', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                placeholder: 'Escriba para buscar empresa...'
            });
        });

        // --- Acciones de Cambio Masivo ---
        function clearSelection() {
            selectedIds.clear();
            syncCheckboxes();
            updateBulkActionsVisibility();
        }

        function openAssignModal() {
            if(selectedIds.size === 0) return;

            document.getElementById('selectedCountDisplay').innerText = selectedIds.size;
            const hiddenInputsContainer = document.getElementById('hiddenSelectedInputs');
            hiddenInputsContainer.innerHTML = '';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });

            document.getElementById('assignModal').classList.remove('hidden');
            document.getElementById('assignModal').classList.add('flex');
        }

        function openStatusModal() {
            if(selectedIds.size === 0) return;

            document.getElementById('statusCountDisplay').innerText = selectedIds.size;
            const hiddenInputsContainer = document.getElementById('hiddenStatusSelectedInputs');
            hiddenInputsContainer.innerHTML = '';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });

            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusModal').classList.add('flex');
        }

        // Acciones Individuales
        function quickComplete(uuid, name, btn) {
            Swal.fire({
                title: 'Finalizar Carnetización',
                html: `<p class="text-sm">¿Estás seguro de marcar como <strong>Completado</strong> a:<br><span class="text-primary font-bold">${name}</span>?</p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, Completar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = btn.closest('tr');
                    row.style.opacity = '0.5';

                    fetch(`/afiliados/${uuid}/estado_single`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            estado_id: {{ $estados->where('nombre', 'Completado')->first()->id ?? 9 }},
                            motivo_rapido: 'Finalizado mediante acción rápida desde el listado.'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        row.style.opacity = '1';
                        if (data.success) {
                            const badge = row.querySelector('.status-badge');
                            if (badge) {
                                badge.className = 'status-badge px-2.5 py-1 rounded-full text-[0.6875rem] font-bold border border-emerald-100 bg-emerald-50 text-emerald-600 uppercase';
                                badge.innerText = 'Completado';
                            }
                            row.querySelector('.action-buttons').innerHTML = '<span class="text-emerald-500 material-symbols-outlined">check_circle</span>';
                            Swal.fire({ icon: 'success', title: 'Completado', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                        }
                    });
                }
            });
        }

        function openCompanyModal() {
            if(selectedIds.size === 0) return;

            document.getElementById('companyCountDisplay').innerText = selectedIds.size;
            const hiddenInputsContainer = document.getElementById('hiddenCompanySelectedInputs');
            hiddenInputsContainer.innerHTML = '';

            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });

            document.getElementById('companyModal').classList.remove('hidden');
            document.getElementById('companyModal').classList.add('flex');
        }

        function closeCompanyModal() {
            document.getElementById('companyModal').classList.add('hidden');
            document.getElementById('companyModal').classList.remove('flex');
        }

        function closeAssignModal() {
            document.getElementById('assignModal').classList.add('hidden');
            document.getElementById('assignModal').classList.remove('flex');
        }

        function confirmReceipt(uuid, name, btn) {
            Swal.fire({
                title: 'Confirmar Recepción Final',
                html: `<p class="text-sm">¿Confirmas que has recibido físicamente <strong>el formulario firmado</strong> y carnet de:<br><span class="text-primary font-bold">${name}</span>?<br><br><span class="text-xs text-slate-500">Esta acción cerrará el expediente como Completado.</span></p>`,
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, Finalizar Entrega',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = btn.closest('tr');
                    row.style.opacity = '0.5';
                    row.style.pointerEvents = 'none';

                    fetch(`/afiliados/${uuid}/confirm-receipt`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            row.style.opacity = '1';
                            row.style.pointerEvents = 'auto';
                            // Actualizar badge de estado
                            const badge = row.querySelector('.status-badge');
                            if (badge) {
                                badge.className = 'status-badge px-2.5 py-1 rounded-full text-[0.6875rem] font-bold border border-emerald-100 bg-emerald-50 text-emerald-600 uppercase';
                                badge.innerText = 'Completado';
                            }
                            // Ocultar botones de acción innecesarios
                            row.querySelector('.action-buttons').innerHTML = '<span class="text-emerald-500 material-symbols-outlined">check_circle</span>';
                            
                            Swal.fire({ icon: 'success', title: 'Completado', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                        } else {
                            row.style.opacity = '1';
                            row.style.pointerEvents = 'auto';
                            Swal.fire('Error', data.error || 'No se pudo procesar', 'error');
                        }
                    });
                }
            });
        }

        function confirmAcuse(uuid, name, btn) {
            Swal.fire({
                title: 'Marcar Acuse de Recibo',
                html: `<p class="text-sm">¿Confirmas que has recibido el <strong>Acuse de Recibo</strong> (pero no el formulario) de:<br><span class="text-primary font-bold">${name}</span>?<br><br><span class="text-xs text-slate-500">El estado cambiará a 'Acuse recibido' pero el expediente seguirá abierto.</span></p>`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Confirmar solo Acuse',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    const row = btn.closest('tr');
                    row.style.opacity = '0.5';

                    fetch(`/afiliados/${uuid}/confirm-acuse`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        row.style.opacity = '1';
                        if (data.success) {
                            const badge = row.querySelector('.status-badge');
                            if (badge) {
                                badge.className = 'status-badge px-2.5 py-1 rounded-full text-[0.6875rem] font-bold border border-blue-100 bg-blue-50 text-blue-600 uppercase';
                                badge.innerText = 'Acuse recibido';
                            }
                            // Ocultar botón de acuse
                            btn.remove();
                            Swal.fire({ icon: 'success', title: 'Acuse Recibido', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                        }
                    });
                }
            });
        }

        function syncSingle(uuid, btn) {
            const icon = btn.querySelector('.material-symbols-outlined');
            icon.classList.add('animate-spin');
            btn.disabled = true;

            fetch(`/afiliados/${uuid}/sync_single`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                icon.classList.remove('animate-spin');
                btn.disabled = false;
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sincronizado',
                        text: `Estado actualizado: ${data.estado}`,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    // Actualizar UI localmente
                    const row = btn.closest('tr');
                    const badge = row.querySelector('.status-badge');
                    if (badge) {
                        badge.innerText = data.estado;
                        // Podríamos actualizar colores dinámicamente aquí si fuera necesario
                    }
                } else {
                    Swal.fire('Error', data.error || 'No se pudo sincronizar', 'error');
                }
            })
            .catch(error => {
                icon.classList.remove('animate-spin');
                btn.disabled = false;
                Swal.fire('Error', 'Error de conexión', 'error');
            });
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusModal').classList.remove('flex');
        }

        function confirmGlobalSync() {
            Swal.fire({
                title: '¿Iniciar Sincronización?',
                text: "Esta acción consultará los cambios recientes en Firebase. No es necesaria para búsquedas locales.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, Sincronizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('globalSyncForm').submit();
                }
            });
        }
    </script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('sync-progress-container');
        const progressBar = document.getElementById('sync-progress-bar');
        const percentageText = document.getElementById('sync-percentage');
        const labelText = document.getElementById('sync-label');
        let pollInterval = null;

        function checkProgress() {
            fetch('{{ route('carnetizacion.afiliados.sync_progress') }}')
                .then(response => response.json())
                .then(data => {
                    if (data.active) {
                        container.classList.remove('hidden');
                        progressBar.style.width = data.progress + '%';
                        percentageText.textContent = data.progress + '%';
                        labelText.textContent = data.label;
                        
                        if (!pollInterval) {
                            startPolling();
                        }
                    } else if (data.progress >= 100 || !data.active) {
                        if (container.classList.contains('hidden')) return;
                        
                        progressBar.style.width = '100%';
                        percentageText.textContent = '100%';
                        labelText.textContent = 'Sincronización Completada';
                        
                        setTimeout(() => {
                            container.classList.add('hidden');
                            window.location.reload(); // Refresh to see new data
                        }, 2000);
                        
                        stopPolling();
                    }
                })
                .catch(error => console.error('Error polling sync status:', error));
        }

        function startPolling() {
            if (pollInterval) return;
            pollInterval = setInterval(checkProgress, 2000);
        }

        function stopPolling() {
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        }

        // Check on load if a sync is already active
        checkProgress();

        // Also check when clicking the sync forms
        const syncForm = document.querySelector('form[action*="sync-firebase"]');
        if (syncForm) {
            syncForm.addEventListener('submit', function() {
                setTimeout(checkProgress, 1000); // Wait a bit for the job to be dispatched
            });
        }
    });
</script>
@endpush
