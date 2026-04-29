@extends('layouts.app')

@section('content')
<div class="p-8 lg:p-12 max-w-5xl mx-auto space-y-12">
    <!-- Header -->
    <div class="space-y-2">
        <div class="flex items-center gap-3">
            <div class="w-1.5 h-10 bg-primary rounded-full"></div>
            <h2 class="text-4xl font-extrabold font-headline text-slate-950 tracking-tighter uppercase">Centro de <span class="text-primary font-light underline decoration-indigo-200 decoration-4">Exportación</span></h2>
        </div>
        <p class="text-slate-500 font-medium pl-5">Generación de archivos inteligentes por cortes, estados y períodos operativos.</p>
    </div>

    <!-- Export Core Card -->
    <div class="bg-white/80 backdrop-blur-2xl p-10 rounded-[40px] border border-white shadow-2xl shadow-slate-200/50 relative overflow-hidden">
        {{-- Decoration --}}
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <span class="material-symbols-outlined text-[180px]">file_download</span>
        </div>

        <form action="{{ route('reportes.export') }}" method="GET" class="relative z-10 space-y-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <!-- Select Corte -->
                <div class="space-y-3">
                    <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">calendar_view_day</span>
                        Seleccionar Corte
                    </label>
                    <select name="corte_id" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-primary focus:border-primary transition-all">
                        <option value="">TODOS LOS CORTES</option>
                        @foreach($cortes as $c)
                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                    <p class="text-[9px] text-slate-400 font-medium pl-4 uppercase">Filtra la data por el período de carga específico.</p>
                </div>

                <!-- Select Estado -->
                <div class="space-y-3">
                    <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">rule</span>
                        Estado Operativo
                    </label>
                    <select name="estado_id" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-primary focus:border-primary transition-all">
                        <option value="">TODOS LOS ESTADOS</option>
                        @if($canSeeCmd)
                        <optgroup label="CRÍTICOS">
                            <option value="7">PENDIENTE DE RECEPCIÓN (CMD)</option>
                            <option value="9">COMPLETADO (ENTREGADO)</option>
                        </optgroup>
                        @endif
                        <optgroup label="PROCESO">
                            @foreach($estados as $e)
                                @if(!in_array($e->id, [7,9]))
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>
                    <p class="text-[9px] text-slate-400 font-medium pl-4 uppercase">Selecciona el estado operativo para el reporte maestro.</p>
                </div>

                <!-- Fecha Desde -->
                <div class="space-y-3">
                    <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">event</span>
                        Fecha Desde
                    </label>
                    <input type="date" name="fecha_desde" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700">
                </div>

                <!-- Fecha Hasta -->
                <div class="space-y-3">
                    <label class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">event</span>
                        Fecha Hasta
                    </label>
                    <input type="date" name="fecha_hasta" value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700">
                </div>
            </div>

            <!-- Action Area -->
            <div class="pt-10 border-t border-slate-50 flex flex-col md:flex-row items-center justify-end gap-4">
                <div class="flex items-center gap-4 mr-auto">
                    <div class="flex -space-x-3 overflow-hidden p-1">
                        <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-blue-100 flex items-center justify-center text-[10px] font-black text-blue-600">CSV</div>
                        <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-emerald-100 flex items-center justify-center text-[10px] font-black text-emerald-600">XLS</div>
                    </div>
                    <p class="text-xs text-slate-400 font-medium italic">Selecciona el tipo de detalle que necesitas.</p>
                </div>

                @if($canSeeCmd)
                <a href="{{ route('reportes.resumen') }}" class="w-full md:w-max bg-indigo-50 text-indigo-600 border border-indigo-100 px-8 py-5 rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:bg-indigo-600 hover:text-white transition-all flex items-center justify-center gap-4 group">
                    Resumen Gerencial
                    <span class="material-symbols-outlined text-lg">analytics</span>
                </a>
                @endif

                <button type="submit" name="type" value="detail" class="w-full md:w-max bg-slate-900 text-white px-10 py-5 rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:bg-primary hover:shadow-2xl hover:shadow-primary/30 transition-all flex items-center justify-center gap-4 group">
                    Reporte Maestro Detallado
                    <span class="material-symbols-outlined text-lg group-hover:translate-x-1 transition-transform">download_2</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Info Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 opacity-80">
        <div class="p-6 bg-blue-50/50 rounded-3xl border border-blue-100/50">
             <span class="material-symbols-outlined text-blue-500 mb-3">history_edu</span>
             <h5 class="text-[0.65rem] font-black text-blue-700 uppercase tracking-widest mb-1">Cortes Históricos</h5>
             <p class="text-[10px] text-blue-600/80 leading-relaxed font-medium">Exporta la data por el grupo de carga original para conciliaciones mensuales.</p>
        </div>
        <div class="p-6 bg-emerald-50/50 rounded-3xl border border-emerald-100/50">
             <span class="material-symbols-outlined text-emerald-500 mb-3">verified</span>
             <h5 class="text-[0.65rem] font-black text-emerald-700 uppercase tracking-widest mb-1">Registros Finalizados</h5>
             <p class="text-[10px] text-emerald-600/80 leading-relaxed font-medium">Filtra por el estado final para obtener el reporte de cierre de ciclo.</p>
        </div>
        @if($canSeeCmd)
        <div class="p-6 bg-amber-50/50 rounded-3xl border border-amber-100/50">
             <span class="material-symbols-outlined text-amber-500 mb-3">move_to_inbox</span>
             <h5 class="text-[0.65rem] font-black text-amber-700 uppercase tracking-widest mb-1">Pendiente Recepción</h5>
             <p class="text-[10px] text-amber-600/80 leading-relaxed font-medium">Identifica carnet físicamente entregados que aún no tienen el acuse formal validado.</p>
        </div>
        @endif
    </div>
</div>
@endsection
