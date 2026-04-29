<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoja de Despacho #{{ str_pad($despacho->id, 5, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { font-size: 10pt; background: white; }
            .box { break-inside: avoid; }
        }
        @page { margin: 1cm; }
    </style>
</head>
<body class="bg-slate-50 font-sans p-8">
    
    <div class="max-w-4xl mx-auto bg-white p-10 shadow-lg border border-slate-100 rounded-xl relative">
        {{-- Print Action --}}
        <div class="absolute right-10 top-18 no-print">
            <button onclick="window.print()" class="bg-primary hover:bg-slate-900 text-white px-6 py-2 rounded-lg font-bold flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Imprimir Hoja
            </button>
        </div>

        {{-- Header --}}
        <div class="flex justify-between items-start border-b-2 border-slate-900 pb-8 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-950 uppercase tracking-tighter">Hoja de Ruta / Despacho</h1>
                <p class="text-slate-500 font-bold mt-1 uppercase tracking-widest text-xs">Sistema de Gestión de Identificaciones - ARS CMD</p>
            </div>
            <div class="text-right">
                <p class="text-4xl font-black text-slate-900 leading-none">#{{ str_pad($despacho->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-[0.65rem] font-bold text-slate-400 mt-2 italic uppercase tracking-widest">Generado: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        {{-- Info Grid --}}
        <div class="grid grid-cols-2 gap-10 mb-10">
            <div class="space-y-4">
                <div class="group">
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Mensajero Asignado</p>
                    <p class="text-lg font-black text-slate-900 leading-tight">{{ $despacho->mensajero->nombre }}</p>
                    <p class="text-xs font-bold text-slate-500 italic">{{ $despacho->mensajero->vehiculo_tipo }} - {{ $despacho->mensajero->placa ?? 'S/P' }}</p>
                </div>
                <div>
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Ruta / Zona</p>
                    <p class="text-sm font-black text-slate-800">{{ $despacho->ruta->nombre ?? 'DESPACHO LIBRE' }}</p>
                    <p class="text-[0.65rem] font-bold text-slate-500">{{ $despacho->ruta->zona ?? 'Nivel Nacional' }}</p>
                </div>
            </div>
            <div class="space-y-4 text-right">
                <div>
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Fecha de Salida</p>
                    <p class="text-sm font-bold text-slate-800">{{ $despacho->fecha_salida ? $despacho->fecha_salida->format('d/m/Y H:i') : '-' }}</p>
                </div>
                <div>
                    <p class="text-[0.6rem] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Total de Items</p>
                    <p class="text-sm font-bold text-slate-800">{{ $despacho->items->count() }} Entregas</p>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-hidden border-2 border-slate-900 rounded-lg">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        <th class="px-6 py-4 text-[0.65rem] font-black uppercase tracking-widest w-12 text-center">#</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black uppercase tracking-widest">Afiliado / Identificación</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black uppercase tracking-widest">Ubicación y Dirección</th>
                        <th class="px-6 py-4 text-[0.65rem] font-black uppercase tracking-widest text-center">Firma Recibido</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 border-b border-slate-900">
                    @foreach($despacho->items as $index => $item)
                        <tr class="box border-b border-slate-200">
                            <td class="px-6 py-5 text-sm font-black text-slate-400 text-center">{{ $index + 1 }}</td>
                            <td class="px-6 py-5 shrink-0">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 leading-tight mb-0.5">{{ $item->afiliado->nombre_completo }}</span>
                                    <span class="text-[0.65rem] font-bold text-slate-500 uppercase tracking-wider">Cédula: {{ $item->afiliado->cedula }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[0.6rem] font-black text-slate-800 uppercase tracking-tighter leading-tight">{{ $item->afiliado->empresaModel->nombre ?? 'ENTREGA DIRECTA' }}</span>
                                        <span class="text-[0.55rem] font-black px-1.5 py-0.5 rounded {{ $item->afiliado->direccion ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}">
                                            {{ $item->afiliado->direccion ? '📍 PERSONAL' : '🏢 EMPRESA' }}
                                        </span>
                                    </div>
                                    <span class="text-[0.65rem] font-bold text-slate-400 mt-0.5 italic">{{ $item->afiliado->provincia_nombre }}, {{ $item->afiliado->municipio_nombre }}</span>
                                    <p class="text-[0.7rem] font-medium text-slate-900 mt-2 line-clamp-2 leading-tight bg-slate-50 p-2 rounded border border-dotted border-slate-300">
                                        {{ $item->afiliado->direccion_final }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="w-24 h-12 border-b-2 border-slate-300 mx-auto mt-4"></div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div class="mt-12 pt-8 border-t border-slate-200 text-center text-slate-400">
            <p class="text-[0.6rem] font-bold uppercase tracking-[0.3em]">Documento Oficial de Control Logístico ARS CMD - SafeSure</p>
            <p class="text-[0.55rem] mt-2 italic">Este documento certifica la salida de carnets para su entrega final. Favor manejar con discreción.</p>
        </div>
    </div>

    <script>
        // Opcional: Auto-imprimir al cargar si se desea
        // window.onload = () => window.print();
    </script>
</body>
</html>
