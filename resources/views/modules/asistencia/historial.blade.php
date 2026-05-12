@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-10 space-y-10 min-h-screen bg-[#f8fafc]">
    
    <div class="flex items-center gap-4 mb-8 text-[11px] font-black uppercase tracking-widest text-slate-400">
        <a href="{{ route('asistencia.index') }}" class="hover:text-primary transition-colors flex items-center gap-2">
            <i class="ph-bold ph-arrow-left"></i> Volver al Reloj
        </a>
        <span>/</span>
        <span class="text-slate-900">Mi Historial de Asistencia</span>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-[900] text-slate-900 tracking-tight">Registro de Actividad</h3>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">Consulta tus marcas de tiempo históricas</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Fecha</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Entrada</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Almuerzo</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Salida</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Horas Netas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($registros as $reg)
                    <tr class="group hover:bg-slate-50/40 transition-all duration-300">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $reg->fecha->translatedFormat('d M, Y') }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ $reg->fecha->translatedFormat('l') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-black text-slate-900">{{ $reg->hora_entrada ? $reg->hora_entrada->format('h:i A') : '---' }}</span>
                                @if($reg->minutos_tardanza > 0)
                                    <span class="text-[9px] font-black text-rose-600 uppercase tracking-tighter">Tarde (+{{ $reg->minutos_tardanza }}m)</span>
                                @elseif($reg->hora_entrada)
                                    <span class="text-[9px] font-black text-emerald-600 uppercase tracking-tighter">A Tiempo</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @if($reg->inicio_almuerzo)
                                <span class="text-xs font-black text-slate-900">{{ $reg->inicio_almuerzo->format('h:i') }} - {{ $reg->fin_almuerzo ? $reg->fin_almuerzo->format('h:i') : '...' }}</span>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 italic">No registrado</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-center text-sm font-black text-slate-900">
                            {{ $reg->hora_salida ? $reg->hora_salida->format('h:i A') : 'PENDIENTE' }}
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-black text-slate-900">{{ floor($reg->minutos_trabajados_neto / 60) }}h {{ $reg->minutos_trabajados_neto % 60 }}m</span>
                                <span class="text-[9px] font-black uppercase tracking-tighter {{ $reg->cumplio_jornada ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $reg->cumplio_jornada ? 'Cumplido' : 'Incompleto' }}
                                </span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-8 bg-slate-50 border-t border-slate-100">
            {{ $registros->links() }}
        </div>
    </div>

</div>
@endsection
