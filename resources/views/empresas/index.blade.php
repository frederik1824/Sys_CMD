@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-fade-in pb-20">
    {{-- Original Header Section (Fusionado con mejoras) --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <span class="text-primary/60">Sistema de Gestión</span>
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span class="text-primary text-bold">Portafolio Corporativo</span>
            </nav>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none">Módulo de Empresas</h2>
            <p class="text-slate-500 text-sm font-medium">Control estratégico y trazabilidad de entidades vinculadas.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('sistema.empresas.enrich') }}" class="inline-flex items-center px-5 py-3 bg-white text-slate-500 rounded-2xl font-bold text-[0.65rem] uppercase tracking-widest border border-slate-200 hover:bg-slate-50 transition-all gap-2 shadow-sm">
                <span class="material-symbols-outlined text-base">auto_fix_high</span>
                Saneamiento
            </a>
            <form action="{{ route('sistema.empresas.sync_firebase') }}" method="POST" class="m-0 p-0">
                @csrf
                <button type="submit" class="inline-flex items-center px-5 py-3 bg-white text-primary rounded-2xl font-bold text-[0.65rem] uppercase tracking-widest border border-primary/20 hover:bg-primary/5 transition-all gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-base">sync</span>
                    Sincronizar
                </button>
            </form>
            <a href="{{ route('sistema.empresas.create') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-slate-900 text-white rounded-2xl font-black text-[0.65rem] uppercase tracking-widest hover:bg-primary hover:-translate-y-1 transition-all shadow-xl shadow-slate-200 gap-3 group">
                <span class="material-symbols-outlined text-lg group-hover:rotate-90 transition-transform">add_business</span>
                Nueva Entidad
            </a>
        </div>
    </div>

    <!-- Progress Bar for Sync -->
    <div id="sync-progress-container" class="hidden mb-10 animate-in fade-in slide-in-from-top duration-500">
        <div class="bg-white p-8 rounded-[2.5rem] border border-primary/20 shadow-xl shadow-primary/5">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary animate-spin">sync</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-800 tracking-tight">Procesando Sincronización Cloud</h4>
                        <p id="sync-label" class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-widest mt-1">Sincronizando entidades...</p>
                    </div>
                </div>
                <span id="sync-percentage" class="text-2xl font-black text-primary italic">0%</span>
            </div>
            <div class="w-full bg-slate-50 h-4 rounded-full overflow-hidden border border-slate-100 p-1">
                <div id="sync-progress-bar" class="bg-primary h-full rounded-full transition-all duration-700 ease-out shadow-[0_0_20px_rgba(37,99,235,0.4)]" style="width: 0%"></div>
            </div>
        </div>
    </div>

    {{-- Original KPI Grid Restoration --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {{-- Total Card --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-primary/5 transition-all duration-500 group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 mb-6 group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                    <span class="material-symbols-outlined text-2xl">database</span>
                </div>
                <p class="text-[0.6rem] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Total Registros</p>
                <h3 class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['total']) }}</h3>
            </div>
        </div>

        {{-- Verified Card --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-blue-500/5 transition-all duration-500 group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-500 mb-6 group-hover:bg-blue-500 group-hover:text-white transition-colors duration-300">
                    <span class="material-symbols-outlined text-2xl">verified</span>
                </div>
                <p class="text-[0.6rem] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Entidades Reales</p>
                <h3 class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['reales']) }}</h3>
            </div>
        </div>

        {{-- Filial Card --}}
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-purple-500/5 transition-all duration-500 group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-500 mb-6 group-hover:bg-purple-500 group-hover:text-white transition-colors duration-300">
                    <span class="material-symbols-outlined text-2xl">account_tree</span>
                </div>
                <p class="text-[0.6rem] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Sucursales ARS</p>
                <h3 class="text-4xl font-black text-slate-800 tracking-tighter">{{ number_format($stats['filiales']) }}</h3>
            </div>
        </div>

        {{-- Analytics / Distribution --}}
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-32 h-32 bg-primary/20 blur-3xl rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <p class="text-[0.55rem] font-black uppercase text-slate-400 tracking-[0.2em] mb-6 relative z-10">Mix de Portafolio</p>
            <div class="relative z-10 flex items-center justify-center h-20">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Interactive Livewire Core (Fusion de los ajustes de búsqueda/mapa con la vista anterior) --}}
    <livewire:empresa.index />
</div>
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
            fetch('{{ route('carnetizacion.afiliados.sync_progress') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
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
                        labelText.textContent = 'Procesamiento Finalizado';
                        
                        setTimeout(() => {
                            container.classList.add('hidden');
                            window.location.reload(); 
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

        checkProgress();

        const syncForm = document.querySelector('form[action*="sync-firebase"]');
        if (syncForm) {
            syncForm.addEventListener('submit', function() {
                setTimeout(checkProgress, 1000);
            });
        }
    });
</script>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('distributionChart').getContext('2d');
        const distribution = @json($stats['distribution']);
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Verificadas', 'Sucursales', 'Otros'],
                datasets: [{
                    data: [distribution.reales, distribution.filiales, distribution.otros],
                    backgroundColor: ['#3b82f6', '#a855f7', '#475569'],
                    borderWidth: 0,
                    weight: 0.5
                }]
            },
            options: {
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                },
                maintainAspectRatio: false
            }
        });
    });
</script>
@endpush
