@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter flex items-center gap-4">
                <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center shadow-lg shadow-slate-900/20">
                    <i class="ph ph-clock-counter-clockwise text-white text-2xl"></i>
                </div>
                Trazabilidad de Seguridad
            </h1>
            <p class="text-slate-500 mt-2 font-medium">Historial cronológico de cambios en permisos, accesos e identidad.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="px-6 py-3 bg-white border border-slate-100 rounded-2xl shadow-sm">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Eventos Registrados</span>
                <span class="text-xl font-black text-slate-900">{{ $logs->total() }} Actividades</span>
            </div>
        </div>
    </div>

    <!-- Timeline Wrapper -->
    <div class="relative">
        <!-- Vertical Line -->
        <div class="absolute left-1/2 -translate-x-1/2 top-0 bottom-0 w-px bg-slate-200 hidden lg:block"></div>

        <div class="space-y-12 relative">
            @forelse($logs as $log)
            <div class="flex flex-col lg:flex-row items-center gap-8 group">
                <!-- Date/Time (Left on Desktop) -->
                <div class="lg:w-1/2 lg:text-right order-2 lg:order-1">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">{{ \Carbon\Carbon::parse($log->created_at)->format('d M, Y') }}</p>
                    <p class="text-xl font-black text-slate-900">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}</p>
                </div>

                <!-- Status Orb -->
                <div class="relative z-10 order-1 lg:order-2">
                    <div class="w-12 h-12 rounded-full border-4 border-white shadow-xl flex items-center justify-center transition-all duration-500
                        @if(str_contains($log->action, 'grant')) bg-emerald-500 text-white
                        @elseif(str_contains($log->action, 'revoke')) bg-rose-500 text-white
                        @elseif(str_contains($log->action, 'impersonate')) bg-amber-500 text-white
                        @else bg-blue-500 text-white @endif">
                        <i class="ph ph-{{ str_contains($log->action, 'impersonate') ? 'eye' : (str_contains($log->action, 'revoke') ? 'lock' : 'shield-check') }} text-xl"></i>
                    </div>
                </div>

                <!-- Content (Right on Desktop) -->
                <div class="lg:w-1/2 order-3">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/40 group-hover:shadow-2xl transition-all duration-500 relative overflow-hidden">
                        
                        <div class="flex items-start justify-between mb-4">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                                @if(str_contains($log->action, 'grant')) bg-emerald-50 text-emerald-600
                                @elseif(str_contains($log->action, 'revoke')) bg-rose-50 text-rose-600
                                @else bg-slate-100 text-slate-500 @endif">
                                {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                            </span>
                        </div>

                        <!-- Human Readable Logic -->
                        <div class="space-y-3">
                            <p class="text-sm font-medium text-slate-600 leading-relaxed">
                                <span class="font-black text-slate-900">{{ $log->performer_name ?: 'Sistema' }}</span> 
                                @if($log->action == 'grant_access')
                                    otorgó acceso al módulo <span class="font-black text-slate-900">{{ $log->application_key }}</span> a
                                @elseif($log->action == 'revoke_access')
                                    revocó el acceso de <span class="font-black text-slate-900">{{ $log->application_key }}</span> a
                                @elseif($log->action == 'impersonate_start')
                                    inició una sesión de soporte (impersonación) como
                                @else
                                    realizó una acción sobre
                                @endif
                                <span class="font-black text-slate-900">{{ $log->target_name }}</span>.
                            </p>
                        </div>

                        <!-- Technical Metadata -->
                        <div class="mt-6 pt-6 border-t border-slate-50 flex items-center gap-6">
                            <div class="flex items-center gap-2">
                                <i class="ph ph-map-pin text-slate-400"></i>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $log->ip_address }}</span>
                            </div>
                            <div class="flex items-center gap-2 max-w-[150px] truncate" title="{{ $log->user_agent }}">
                                <i class="ph ph-browser text-slate-400"></i>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ explode(' ', $log->user_agent)[0] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-32 text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ph ph-ghost text-4xl text-slate-200"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900">Historial Limpio</h3>
                <p class="text-slate-400">Aún no se han registrado eventos de seguridad.</p>
            </div>
            @endforelse
        </div>

        @if($logs->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
