@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-8">
    
    <!-- Hero Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        @if($canSeeCmd)
        <div class="bg-gradient-to-br from-primary to-primary-container p-6 rounded-[2rem] shadow-2xl shadow-primary/20 text-white relative overflow-hidden group">
            <div class="relative z-10">
                <span class="text-[0.6rem] font-black uppercase tracking-widest opacity-80">Total General Carnets</span>
                <div class="text-4xl font-black mt-2 tracking-tighter">{{ number_format($stats['total_afiliados']) }}</div>
                <div class="text-[0.65rem] font-bold mt-1 opacity-70">Afiliados registrados</div>
            </div>
            <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-white/10 text-9xl group-hover:scale-110 transition-transform">groups</span>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 group">
            <span class="text-[0.6rem] font-black uppercase tracking-widest text-slate-400">Completados</span>
            <div class="text-4xl font-black mt-2 tracking-tighter text-emerald-500">{{ number_format($stats['completados']) }}</div>
            @php $perc = $stats['total_afiliados'] > 0 ? ($stats['completados'] / $stats['total_afiliados']) * 100 : 0; @endphp
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $perc }}%"></div>
            </div>
            <div class="text-[0.6rem] font-bold mt-2 text-slate-400">{{ round($perc, 1) }}% de avance global</div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 group">
            <span class="text-[0.6rem] font-black uppercase tracking-widest text-slate-400">Pendiente SAFESURE</span>
            <div class="text-4xl font-black mt-2 tracking-tighter text-amber-500">RD$ {{ number_format($stats['por_liquidar'], 2) }}</div>
            <div class="text-[0.6rem] font-bold mt-2 text-slate-400 italic">Monto por liquidar hoy</div>
        </div>

        <div class="bg-amber-50 p-6 rounded-[2rem] shadow-sm border border-amber-100 group">
            <span class="text-[0.6rem] font-black uppercase tracking-widest text-amber-500">Pendiente de Recepción</span>
            <div class="text-4xl font-black mt-2 tracking-tighter text-amber-600">{{ number_format($stats['pendiente_recepcion']) }}</div>
            <div class="text-[0.6rem] font-bold mt-2 text-amber-500 uppercase tracking-tighter font-black italic">Validación CMD requerida</div>
        </div>
        @else
        <div class="md:col-span-4 bg-slate-50 p-12 rounded-[3rem] border border-dashed border-slate-300 text-center">
            <span class="material-symbols-outlined text-6xl text-slate-200 mb-4">analytics</span>
            <h3 class="text-xl font-black text-slate-400 uppercase tracking-widest">Módulo de Reportes Ejecutivos</h3>
            <p class="text-slate-400 text-sm mt-2">Bienvenido al centro de inteligencia. Selecciona una opción en el menú para comenzar.</p>
        </div>
        @endif
    </div>

    @if($canSeeCmd)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Avance por Corte -->
        <div class="lg:col-span-2 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Progreso por Corte</h3>
                    <p class="text-xs text-slate-400 font-medium">Comparativa de entrega y cierre por período.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('reportes.pendientes', ['context' => 'executive']) }}" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-[0.65rem] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">inventory_2</span>
                        Pendientes
                    </a>
                    <a href="{{ route('reportes.supervision', ['context' => 'executive']) }}" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-[0.65rem] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-slate-800/20 flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">monitoring</span>
                        Supervisión
                    </a>
                    <a href="{{ route('reportes.export_center', ['context' => 'executive']) }}" class="bg-primary text-white px-5 py-2.5 rounded-xl text-[0.65rem] font-black uppercase tracking-widest hover:bg-primary-dark transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                        <span class="material-symbols-outlined text-lg">export_notes</span>
                        Exportación
                    </a>
                </div>
            </div>

            <div class="space-y-6">
                @foreach($cortes_progreso as $c)
                <div class="group">
                    <div class="flex justify-between items-end mb-2">
                        <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">{{ $c->nombre }}</span>
                        <span class="text-[0.65rem] font-black text-slate-400">{{ $c->completados_count }} / {{ $c->afiliados_count }}</span>
                    </div>
                    @php $cperc = $c->afiliados_count > 0 ? ($c->completados_count / $c->afiliados_count) * 100 : 0; @endphp
                    <div class="w-full bg-slate-50 h-3 rounded-full overflow-hidden border border-slate-100">
                        <div class="bg-gradient-to-r from-primary to-primary-container h-full rounded-full transition-all duration-1000" style="width: {{ $cperc }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Distribución de Estados -->
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl text-white relative overflow-hidden">
            <h3 class="text-lg font-black uppercase tracking-tighter mb-6">Distribución de Estados</h3>
            <div class="space-y-4">
                @foreach($estados_labels as $index => $label)
                <div class="flex items-center justify-between bg-white/5 p-4 rounded-2xl hover:bg-white/10 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                        <span class="text-xs font-bold">{{ $label }}</span>
                    </div>
                    <span class="text-xs font-black text-primary">{{ $estados_counts[$index] }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-8 pt-6 border-t border-white/10 opacity-60 text-center">
                <a href="{{ route('reportes.heatmap', ['context' => 'executive']) }}" class="text-[0.6rem] font-bold text-primary group hover:underline flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-sm">map</span> Ver Densidad Geográfica
                </a>
            </div>
            
            <!-- Adorno visual -->
            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-primary/20 rounded-full blur-3xl"></div>
        </div>
    </div>
    @endif

</div>
@endsection
