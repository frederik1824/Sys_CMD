@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-10 space-y-10 min-h-screen bg-[#f8fafc]">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <span class="text-primary/60">Supervisión</span>
                <span class="ph ph-caret-right text-[10px]"></span>
                <span class="text-primary">Bandeja de Permisos</span>
            </div>
            <h1 class="text-4xl md:text-5xl font-[900] text-slate-900 tracking-tighter leading-none flex items-center gap-4">
                Solicitudes <span class="text-primary italic font-light">Pendientes</span>
                <span class="p-2 bg-amber-50 text-amber-600 rounded-2xl hidden md:block">
                    <i class="ph ph-envelope-open text-3xl"></i>
                </span>
            </h1>
            <p class="text-slate-500 font-medium text-lg">Revisión y aprobación de licencias del personal asignado.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8">
        @forelse($solicitudes as $sol)
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl overflow-hidden group hover:border-primary/20 transition-all duration-500">
            <div class="flex flex-col lg:flex-row">
                <!-- Info del Empleado -->
                <div class="lg:w-1/3 p-8 bg-slate-50/50 border-r border-slate-100">
                    <div class="flex items-center gap-6 mb-8">
                        <div class="w-20 h-20 bg-slate-900 text-white rounded-[2rem] flex items-center justify-center text-3xl font-black shadow-2xl">
                            {{ substr($sol->empleado->nombre_completo, 0, 1) }}
                        </div>
                        <div>
                            <span class="text-xs font-black text-primary uppercase tracking-widest">{{ $sol->empleado->codigo_empleado }}</span>
                            <h4 class="text-xl font-black text-slate-900 leading-tight uppercase">{{ $sol->empleado->nombre_completo }}</h4>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $sol->empleado->cargo->nombre }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <span>Fecha Solicitud</span>
                            <span class="text-slate-900">{{ $sol->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <span>Tipo Permiso</span>
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg border border-blue-100">{{ $sol->tipo->nombre }}</span>
                        </div>
                    </div>
                </div>

                <!-- Detalle de la Solicitud -->
                <div class="lg:w-2/3 p-10 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="p-3 bg-slate-100 rounded-2xl">
                                <i class="ph ph-calendar-blank text-2xl text-slate-600"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Periodo Solicitado</p>
                                <p class="text-lg font-black text-slate-900 uppercase">
                                    Del {{ $sol->fecha_desde->format('d M') }} al {{ $sol->fecha_hasta->format('d M, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 mb-8">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Justificación del Empleado</p>
                            <p class="text-sm font-bold text-slate-700 leading-relaxed italic">
                                "{{ $sol->motivo }}"
                            </p>
                        </div>

                        @if($sol->evidencia_path)
                        <a href="{{ Storage::url($sol->evidencia_path) }}" target="_blank" class="inline-flex items-center gap-3 px-6 py-3 bg-white border border-slate-200 rounded-2xl text-[11px] font-black uppercase tracking-widest text-slate-600 hover:border-primary hover:text-primary transition-all shadow-sm">
                            <i class="ph ph-paperclip text-xl"></i>
                            Ver Evidencia Adjunta
                        </a>
                        @endif
                    </div>

                    <!-- Acciones del Supervisor -->
                    <form action="{{ route('asistencia.permisos.decidir', $sol->id) }}" method="POST" class="mt-10 pt-10 border-t border-slate-50">
                        @csrf
                        <div class="flex flex-col md:flex-row items-end gap-6">
                            <div class="flex-1 w-full space-y-2">
                                <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Comentario de Respuesta (Opcional)</label>
                                <input type="text" name="comentario" placeholder="Ej: Solicitud aprobada, favor notificar a RRHH." 
                                       class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 transition-all">
                            </div>
                            <div class="flex gap-4 w-full md:w-auto">
                                <button type="submit" name="estado" value="rechazado" class="flex-1 md:flex-none px-8 py-4 bg-rose-50 text-rose-600 rounded-2xl text-[11px] font-black uppercase tracking-widest border border-rose-100 hover:bg-rose-100 transition-all">
                                    Rechazar
                                </button>
                                <button type="submit" name="estado" value="aprobado" class="flex-1 md:flex-none px-10 py-4 bg-slate-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-slate-200 hover:scale-[1.05] active:scale-[0.95] transition-all">
                                    Aprobar Solicitud
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="py-40 flex flex-col items-center justify-center opacity-30">
            <div class="w-32 h-32 bg-slate-100 rounded-[3rem] flex items-center justify-center mb-6">
                <i class="ph ph-envelope-simple-open text-6xl text-slate-400"></i>
            </div>
            <p class="text-xl font-black uppercase tracking-widest">Bandeja de Entrada Vacía</p>
            <p class="text-sm font-bold mt-2">No hay solicitudes de permisos pendientes de revisión.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
