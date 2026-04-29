@extends('layouts.app')

@section('content')
<div class="p-8 lg:p-12 max-w-6xl mx-auto space-y-12 no-print">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-slate-100 pb-8">
        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="w-2 h-10 bg-indigo-600 rounded-full shadow-lg shadow-indigo-600/30"></div>
                <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase">Resumen <span class="text-indigo-600 font-light italic">Ejecutivo</span></h2>
            </div>
            <p class="text-slate-400 font-medium pl-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">schedule</span>
                Actualizado en tiempo real • {{ date('d M, Y H:i') }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 no-print">
            <!-- Filter Form -->
            <form action="{{ route('reportes.resumen') }}" method="GET" class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl border border-slate-200">
                <span class="material-symbols-outlined text-slate-400 pl-2 text-lg">filter_alt</span>
                <select name="responsable_id" onchange="this.form.submit()" class="bg-transparent border-none text-[0.65rem] font-black uppercase tracking-widest text-slate-700 focus:ring-0 cursor-pointer pr-8">
                    <option value="">Todas las Gestoras</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ request('responsable_id') == $resp->id ? 'selected' : '' }}>
                            {{ $resp->nombre }}
                        </option>
                    @endforeach
                </select>
            </form>

            <button onclick="window.print()" class="bg-white text-slate-700 border border-slate-200 px-6 py-3 rounded-2xl font-black text-[0.65rem] uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-lg">print</span>
                Imprimir Informe
            </button>
            <a href="{{ route('reportes.export_center') }}" class="bg-slate-900 text-white px-6 py-3 rounded-2xl font-black text-[0.65rem] uppercase tracking-widest hover:bg-slate-800 transition-all flex items-center gap-2 shadow-xl shadow-slate-900/20">
                <span class="material-symbols-outlined text-lg">arrow_back</span>
                Cerrar
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php 
            $tot = $data->sum('total'); 
            $ent = $data->sum('entregados');
            $acu = $data->sum('con_acuse');
        @endphp
        <div class="bg-indigo-600 p-8 rounded-[2.5rem] text-white shadow-2xl shadow-indigo-600/20 relative overflow-hidden group">
            <span class="text-[0.6rem] font-black uppercase tracking-widest opacity-70">Volumen Total</span>
            <div class="text-5xl font-black mt-2">{{ number_format($tot) }} <span class="text-lg opacity-50 font-medium">casos</span></div>
            <span class="material-symbols-outlined absolute -bottom-6 -right-6 text-white/10 text-9xl">analytics</span>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <span class="text-[0.6rem] font-black uppercase tracking-widest text-slate-400">Entrega Finalizada</span>
            <div class="text-5xl font-black mt-2 text-emerald-500">{{ number_format($ent) }}</div>
            <div class="flex items-center gap-1 mt-2">
                <div class="h-1 bg-emerald-500 rounded-full" style="width: {{ $tot > 0 ? ($ent/$tot)*100 : 0 }}%"></div>
                <span class="text-[10px] font-bold text-emerald-600">{{ $tot > 0 ? round(($ent/$tot)*100, 1) : 0 }}%</span>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm text-slate-400">
            <span class="text-[0.6rem] font-black uppercase tracking-widest">En Proceso Acuse</span>
            <div class="text-5xl font-black mt-2 text-slate-800">{{ number_format($acu) }}</div>
            <div class="text-[10px] font-bold mt-2 uppercase tracking-tighter">Validación de formulario pendiente</div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-slate-50/50">
                    <th class="px-8 py-6 text-left text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Gestora / Responsable</th>
                    <th class="px-8 py-6 text-left text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Corte Operativo</th>
                    <th class="px-8 py-6 text-center text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Total</th>
                    <th class="px-8 py-6 text-center text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Entregados</th>
                    <th class="px-8 py-6 text-center text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Con Acuse</th>
                    <th class="px-8 py-6 text-center text-[0.6rem] font-black text-slate-400 uppercase tracking-widest">Progreso</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($data as $row)
                <tr class="hover:bg-slate-50/30 transition-colors group">
                    <td class="px-8 py-6">
                        <div class="flex items-center gap-3">
                            <div class="w-1.5 h-6 {{ $row->responsable_nombre == 'SAFESURE' ? 'bg-amber-400' : 'bg-indigo-400' }} rounded-full"></div>
                            <span class="text-sm font-black text-slate-800 tracking-tight">{{ $row->responsable_nombre ?? 'SIN ASIGNAR' }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-6">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-tighter">{{ $row->corte_nombre ?? 'N/A' }}</span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="text-sm font-black text-slate-900">{{ number_format($row->total) }}</span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-full text-xs font-black">{{ number_format($row->entregados) }}</span>
                    </td>
                    <td class="px-8 py-6 text-center">
                        <span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-xs font-black">{{ number_format($row->con_acuse) }}</span>
                    </td>
                    <td class="px-8 py-6">
                        @php $prog = $row->total > 0 ? ($row->entregados / $row->total) * 100 : 0; @endphp
                        <div class="flex items-center gap-3 justify-center">
                            <div class="w-16 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $prog }}%"></div>
                            </div>
                            <span class="text-[10px] font-black text-slate-400">{{ round($prog) }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-900 text-white font-black">
                <tr>
                    <td colspan="2" class="px-8 py-5 text-[0.65rem] uppercase tracking-widest">Totales Consolidados</td>
                    <td class="px-8 py-5 text-center text-sm">{{ number_format($tot) }}</td>
                    <td class="px-8 py-5 text-center text-sm text-emerald-400">{{ number_format($ent) }}</td>
                    <td class="px-8 py-5 text-center text-sm text-amber-400">{{ number_format($acu) }}</td>
                    <td class="px-8 py-5">
                        @php $totalProg = $tot > 0 ? ($ent / $tot) * 100 : 0; @endphp
                        <div class="flex items-center gap-3 justify-center">
                            <div class="w-16 bg-white/10 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-emerald-400 h-full rounded-full" style="width: {{ $totalProg }}%"></div>
                            </div>
                            <span class="text-[10px]">{{ round($totalProg) }}%</span>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Disclaimer -->
    <div class="p-8 bg-slate-50 rounded-[2rem] border border-slate-100 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="material-symbols-outlined text-slate-400">info</span>
            <p class="text-[0.65rem] text-slate-400 font-bold uppercase tracking-widest leading-relaxed">
                Este informe consolida la data operacional de ambas gestoras.<br>
                Los porcentajes de progreso se basan únicamente en entregas completadas físicamente.
            </p>
        </div>
        <img src="https://www.arsmcmd.com/wp-content/uploads/2021/04/logo-ars-cmd.png" class="h-8 grayscale opacity-20" alt="Logo">
    </div>
</div>

<!-- Print-only View -->
<div class="only-print p-10 bg-white min-h-screen text-slate-950">
    <div class="flex justify-between items-center border-b-2 border-slate-900 pb-8 mb-10">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tighter">Informe de Situación</h1>
            <p class="text-lg font-bold text-slate-500">Gestión Operativa de Servicios</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-black uppercase">Fecha del Reporte</p>
            <p class="text-xl font-bold">{{ date('d / m / Y') }}</p>
        </div>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b-2 border-slate-950">
                <th class="py-4 text-[10px] font-black uppercase tracking-widest">Responsable</th>
                <th class="py-4 text-[10px] font-black uppercase tracking-widest">Corte Operativo</th>
                <th class="py-4 text-center text-[10px] font-black uppercase tracking-widest">Total</th>
                <th class="py-4 text-center text-[10px] font-black uppercase tracking-widest">Entregados</th>
                <th class="py-4 text-center text-[10px] font-black uppercase tracking-widest">Con Acuse</th>
                <th class="py-4 text-center text-[10px] font-black uppercase tracking-widest">% Avance</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr class="border-b border-slate-200">
                <td class="py-4 font-black text-sm uppercase">{{ $row->responsable_nombre ?? 'S/A' }}</td>
                <td class="py-4 font-bold text-xs uppercase">{{ $row->corte_nombre ?? 'N/A' }}</td>
                <td class="py-4 text-center font-bold">{{ number_format($row->total) }}</td>
                <td class="py-4 text-center font-bold text-emerald-700">{{ number_format($row->entregados) }}</td>
                <td class="py-4 text-center font-bold text-amber-700">{{ number_format($row->con_acuse) }}</td>
                <td class="py-4 text-center font-black">
                    {{ $row->total > 0 ? round(($row->entregados / $row->total) * 100) : 0 }}%
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-slate-50 font-black border-t-2 border-slate-900">
                <td colspan="2" class="py-4 px-2 uppercase tracking-tighter">Totales Consolidados</td>
                <td class="py-4 text-center">{{ number_format($tot) }}</td>
                <td class="py-4 text-center">{{ number_format($ent) }}</td>
                <td class="py-4 text-center">{{ number_format($acu) }}</td>
                <td class="py-4 text-center">{{ $tot > 0 ? round(($ent/$tot)*100) : 0 }}%</td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-20 grid grid-cols-2 gap-20">
        <div class="border-t border-slate-950 pt-4 text-center">
            <p class="text-[10px] font-black uppercase">Firma Autorizada CMD</p>
        </div>
        <div class="border-t border-slate-950 pt-4 text-center">
            <p class="text-[10px] font-black uppercase">Firma Recibido Safesure</p>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    .only-print { display: block !important; }
    body { background: white !important; }
    @page { margin: 2cm; }
}
@media screen {
    .only-print { display: none !important; }
}
</style>
@endsection
