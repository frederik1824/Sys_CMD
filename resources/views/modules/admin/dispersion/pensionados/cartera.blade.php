@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto" x-data="{ showCreateModal: false, showImportModal: false }">
    <!-- Header: Strategic View -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-12">
        <div class="flex items-center gap-6">
            <div class="w-20 h-20 rounded-[2.5rem] bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center text-white shadow-2xl shadow-slate-900/20 border border-white/10">
                <i class="ph-fill ph-users-three text-4xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.4em] mb-1">Ecosistema de Dispersión</p>
                <h1 class="text-5xl font-black text-slate-900 tracking-tight">Cartera de Pensionados</h1>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-4 bg-white p-3 rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100">
            <form action="{{ route('dispersion.index') }}" method="GET" class="relative flex items-center gap-2">
                <input type="hidden" name="view" value="cartera">
                <div class="relative group">
                    <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Buscar cédula, NSS o nombre..." 
                        class="pl-12 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 w-80 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400">
                </div>
                <button type="submit" class="px-6 py-4 bg-slate-100 text-slate-700 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-200 transition-all">
                    Buscar
                </button>
            </form>
            
            <button @click="showImportModal = true" class="flex items-center gap-3 px-6 py-4 bg-emerald-50 text-emerald-600 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all border border-emerald-100 shadow-lg shadow-emerald-200/20 group">
                <i class="ph-bold ph-file-arrow-up text-lg group-hover:-translate-y-1 transition-transform"></i>
                Importación Masiva
            </button>

            <button @click="showCreateModal = true" class="flex items-center gap-3 px-8 py-4 bg-indigo-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 group">
                <i class="ph-bold ph-plus-circle text-lg group-hover:rotate-90 transition-transform"></i>
                Nuevo Pensionado
            </button>
        </div>
    </div>

    <!-- Stats Summary: Elevated Design -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8 mb-12">
        <div class="group bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/30 hover:shadow-2xl hover:shadow-indigo-200/40 transition-all duration-500">
            <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 mb-6 group-hover:scale-110 transition-transform">
                <i class="ph-fill ph-users text-2xl"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Total en Cartera</p>
            <p class="text-4xl font-black text-slate-900 tracking-tighter">{{ number_format($totalCartera) }}</p>
        </div>

        <div class="group bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/30 hover:shadow-2xl hover:shadow-emerald-200/40 transition-all duration-500">
            <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 mb-6 group-hover:scale-110 transition-transform">
                <i class="ph-fill ph-check-circle text-2xl"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Activos Detectados</p>
            <p class="text-4xl font-black text-emerald-600 tracking-tighter">{{ number_format($totalActivos) }}</p>
        </div>

        <div class="group bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/30 hover:shadow-2xl hover:shadow-amber-200/40 transition-all duration-500">
            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600 mb-6 group-hover:scale-110 transition-transform">
                <i class="ph-fill ph-clock-countdown text-2xl"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">En Proceso</p>
            <p class="text-4xl font-black text-amber-600 tracking-tighter">{{ number_format($totalEnProceso) }}</p>
        </div>

        <div class="group bg-white p-8 rounded-[3rem] border border-slate-100 shadow-xl shadow-slate-200/30 hover:shadow-2xl hover:shadow-rose-200/40 transition-all duration-500">
            <div class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 mb-6 group-hover:scale-110 transition-transform relative">
                <i class="ph-fill ph-bell-ringing text-2xl"></i>
                @if($totalNuevos > 0)
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-rose-500 rounded-full border-2 border-white animate-ping"></span>
                @endif
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Nuevos Matches</p>
            <p class="text-4xl font-black text-rose-600 tracking-tighter">{{ number_format($totalNuevos) }}</p>
        </div>
    </div>

    <!-- Main Table Card: Premium Layout -->
    <div class="bg-white rounded-[4rem] border border-slate-100 shadow-2xl shadow-slate-200/30 overflow-hidden mb-12">
        <div class="px-10 py-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h2 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-3">
                <span class="w-2 h-8 bg-indigo-600 rounded-full"></span>
                Listado Maestro de Afiliados
            </h2>
            @if($search)
                <span class="text-xs font-bold text-slate-400">Resultados para: <span class="text-indigo-600">"{{ $search }}"</span></span>
            @endif
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/20">
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identificación</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Afiliado</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Tipo / Institución</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estatus</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Confirmación Pago</th>
                        <th class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pensionados as $p)
                    <tr class="hover:bg-indigo-50/20 transition-all group/row">
                        <td class="px-10 py-8">
                            <div class="flex flex-col">
                                <span class="text-sm font-black text-slate-900 group-hover/row:text-indigo-600 transition-colors">{{ $p->cedula }}</span>
                                <span class="text-[10px] font-bold text-slate-400 tracking-wider">NSS: {{ $p->nss ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 font-black text-xs">
                                    {{ substr($p->nombre_completo, 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-slate-700 uppercase tracking-tight">{{ $p->nombre_completo }}</span>
                                @if(!$p->notificado_at && $p->ultimo_pago_confirmado_at)
                                    <span class="px-2 py-0.5 bg-rose-500 text-white text-[8px] font-black uppercase rounded-md animate-pulse shadow-lg shadow-rose-200">Nuevo Match</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-black text-slate-900 uppercase tracking-tighter">{{ $p->tipo_pension }}</span>
                                <span class="text-[10px] font-bold text-slate-400 truncate max-w-[200px]">{{ $p->institucion_pension ?? 'Sin institución' }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-center">
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border
                                {{ $p->estado_sistema === 'ACTIVO' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 
                                   ($p->estado_sistema === 'EN PROCESO' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-slate-50 text-slate-500 border-slate-200') }}">
                                {{ $p->estado_sistema }}
                            </span>
                        </td>
                        <td class="px-10 py-8 text-right">
                            @if($p->ultimo_pago_confirmado_at)
                                <div class="flex flex-col items-end">
                                    <div class="flex items-center gap-2 text-emerald-600 mb-1">
                                        <i class="ph-fill ph-check-circle text-lg"></i>
                                        <span class="text-xs font-black uppercase">{{ $p->ultimo_pago_confirmado_at->format('M Y') }}</span>
                                    </div>
                                    <span class="text-[9px] font-bold text-slate-400">{{ $p->ultimo_pago_confirmado_at->diffForHumans() }}</span>
                                </div>
                            @else
                                <div class="flex items-center justify-end gap-2 text-slate-300">
                                    <i class="ph ph-warning-circle text-lg"></i>
                                    <span class="text-[10px] font-bold uppercase italic tracking-tighter">Sin pagos detectados</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-10 py-8 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @php
                                    $waService = app(\App\Services\Common\WhatsAppService::class);
                                    // Usamos el campo data_adicional->telefono si existe, o el campo telefono directo si lo agregamos
                                    $telefono = $p->data_adicional['telefono'] ?? null;
                                    $waLink = $telefono ? $waService->generateLink($telefono, $waService->templatePaymentConfirmed($p->nombre_completo, now()->format('Y-m'))) : '#';
                                @endphp
                                
                                <a href="{{ $waLink }}" 
                                   target="_blank" 
                                   onclick="markAsNotified('{{ $p->uuid }}')"
                                   class="w-10 h-10 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all {{ !$telefono ? 'opacity-20 pointer-events-none' : '' }}" title="Enviar WhatsApp">
                                    <i class="ph ph-whatsapp-logo text-xl"></i>
                                </a>

                                <button onclick="openHistory('{{ $p->uuid }}', '{{ $p->nombre_completo }}')" class="w-10 h-10 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-2xl hover:bg-indigo-600 hover:text-white transition-all" title="Ver Historial 360">
                                    <i class="ph ph-clock-counter-clockwise text-xl"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-10 py-32 text-center">
                            <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 border border-slate-100">
                                <i class="ph ph-user-circle-plus text-5xl text-slate-300"></i>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-3 tracking-tight">No se encontraron pensionados</h3>
                            <p class="text-slate-400 text-sm max-w-sm mx-auto font-medium">Pruebe ajustando los filtros de búsqueda o registre un nuevo pensionado manualmente.</p>
                            @if($search)
                                <a href="{{ route('dispersion.index') }}?view=cartera" class="inline-flex items-center mt-8 text-indigo-600 font-black text-xs uppercase tracking-widest gap-2 hover:gap-4 transition-all">
                                    Limpiar búsqueda <i class="ph ph-arrow-right"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($pensionados->hasPages())
        <div class="px-10 py-8 bg-slate-50/50 border-t border-slate-100">
            {{ $pensionados->links() }}
        </div>
        @endif
    </div>

    <!-- CREATE MODAL (Glassmorphism & Premium UI) -->
    <div x-show="showCreateModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md"
         style="display: none;">
        
        <div @click.away="showCreateModal = false" class="bg-white w-full max-w-2xl rounded-[3.5rem] shadow-2xl overflow-hidden border border-white/20">
            <div class="p-10 border-b border-slate-50 flex items-center justify-between bg-gradient-to-r from-indigo-50/50 to-transparent">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">Nuevo Registro</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Ingreso Manual a Cartera Maestra</p>
                </div>
                <button @click="showCreateModal = false" class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center">
                    <i class="ph-bold ph-x text-xl"></i>
                </button>
            </div>

            <form action="{{ route('dispersion.pensionados.master.store') }}" method="POST" class="p-10 space-y-8">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cédula de Identidad</label>
                        <input type="text" name="cedula" required placeholder="000-0000000-0" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">NSS</label>
                        <input type="text" name="nss" placeholder="Opcional" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nombre Completo</label>
                        <input type="text" name="nombre_completo" required placeholder="Nombres y Apellidos" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Tipo de Pensión</label>
                        <select name="tipo_pension" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                            <option value="Titular">Titular</option>
                            <option value="Dependiente">Dependiente</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Institución</label>
                        <input type="text" name="institucion_pension" placeholder="Ej: DGJP, Fuerzas Armadas" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Teléfono de Contacto</label>
                        <input type="text" name="telefono" placeholder="809-000-0000" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold focus:ring-4 focus:ring-indigo-500/10 transition-all">
                    </div>
                </div>

                <div class="pt-6 flex gap-4">
                    <button type="button" @click="showCreateModal = false" class="flex-1 px-8 py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-500 bg-slate-50 hover:bg-slate-100 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-[2] bg-slate-900 text-white px-8 py-5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-2xl shadow-slate-900/10">
                        Guardar en Cartera
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- IMPORT MODAL -->
    <div x-show="showImportModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[200] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md"
         style="display: none;">
        
        <div @click.away="showImportModal = false" class="bg-white w-full max-w-xl rounded-[3.5rem] shadow-2xl overflow-hidden border border-white/20">
            <div class="p-10 border-b border-slate-50 flex items-center justify-between bg-gradient-to-r from-emerald-50/50 to-transparent">
                <div>
                    <h2 class="text-3xl font-black text-slate-900 tracking-tight">Importación Masiva</h2>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Cargar Cartera desde Excel/CSV</p>
                </div>
                <button @click="showImportModal = false" class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center">
                    <i class="ph-bold ph-x text-xl"></i>
                </button>
            </div>

            <form action="{{ route('dispersion.pensionados.master.import') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
                @csrf
                <div class="bg-slate-50 rounded-3xl p-8 border-2 border-dashed border-slate-200 text-center group hover:border-emerald-300 transition-all">
                    <i class="ph-fill ph-file-xls text-6xl text-slate-200 group-hover:text-emerald-500 transition-all mb-4"></i>
                    <p class="text-sm font-bold text-slate-500 mb-6">Seleccione su archivo .xlsx o .csv</p>
                    
                    <label class="inline-flex items-center gap-3 px-8 py-4 bg-white text-slate-700 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm border border-slate-100 cursor-pointer">
                        <i class="ph-bold ph-folder-open"></i>
                        Seleccionar Archivo
                        <input type="file" name="file" required class="hidden" accept=".xlsx,.csv,.xls">
                    </label>
                </div>

                <div class="bg-indigo-50/50 rounded-2xl p-6">
                    <h4 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-3">Instrucciones de Formato:</h4>
                    <ul class="text-[11px] font-bold text-slate-600 space-y-2">
                        <li class="flex items-center gap-2"><i class="ph ph-check text-emerald-500"></i> Encabezados requeridos: <span class="text-indigo-600">cedula, nombre_completo</span></li>
                        <li class="flex items-center gap-2"><i class="ph ph-check text-emerald-500"></i> Opcionales: <span class="text-slate-400">nss, fecha_nacimiento, genero, tipo_pension, institucion_pension, monto_pension, telefono</span></li>
                        <li class="flex items-center gap-2"><i class="ph ph-info text-indigo-500"></i> Si el pensionado ya existe, sus datos se actualizarán.</li>
                    </ul>
                </div>

                <div class="pt-6 flex gap-4">
                    <button type="button" @click="showImportModal = false" class="flex-1 px-8 py-5 rounded-2xl text-xs font-black uppercase tracking-widest text-slate-500 bg-slate-50 hover:bg-slate-100 transition-all">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-[2] bg-emerald-600 text-white px-8 py-5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-2xl shadow-emerald-200">
                        Iniciar Importación
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- HISTORY MODAL (360 Traceability) -->
    <div id="historyModal" class="fixed inset-0 z-[250] hidden flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-xl">
        <div class="bg-white w-full max-w-3xl rounded-[4rem] shadow-2xl overflow-hidden border border-white/20">
            <div class="p-12 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h2 class="text-4xl font-black text-slate-900 tracking-tighter" id="historyTitle">Historial 360</h2>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-2" id="historySubtitle">Trazabilidad de pagos detectados</p>
                </div>
                <button onclick="closeHistory()" class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center">
                    <i class="ph-bold ph-x text-2xl"></i>
                </button>
            </div>

            <div class="p-12 max-h-[60vh] overflow-y-auto" id="historyContent">
                <!-- Timeline container -->
                <div class="relative pl-12 border-l-4 border-slate-50 space-y-12" id="historyTimeline">
                    <!-- Dynamic content via JS -->
                </div>
            </div>

            <div class="p-12 bg-slate-50 text-center">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Fin del historial oficial de dispersión</p>
            </div>
        </div>
    </div>

    <script>
        function openHistory(uuid, nombre) {
            const modal = document.getElementById('historyModal');
            const title = document.getElementById('historyTitle');
            const timeline = document.getElementById('historyTimeline');
            
            title.innerText = nombre;
            timeline.innerHTML = '<div class="text-slate-400 font-bold italic py-10">Cargando trazabilidad...</div>';
            modal.classList.remove('hidden');

            fetch(`/dispersion/pensionados/master/${uuid}/history`)
                .then(response => response.json())
                .then(data => {
                    timeline.innerHTML = '';
                    if (!data.historial || data.historial.length === 0) {
                        timeline.innerHTML = '<div class="text-slate-400 font-bold italic py-10">No se han detectado pagos históricos para este afiliado.</div>';
                        return;
                    }

                    data.historial.forEach(pago => {
                        const item = document.createElement('div');
                        item.className = 'relative';
                        item.innerHTML = `
                            <div class="absolute -left-[54px] top-0 w-8 h-8 rounded-full bg-white border-4 border-indigo-500 shadow-lg shadow-indigo-200"></div>
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/20">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xl font-black text-slate-900 tracking-tighter">${pago.periodo}</span>
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-[9px] font-black uppercase">${pago.tipo_pensionado}</span>
                                </div>
                                <div class="flex items-center gap-4 text-slate-400">
                                    <div class="flex items-center gap-1">
                                        <i class="ph ph-buildings"></i>
                                        <span class="text-xs font-bold">${pago.origen_pension}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <i class="ph ph-calendar"></i>
                                        <span class="text-xs font-bold">Detección: ${pago.fecha_deteccion}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                        timeline.appendChild(item);
                    });
                });
        }

        function closeHistory() {
            document.getElementById('historyModal').classList.add('hidden');
        }

        function markAsNotified(uuid) {
            fetch(`/dispersion/pensionados/master/${uuid}/notified`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                // No necesitamos refrescar forzosamente, el badge desaparecerá en el próximo refresh
                // Pero podríamos ocultar el badge actual con JS si quisiéramos.
            });
        }
    </script>
@endsection
