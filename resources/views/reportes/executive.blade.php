@extends('layouts.app')

@section('content')
<style>
    .glass-morphism {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    .dark-glass {
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: auto;
        gap: 1.5rem;
    }
    .bento-item {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .bento-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
    }
    @media (max-width: 1024px) {
        .bento-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .bento-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="p-4 lg:p-8 space-y-8 min-h-screen bg-[#f1f5f9] bg-fixed" style="background-image: radial-gradient(at 0% 0%, hsla(210,100%,98%,1) 0, transparent 50%), radial-gradient(at 100% 100%, hsla(210,100%,95%,1) 0, transparent 50%);">
    
    <!-- Header Premium -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-4xl font-[900] tracking-tighter text-slate-900 mb-1 flex items-center gap-3">
                <span class="p-2 bg-primary text-white rounded-2xl shadow-xl shadow-primary/20">
                    <span class="material-symbols-outlined text-3xl">analytics</span>
                </span>
                Dashboard <span class="text-primary italic font-light">Ejecutivo</span>
            </h1>
            <p class="text-slate-400 font-bold flex items-center gap-2 text-xs uppercase tracking-[0.3em] pl-16">
                Unificación Estratégica • {{ now()->translatedFormat('F Y') }}
            </p>
        </div>
        <div class="flex items-center gap-3 bg-white/50 backdrop-blur-md p-2 rounded-[2rem] border border-white shadow-sm">
            <div class="flex -space-x-3">
                @foreach(range(1, 3) as $i)
                    <div class="w-10 h-10 rounded-full border-2 border-white bg-slate-200 overflow-hidden shadow-sm">
                        <img src="https://ui-avatars.com/api/?name=Admin+User&background=random" class="w-full h-full object-cover">
                    </div>
                @endforeach
            </div>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-4">Live Monitoring</span>
        </div>
    </header>

    <!-- Bento Grid Section -->
    <div class="bento-grid">
        
        <!-- Health Score (Large Item) -->
        <div class="col-span-1 lg:col-span-2 row-span-1 dark-glass p-8 rounded-[3rem] text-white flex flex-col justify-between bento-item relative overflow-hidden group">
            <div class="absolute top-0 right-0 -translate-y-1/4 translate-x-1/4 w-64 h-64 bg-primary/20 rounded-full blur-3xl opacity-50 group-hover:scale-125 transition-transform duration-1000"></div>
            
            <div class="relative z-10 flex justify-between items-start">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-primary-light">Salud Operativa Global</h2>
                    <p class="text-xs text-slate-400 font-bold mt-1">Algoritmo de efectividad unificada</p>
                </div>
                <span class="px-4 py-2 bg-white/10 rounded-2xl text-xs font-black uppercase tracking-widest border border-white/10">Score: {{ $healthIndex }}</span>
            </div>

            <div class="relative z-10 py-10 flex items-end gap-8">
                <div class="text-8xl font-[900] tracking-tighter leading-none">{{ $healthIndex }}%</div>
                <div class="flex-1 pb-2">
                    <div class="w-full bg-white/10 h-3 rounded-full overflow-hidden border border-white/5">
                        <div class="h-full bg-gradient-to-r from-blue-400 via-indigo-500 to-purple-600 rounded-full shadow-[0_0_20px_rgba(59,130,246,0.5)] transition-all duration-1000" style="width: {{ $healthIndex }}%"></div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 grid grid-cols-{{ ($canSeeCmd ? 1 : 0) + ($canSeeAfiliacion ? 1 : 0) + ($canSeeTraspasos ? 1 : 0) }} gap-4 border-t border-white/10 pt-6">
                @if($canSeeCmd)
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Carnetización</p>
                    <p class="text-lg font-black">{{ $cmdData['porcentaje'] }}%</p>
                </div>
                @endif
                @if($canSeeAfiliacion)
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Afiliación</p>
                    <p class="text-lg font-black">{{ $afiliacionData['porcentaje'] }}%</p>
                </div>
                @endif
                @if($canSeeTraspasos)
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Traspasos</p>
                    <p class="text-lg font-black">{{ $traspasosData['porcentaje'] }}%</p>
                </div>
                @endif
            </div>
        </div>

        @if($canSeeCmd)
        <!-- Módulo CMD (Square Item) -->
        <div class="glass-morphism p-8 rounded-[3rem] bento-item border-l-8 border-l-blue-500">
            <div class="flex justify-between items-start mb-6">
                <span class="material-symbols-outlined text-blue-500 text-3xl p-3 bg-blue-50 rounded-2xl">badge</span>
                <span class="text-[10px] font-black text-blue-600/50 uppercase tracking-widest">CMD</span>
            </div>
            <h3 class="text-lg font-black text-slate-800 tracking-tight mb-1">Carnetización</h3>
            <div class="text-3xl font-black text-slate-900 mb-6">{{ number_format($cmdData['total']) }}</div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 font-bold uppercase tracking-tighter">Completados</span>
                    <span class="text-emerald-500 font-black">{{ number_format($cmdData['completados']) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 font-bold uppercase tracking-tighter">Críticos SLA</span>
                    <span class="text-rose-500 font-black animate-pulse">{{ $cmdData['criticos'] }}</span>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-4">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Cartera Pendiente</p>
                    <p class="text-lg font-black text-slate-800">RD$ {{ number_format($cmdData['monto_pendiente'], 0) }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($canSeeAfiliacion)
        <!-- Módulo Afiliación (Square Item) -->
        <div class="glass-morphism p-8 rounded-[3rem] bento-item border-l-8 border-l-indigo-500">
            <div class="flex justify-between items-start mb-6">
                <span class="material-symbols-outlined text-indigo-500 text-3xl p-3 bg-indigo-50 rounded-2xl">person_add</span>
                <span class="text-[10px] font-black text-indigo-600/50 uppercase tracking-widest">OPS</span>
            </div>
            <h3 class="text-lg font-black text-slate-800 tracking-tight mb-1">Afiliaciones</h3>
            <div class="text-3xl font-black text-slate-900 mb-6">{{ number_format($afiliacionData['total']) }}</div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 font-bold uppercase tracking-tighter">Aprobadas</span>
                    <span class="text-emerald-500 font-black">{{ number_format($afiliacionData['aprobadas']) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 font-bold uppercase tracking-tighter">Ingreso Hoy</span>
                    <span class="text-indigo-600 font-black">+{{ $afiliacionData['hoy'] }}</span>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-4">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Tasa Devolución</p>
                    <p class="text-lg font-black text-amber-600">{{ $afiliacionData['tasa_devolucion'] }}%</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Tendencia Global (Wide Item) -->
        <div class="col-span-1 lg:col-span-3 glass-morphism p-8 rounded-[3rem] bento-item">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-xl font-black text-slate-800 tracking-tighter">Tendencia Multimodular</h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1">Crecimiento consolidado 6 meses</p>
                </div>
                <div class="flex gap-2">
                    @if($canSeeCmd)
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="text-[9px] font-black text-blue-600 uppercase">CMD</span>
                    </div>
                    @endif
                    @if($canSeeAfiliacion)
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-indigo-50 border border-indigo-100">
                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                        <span class="text-[9px] font-black text-indigo-600 uppercase">OPS</span>
                    </div>
                    @endif
                    @if($canSeeTraspasos)
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-50 border border-amber-100">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span class="text-[9px] font-black text-amber-600 uppercase">TRA</span>
                    </div>
                    @endif
                </div>
            </div>
            <div class="h-[300px]">
                <canvas id="mainTrendChart"></canvas>
            </div>
        </div>

        @if($canSeeTraspasos)
        <!-- Traspasos (Square Item) -->
        <div class="glass-morphism p-8 rounded-[3rem] bento-item border-l-8 border-l-amber-500">
            <div class="flex justify-between items-start mb-6">
                <span class="material-symbols-outlined text-amber-500 text-3xl p-3 bg-amber-50 rounded-2xl">swap_horiz</span>
                <span class="text-[10px] font-black text-amber-600/50 uppercase tracking-widest">TRA</span>
            </div>
            <h3 class="text-lg font-black text-slate-800 tracking-tight mb-1">Traspasos</h3>
            <div class="text-3xl font-black text-slate-900 mb-6">{{ number_format($traspasosData['total']) }}</div>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 font-bold uppercase tracking-tighter">Efectivos</span>
                    <span class="text-emerald-500 font-black">{{ number_format($traspasosData['efectivos']) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-slate-400 font-bold uppercase tracking-tighter">Mes Actual</span>
                    <span class="text-slate-800 font-black">{{ $traspasosData['mes_actual'] }}</span>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-4 text-center">
                    <p class="text-[9px] font-black text-slate-400 uppercase mb-2">Meta Mensual</p>
                    <div class="text-2xl font-black text-slate-900">{{ $traspasosData['cumplimiento_meta'] }}%</div>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                        <div class="h-full bg-amber-500" style="width: {{ $traspasosData['cumplimiento_meta'] }}%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    <!-- Hidden Data for JS -->
    <div id="exec-data" class="hidden"
        data-labels='{!! json_encode($tendencia['labels']) !!}'
        data-cmd='{!! json_encode($tendencia['cmd']) !!}'
        data-afiliacion='{!! json_encode($tendencia['afiliacion']) !!}'
        data-traspasos='{!! json_encode($tendencia['traspasos']) !!}'>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dataContainer = document.getElementById('exec-data');
    if (!dataContainer) return;

    const labels = JSON.parse(dataContainer.dataset.labels);
    const cmdData = JSON.parse(dataContainer.dataset.cmd);
    const afiData = JSON.parse(dataContainer.dataset.afiliacion);
    const traData = JSON.parse(dataContainer.dataset.traspasos);

    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.color = '#94a3b8';

    const ctx = document.getElementById('mainTrendChart').getContext('2d');
    
    // Gradients
    const gradBlue = ctx.createLinearGradient(0, 0, 0, 300);
    gradBlue.addColorStop(0, 'rgba(59, 130, 246, 0.1)');
    gradBlue.addColorStop(1, 'rgba(59, 130, 246, 0)');

    const gradIndi = ctx.createLinearGradient(0, 0, 0, 300);
    gradIndi.addColorStop(0, 'rgba(99, 102, 241, 0.1)');
    gradIndi.addColorStop(1, 'rgba(99, 102, 241, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                @if($canSeeCmd)
                {
                    label: 'Carnetización',
                    data: cmdData,
                    borderColor: '#3b82f6',
                    backgroundColor: gradBlue,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#3b82f6',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                },
                @endif
                @if($canSeeAfiliacion)
                {
                    label: 'Afiliación',
                    data: afiData,
                    borderColor: '#6366f1',
                    backgroundColor: gradIndi,
                    fill: true,
                    tension: 0.4,
                    borderWidth: 4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                },
                @endif
                @if($canSeeTraspasos)
                {
                    label: 'Traspasos',
                    data: traData,
                    borderColor: '#f59e0b',
                    fill: false,
                    tension: 0.4,
                    borderWidth: 4,
                    borderDash: [5, 5],
                    pointRadius: 0
                }
                @endif
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: '#0f172a',
                    padding: 15,
                    cornerRadius: 15,
                    titleFont: { size: 14, weight: '900' },
                    bodyFont: { size: 12, weight: 'bold' }
                }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                    ticks: { padding: 10 }
                },
                x: {
                    grid: { display: false },
                    ticks: { padding: 10 }
                }
            }
        }
    });
});
</script>
@endsection
