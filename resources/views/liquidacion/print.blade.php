<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relación de Liquidación - {{ $recibo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .print-container { box-shadow: none; border: none; width: 100%; max-width: 100%; margin: 0; padding: 0; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 md:p-8">

    <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-3xl overflow-hidden print-container">
        <!-- Header -->
        <div class="bg-slate-900 p-8 text-white relative">
            <div class="flex justify-between items-start relative z-10">
                <div>
                    <h1 class="text-3xl font-black uppercase tracking-tighter">Relación de Liquidación</h1>
                    <p class="text-slate-400 text-sm mt-1">Soporte de Pago de Carnetización</p>
                </div>
                <div class="text-right">
                    <div class="text-xl font-black text-emerald-400">RD$ {{ number_format($totalMonto, 2) }}</div>
                    <p class="text-[0.65rem] font-bold uppercase tracking-widest text-slate-500">Monto Total a Liquidar</p>
                </div>
            </div>
            
            <div class="mt-8 grid grid-cols-3 gap-6 border-t border-white/10 pt-6">
                <div>
                    <span class="text-[0.6rem] font-black uppercase text-slate-500 block mb-1">Responsable / Entidad</span>
                    <span class="text-sm font-bold">{{ $responsable }}</span>
                </div>
                <div>
                    <span class="text-[0.6rem] font-black uppercase text-slate-500 block mb-1">Referencia Recibo</span>
                    <span class="text-sm font-bold">{{ $recibo }}</span>
                </div>
                <div>
                    <span class="text-[0.6rem] font-black uppercase text-slate-500 block mb-1">Fecha de Liquidación</span>
                    <span class="text-sm font-bold">{{ $fecha?->format('d/m/Y') ?? now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="p-8">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b-2 border-slate-100">
                        <th class="py-3 text-[0.65rem] font-black uppercase text-slate-400">#</th>
                        <th class="py-3 text-[0.65rem] font-black uppercase text-slate-400">Cédula</th>
                        <th class="py-3 text-[0.65rem] font-black uppercase text-slate-400">Nombre del Afiliado</th>
                        <th class="py-3 text-[0.65rem] font-black uppercase text-slate-400">Empresa</th>
                        <th class="py-3 text-[0.65rem] font-black uppercase text-slate-400 text-right">Costo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($afiliados as $index => $afiliado)
                    <tr>
                        <td class="py-3 text-xs font-bold text-slate-400">{{ $index + 1 }}</td>
                        <td class="py-3 text-xs font-bold text-slate-800">{{ $afiliado->cedula }}</td>
                        <td class="py-3 text-xs font-black text-slate-700 capitalize">{{ strtolower($afiliado->nombre_completo) }}</td>
                        <td class="py-3 text-[0.65rem] font-bold text-slate-500">{{ $afiliado->empresa ?? 'N/A' }}</td>
                        <td class="py-3 text-xs font-black text-slate-900 text-right">RD$ {{ number_format($afiliado->costo_entrega, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-900">
                        <td colspan="4" class="py-4 text-sm font-black text-slate-900 uppercase text-right">Total General:</td>
                        <td class="py-4 text-lg font-black text-slate-900 text-right underline decoration-emerald-500 decoration-4 underline-offset-4">RD$ {{ number_format($totalMonto, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

            <!-- Signatures -->
            <div class="mt-20 grid grid-cols-2 gap-20">
                <div class="border-t border-slate-300 pt-4 text-center">
                    <p class="text-xs font-black uppercase text-slate-400">Preparado por</p>
                    <p class="text-sm font-bold text-slate-800 mt-1">{{ auth()->user()->name }}</p>
                </div>
                <div class="border-t border-slate-300 pt-4 text-center">
                    <p class="text-xs font-black uppercase text-slate-400">Autorizado por</p>
                    <div class="h-10"></div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 p-6 text-center border-t border-slate-100">
            <p class="text-[0.6rem] text-slate-400 font-bold uppercase tracking-widest">Este documento es una relación oficial de servicios prestados y liquidados | Generado por SysCarnet v2.0</p>
        </div>
    </div>

    <!-- Actions -->
    <div class="max-w-4xl mx-auto mt-8 flex justify-between gap-4 no-print pb-10">
        <a href="{{ route('liquidacion.index') }}" class="flex items-center gap-2 px-6 py-3 bg-white text-slate-600 font-black text-xs uppercase tracking-widest rounded-2xl border border-slate-200 hover:bg-slate-50 transition-all shadow-sm">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Volver
        </a>
        <button onclick="window.print()" class="flex items-center gap-2 px-8 py-4 bg-emerald-600 text-white font-black text-sm uppercase tracking-widest rounded-2xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-500/20 active:scale-95">
            <span class="material-symbols-outlined text-lg">print</span>
            Imprimir Relación
        </button>
    </div>

    <!-- Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</body>
</html>
