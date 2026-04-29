@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Liquidación y Auditoría - SAFESURE
    </h2>
@endsection

@section('content')
<div class="py-12 px-4 max-w-7xl mx-auto space-y-6">
    
    <!-- Totales / Dashboard de Auditoría -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-emerald-500">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendiente de Pago</span>
            <div class="text-2xl font-black text-slate-800 mt-1">RD$ {{ number_format($totales['pendiente_monto'], 2) }}</div>
            <p class="text-[0.7rem] text-slate-500 mt-1">{{ $totales['pendiente_conteo'] }} expedientes completados.</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-blue-500">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Liquidado Histórico</span>
            <div class="text-2xl font-black text-slate-800 mt-1">RD$ {{ number_format($totales['liquidado_monto'], 2) }}</div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-rose-500">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Fuera de SLA (Crítico)</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ $totales['fuera_sla'] }}</div>
            <p class="text-[0.7rem] text-slate-500 mt-1">> 20 días sin cerrar.</p>
        </div>
        <div class="bg-primary p-6 rounded-2xl shadow-lg relative overflow-hidden group">
            <div class="relative z-10 text-white">
                <span class="text-xs font-bold uppercase tracking-wider opacity-80">Acción Requerida</span>
                <div class="text-lg font-black mt-2 leading-tight">Liquidar Lote Seleccionado</div>
                <button onclick="document.getElementById('modalLiquidacion').classList.remove('hidden')" class="mt-4 w-full py-2 bg-white text-primary rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-colors">Procesar Pago</button>
            </div>
            <span class="material-symbols-outlined absolute -bottom-4 -right-4 text-white/20 text-7xl group-hover:scale-125 transition-transform">account_balance_wallet</span>
        </div>
    </div>
    <!-- Eficiencia y SLA por Responsable (Punto 2) -->
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 mb-6">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600">
                <span class="material-symbols-outlined">analytics</span>
            </div>
            <div>
                <h4 class="text-sm font-black text-slate-800 uppercase tracking-tighter">Eficiencia de Entrega (SLA 20 Días)</h4>
                <p class="text-[0.65rem] text-slate-500 font-bold uppercase tracking-widest">Análisis por cada responsable asignado</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($eficiencia as $resp)
                @if($resp->total > 0)
                <div class="space-y-2">
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-black text-slate-700 uppercase tracking-tight">{{ $resp->nombre }}</span>
                        <span class="text-[0.6rem] font-bold text-slate-400">{{ $resp->total }} carnets cerrados</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden flex">
                        <div class="bg-emerald-500 h-full" style="width: {{ 100 - $resp->porcentaje_alerta }}%"></div>
                        <div class="bg-rose-500 h-full" style="width: {{ $resp->porcentaje_alerta }}%"></div>
                    </div>
                    <div class="flex justify-between text-[0.6rem] font-bold uppercase tracking-widest">
                        <span class="text-emerald-600">Eficiente: {{ number_format(100 - $resp->porcentaje_alerta, 1) }}%</span>
                        <span class="text-rose-600">Fuera de SLA: {{ number_format($resp->porcentaje_alerta, 1) }}%</span>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Lista de Auditoría -->
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="font-black text-slate-800 uppercase tracking-tighter text-lg">Auditoría de Entregas</h3>
                <p class="text-xs text-slate-500">Listado de expedientes completados listos para pago.</p>
                <div class="mt-4">
                    <a href="{{ route('liquidacion.history') }}" class="inline-flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-widest bg-slate-900 text-white px-4 py-2 rounded-xl shadow-xl shadow-slate-900/20 hover:scale-105 transition-all">
                        <span class="material-symbols-outlined text-sm">history</span>
                        Ver Histórico de Pagos
                    </a>
                </div>
            </div>
            <form id="filterForm" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="responsable_id" onchange="this.form.submit()" class="text-[0.7rem] bg-white border-slate-200 rounded-xl px-3 py-2 focus:ring-primary ring-0 font-bold text-slate-600">
                    <option value="">Todos los Responsables</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ request('responsable_id') == $resp->id ? 'selected' : '' }}>{{ $resp->nombre }}</option>
                    @endforeach
                </select>

                <select name="proveedor_id" onchange="this.form.submit()" class="text-[0.7rem] bg-white border-slate-200 rounded-xl px-3 py-2 focus:ring-primary ring-0 font-bold text-slate-600">
                    <option value="">Todos los Proveedores</option>
                    @foreach($proveedores as $prov)
                        <option value="{{ $prov->id }}" {{ request('proveedor_id') == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                    @endforeach
                </select>

                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cédula o Nombre..." class="text-[0.7rem] bg-white border-slate-200 rounded-xl pl-9 pr-4 py-2 w-48 focus:ring-primary ring-0 font-bold text-slate-600">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                </div>

                <label class="flex items-center gap-2 text-[0.7rem] font-bold text-slate-600 bg-white px-3 py-2 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                    <input type="checkbox" name="show_all" {{ request('show_all') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-primary focus:ring-primary border-slate-300">
                    Ver Liquidados
                </label>
            </form>
        </div>

        <div class="overflow-x-auto">
            <form id="liquidacionForm" action="{{ route('liquidacion.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-100/30">
                            <th class="py-4 px-6 text-[0.65rem] font-bold uppercase text-slate-400">
                                <input type="checkbox" onclick="toggleAll(this)" class="rounded text-primary border-slate-300">
                            </th>
                            <th class="py-4 px-2 text-[0.65rem] font-bold uppercase text-slate-400">Afiliado</th>
                            <th class="py-4 px-4 text-[0.65rem] font-bold uppercase text-slate-400 text-center">Estatus SLA</th>
                            <th class="py-4 px-4 text-[0.65rem] font-bold uppercase text-slate-400 text-right">Costo Entrega</th>
                            <th class="py-4 px-4 text-[0.65rem] font-bold uppercase text-slate-400">Entrega SAFESURE</th>
                            <th class="py-4 px-4 text-[0.65rem] font-bold uppercase text-slate-400">Estado Pago</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($afiliados as $afiliado)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                @if(!$afiliado->liquidado)
                                    <input type="checkbox" name="selected[]" value="{{ $afiliado->uuid }}" class="rounded text-emerald-500 border-slate-300 sel-check">
                                @endif
                            </td>
                            <td class="py-4 px-2">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-700 leading-tight">{{ $afiliado->nombre_completo }}</span>
                                    <span class="text-[0.65rem] font-bold text-primary">{{ $afiliado->cedula }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                @php
                                    $sla = $afiliado->sla_status;
                                    $color = $sla === 'critico' ? 'bg-rose-500' : ($sla === 'alerta' ? 'bg-amber-500' : ($sla === 'completado' ? 'bg-emerald-500' : 'bg-blue-500'));
                                @endphp
                                <div class="inline-flex items-center gap-1 bg-slate-100 px-2 py-1 rounded-full border border-slate-200">
                                    <div class="w-2 h-2 rounded-full {{ $color }}"></div>
                                    <span class="text-[0.6rem] font-black uppercase text-slate-600">{{ $afiliado->dias_transcurridos }} Días</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <span class="text-sm font-black text-slate-700">RD$ {{ number_format($afiliado->costo_entrega, 2) }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-xs font-bold text-slate-500 tracking-tighter">{{ $afiliado->fecha_entrega_safesure?->format('d M, Y') ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                @if($afiliado->liquidado)
                                    <div class="flex flex-col">
                                        <span class="text-[0.65rem] font-black text-emerald-600 uppercase flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[1rem]">check_circle</span> Liquidado
                                        </span>
                                        <span class="text-[0.6rem] text-slate-400 font-bold uppercase">{{ $afiliado->recibo_liquidacion }} ({{ $afiliado->fecha_liquidacion?->format('d/m/y') }})</span>
                                    </div>
                                @else
                                    <span class="text-[0.65rem] font-black text-amber-500 uppercase flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[1rem]">timer</span> Pendiente Pago
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div id="modalLiquidacion" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
                    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden p-8 border border-white/20">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-3xl">payments</span>
                            </div>
                            <div>
                                <h4 class="text-lg font-black text-slate-800 uppercase tracking-tighter">Procesar Liquidación</h4>
                                <p class="text-xs text-slate-500">Registrar pago masivo a SAFESURE.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-1.5 tracking-widest pl-1">Número de Recibo / Cheque</label>
                                <input type="text" name="recibo" required placeholder="Ej: CHQ-5501 o LOTE-MARZO" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-primary py-3 px-4 font-bold text-slate-700">
                            </div>
                            <div>
                                <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-1.5 tracking-widest pl-1">Fecha de Pago</label>
                                <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-primary py-3 px-4 font-bold text-slate-700">
                            </div>
                            <div>
                                <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-1.5 tracking-widest pl-1 border-t border-slate-100 pt-4 mt-4">Soporte / Evidencia (Foto/PDF)</label>
                                <input type="file" name="evidencia" accept="image/*,.pdf" class="w-full bg-slate-50 border-none rounded-2xl focus:ring-2 focus:ring-primary py-3 px-4 font-bold text-slate-700 text-xs">
                                <p class="text-[0.6rem] text-slate-400 mt-1 pl-1 italic">Opcional: Captura de cheque o transferencia.</p>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button type="button" onclick="document.getElementById('modalLiquidacion').classList.add('hidden')" class="flex-1 py-3 text-sm font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50 rounded-2xl transition-colors">Cancelar</button>
                            <button type="submit" class="flex-[2] py-4 bg-primary text-white text-sm font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">Confirmar Liquidación</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="p-6 bg-slate-50/30 border-t border-slate-50">
            {{ $afiliados->links() }}
        </div>
    </div>
</div>

<script>
    function toggleAll(source) {
        checkboxes = document.getElementsByName('selected[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
</script>
@endsection
