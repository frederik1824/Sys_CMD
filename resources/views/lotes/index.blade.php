@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto space-y-8">
    
    <!-- Encabezado de Poder -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black tracking-tighter text-slate-800 uppercase">Procesamiento de Lotes</h1>
            <p class="text-slate-500 text-sm mt-1 font-medium">Asignación masiva y control de entrega a SAFESURE.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="document.getElementById('modalLote').classList.remove('hidden')" class="bg-primary text-white px-8 py-3 rounded-2xl shadow-xl shadow-primary/20 font-black uppercase text-xs tracking-widest hover:scale-[1.02] transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">settings_suggest</span>
                Ejecutar Proceso Masivo
            </button>
        </div>
    </div>

    <!-- Filtros Inteligentes -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 flex flex-wrap items-center gap-4">
        <form method="GET" class="flex flex-wrap items-center gap-4 w-full">
            <div class="flex-1 min-w-[200px]">
                <label class="text-[0.6rem] font-black uppercase text-slate-400 mb-1 block pl-1">Filtrar por Corte</label>
                <select name="corte_id" onchange="this.form.submit()" class="w-full bg-slate-50 border-none rounded-xl py-2.5 px-4 text-xs font-bold text-slate-600">
                    <option value="">Todos los Cortes</option>
                    @foreach($cortes as $c)
                        <option value="{{ $c->id }}" {{ request('corte_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="text-[0.6rem] font-black uppercase text-slate-400 mb-1 block pl-1">Estado Operativo</label>
                <select name="estado_id" onchange="this.form.submit()" class="w-full bg-slate-50 border-none rounded-xl py-2.5 px-4 text-xs font-bold text-slate-600">
                    <option value="">Cualquier Estado</option>
                    @foreach($estados as $e)
                        <option value="{{ $e->id }}" {{ request('estado_id') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex border-l border-slate-200 pl-4 items-center gap-2">
                <input type="checkbox" name="sin_fecha_entrega" value="1" {{ request('sin_fecha_entrega') ? 'checked' : '' }} onchange="this.form.submit()" class="rounded text-primary focus:ring-primary border-slate-300">
                <span class="text-xs font-black text-rose-500 uppercase tracking-tighter">Sin Entrega a SAFESURE</span>
            </div>
            <a href="{{ route('lotes.index') }}" class="ml-auto text-xs font-bold text-slate-400 hover:text-rose-500 transition-colors">Limpiar Filtros</a>
        </form>
    </div>

    <!-- Tabla Dinámica -->
    <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100">
        <form id="loteForm" action="{{ route('lotes.process') }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="py-5 px-6"><input type="checkbox" onclick="toggleAll(this)" class="rounded border-slate-300 text-primary"></th>
                            <th class="py-5 px-2 text-[0.65rem] font-black uppercase text-slate-400 tracking-wider">Afiliado / Cédula</th>
                            <th class="py-5 px-4 text-[0.65rem] font-black uppercase text-slate-400 tracking-wider">Corte Actual</th>
                            <th class="py-5 px-4 text-[0.65rem] font-black uppercase text-slate-400 tracking-wider">Estado</th>
                            <th class="py-5 px-4 text-[0.65rem] font-black uppercase text-slate-400 tracking-wider">Entrega SAFESURE</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($afiliados as $a)
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="py-4 px-6"><input type="checkbox" name="selected[]" value="{{ $a->uuid }}" class="rounded border-slate-300 text-primary sel-check"></td>
                            <td class="py-4 px-2">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-800 tracking-tight">{{ $a->nombre_completo }}</span>
                                    <span class="text-[0.65rem] font-bold text-primary">{{ $a->cedula }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4"><span class="text-[0.7rem] font-black bg-slate-100 px-3 py-1 rounded-lg text-slate-600 uppercase">{{ $a->corte->nombre ?? 'N/A' }}</span></td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-lg text-[0.65rem] font-black uppercase border border-opacity-20
                                    {{ strtolower($a->estado?->nombre) === 'completado' ? 'bg-emerald-50 text-emerald-600 border-emerald-600' : 'bg-amber-50 text-amber-600 border-amber-600' }}">
                                    {{ $a->estado->nombre ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @if($a->fecha_entrega_safesure)
                                    <div class="flex items-center gap-1.5 text-emerald-600 font-bold text-xs uppercase">
                                        <span class="material-symbols-outlined text-[1rem]">check_circle</span>
                                        {{ $a->fecha_entrega_safesure->format('d/m/y') }}
                                    </div>
                                @else
                                    <span class="text-[0.65rem] font-bold text-slate-300 italic uppercase">Pendiente Entrega</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="py-12 text-center text-slate-400 font-bold uppercase text-[0.7rem]">No hay afiliados según los filtros.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-100">
                {{ $afiliados->links() }}
            </div>

            <!-- MODAL DE PROCESAMIENTO -->
            <div id="modalLote" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
                <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden p-8 border border-white/20 scale-95 animate-in slide-in-from-bottom-4 fade-in duration-300">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-4xl">batch_prediction</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-800 uppercase tracking-tighter">Acción en Lote</h2>
                            <p class="text-xs text-slate-500 font-medium">Aplique una operación a los registros seleccionados.</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-2 tracking-widest px-1">¿Qué desea hacer?</label>
                            <select name="action" id="actionSelector" required onchange="toggleInputs(this.value)" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-5 font-black text-slate-700 focus:ring-4 focus:ring-primary/10 transition-all">
                                <option value="">Seleccione una acción...</option>
                                <option value="entrega_proveedor">Registrar Entrega a Proveedor</option>
                                <option value="cambio_estado">Actualizar Estado Masivo</option>
                                <option value="asignar_responsable">Asignar Responsable</option>
                            </select>
                        </div>

                        <!-- Inputs condicionales -->
                        <div id="input_entrega" class="hidden animate-in fade-in slide-in-from-top-2 duration-300 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-2 tracking-widest px-1">Proveedor (Delivery)</label>
                                <select name="proveedor_id" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-5 font-bold text-slate-700">
                                    <option value="">Seleccione...</option>
                                    @foreach($proveedores as $p)
                                        <option value="{{ $p->id }}">{{ $p->nombre }} (RD$ {{ $p->precio_base }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-2 tracking-widest px-1">Fecha Entrega</label>
                                <input type="date" name="fecha_entrega" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-5 font-bold text-slate-700">
                            </div>
                            <div>
                                <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-2 tracking-widest px-1">Costo RD$ (Opcional)</label>
                                <input type="number" name="costo" placeholder="0.00" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-5 font-bold text-slate-700">
                            </div>
                        </div>

                        <div id="input_estado" class="hidden animate-in fade-in slide-in-from-top-2 duration-300">
                            <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-2 tracking-widest px-1">Nuevo Estado</label>
                            <select name="estado_id" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-5 font-black text-slate-700">
                                @foreach($estados as $e)
                                    <option value="{{ $e->id }}">{{ $e->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="input_responsable" class="hidden animate-in fade-in slide-in-from-top-2 duration-300">
                            <label class="block text-[0.65rem] font-black uppercase text-slate-400 mb-2 tracking-widest px-1">Asignar a:</label>
                            <select name="responsable_id" class="w-full bg-slate-50 border-none rounded-2xl py-4 px-5 font-black text-slate-700">
                                @foreach($responsables as $r)
                                    <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-10 flex gap-4">
                        <button type="button" onclick="document.getElementById('modalLote').classList.add('hidden')" class="flex-1 py-4 text-xs font-black text-slate-400 uppercase tracking-widest hover:bg-slate-50 rounded-2xl transition-all">Cerrar</button>
                        <button type="submit" class="flex-[2] py-4 bg-primary text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all">Ejecutar Lote</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAll(source) {
        checkboxes = document.getElementsByName('selected[]');
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function toggleInputs(action) {
        document.getElementById('input_entrega').classList.add('hidden');
        document.getElementById('input_estado').classList.add('hidden');
        document.getElementById('input_responsable').classList.add('hidden');

        if(action === 'entrega_proveedor') document.getElementById('input_entrega').classList.remove('hidden');
        if(action === 'cambio_estado') document.getElementById('input_estado').classList.remove('hidden');
        if(action === 'asignar_responsable') document.getElementById('input_responsable').classList.remove('hidden');
    }
</script>
@endsection
