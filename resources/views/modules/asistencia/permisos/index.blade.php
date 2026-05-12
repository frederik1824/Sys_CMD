@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-10 space-y-10 min-h-screen bg-[#f8fafc]">
    
    <div class="flex items-center gap-4 mb-8 text-[11px] font-black uppercase tracking-widest text-slate-400">
        <a href="{{ route('asistencia.index') }}" class="hover:text-primary transition-colors flex items-center gap-2">
            <i class="ph-bold ph-arrow-left"></i> Volver al Reloj
        </a>
        <span>/</span>
        <span class="text-slate-900">Gestión de Permisos y Licencias</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Formulario de Solicitud -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl p-8 sticky top-10">
                <div class="mb-8">
                    <h3 class="text-xl font-[900] text-slate-900 tracking-tight">Solicitar Permiso</h3>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">Completa los datos para revisión</p>
                </div>

                <form action="{{ route('asistencia.permisos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Tipo de Permiso</label>
                        <select name="tipo_permiso_id" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                            <option value="">Seleccionar motivo...</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Desde</label>
                            <input type="date" name="fecha_desde" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Hasta</label>
                            <input type="date" name="fecha_hasta" required class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Justificación / Motivo</label>
                        <textarea name="motivo" rows="4" required placeholder="Describe brevemente el motivo..." 
                                  class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20"></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Evidencia (Opcional)</label>
                        <div class="relative group">
                            <input type="file" name="evidencia" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="w-full bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl px-6 py-8 text-center group-hover:border-primary/30 transition-colors">
                                <i class="ph ph-cloud-arrow-up text-3xl text-slate-400 group-hover:text-primary transition-colors"></i>
                                <p class="text-[10px] font-black text-slate-400 uppercase mt-2">PDF, JPG o PNG (Max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white rounded-2xl py-5 text-xs font-black uppercase tracking-widest shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Enviar Solicitud
                    </button>
                </form>
            </div>
        </div>

        <!-- Historial de Solicitudes -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-50">
                    <h3 class="text-xl font-[900] text-slate-900 tracking-tight">Mis Solicitudes</h3>
                    <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">Estado de tus permisos actuales e históricos</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipo / Periodo</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Justificación</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                                <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($permisos as $permiso)
                            <tr class="group hover:bg-slate-50/40 transition-all duration-300">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $permiso->tipo->nombre }}</span>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase">
                                            {{ $permiso->fecha_desde->format('d/m') }} al {{ $permiso->fecha_hasta->format('d/m/Y') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-bold text-slate-600 max-w-xs truncate">{{ $permiso->motivo }}</p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    @php
                                        $colors = [
                                            'pendiente' => ['bg' => 'amber-50', 'text' => 'amber-600', 'dot' => 'amber-500'],
                                            'aprobado' => ['bg' => 'emerald-50', 'text' => 'emerald-600', 'dot' => 'emerald-500'],
                                            'rechazado' => ['bg' => 'rose-50', 'text' => 'rose-600', 'dot' => 'rose-500'],
                                        ];
                                        $c = $colors[$permiso->estado] ?? $colors['pendiente'];
                                    @endphp
                                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-{{ $c['bg'] }} text-{{ $c['text'] }} text-[10px] font-black uppercase rounded-lg border border-{{ $c['text'] }}/10">
                                        <span class="w-1.5 h-1.5 bg-{{ $c['dot'] }} rounded-full"></span>
                                        {{ $permiso->estado }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    @if($permiso->evidencia_path)
                                        <a href="{{ Storage::url($permiso->evidencia_path) }}" target="_blank" class="p-2 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 transition-all inline-flex items-center gap-2 text-[10px] font-black uppercase">
                                            <i class="ph ph-file-pdf text-lg"></i>
                                            Ver Doc
                                        </a>
                                    @else
                                        <span class="text-[9px] font-black text-slate-300 uppercase">Sin Doc</span>
                                    @endif
                                </td>
                            </tr>
                            @if($permiso->comentario_aprobador)
                                <tr class="bg-slate-50/30">
                                    <td colspan="4" class="px-8 py-4">
                                        <div class="flex items-start gap-3">
                                            <i class="ph ph-chat-centered-text text-lg text-slate-400"></i>
                                            <div class="flex-1">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Respuesta del Supervisor</p>
                                                <p class="text-xs font-bold text-slate-600 italic">"{{ $permiso->comentario_aprobador }}"</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center opacity-30">
                                        <i class="ph ph-folder-open text-6xl mb-4"></i>
                                        <p class="text-sm font-black uppercase">No tienes solicitudes registradas</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
