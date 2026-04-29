@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1200px] mx-auto" x-data="{ 
    showTipoModal: false, 
    showDocModal: false, 
    selectedTipo: null,
    editingTipo: null 
}">
    <!-- HEADER -->
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tighter text-slate-900">Configuración de Procesos</h1>
            <p class="text-slate-500 font-medium mt-1 text-lg">Define los flujos de trabajo, tiempos de respuesta y documentos por trámite.</p>
        </div>
        <button @click="showTipoModal = true; editingTipo = null" class="bg-indigo-600 text-white rounded-2xl px-8 py-4 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-xl shadow-indigo-100">
            <i class="ph ph-plus-circle text-lg"></i> Nuevo Proceso
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($tipos as $tipo)
        <div class="bg-white rounded-[40px] border border-slate-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-8 bg-slate-50/50 border-b border-slate-50">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-3 py-1 rounded-lg bg-indigo-100 text-indigo-700 text-[9px] font-black uppercase tracking-widest">SLA: {{ $tipo->sla_horas }}h</span>
                    <button @click="showTipoModal = true; editingTipo = {{ $tipo->toJson() }}" class="text-slate-400 hover:text-indigo-600 transition-all">
                        <i class="ph ph-pencil-simple text-xl"></i>
                    </button>
                </div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">{{ $tipo->nombre }}</h3>
                <p class="text-xs font-medium text-slate-500 mt-2 line-clamp-2">{{ $tipo->descripcion ?? 'Sin descripción.' }}</p>
            </div>
            
            <div class="p-8 flex-1">
                <div class="flex justify-between items-center mb-6">
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Documentos Requeridos</h4>
                    <button @click="showDocModal = true; selectedTipo = {{ $tipo->id }}" class="text-indigo-600 hover:scale-110 transition-all">
                        <i class="ph-bold ph-plus-circle text-lg"></i>
                    </button>
                </div>
                
                <div class="space-y-3">
                    @forelse($tipo->documentosRequeridos as $doc)
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100 group">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full {{ $doc->obligatorio ? 'bg-rose-500' : 'bg-slate-300' }}"></div>
                            <span class="text-xs font-bold text-slate-700">{{ $doc->nombre_documento }}</span>
                        </div>
                        <form action="{{ route('solicitudes-afiliacion.config.documentos.delete', $doc) }}" method="POST" onsubmit="return confirm('¿Eliminar este requisito?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-slate-300 hover:text-rose-500 opacity-0 group-hover:opacity-100 transition-all">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                    @empty
                    <p class="text-[10px] font-bold text-slate-400 italic text-center py-4">No hay documentos definidos.</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- MODAL TIPO PROCESO -->
    <div x-show="showTipoModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] flex items-center justify-center p-6" x-cloak>
        <div class="bg-white rounded-[40px] w-full max-w-lg p-10 shadow-2xl" @click.away="showTipoModal = false">
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter mb-2" x-text="editingTipo ? 'Editar Proceso' : 'Nuevo Proceso'"></h3>
            <p class="text-slate-500 font-medium mb-8 text-sm">Define el nombre y el tiempo de respuesta esperado (SLA).</p>
            
            <form :action="editingTipo ? `/solicitudes-afiliacion/configuracion/tipos/${editingTipo.id}` : '{{ route('solicitudes-afiliacion.config.tipos.store') }}'" method="POST">
                @csrf
                <template x-if="editingTipo"><input type="hidden" name="_method" value="PATCH"></template>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Nombre del Trámite</label>
                        <input type="text" name="nombre" required :value="editingTipo ? editingTipo.nombre : ''" placeholder="Ej: Inclusión de Dependiente"
                            class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">SLA (Horas laborables)</label>
                        <input type="number" name="sla_horas" required :value="editingTipo ? editingTipo.sla_horas : 24"
                            class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Descripción (Opcional)</label>
                        <textarea name="descripcion" rows="3" :value="editingTipo ? editingTipo.descripcion : ''"
                            class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all"></textarea>
                    </div>
                </div>
                
                <div class="flex gap-4 mt-10">
                    <button type="button" @click="showTipoModal = false" class="flex-1 py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] py-5 rounded-2xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">Guardar Proceso</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL DOCUMENTO -->
    <div x-show="showDocModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] flex items-center justify-center p-6" x-cloak>
        <div class="bg-white rounded-[40px] w-full max-w-lg p-10 shadow-2xl" @click.away="showDocModal = false">
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter mb-2">Añadir Requisito</h3>
            <p class="text-slate-500 font-medium mb-8 text-sm">El documento aparecerá automáticamente en el formulario de creación.</p>
            
            <form action="{{ route('solicitudes-afiliacion.config.documentos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tipo_solicitud_id" :value="selectedTipo">
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Nombre del Documento</label>
                        <input type="text" name="nombre_documento" required placeholder="Ej: Copia de Cédula"
                            class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>

                    <div class="flex items-center gap-3 px-4">
                        <input type="checkbox" name="obligatorio" id="doc_obligatorio" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="doc_obligatorio" class="text-xs font-black text-slate-600 uppercase tracking-widest">Es obligatorio para enviar</label>
                    </div>
                </div>
                
                <div class="flex gap-4 mt-10">
                    <button type="button" @click="showDocModal = false" class="flex-1 py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] py-5 rounded-2xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest hover:bg-indigo-700 shadow-xl shadow-indigo-100 transition-all">Añadir Requisito</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
