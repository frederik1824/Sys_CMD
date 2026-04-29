@extends('layouts.app')

@section('content')
    <script>
        function traspasosDashboard() {
            return {
                showModal: false,
                selectedTraspaso: { id: null, nombre: '', fecha_efectivo: '', periodo_efectivo: '', cantidad_dependientes: 0 },
                
                openEdit(traspaso) {
                    this.selectedTraspaso = {
                        id: traspaso.id,
                        nombre: traspaso.nombre,
                        fecha_efectivo: traspaso.fecha_efectivo_raw || '',
                        periodo_efectivo: traspaso.periodo_efectivo || '',
                        cantidad_dependientes: traspaso.cantidad_dependientes || 0
                    };
                    this.showModal = true;
                },

                async emitirCarnet(traspasoId) {
                    const result = await Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Deseas enviar este registro al módulo de carnetización?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0f172a',
                        cancelButtonColor: '#94a3b8',
                        confirmButtonText: 'Sí, enviar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true,
                        customClass: {
                            popup: 'rounded-[32px]',
                            confirmButton: 'rounded-2xl px-6 py-3 uppercase text-[10px] font-black tracking-widest',
                            cancelButton: 'rounded-2xl px-6 py-3 uppercase text-[10px] font-black tracking-widest'
                        }
                    });

                    if (!result.isConfirmed) return;
                    
                    try {
                        const url = "{{ route('traspasos.emitir-carnet', ':id') }}".replace(':id', traspasoId);
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        
                        if (data.success) {
                            await Swal.fire({
                                title: '¡Completado!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#0f172a',
                                customClass: { popup: 'rounded-[32px]' }
                            });
                            window.location.reload();
                        } else {
                            Swal.fire({
                                title: 'Aviso',
                                text: data.message,
                                icon: 'warning',
                                confirmButtonColor: '#0f172a',
                                customClass: { popup: 'rounded-[32px]' }
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al procesar la solicitud. Verifica que existan periodos de corte activos.',
                            icon: 'error',
                            confirmButtonColor: '#0f172a',
                            customClass: { popup: 'rounded-[32px]' }
                        });
                    }
                },

                async saveEnrichment() {
                    const response = await fetch(`/traspasos/${this.selectedTraspaso.id}/enriquecer`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            fecha_efectivo: this.selectedTraspaso.fecha_efectivo,
                            periodo_efectivo: this.selectedTraspaso.periodo_efectivo,
                            cantidad_dependientes: this.selectedTraspaso.cantidad_dependientes
                        })
                    });
                    if (response.ok) {
                        window.location.reload();
                    }
                }
            }
        }
    </script>
