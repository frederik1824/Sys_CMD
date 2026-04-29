@extends('layouts.app')

@section('content')
<div class="p-8 max-w-4xl mx-auto">
    <div class="mb-10">
        <a href="{{ route('traspasos.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-primary font-bold text-xs mb-4 transition-colors">
            <i class="ph ph-arrow-left"></i> Volver al listado
        </a>
        <h1 class="text-4xl font-extrabold tracking-tighter text-slate-900">Importar Reporte Unipago</h1>
        <p class="text-slate-500 font-medium mt-1">Sincroniza el estado de las solicitudes copiando y pegando desde Excel.</p>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden p-10">
        <div class="mb-8 p-6 bg-blue-50 border border-blue-100 rounded-3xl">
            <h3 class="text-blue-900 font-black text-xs uppercase tracking-widest mb-3 flex items-center gap-2">
                <i class="ph ph-info text-lg"></i> Instrucciones de pegado
            </h3>
            <ol class="text-blue-800 text-xs font-medium space-y-2 list-decimal ml-4">
                <li>Abre tu archivo Excel de Unipago.</li>
                <li>Selecciona todas las filas (incluyendo encabezados).</li>
                <li>Presiona <kbd class="px-2 py-0.5 bg-blue-200 rounded text-[10px] font-black">CTRL + C</kbd>.</li>
                <li>Pega el contenido en el recuadro de abajo (<kbd class="px-2 py-0.5 bg-blue-200 rounded text-[10px] font-black">CTRL + V</kbd>).</li>
                <li>El sistema detectará automáticamente los cambios de estado y nuevos registros.</li>
            </ol>
        </div>

        <form action="{{ route('traspasos.import.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-3 ml-2">Datos del Excel (TSV)</label>
                <textarea 
                    name="data" 
                    rows="15" 
                    placeholder="Pega aquí los datos del Excel..."
                    class="w-full bg-slate-50 border-2 border-dashed border-slate-200 rounded-[32px] p-8 text-xs font-mono focus:border-primary focus:ring-0 transition-all"
                ></textarea>
            </div>

            <button type="submit" class="w-full py-6 bg-primary text-white rounded-3xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-primary/20 hover:bg-secondary transition-all active:scale-[0.98]">
                Procesar e Importar Datos
            </button>
        </form>
    </div>
</div>
@endsection
