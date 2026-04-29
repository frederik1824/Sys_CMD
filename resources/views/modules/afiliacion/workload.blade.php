@extends('layouts.app')

@section('content')
<div class="p-4 md:p-10 max-w-[1600px] mx-auto min-h-[calc(100vh-100px)]">
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('solicitudes-afiliacion.index') }}" 
                   class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all">
                    <i class="ph-bold ph-arrow-left"></i>
                </a>
                <div class="h-1.5 w-1.5 rounded-full bg-slate-300"></div>
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Supervisión Operativa</span>
            </div>
            <h1 class="text-5xl font-black tracking-tighter text-slate-900 leading-tight">
                Balanceo de <span class="text-indigo-600">Carga</span>
            </h1>
            <p class="text-slate-500 font-medium text-lg max-w-2xl leading-relaxed mt-2">
                Monitorea la distribución de expedientes en tiempo real para optimizar los tiempos de respuesta y garantizar un flujo de trabajo equitativo.
            </p>
        </div>
        
        <div class="bg-slate-900 px-8 py-6 rounded-[32px] shadow-2xl shadow-slate-200 border border-slate-800 flex items-center gap-6 relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-white relative z-10">
                <i class="ph-fill ph-tray text-3xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Bandeja Global Pendiente</p>
                <p class="text-3xl font-black text-white leading-none">{{ $pendientes }} <span class="text-xs font-bold text-slate-500">Casos</span></p>
            </div>
        </div>
    </div>

    <!-- ANALYSTS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @foreach($analistas as $analista)
        <div class="bg-white rounded-[48px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 md:p-10 relative group overflow-hidden transition-all hover:shadow-2xl hover:-translate-y-1">
            <div class="absolute top-0 right-0 w-32 h-32 bg-slate-50 rounded-full -mr-16 -mt-16 blur-2xl group-hover:bg-indigo-50/50 transition-colors"></div>
            
            <div class="flex items-center gap-5 mb-10 relative z-10">
                <div class="relative">
                    <img src="{{ $analista->avatar_url }}" class="w-16 h-16 rounded-3xl object-cover border-4 border-white shadow-lg" alt="{{ $analista->name }}">
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-4 border-white rounded-full"></div>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900 leading-tight mb-1">{{ $analista->name }}</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $analista->getRoleNames()->first() ?? 'Analista' }}</p>
                </div>
            </div>

            <div class="space-y-8 relative z-10">
                <!-- GRID DE MÉTRICAS -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-3xl border border-slate-100 group/item hover:bg-indigo-600 transition-all duration-500">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 group-hover/item:text-indigo-200">Activos</p>
                        <p class="text-2xl font-black text-slate-900 group-hover/item:text-white">{{ $analista->solicitudes_count }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-3xl border border-slate-100 group/item hover:bg-emerald-600 transition-all duration-500">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 group-hover/item:text-emerald-200">Resueltos</p>
                        <p class="text-2xl font-black text-slate-900 group-hover/item:text-white">{{ $analista->completados_count }}</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-3xl border border-slate-100 group/item hover:bg-rose-600 transition-all duration-500">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 group-hover/item:text-rose-200">Rechazos</p>
                        <p class="text-2xl font-black text-slate-900 group-hover/item:text-white">{{ $analista->rechazados_count }}</p>
                    </div>
                    <div class="p-4 bg-indigo-50 rounded-3xl border border-indigo-100 group/item hover:bg-slate-900 transition-all duration-500">
                        <p class="text-[9px] font-black text-indigo-400 uppercase tracking-widest mb-1 group-hover/item:text-slate-500">Hoy</p>
                        <p class="text-2xl font-black text-indigo-600 group-hover/item:text-white">{{ $analista->hoy_count }}</p>
                    </div>
                </div>

                <div>
                    @php
                        $maxCapacity = $isCSRSupervisor ? 10 : 15;
                        $percentage = $maxCapacity > 0 ? ($analista->solicitudes_count / $maxCapacity) * 100 : 0;
                        $colorClass = $percentage > 80 ? 'bg-rose-500' : ($percentage > 50 ? 'bg-amber-500' : 'bg-emerald-500');
                    @endphp

                    <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest mb-2">
                        <span class="text-slate-400">Nivel de Ocupación</span>
                        <span class="{{ str_replace('bg-', 'text-', $colorClass) }}">{{ min($percentage, 100) }}%</span>
                    </div>
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $colorClass }} transition-all duration-1000" style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <a href="{{ route('solicitudes-afiliacion.index', ['user_id' => $analista->id, 'estado' => $estadoToWatch]) }}" 
                   class="w-full py-4 rounded-2xl bg-slate-50 text-slate-600 text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center gap-2 group/btn">
                    Ver Expedientes
                    <i class="ph-bold ph-arrow-right group-hover/btn:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
        @endforeach

        @if($analistas->isEmpty())
        <div class="col-span-full py-20 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-[32px] flex items-center justify-center text-slate-300 mx-auto mb-6">
                <i class="ph ph-users-three text-4xl"></i>
            </div>
            <h3 class="text-xl font-black text-slate-900 mb-2">No hay analistas activos</h3>
            <p class="text-slate-500 font-medium">No se encontraron colaboradores registrados en este departamento.</p>
        </div>
        @endif
    </div>
</div>
@endsection
