@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-in fade-in duration-700">
    <!-- Header de Contexto -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Centro de Control de Cargas</h1>
            <p class="text-slate-500 font-medium italic">Historial estratégico y métricas de importación diaria.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('call-center.worklist') }}" class="inline-flex items-center gap-3 bg-slate-900 hover:bg-black text-white px-6 py-3 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-lg active:scale-95">
                <i class="ph-bold ph-headset text-lg"></i>
                Bandeja de Trabajo
            </a>
            <a href="{{ route('call-center.import') }}" class="inline-flex items-center gap-3 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-lg shadow-emerald-500/20 active:scale-95">
                <i class="ph-bold ph-plus-circle text-lg"></i>
                Nueva Carga
            </a>
        </div>
    </div>

    <!-- Estadísticas Globales Premium -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/40 overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-50">
            <!-- Stat 1 -->
            <div class="p-8 flex items-center gap-6 group hover:bg-slate-50/50 transition-colors">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:rotate-12 transition-transform">
                    <i class="ph-bold ph-tray text-2xl"></i>
                </div>
                <div>
                    <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Operaciones</h6>
                    <p class="text-3xl font-black text-slate-900 leading-none tracking-tight">{{ $cargas->total() }}</p>
                </div>
            </div>
            <!-- Stat 2 -->
            <div class="p-8 flex items-center gap-6 group hover:bg-slate-50/50 transition-colors">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:rotate-12 transition-transform">
                    <i class="ph-bold ph-user-plus text-2xl"></i>
                </div>
                <div>
                    <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Registros Nuevos</h6>
                    <p class="text-3xl font-black text-slate-900 leading-none tracking-tight">{{ $cargas->sum('registros_nuevos') }}</p>
                </div>
            </div>
            <!-- Stat 3 -->
            <div class="p-8 flex items-center gap-6 group hover:bg-slate-50/50 transition-colors">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-inner group-hover:rotate-12 transition-transform">
                    <i class="ph-bold ph-arrows-counter-clockwise text-2xl"></i>
                </div>
                <div>
                    <h6 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Data Actualizada</h6>
                    <p class="text-3xl font-black text-slate-900 leading-none tracking-tight">{{ $cargas->sum('registros_actualizados') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Cargas -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <h5 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ph-bold ph-clock-counter-clockwise text-slate-400"></i>
                Cronología de Importaciones
            </h5>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identificador de Batch</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha & Responsable</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Nuevos</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Actualizados</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Total</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($cargas as $carga)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center shadow-inner group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-all">
                                    <i class="ph-bold ph-package text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-900 tracking-tight">{{ $carga->nombre }}</h4>
                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg">ID: #{{ $carga->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm font-black text-slate-700 mb-1">{{ $carga->created_at->format('d/m/Y h:i A') }}</div>
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
                                <div class="w-5 h-5 rounded-full overflow-hidden border border-slate-200">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($carga->user->name) }}&background=f8fafc&color=64748b" class="w-full h-full object-cover">
                                </div>
                                {{ $carga->user->name }}
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="inline-flex px-3 py-1 rounded-lg bg-emerald-50 text-emerald-600 text-xs font-black">
                                +{{ $carga->registros_nuevos }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="inline-flex px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-black">
                                ~{{ $carga->registros_actualizados }}
                            </span>
                        </td>
                        <td class="px-6 py-6 text-center">
                            <span class="text-sm font-black text-slate-900">{{ $carga->total_registros }}</span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 text-slate-500 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-100">
                                <i class="ph-bold ph-check-circle text-emerald-500 text-sm"></i>
                                Procesado
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-32 h-32 bg-slate-50 rounded-[40px] flex items-center justify-center mb-6 border border-slate-100">
                                    <i class="ph-bold ph-tray text-6xl text-slate-200"></i>
                                </div>
                                <h3 class="text-xl font-black text-slate-900 mb-2">No hay cargas registradas</h3>
                                <p class="text-slate-400 font-medium mb-8 max-w-sm mx-auto italic">Inicia la operación cargando la data diaria desde tu hoja de cálculo para activar el Call Center.</p>
                                <a href="{{ route('call-center.import') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-lg active:scale-95">
                                    Iniciar Primera Carga
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($cargas->hasPages())
        <div class="p-8 border-t border-slate-50 bg-slate-50/30">
            {{ $cargas->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
