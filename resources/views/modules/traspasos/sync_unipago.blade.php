@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tighter">Sincronización Unipago</h1>
                <p class="text-slate-500 font-medium">Actualiza estados y motivos de traspasos masivamente</p>
            </div>
            <a href="{{ route('traspasos.index') }}" class="flex items-center gap-2 text-slate-400 hover:text-slate-900 transition-colors font-bold text-sm uppercase">
                <i class="ph ph-arrow-left"></i>
                Volver
            </a>
        </div>

        <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 bg-slate-50/50">
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i class="ph ph-arrows-counter-clockwise text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-slate-900 uppercase tracking-tight">Carga de Reporte Detallado</h2>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Copia y pega las 13 columnas desde tu Excel de Unipago</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('traspasos.sync.unipago.store') }}" method="POST" class="p-8">
                @csrf
                
                <!-- Guía de Columnas -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="w-6 h-6 bg-slate-900 text-white text-[10px] font-black rounded-full flex items-center justify-center">?</span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Estructura esperada (13 columnas)</span>
                    </div>
                    <div class="bg-slate-900 rounded-2xl p-4 overflow-x-auto">
                        <code class="text-[10px] text-blue-400 font-mono whitespace-nowrap">
                            [Num. Solicitud] [Fecha] [NSS] [Cédula] [NUI] [Tipo] [Ced. Solicitante] [Entidad] [Nombre] [Participación] [Motivo Exc.] [Estado] [Motivo Estado]
                        </code>
                    </div>
                </div>

                <div class="space-y-4">
                    <label class="block">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-widest ml-1 mb-2 block">Datos del Reporte</span>
                        <textarea 
                            name="data" 
                            rows="12" 
                            class="w-full rounded-3xl border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all text-sm font-medium p-6 placeholder:text-slate-300"
                            placeholder="Pega aquí las filas de tu Excel..."
                            required
                        ></textarea>
                    </label>

                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 flex gap-4 items-start">
                        <i class="ph ph-warning-circle text-amber-500 text-xl"></i>
                        <div>
                            <p class="text-[11px] font-bold text-amber-700 uppercase leading-tight">Aviso de Sincronización</p>
                            <p class="text-[10px] text-amber-600 font-medium mt-1">El sistema usará el "Número de Solicitud Traspaso" para encontrar registros existentes. Si el número no existe, se creará un nuevo registro como "Importado Unipago".</p>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-black text-white font-black py-5 rounded-3xl shadow-xl shadow-slate-200 transition-all active:scale-[0.98] flex items-center justify-center gap-3 uppercase tracking-widest text-sm">
                        <i class="ph ph-lightning"></i>
                        Iniciar Sincronización Masiva
                    </button>
                </div>
            </form>
        </div>

        <!-- Tips -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-8">
            <div class="p-6 bg-emerald-50 rounded-[32px] border border-emerald-100">
                <h4 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2 italic">Estados Automáticos</h4>
                <p class="text-xs text-emerald-700 font-medium leading-relaxed">
                    PE = En Proceso<br>
                    RE = Rechazado (Busca el motivo por código)<br>
                    OK = Efectivo (Cierra el ciclo)
                </p>
            </div>
            <div class="p-6 bg-blue-50 rounded-[32px] border border-blue-100">
                <h4 class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-2 italic">Recomendación</h4>
                <p class="text-xs text-blue-700 font-medium leading-relaxed">
                    Asegúrate de copiar las columnas desde el encabezado hasta el final para que el mapeo sea exacto.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