<div class="p-8 max-w-[1600px] mx-auto" x-data="traspasosDashboard()">

    <!-- HEADER -->
    <div class="flex justify-between items-end mb-10">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tighter text-slate-900">Bandeja de Traspasos</h1>
            <p class="text-slate-500 font-medium mt-1 text-lg">Seguimiento operativo y control de efectividad mensual.</p>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('traspasos.export', request()->all()) }}" class="bg-white text-slate-900 border border-slate-200 rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <i class="ph ph-download-simple text-lg"></i> Exportar Datos
            </a>
            <a href="{{ route('traspasos.import') }}" class="bg-slate-900 text-white rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center gap-2 shadow-xl shadow-slate-200">
                <i class="ph ph-upload-simple text-lg"></i> Importar Reporte
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <i class="ph ph-files text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Generados ({{ now()->translatedFormat('M') }})</span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ number_format($stats['generados_mes']) }}</div>
            <div class="mt-2 text-[10px] font-bold text-slate-400">Total Histórico: {{ number_format($stats['total']) }}</div>
        </div>
        
        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm border-l-4 border-l-emerald-500">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                    <i class="ph ph-check-circle text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Efectivos (Total)</span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ number_format($stats['efectivos']) }}</div>
            <div class="mt-2 text-[10px] font-bold text-emerald-600">{{ $stats['total'] > 0 ? round(($stats['efectivos'] / $stats['total']) * 100, 1) : 0 }}% Éxito Global</div>
        </div>

        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm relative overflow-hidden group">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <i class="ph ph-calendar-check text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Efectivos (Mes)</span>
            </div>
            <div class="flex justify-between items-end mb-2">
                <div class="text-3xl font-black text-slate-900">{{ number_format($stats['efectivos_mes']) }}</div>
                @if($stats['meta_mes'] > 0)
                    <div class="text-[10px] font-black text-blue-600 uppercase">{{ round(($stats['efectivos_mes'] / $stats['meta_mes']) * 100, 1) }}% de Meta</div>
                @endif
            </div>
            @if($stats['meta_mes'] > 0)
                <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600 transition-all duration-1000" style="width: {{ min(($stats['efectivos_mes'] / $stats['meta_mes']) * 100, 100) }}%"></div>
                </div>
                <div class="mt-2 text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Meta: {{ number_format($stats['meta_mes']) }} Traspasos</div>
            @else
                <div class="mt-2 text-[10px] font-bold text-blue-600 italic">Sin meta definida</div>
            @endif
        </div>

        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600">
                    <i class="ph ph-x-circle text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Rechazados</span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ number_format($stats['rechazados']) }}</div>
            <div class="mt-2 text-[10px] font-bold text-rose-600">{{ $stats['total'] > 0 ? round(($stats['rechazados'] / $stats['total']) * 100, 1) : 0 }}% Tasa Rechazo</div>
        </div>

        <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                    <i class="ph ph-warning-circle text-2xl"></i>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Pendientes Datos</span>
            </div>
            <div class="text-3xl font-black text-slate-900">{{ number_format($stats['pendientes_datos']) }}</div>
            <div class="mt-2 text-[10px] font-bold text-amber-600">Requieren Atención</div>
        </div>
    </div>

    <!-- TREND CHART SECTION -->
    <div class="bg-white p-10 rounded-[40px] border border-slate-100 shadow-xl mb-10">
        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-8 flex items-center gap-2">
            <i class="ph-fill ph-chart-line-up text-blue-500 text-lg"></i> Tendencia de Producción (Últimos 6 meses)
        </h3>
        <div class="h-[300px]">
            <canvas id="productionChart"></canvas>
        </div>
    </div>

    <!-- ADVANCED DASHBOARD SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
        <!-- Ranking Agentes -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-6 flex items-center gap-2">
                <i class="ph-fill ph-trophy text-amber-500 text-lg"></i> Top 10 Agentes
            </h3>
            <div class="space-y-6">
                @foreach($rankingAgentes as $ag)
                    <div class="space-y-2">
                        <div class="flex justify-between items-end">
                            <span class="text-[11px] font-black text-slate-800 uppercase">{{ $ag->agente }}</span>
                            <span class="text-[10px] font-black text-slate-400">{{ $ag->efectivos }} / {{ $ag->total }} Efectivos</span>
                        </div>
                        <div class="h-2 w-full bg-slate-50 rounded-full overflow-hidden flex">
                            <div class="h-full bg-blue-500 transition-all duration-500" style="width: {{ ($ag->total / $stats['total']) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Rendimiento por Equipo -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-6 flex items-center gap-2">
                <i class="ph-fill ph-users-three text-blue-500 text-lg"></i> Rendimiento por Equipo
            </h3>
            <div class="space-y-6">
                @foreach($rankingEquipos as $eq)
                    <div class="p-4 rounded-3xl bg-slate-50/50 border border-slate-100 hover:border-blue-200 transition-all">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-black text-slate-900">{{ $eq->nombre }}</span>
                            <span class="text-xl font-black text-blue-600">{{ $eq->total_solicitudes }}</span>
                        </div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">Solicitudes Totales</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Motivos de Rechazo -->
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-xl">
            <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-6 flex items-center gap-2">
                <i class="ph-fill ph-warning-octagon text-rose-500 text-lg"></i> Principales Motivos Rechazo
            </h3>
            <div class="space-y-4">
                @foreach($motivosRechazo as $motivo)
                    <div class="flex items-center gap-4">
                        <div class="shrink-0 w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 font-black text-xs">
                            {{ $motivo->total }}
                        </div>
                        <p class="text-[11px] font-bold text-slate-600 leading-tight uppercase">{{ $motivo->motivos_estado }}</p>
                    </div>
                @endforeach
                @if($motivosRechazo->isEmpty())
                    <p class="text-xs font-bold text-slate-400 italic py-10 text-center">No hay motivos de rechazo registrados.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm mb-8">
        <form method="GET" action="{{ route('traspasos.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="relative">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cédula, nombre o EPBD..." class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
            </div>
            
            <select name="supervisor" class="bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                <option value="all">Todos los Supervisores</option>
                @foreach($supervisores as $sup)
                    <option value="{{ $sup->id }}" {{ request('supervisor') == $sup->id ? 'selected' : '' }}>{{ $sup->nombre }}</option>
                @endforeach
            </select>

            <select name="agente" class="bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                <option value="all">Todos los Agentes</option>
                @foreach($agentes as $agente)
                    <option value="{{ $agente }}" {{ request('agente') == $agente ? 'selected' : '' }}>{{ $agente }}</option>
                @endforeach
            </select>

            <select name="estado" class="bg-slate-50 border-none rounded-2xl px-6 py-3.5 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                <option value="all">Todos los Estados</option>
                @foreach($estados as $estado)
                    <option value="{{ $estado }}" {{ request('estado') == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-primary text-white rounded-2xl px-6 py-3.5 text-[10px] font-black uppercase tracking-widest hover:bg-secondary transition-all">
                Filtrar Resultados
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden mb-10">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Afiliado</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Solicitud Unipago</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Agente / Equipo</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Estado Unipago</th>
                    <th class="px-8 py-6 text-[11px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">Enriquecimiento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($traspasos as $traspaso)
                    <tr class="group hover:bg-slate-50/50 transition-all duration-300">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 group-hover:text-primary transition-colors uppercase">{{ $traspaso->nombre_afiliado }}</span>
                                <span class="text-[11px] font-bold text-slate-400">{{ $traspaso->cedula_afiliado }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-blue-600">{{ $traspaso->numero_solicitud_epbd }}</span>
                                <span class="text-[11px] font-bold text-slate-400">{{ $traspaso->fecha_solicitud ? $traspaso->fecha_solicitud->format('d/m/Y') : 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-slate-700 uppercase">{{ $traspaso->agente }}</span>
                                @php
                                    $agenteModel = \App\Models\AgenteTraspaso::where('nombre', $traspaso->agente)->first();
                                @endphp
                                @if($agenteModel)
                                    <span class="text-[10px] font-bold text-slate-400 uppercase italic">Eq. {{ $agenteModel->supervisor->nombre }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col gap-1">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter w-fit
                                    {{ str_contains($traspaso->estado, 'EN') ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 
                                       (str_contains($traspaso->estado, 'RE') ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-amber-50 text-amber-600 border border-amber-100') }}">
                                    {{ $traspaso->estado }}
                                </span>
                                @if(str_contains($traspaso->estado, 'RE') && $traspaso->motivos_estado)
                                    <span class="text-[10px] font-medium text-slate-400 italic max-w-[200px] leading-tight">{{ $traspaso->motivos_estado }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Efectivo:</span>
                                    <span class="text-xs font-black {{ $traspaso->fecha_efectivo ? 'text-emerald-600' : 'text-slate-300 italic' }}">
                                        {{ $traspaso->fecha_efectivo ? $traspaso->fecha_efectivo->format('d/m/Y') : 'Pendiente' }}
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Periodo:</span>
                                    <span class="text-xs font-black {{ $traspaso->periodo_efectivo ? 'text-blue-600' : 'text-slate-300 italic' }}">
                                        {{ $traspaso->periodo_efectivo ?? '---' }}
                                    </span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Dep:</span>
                                    <span class="text-xs font-black {{ $traspaso->cantidad_dependientes > 0 ? 'text-slate-800' : 'text-slate-300' }}">
                                        {{ $traspaso->cantidad_dependientes }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($traspaso->fecha_efectivo)
                                        @if($traspaso->es_emitido)
                                            <span class="px-2 py-1 bg-blue-50 text-blue-600 text-[9px] font-black uppercase rounded-md flex items-center gap-1 border border-blue-100">
                                                <i class="ph-fill ph-check-circle"></i> Emitido
                                            </span>
                                        @else
                                            <button @click="emitirCarnet({{ $traspaso->id }})" 
                                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" 
                                                    title="Enviar a Carnetización">
                                                <i class="ph ph-identification-card text-lg"></i>
                                            </button>
                                        @endif
                                    @endif
                                    <button @click="openEdit({
                                        id: {{ $traspaso->id }},
                                        nombre: '{{ addslashes($traspaso->nombre_afiliado) }}',
                                        fecha_efectivo_raw: '{{ $traspaso->fecha_efectivo ? $traspaso->fecha_efectivo->format('Y-m-d') : '' }}',
                                        periodo_efectivo: '{{ $traspaso->periodo_efectivo }}',
                                        cantidad_dependientes: {{ $traspaso->cantidad_dependientes }}
                                    })" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all">
                                        <i class="ph ph-pencil-simple-line text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $traspasos->links() }}
    </div>

    <!-- MODAL PREMIUM -->
    <div x-show="showModal" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="showModal = false"></div>

        <!-- Modal Content -->
        <div class="bg-white w-full max-w-lg rounded-[40px] shadow-2xl relative z-10 overflow-hidden transform"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-8">
            
            <div class="p-8">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-[0.2em] text-blue-600 mb-1">Enriquecer Registro</h3>
                        <p class="text-2xl font-black text-slate-900 tracking-tighter uppercase" x-text="selectedTraspaso.nombre"></p>
                    </div>
                    <button @click="showModal = false" class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="ph ph-x text-xl font-bold"></i>
                    </button>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Fecha de Efectividad</label>
                        <div class="relative">
                            <i class="ph ph-calendar absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="date" x-model="selectedTraspaso.fecha_efectivo" 
                                   class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Periodo Operativo (Mes)</label>
                        <div class="relative">
                            <i class="ph ph-calendar-check absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="month" x-model="selectedTraspaso.periodo_efectivo" 
                                   class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 block">Cantidad de Dependientes</label>
                        <div class="relative">
                            <i class="ph ph-users absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="number" x-model="selectedTraspaso.cantidad_dependientes" min="0"
                                   class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-10">
                    <button @click="showModal = false" class="bg-slate-100 text-slate-600 rounded-2xl py-4 text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                        Cancelar
                    </button>
                    <button @click="saveEnrichment()" class="bg-blue-600 text-white rounded-2xl py-4 text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 flex items-center justify-center gap-2">
                        <i class="ph ph-floppy-disk text-lg"></i> Guardar Datos
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('productionChart').getContext('2d');
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Generados',
                        data: chartData.generados,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Efectivos',
                        data: chartData.efectivos,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#10b981'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 11,
                                weight: '800',
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        cornerRadius: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0,0,0,0.03)'
                        },
                        ticks: {
                            font: { size: 11, weight: '600' }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11, weight: '600' }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
