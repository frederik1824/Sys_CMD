@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-900 text-white p-6 lg:p-12 font-sans selection:bg-blue-500/30">
    <!-- Background Decor -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-blue-600/10 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-indigo-600/10 rounded-full blur-[120px] animate-pulse"></div>
    </div>

    <div class="relative z-10 max-w-[1600px] mx-auto">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-16">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <span class="px-4 py-1 bg-blue-500/10 border border-blue-500/20 rounded-full text-[10px] font-black uppercase tracking-[0.3em] text-blue-400">Executive Intelligence</span>
                </div>
                <h1 class="text-5xl font-black tracking-tighter mb-2">Dashboard <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Ejecutivo</span></h1>
                <p class="text-slate-400 font-medium max-w-lg">Visión consolidada de operaciones, crecimiento CRM y cumplimiento de seguridad.</p>
            </div>

            <!-- Global Health Gauge -->
            <div class="bg-white/5 backdrop-blur-xl border border-white/10 rounded-[3rem] p-8 flex items-center gap-8 shadow-2xl">
                <div class="relative w-24 h-24">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5"></circle>
                        <circle cx="48" cy="48" r="40" stroke="currentColor" stroke-width="8" fill="transparent" 
                                class="@if($healthIndex > 80) text-emerald-500 @elseif($healthIndex > 50) text-amber-500 @else text-rose-500 @endif"
                                stroke-dasharray="251.2"
                                stroke-dashoffset="{{ 251.2 * (1 - $healthIndex / 100) }}"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-2xl font-black">{{ $healthIndex }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Índice de Salud</p>
                    <p class="text-lg font-black text-white">Operación @if($healthIndex > 80) Óptima @else en Riesgo @endif</p>
                </div>
            </div>
        </div>

        <!-- Metric Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            <!-- 1. CRM Metrics -->
            @if($canSeeCallCenter)
            <div class="bg-gradient-to-br from-blue-600/20 to-transparent backdrop-blur-md border border-white/10 rounded-[2.5rem] p-8 group hover:border-blue-500/30 transition-all">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-600/30">
                        <i class="ph-duotone ph-phone-call text-2xl"></i>
                    </div>
                    <span class="text-emerald-400 text-xs font-bold">+{{ $callCenterData['tasa_conversion'] }}% Conv.</span>
                </div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Crecimiento CRM</h3>
                <p class="text-4xl font-black mb-6">{{ number_format($callCenterData['total']) }} <span class="text-lg text-slate-500 font-bold tracking-normal">Leads</span></p>
                <div class="w-full bg-white/5 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-blue-500 h-full transition-all duration-1000" style="width: {{ $callCenterData['porcentaje_gestion'] }}%"></div>
                </div>
                <p class="text-[10px] font-bold text-slate-500 mt-3">{{ $callCenterData['gestionados'] }} gestiones realizadas hoy</p>
            </div>
            @endif

            <!-- 2. Security Compliance -->
            @if($canSeeSecurity)
            <div class="bg-gradient-to-br @if($securityData['conflictos_activos'] > 0) from-rose-600/20 @else from-emerald-600/20 @endif to-transparent backdrop-blur-md border border-white/10 rounded-[2.5rem] p-8 group hover:scale-[1.02] transition-all">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-14 h-14 @if($securityData['conflictos_activos'] > 0) bg-rose-600 shadow-rose-600/30 @else bg-emerald-600 shadow-emerald-600/30 @endif rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="ph-duotone @if($securityData['conflictos_activos'] > 0) ph-shield-warning @else ph-shield-check @endif text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Seguridad (SoD)</h3>
                <p class="text-4xl font-black mb-6 text-@if($securityData['conflictos_activos'] > 0) rose @else emerald @endif-400">{{ $securityData['conflictos_activos'] }} <span class="text-lg text-slate-500 font-bold tracking-normal">Alertas</span></p>
                <p class="text-[11px] font-bold leading-tight @if($securityData['conflictos_activos'] > 0) text-rose-300 @else text-emerald-300 @endif">
                    @if($securityData['conflictos_activos'] > 0)
                        Se han detectado conflictos de segregación de funciones en los roles.
                    @else
                        Todas las jerarquías de acceso cumplen con el estándar de seguridad.
                    @endif
                </p>
            </div>
            @endif

            <!-- 3. Operation Volume (CMD) -->
            @if($canSeeCmd)
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-[2.5rem] p-8">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-14 h-14 bg-amber-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30">
                        <i class="ph-duotone ph-identification-card text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Carnetización</h3>
                <p class="text-4xl font-black mb-6">{{ number_format($cmdData['total']) }}</p>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black px-2 py-0.5 bg-amber-500/20 text-amber-500 rounded-md">{{ $cmdData['porcentaje'] }}%</span>
                    <span class="text-[10px] text-slate-500 font-bold">Producción Completada</span>
                </div>
            </div>
            @endif

            <!-- 4. Affiliation Funnel -->
            @if($canSeeAfiliacion)
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-[2.5rem] p-8">
                <div class="flex justify-between items-start mb-8">
                    <div class="w-14 h-14 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-600/30">
                        <i class="ph-duotone ph-user-plus text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Solicitudes</h3>
                <p class="text-4xl font-black mb-6">{{ number_format($afiliacionData['total']) }}</p>
                <p class="text-[10px] text-slate-500 font-bold italic">{{ $afiliacionData['hoy'] }} nuevas solicitudes recibidas hoy</p>
            </div>
            @endif
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white/5 backdrop-blur-md border border-white/10 rounded-[3rem] p-10 shadow-2xl">
                <div class="flex items-center justify-between mb-12">
                    <div>
                        <h3 class="text-xl font-black tracking-tight mb-1">Tendencia de Crecimiento Multi-App</h3>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">Comparativa de los últimos 6 meses</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            <span class="text-[10px] font-bold text-slate-400">CRM</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-amber-500 rounded-full"></span>
                            <span class="text-[10px] font-bold text-slate-400">Carnet</span>
                        </div>
                    </div>
                </div>
                <div class="h-[400px]">
                    <canvas id="mainTrendChart"></canvas>
                </div>
            </div>

            <!-- Recent Security Audit -->
            @if($canSeeSecurity)
            <div class="bg-slate-950/50 backdrop-blur-md border border-white/5 rounded-[3rem] p-10 flex flex-col shadow-2xl">
                <h3 class="text-xl font-black mb-8">Control de Accesos</h3>
                <div class="space-y-6 flex-1 overflow-y-auto no-scrollbar pr-4">
                    <div class="flex items-center gap-6 p-4 rounded-3xl bg-white/5 border border-white/5">
                        <div class="w-10 h-10 bg-blue-500/20 text-blue-500 rounded-xl flex items-center justify-center">
                            <i class="ph-bold ph-eye text-lg"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-white">Auditorías de Hoy</p>
                            <p class="text-2xl font-black">{{ $securityData['auditorias_hoy'] }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-4 ml-1">Ecosistema de Roles</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-slate-900 rounded-2xl border border-white/5">
                                <p class="text-2xl font-black text-amber-500">{{ $securityData['roles_criticos'] }}</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Perfiles Activos</p>
                            </div>
                            <div class="p-4 bg-slate-900 rounded-2xl border border-white/5">
                                <p class="text-2xl font-black text-blue-500">99.9%</p>
                                <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Uptime Seguridad</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <a href="{{ route('admin.access.audit') }}" class="mt-8 w-full py-4 bg-white/5 hover:bg-white/10 text-center rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] transition-all border border-white/5">
                    Ver Bitácora de Auditoría
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('mainTrendChart').getContext('2d');
    
    const gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
    gradientBlue.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
    gradientBlue.addColorStop(1, 'rgba(59, 130, 246, 0)');

    const gradientAmber = ctx.createLinearGradient(0, 0, 0, 400);
    gradientAmber.addColorStop(0, 'rgba(245, 158, 11, 0.3)');
    gradientAmber.addColorStop(1, 'rgba(245, 158, 11, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($tendencia['labels']) !!},
            datasets: [
                {
                    label: 'CRM',
                    data: {!! json_encode($tendencia['crm']) !!},
                    borderColor: '#3b82f6',
                    borderWidth: 4,
                    fill: true,
                    backgroundColor: gradientBlue,
                    tension: 0.4,
                    pointRadius: 0
                },
                {
                    label: 'Carnetización',
                    data: {!! json_encode($tendencia['cmd']) !!},
                    borderColor: '#f59e0b',
                    borderWidth: 4,
                    fill: true,
                    backgroundColor: gradientAmber,
                    tension: 0.4,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { weight: 'bold', size: 10 } }
                }
            }
        }
    });
</script>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    canvas { filter: drop-shadow(0 20px 30px rgba(0,0,0,0.5)); }
</style>
@endsection
