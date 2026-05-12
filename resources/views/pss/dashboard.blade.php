@extends('layouts.portal')

@section('content')
<div class="p-8 space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight font-outfit">Red de Prestadores (PSS)</h1>
            <p class="text-slate-500 font-medium">Panel administrativo de médicos, centros y especialidades.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="bg-white text-slate-700 px-5 py-2.5 rounded-2xl font-bold text-sm shadow-sm border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="ph-bold ph-file-arrow-down text-lg"></i>
                Exportar
            </button>
            <a href="{{ route('pss.import.index') }}" class="bg-primary text-white px-6 py-2.5 rounded-2xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-secondary transition-all flex items-center gap-2">
                <i class="ph-bold ph-plus text-lg"></i>
                Nueva Importación
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card: Total Médicos -->
        <div class="glass-card p-6 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="flex justify-between items-start">
                <div class="w-14 h-14 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-colors duration-500">
                    <i class="ph-duotone ph-stethoscope"></i>
                </div>
                <span class="text-xs font-black text-blue-500/50 uppercase tracking-widest">Total Médicos</span>
            </div>
            <div class="mt-4">
                <h3 class="text-4xl font-black text-slate-900 leading-none">{{ number_format($stats['total_medicos']) }}</h3>
                <p class="text-sm font-bold text-slate-400 mt-2">Profesionales registrados</p>
            </div>
        </div>

        <!-- Card: Total Centros -->
        <div class="glass-card p-6 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="flex justify-between items-start">
                <div class="w-14 h-14 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-500">
                    <i class="ph-duotone ph-hospital"></i>
                </div>
                <span class="text-xs font-black text-indigo-500/50 uppercase tracking-widest">Total Centros</span>
            </div>
            <div class="mt-4">
                <h3 class="text-4xl font-black text-slate-900 leading-none">{{ number_format($stats['total_centros']) }}</h3>
                <p class="text-sm font-bold text-slate-400 mt-2">Clínicas y laboratorios</p>
            </div>
        </div>

        <!-- Card: Ciudades -->
        <div class="glass-card p-6 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="flex justify-between items-start">
                <div class="w-14 h-14 bg-amber-100 text-amber-600 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-amber-600 group-hover:text-white transition-colors duration-500">
                    <i class="ph-duotone ph-map-pin"></i>
                </div>
                <span class="text-xs font-black text-amber-500/50 uppercase tracking-widest">Cobertura</span>
            </div>
            <div class="mt-4">
                <h3 class="text-4xl font-black text-slate-900 leading-none">{{ number_format($stats['total_ciudades']) }}</h3>
                <p class="text-sm font-bold text-slate-400 mt-2">Ciudades con red activa</p>
            </div>
        </div>

        <!-- Card: Errores/Incompletos -->
        <div class="glass-card p-6 rounded-[2.5rem] shadow-sm flex flex-col justify-between group hover:shadow-xl hover:-translate-y-1 transition-all duration-300 bg-rose-50/30">
            <div class="flex justify-between items-start">
                <div class="w-14 h-14 bg-rose-100 text-rose-600 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-rose-600 group-hover:text-white transition-colors duration-500">
                    <i class="ph-duotone ph-warning-octagon"></i>
                </div>
                <span class="text-xs font-black text-rose-500/50 uppercase tracking-widest">Pendientes</span>
            </div>
            <div class="mt-4">
                <h3 class="text-4xl font-black text-rose-600 leading-none">{{ number_format($stats['registros_sin_telefono']) }}</h3>
                <p class="text-sm font-bold text-rose-400 mt-2">Registros sin teléfono</p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart/Distribution Area -->
        <div class="lg:col-span-2 space-y-8">
            <div class="glass-card p-8 rounded-[2.5rem] shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xl font-black text-slate-900">Médicos por Especialidad</h3>
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Distribución Top 5</div>
                </div>
                
                <div class="space-y-6">
                    @foreach($medicosPorEspecialidad as $especialidad)
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="font-bold text-slate-600">{{ $especialidad->nombre }}</span>
                            <span class="font-black text-primary">{{ $especialidad->total }}</span>
                        </div>
                        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: {{ ($especialidad->total / max(1, $stats['total_medicos'])) * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card p-8 rounded-[3rem] bg-gradient-to-br from-primary to-secondary text-white relative overflow-hidden shadow-2xl">
                    <i class="ph-fill ph-circles-three-plus absolute -right-10 -bottom-10 text-[12rem] opacity-10"></i>
                    <h4 class="text-lg font-black uppercase tracking-tighter mb-2">Administrar Médicos</h4>
                    <p class="text-white/60 text-sm mb-8 leading-relaxed">Gestiona especialistas, clínicas y estatus operativos de la red.</p>
                    <a href="{{ route('pss.medicos.index') }}" class="inline-flex items-center gap-2 bg-white text-primary px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-amber-100 transition-colors">
                        Ver Médicos
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
                <div class="glass-card p-8 rounded-[3rem] bg-dark text-white relative overflow-hidden shadow-2xl">
                    <i class="ph-fill ph-buildings absolute -right-10 -bottom-10 text-[12rem] opacity-10"></i>
                    <h4 class="text-lg font-black uppercase tracking-tighter mb-2">Gestión de Centros</h4>
                    <p class="text-white/60 text-sm mb-8 leading-relaxed">Laboratorios, clínicas y centros de diagnóstico autorizados.</p>
                    <a href="{{ route('pss.centros.index') }}" class="inline-flex items-center gap-2 bg-white/10 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white/20 transition-colors">
                        Ver Centros
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
                <div class="glass-card p-8 rounded-[3rem] bg-white text-slate-800 relative overflow-hidden shadow-xl border border-slate-100">
                    <i class="ph-fill ph-list-bullets absolute -right-10 -bottom-10 text-[12rem] text-slate-100"></i>
                    <h4 class="text-lg font-black uppercase tracking-tighter mb-2">Catálogos</h4>
                    <p class="text-slate-400 text-sm mb-8 leading-relaxed">Depura ciudades, especialidades, grupos y clínicas.</p>
                    <a href="{{ route('pss.catalogos.index') }}" class="inline-flex items-center gap-2 bg-slate-100 text-slate-600 px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-colors">
                        Gestionar Tablas
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Activity Area -->
        <div class="space-y-8">
            <div class="glass-card p-8 rounded-[2.5rem] bg-amber-50 border-amber-100 border relative overflow-hidden">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-amber-200 text-amber-700 rounded-lg flex items-center justify-center">
                        <i class="ph-fill ph-lightbulb"></i>
                    </div>
                    <h4 class="text-sm font-black text-amber-900 uppercase">Tip Pro</h4>
                </div>
                <p class="text-xs font-bold text-amber-700/80 leading-relaxed">
                    Puedes normalizar los nombres de ciudades y especialidades masivamente desde el módulo de Catálogos para unificar registros duplicados por ortografía.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
