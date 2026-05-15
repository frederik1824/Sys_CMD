@extends('layouts.app')
@section('title', 'Executive Intelligence Hub - ARS CMD')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --accent-primary: #0f172a;
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.5);
    }
    
    body { 
        font-family: 'Plus Jakarta Sans', sans-serif; 
        background-color: #f1f5f9; 
        background-image: 
            radial-gradient(at 0% 0%, hsla(215,100%,98%,1) 0, transparent 50%), 
            radial-gradient(at 100% 0%, hsla(210,100%,97%,1) 0, transparent 50%),
            radial-gradient(at 50% 100%, hsla(220,100%,98%,1) 0, transparent 50%);
        background-attachment: fixed;
        color: #1e293b;
        letter-spacing: -0.01em;
        font-size: 0.875rem;
    }
    
    h1, h2, h3, h4, h5, h6, .font-heading { font-family: 'Outfit', sans-serif; letter-spacing: -0.02em; }
    
    .premium-header {
        background: linear-gradient(to bottom, rgba(255,255,255,0.95), rgba(255,255,255,0.85));
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    }
    
    .tab-trigger {
        position: relative;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .tab-trigger.active {
        color: white;
        background: #0f172a;
        box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.15);
        transform: translateY(-1px);
    }
    .tab-trigger:not(.active):hover {
        background: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }
    
    .luxury-card {
        background: white;
        border-radius: 2rem;
        border: 1px solid rgba(226, 232, 240, 0.6);
        box-shadow: 0 10px 40px -15px rgba(0,0,0,0.02);
        overflow: hidden;
    }

    .reveal-content {
        animation: revealContent 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes revealContent {
        from { opacity: 0; transform: translateY(15px); filter: blur(5px); }
        to { opacity: 1; transform: translateY(0); filter: blur(0); }
    }

    .status-pulse {
        width: 6px; height: 6px; border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    
    .hide-scrollbar::-webkit-scrollbar { display: none; }
    .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Progressive Bar Customization */
    .bar-luxury {
        height: 6px;
        background: #f1f5f9;
        border-radius: 999px;
        position: relative;
        overflow: hidden;
    }
    .bar-luxury-inner {
        height: 100%;
        border-radius: 999px;
        transition: width 1.5s cubic-bezier(0.16, 1, 0.3, 1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen">
    <!-- Immersive Compact Header -->
    <header class="premium-header sticky top-0 z-50 px-6 lg:px-10 py-4">
        <div class="max-w-[1700px] mx-auto flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="w-12 h-12 bg-slate-900 rounded-[1.2rem] flex items-center justify-center shadow-lg transition-all duration-500 group-hover:rotate-6">
                        <i class="ph-fill ph-shield-star text-2xl text-blue-400"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 status-pulse"></div>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-[9px] font-black text-blue-600 uppercase tracking-[0.3em]">Nexus OS v6.3</span>
                        <span class="text-[9px] font-black text-slate-300 uppercase tracking-[0.3em]">Executive Hub</span>
                    </div>
                    <h1 class="text-2xl font-heading font-black text-slate-900 tracking-tight leading-none">CMD Intelligence Hub</h1>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <div class="bg-white/60 p-1.5 rounded-2xl border border-slate-200/40 shadow-sm backdrop-blur-md flex items-center">
                    <form id="period-form" action="{{ route('reportes.executive') }}" method="GET" class="flex items-center gap-0.5">
                        @foreach(['today' => 'Hoy', 'week' => '7D', 'month' => 'Mes', 'year' => 'Año'] as $key => $label)
                            <button type="submit" name="period" value="{{ $key }}" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 {{ $period === $key ? 'bg-slate-900 text-white shadow-md' : 'text-slate-400 hover:text-slate-900 hover:bg-white' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </form>
                </div>
                
                <a href="{{ route('reportes.executive.export.pdf', ['period' => $period]) }}" class="h-10 px-6 bg-slate-900 text-white font-black rounded-xl shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-2 text-[10px] uppercase tracking-[0.2em]">
                    <i class="ph-bold ph-lightning text-lg"></i>
                    PDF
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-[1700px] mx-auto px-6 lg:px-10 py-8">
        
        <!-- Welcome Summary Compact -->
        <div class="mb-10 reveal-content">
            <div class="flex flex-col lg:flex-row items-end justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-heading font-black text-slate-900 mb-2 tracking-tight">Estatus Estratégico</h2>
                    <p class="text-sm text-slate-500 font-medium max-w-xl leading-relaxed">
                        Bienvenido, Directora. El ecosistema mantiene una eficiencia del <span class="text-slate-900 font-black">94.2%</span>.
                    </p>
                </div>
                <div class="flex items-center gap-4 bg-white/40 px-5 py-3 rounded-2xl border border-white/60 backdrop-blur-lg">
                    <div class="text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Sincronización</p>
                        <p class="text-xs font-black text-slate-900">{{ now()->format('H:i') }} • Online</p>
                    </div>
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="ph-duotone ph-arrows-clockwise text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compact Navigation -->
        <div class="flex justify-center mb-12">
            <nav class="flex overflow-x-auto hide-scrollbar gap-1.5 bg-white/40 p-1.5 rounded-[1.5rem] border border-white/60 shadow-sm backdrop-blur-2xl">
                <button onclick="switchTab('resumen')" class="tab-trigger active px-7 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] whitespace-nowrap" data-target="resumen">General</button>
                @foreach(['cmd' => 'Carnets', 'afiliacion' => 'Afiliación', 'traspasos' => 'Traspasos', 'dispersion' => 'Dispersión', 'pss' => 'Médicos', 'callcenter' => 'Monitoreo de Cartera', 'asistencia' => 'RRHH'] as $key => $label)
                    @if($permissions[$key])
                        <button onclick="switchTab('{{ $key }}')" class="tab-trigger px-7 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] whitespace-nowrap" data-target="{{ $key }}">{{ $label }}</button>
                    @endif
                @endforeach
            </nav>
        </div>

        @if(count($data['alertas']) > 0)
            <div class="mb-10 grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($data['alertas'] as $alerta)
                    <div class="bg-white p-5 rounded-2xl border-l-8 {{ $alerta['type'] === 'danger' ? 'border-rose-500' : 'border-amber-500' }} shadow-sm flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $alerta['type'] === 'danger' ? 'bg-rose-50 text-rose-500' : 'bg-amber-50 text-amber-500' }} shrink-0">
                            <i class="ph-bold {{ $alerta['type'] === 'danger' ? 'ph-warning-octagon' : 'ph-lightning' }} text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-heading font-black text-slate-900 text-sm tracking-tight mb-1">{{ $alerta['title'] }}</h4>
                            <p class="text-[11px] text-slate-500 leading-tight">{{ $alerta['message'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Tab Contents -->
        <div class="tab-contents">
            
            <!-- Tab: RESUMEN -->
            <div id="tab-resumen" class="tab-pane block space-y-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @include('reportes.executive_v4.components.kpi-card', ['title' => 'Afiliaciones', 'value' => number_format($data['resumen']['afiliados']['value']), 'variation' => $data['resumen']['afiliados']['variation'], 'icon' => 'ph-user-plus', 'color' => 'blue'])
                    @include('reportes.executive_v4.components.kpi-card', ['title' => 'Comercial', 'value' => number_format($data['resumen']['traspasos']['value']), 'variation' => $data['resumen']['traspasos']['variation'], 'icon' => 'ph-handshake', 'color' => 'indigo'])
                    @include('reportes.executive_v4.components.kpi-card', ['title' => 'Solicitudes', 'value' => number_format($data['resumen']['solicitudes']['value']), 'variation' => $data['resumen']['solicitudes']['variation'], 'icon' => 'ph-chart-bar-horizontal', 'color' => 'emerald'])
                    @include('reportes.executive_v4.components.kpi-card', ['title' => 'Personal', 'value' => $data['resumen']['asistencia']['value'], 'unit' => 'P.', 'variation' => 0, 'icon' => 'ph-identification-card', 'color' => 'slate'])
                </div>

                <div class="luxury-card p-10 bg-white">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                        <div>
                            <h3 class="text-xl font-heading font-black text-slate-900 tracking-tight">Macro-Tendencias Operativas</h3>
                            <p class="text-xs text-slate-400 font-medium">Análisis consolidado semestral</p>
                        </div>
                    </div>
                    <div id="chart-global-trend" class="w-full h-[400px]"></div>
                </div>
            </div>

            <!-- Tab: DISPERSION -->
            <div id="tab-dispersion" class="tab-pane hidden space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="luxury-card p-10 text-center bg-slate-50">
                        <span class="text-slate-400 font-black mb-2 uppercase tracking-[0.2em] text-[9px] block">Monto Dispersado Anual</span>
                        <span class="text-4xl font-heading font-black text-slate-900 tracking-tight tabular-nums">RD$ {{ number_format($data['dispersion']['monto_total'], 2) }}</span>
                    </div>
                    <div class="luxury-card p-10 text-center">
                        <span class="text-slate-400 font-black mb-2 uppercase tracking-[0.2em] text-[9px] block">Titulares Activos</span>
                        <span class="text-4xl font-heading font-black text-slate-900 tracking-tight tabular-nums">{{ number_format($data['dispersion']['titulares']) }}</span>
                    </div>
                </div>

                <div class="luxury-card border-slate-100">
                    <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                        <h3 class="text-sm font-heading font-black text-slate-800 uppercase tracking-widest">Auditoría de Fondos por Periodo</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-[9px] font-black uppercase tracking-[0.2em] text-slate-400 border-b border-slate-100 bg-slate-50/30">
                                    <th class="px-8 py-5">Ciclo</th>
                                    <th class="px-8 py-5 text-center">C1 (Afil)</th>
                                    <th class="px-8 py-5 text-right">C1 (Monto)</th>
                                    <th class="px-8 py-5 text-center">C2 (Afil)</th>
                                    <th class="px-8 py-5 text-right">C2 (Monto)</th>
                                    <th class="px-8 py-5 text-right text-slate-900">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-[13px]">
                                @forelse($data['dispersion']['desglose'] as $row)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-8 py-4 font-black text-slate-800">{{ $row['mes'] }} {{ $row['anio'] }}</td>
                                    <td class="px-8 py-4 text-center text-slate-500 font-medium">{{ number_format($row['corte_1_afiliados']) }}</td>
                                    <td class="px-8 py-4 text-right text-slate-600 font-bold tabular-nums">RD$ {{ number_format($row['corte_1_monto'], 2) }}</td>
                                    <td class="px-8 py-4 text-center text-slate-500 font-medium">{{ number_format($row['corte_2_afiliados']) }}</td>
                                    <td class="px-8 py-4 text-right text-slate-600 font-bold tabular-nums">RD$ {{ number_format($row['corte_2_monto'], 2) }}</td>
                                    <td class="px-8 py-4 text-right">
                                        <span class="bg-slate-900 text-white px-3 py-1 rounded-lg font-black tabular-nums text-xs">
                                            RD$ {{ number_format($row['total_monto'], 2) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="px-8 py-20 text-center text-slate-300 italic">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab: CARNETS -->
            <div id="tab-cmd" class="tab-pane hidden space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="luxury-card p-10 text-center">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[9px] block mb-4">Tasa de Entrega</span>
                        <span class="text-5xl font-heading font-black text-slate-900 leading-none tabular-nums">{{ $data['carnetizacion']['tasa_entrega'] }}%</span>
                    </div>
                    <div class="luxury-card p-10 text-center">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[9px] block mb-4">Pendientes</span>
                        <span class="text-5xl font-heading font-black text-amber-500 leading-none tabular-nums">{{ number_format($data['carnetizacion']['pendientes_recepcion']) }}</span>
                    </div>
                    <div class="luxury-card p-10 text-center border-rose-100">
                        <span class="text-rose-500 font-black uppercase tracking-[0.2em] text-[9px] block mb-4">Críticos SLA</span>
                        <span class="text-5xl font-heading font-black text-rose-600 leading-none tabular-nums">{{ number_format($data['carnetizacion']['criticos_sla']) }}</span>
                    </div>
                </div>

                <div class="luxury-card p-10">
                    <h3 class="text-sm font-heading font-black text-slate-800 uppercase tracking-widest mb-10 text-center">Distribución de Estatus</h3>
                    <div id="chart-carnets-dist" class="w-full h-[350px]"></div>
                </div>
            </div>

            <!-- Tab: AFILIACION -->
            <div id="tab-afiliacion" class="tab-pane hidden space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="luxury-card p-10 text-center">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[9px] block mb-4">Aprobación</span>
                        <span class="text-5xl font-heading font-black text-emerald-600 leading-none">{{ $data['afiliacion']['tasa_aprobacion'] }}%</span>
                    </div>
                    <div class="luxury-card p-10 text-center">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[9px] block mb-4">SLA Promedio</span>
                        <span class="text-5xl font-heading font-black text-slate-900 leading-none">{{ $data['afiliacion']['sla_horas'] }}h</span>
                    </div>
                    <div class="luxury-card p-10 text-center">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[9px] block mb-4">Devueltas</span>
                        <span class="text-5xl font-heading font-black text-amber-500 leading-none">{{ number_format($data['afiliacion']['devueltas']) }}</span>
                    </div>
                </div>

                <div class="luxury-card p-10">
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($data['afiliacion']['distribucion_estados'] as $estado => $count)
                            <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-center">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">{{ $estado }}</span>
                                <span class="text-2xl font-heading font-black text-slate-900 tabular-nums">{{ number_format($count) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tab: TRASPASOS -->
            <div id="tab-traspasos" class="tab-pane hidden space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="luxury-card p-12 text-center bg-indigo-50/5">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] block mb-4">Hit Rate</span>
                        <span class="text-6xl font-heading font-black text-indigo-600 leading-none">{{ $data['traspasos']['hit_rate'] }}%</span>
                        <p class="text-xs font-bold text-slate-400 mt-6 uppercase tracking-widest">{{ number_format($data['traspasos']['efectivos']) }} Efectivos</p>
                    </div>
                    <div class="luxury-card p-12 text-center">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] block mb-4">Rechazos</span>
                        <span class="text-6xl font-heading font-black text-rose-500 leading-none">{{ number_format($data['traspasos']['rechazados']) }}</span>
                    </div>
                </div>

                <div class="luxury-card p-10">
                    <h3 class="text-sm font-heading font-black text-slate-800 uppercase tracking-widest mb-10 text-center">Tendencia de Efectividad</h3>
                    <div id="chart-traspasos-tendencia" class="w-full h-[350px]"></div>
                </div>
            </div>

            <!-- Tab: PSS -->
            <div id="tab-pss" class="tab-pane hidden space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="luxury-card p-12 text-center">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] block mb-4">Centros Activos</span>
                        <span class="text-6xl font-heading font-black text-slate-900 leading-none">{{ number_format($data['pss']['centros_activos']) }}</span>
                    </div>
                    <div class="luxury-card p-12 text-center bg-emerald-50/5">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] block mb-4">Médicos Activos</span>
                        <span class="text-6xl font-heading font-black text-emerald-600 leading-none">{{ number_format($data['pss']['medicos_activos']) }}</span>
                    </div>
                </div>

                <div class="luxury-card p-10">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-lg font-heading font-black text-slate-900 tracking-tight">Médicos por Especialidad</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-6">
                        @foreach($data['pss']['especialidades'] as $index => $esp)
                            @php 
                                $max = $data['pss']['especialidades']->first()->value;
                                $percent = ($esp->value / $max) * 100;
                            @endphp
                            <div class="group">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-bold text-slate-700 tracking-tight">{{ $esp->label }}</span>
                                    <span class="text-sm font-black text-slate-900 tabular-nums">{{ number_format($esp->value) }}</span>
                                </div>
                                <div class="bar-luxury">
                                    <div class="bar-luxury-inner bg-slate-900" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Tab: CALL CENTER -->
            <div id="tab-callcenter" class="tab-pane hidden">
                 <div class="luxury-card p-16 text-center max-w-4xl mx-auto">
                    <span class="text-slate-400 font-black uppercase tracking-[0.3em] text-[11px] block mb-8">Conversión Comercial</span>
                    <div class="flex items-center justify-center gap-2 mb-10">
                        <span class="text-7xl font-heading font-black text-slate-900 leading-none tabular-nums">{{ $data['callcenter']['tasa_conversion'] }}</span>
                        <span class="text-4xl font-black text-blue-600">%</span>
                    </div>
                    <div class="bg-slate-50 inline-flex items-center gap-6 px-8 py-5 rounded-2xl border border-slate-100">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest"><span class="text-slate-900 font-black">{{ number_format($data['callcenter']['efectivos']) }}</span> Ventas • <span class="text-slate-900 font-black">{{ number_format($data['callcenter']['gestionados']) }}</span> Gestiones</p>
                    </div>
                </div>
            </div>

            <!-- Tab: ASISTENCIA -->
            <div id="tab-asistencia" class="tab-pane hidden space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="luxury-card p-10 text-center bg-emerald-50/5">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] block mb-4">Presencia</span>
                        <span class="text-5xl font-heading font-black text-emerald-600 leading-none">{{ $data['asistencia']['porcentaje_asistencia'] }}%</span>
                    </div>
                    <div class="luxury-card p-10 text-center bg-rose-50/5">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] block mb-4">Ausentes</span>
                        <span class="text-5xl font-heading font-black text-rose-500 leading-none">{{ $data['asistencia']['ausentes'] }}</span>
                    </div>
                    <div class="luxury-card p-10 text-center bg-amber-50/5">
                        <span class="text-slate-400 font-black uppercase tracking-[0.2em] text-[10px] block mb-4">Tardanzas</span>
                        <span class="text-5xl font-heading font-black text-amber-500 leading-none">{{ $data['asistencia']['tardanzas'] }}</span>
                    </div>
                </div>

                <div class="luxury-card p-10">
                    <h3 class="text-sm font-heading font-black text-slate-800 uppercase tracking-widest mb-10 text-center">Asistencia Semanal (%)</h3>
                    <div id="chart-asistencia-tendencia" class="w-full h-[350px]"></div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    function switchTab(targetId) {
        document.querySelectorAll('.tab-pane').forEach(el => {
            el.classList.add('hidden');
            el.classList.remove('block');
        });
        document.querySelectorAll('.tab-trigger').forEach(el => el.classList.remove('active'));
        
        const target = document.getElementById('tab-' + targetId);
        target.classList.remove('hidden');
        target.classList.add('block');
        target.classList.add('reveal-content');
        
        document.querySelector(`.tab-trigger[data-target="${targetId}"]`).classList.add('active');
        
        window.dispatchEvent(new Event('resize'));
    }

    document.addEventListener("DOMContentLoaded", function() {
        const globalOptions = {
            chart: {
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            grid: { borderColor: '#f8fafc', strokeDashArray: 4 },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3, lineCap: 'round' }
        };

        // Chart: Global Trend
        new ApexCharts(document.querySelector("#chart-global-trend"), {
            ...globalOptions,
            series: [{ name: 'Carnets', data: @json($data['tendencia']['cmd']) }, { name: 'Afil.', data: @json($data['tendencia']['afil']) }, { name: 'Trasp.', data: @json($data['tendencia']['tra']) }],
            chart: { ...globalOptions.chart, type: 'area', height: 400 },
            colors: ['#0f172a', '#10b981', '#6366f1'],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.2, opacityTo: 0.05, stops: [0, 90, 100] } },
            xaxis: { categories: @json($data['tendencia']['labels']), labels: { style: { colors: '#94a3b8', fontWeight: 700, fontSize: '10px' } } },
            yaxis: { labels: { style: { colors: '#94a3b8', fontWeight: 700, fontSize: '10px' } } },
            legend: { position: 'top', horizontalAlign: 'right', fontWeight: 800, fontSize: '11px' }
        }).render();

        // Chart: Carnets
        new ApexCharts(document.querySelector("#chart-carnets-dist"), {
            ...globalOptions,
            series: @json(collect($data['carnetizacion']['estados_distribucion'])->pluck('value')),
            labels: @json(collect($data['carnetizacion']['estados_distribucion'])->pluck('label')),
            chart: { ...globalOptions.chart, type: 'donut', height: 350 },
            colors: ['#0f172a', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
            legend: { position: 'bottom', fontWeight: 700, fontSize: '10px' },
            stroke: { show: false },
            plotOptions: { pie: { donut: { size: '75%', labels: { show: true, total: { show: true, label: 'TOTAL', fontSize: '12px', fontWeight: 900 } } } } }
        }).render();

        // Chart: Traspasos
        new ApexCharts(document.querySelector("#chart-traspasos-tendencia"), {
            ...globalOptions,
            series: [{ name: 'Efectivos', data: @json($data['traspasos']['tendencia']['efectivos']) }, { name: 'Rechazados', data: @json($data['traspasos']['tendencia']['rechazados']) }],
            chart: { ...globalOptions.chart, type: 'bar', height: 350, stacked: true },
            colors: ['#0f172a', '#ef4444'],
            plotOptions: { bar: { borderRadius: 8, columnWidth: '30%' } },
            xaxis: { categories: @json($data['traspasos']['tendencia']['labels']), labels: { style: { fontWeight: 700 } } },
            legend: { position: 'top', fontWeight: 800 }
        }).render();

        // Chart: Asistencia
        new ApexCharts(document.querySelector("#chart-asistencia-tendencia"), {
            ...globalOptions,
            series: [{ name: 'Presencia %', data: @json($data['asistencia']['tendencia_semanal']['valores']) }],
            chart: { ...globalOptions.chart, type: 'line', height: 350 },
            colors: ['#10b981'],
            stroke: { curve: 'smooth', width: 4 },
            xaxis: { categories: @json($data['asistencia']['tendencia_semanal']['labels']) },
            yaxis: { max: 100, min: 0, labels: { style: { fontWeight: 700 } } },
            markers: { size: 6, strokeWidth: 3 }
        }).render();
    });
</script>
@endpush
