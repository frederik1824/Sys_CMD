@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-10 animate-in fade-in zoom-in duration-700">
    {{-- Navegación y Acciones --}}
    <div class="flex items-center justify-between no-print">
        <div class="flex items-center gap-4">
            <a href="{{ route('dispersion.reports') }}" class="h-10 w-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-emerald-600 transition-all shadow-sm">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div>
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Vista Previa Ejecutiva</h4>
                <h2 class="text-xl font-black text-slate-900">{{ $period->month_name }} {{ $period->year }}</h2>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="flex items-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="ph ph-printer text-lg"></i>
                Imprimir
            </button>
            <a href="{{ route('dispersion.report', $period->id) }}" class="flex items-center gap-2 px-6 py-3 bg-slate-900 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-2xl shadow-slate-900/20">
                <i class="ph ph-file-pdf text-lg"></i>
                Exportar PDF Institucional
            </a>
        </div>
    </div>

    {{-- Hoja del Reporte --}}
    <div class="bg-white border border-slate-200 rounded-[3rem] shadow-2xl p-12 lg:p-20 relative overflow-hidden print:shadow-none print:border-none print:p-0">
        {{-- Branding y Encabezado del Reporte --}}
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-12 mb-12">
            <div class="space-y-6">
                <img src="{{ asset('img/Logo.png') }}" class="h-16 w-auto grayscale-0 brightness-110">
                <div class="space-y-1">
                    <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Control de Dispersión</h1>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.4em]">ARS CMD - Gestión Operativa PDSS</p>
                </div>
            </div>
            <div class="text-right space-y-4">
                <div class="inline-block px-4 py-2 bg-slate-900 text-white rounded-xl font-black text-[10px] uppercase tracking-[0.3em]">
                    Reporte Institucional
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Periodo Auditado</p>
                    <p class="text-lg font-black text-slate-900">{{ $period->month_name }} {{ $period->year }}</p>
                </div>
            </div>
        </div>

        {{-- Resumen Ejecutivo en Tarjetas --}}
        <div class="grid grid-cols-3 gap-8 mb-16">
            <div class="p-8 bg-slate-50 rounded-[2.5rem] space-y-3 border border-slate-100">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Afiliados PDSS</p>
                <h4 class="text-3xl font-black text-slate-900 tabular-nums">{{ number_format($stats['total_afiliados']) }}</h4>
                <div class="flex items-center gap-2 text-[9px] font-black text-emerald-600 uppercase tracking-widest">
                    <i class="ph-fill ph-check-circle"></i> Auditoría Completada
                </div>
            </div>
            <div class="p-8 bg-slate-50 rounded-[2.5rem] space-y-3 border border-slate-100">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Monto Dispersado</p>
                <h4 class="text-2xl font-black text-slate-900 tabular-nums">RD$ {{ number_format($stats['monto_total'], 2) }}</h4>
                <div class="flex items-center gap-2 text-[9px] font-black text-blue-600 uppercase tracking-widest">
                    <i class="ph-fill ph-bank"></i> Flujo Consolidado
                </div>
            </div>
            <div class="p-8 bg-rose-50/50 rounded-[2.5rem] space-y-3 border border-rose-100">
                <p class="text-[9px] font-black text-rose-400 uppercase tracking-widest">Bajas Reportadas</p>
                <h4 class="text-3xl font-black text-rose-600 tabular-nums">{{ number_format($stats['total_bajas']) }}</h4>
                <div class="flex items-center gap-2 text-[9px] font-black text-rose-600 uppercase tracking-widest">
                    <i class="ph-fill ph-warning-circle"></i> Movimientos de Salida
                </div>
            </div>
        </div>

        {{-- Detalle de Indicadores --}}
        <div class="space-y-8 mb-16">
            <div class="flex items-center gap-4">
                <div class="h-px flex-1 bg-slate-100"></div>
                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.4em]">Detalle de Dispersión</h4>
                <div class="h-px flex-1 bg-slate-100"></div>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-slate-100">
                        <th class="py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Indicador</th>
                        <th class="py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">1er Corte</th>
                        <th class="py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">2do Corte</th>
                        <th class="py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Consolidado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm font-bold text-slate-700">
                    @foreach($indicators as $ind)
                    @php 
                        if (str_contains($ind->name, 'Pérdida de Empleo') || str_contains($ind->name, 'Separación o Divorcio')) continue;
                        
                        $c1 = $period->cortes->where('corte_number', 1)->first();
                        $v1 = $c1 ? $c1->values->where('indicator_id', $ind->id)->first() : null;
                        $val1 = ($ind->category === 'Montos' ? ($v1->amount ?? 0) : ($v1->quantity ?? 0));

                        $c2 = $period->cortes->where('corte_number', 2)->first();
                        $v2 = $c2 ? $c2->values->where('indicator_id', $ind->id)->first() : null;
                        $val2 = ($ind->category === 'Montos' ? ($v2->amount ?? 0) : ($v2->quantity ?? 0));
                        
                        // Lógica de Consolidado (Snapshot para totales, Suma para el resto)
                        $isSnapshot = $ind->code === 'TOTAL_GENERAL_PDSS';
                        $total = ($isSnapshot && $val2 > 0) ? $val2 : ($isSnapshot ? $val1 : ($val1 + $val2));
                    @endphp
                    <tr class="{{ $ind->is_total ? 'bg-slate-50/50' : '' }}">
                        <td class="py-4 {{ $ind->is_total ? 'font-black text-slate-900' : '' }}">{{ $ind->name }}</td>
                        <td class="py-4 text-right tabular-nums">{{ $ind->category === 'Montos' ? number_format($val1, 2) : number_format($val1) }}</td>
                        <td class="py-4 text-right tabular-nums">{{ $ind->category === 'Montos' ? number_format($val2, 2) : number_format($val2) }}</td>
                        <td class="py-4 text-right tabular-nums font-black {{ $ind->is_total ? 'text-slate-900' : 'text-emerald-600' }}">
                            {{ $ind->category === 'Montos' ? number_format($total, 2) : number_format($total) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Detalle de Bajas --}}
        <div class="space-y-8">
            <div class="flex items-center gap-4">
                <div class="h-px flex-1 bg-slate-100"></div>
                <h4 class="text-[10px] font-black text-rose-400 uppercase tracking-[0.4em]">Control de Bajas</h4>
                <div class="h-px flex-1 bg-slate-100"></div>
            </div>

            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-slate-100">
                        <th class="py-5 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">Motivo de Baja</th>
                        <th class="py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">1er Corte</th>
                        <th class="py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">2do Corte</th>
                        <th class="py-5 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm font-bold text-slate-700">
                    @foreach($bajaTypes as $type)
                    @php 
                        $c1 = $period->cortes->where('corte_number', 1)->first();
                        $b1 = $c1 ? $c1->bajaValues->where('baja_type_id', $type->id)->first() : null;
                        $q1 = $b1->quantity ?? 0;

                        $c2 = $period->cortes->where('corte_number', 2)->first();
                        $b2 = $c2 ? $c2->bajaValues->where('baja_type_id', $type->id)->first() : null;
                        $q2 = $b2->quantity ?? 0;
                    @endphp
                    <tr>
                        <td class="py-4">{{ $type->name }}</td>
                        <td class="py-4 text-right tabular-nums">{{ number_format($q1) }}</td>
                        <td class="py-4 text-right tabular-nums">{{ number_format($q2) }}</td>
                        <td class="py-4 text-right tabular-nums font-black text-rose-600">{{ number_format($q1 + $q2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Firmas --}}
        <div class="mt-20 grid grid-cols-2 gap-20">
            <div class="border-t border-slate-200 pt-4 text-center">
                <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Responsable de Operaciones</p>
                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Departamento de Dispersión</p>
            </div>
            <div class="border-t border-slate-200 pt-4 text-center">
                <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Gerencia de Auditoría</p>
                <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">ARS CMD Institutional</p>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body { background: white !important; }
        .no-print { display: none !important; }
        @page { margin: 0; }
    }
</style>
@endsection
