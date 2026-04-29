@extends('layouts.app')

@section('content')
<div class="p-8" x-data="receptionHandler()">
    <div class="max-w-5xl mx-auto space-y-8">
        <header class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter">RECEPCIÓN DE LISTADOS</h1>
                <p class="text-slate-500 text-sm font-medium uppercase tracking-widest">Validación y Carga de Prospectos</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('callcenter.reception.template') }}" class="px-4 py-2 bg-blue-50 text-blue-600 rounded-xl text-xs font-black uppercase hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">download</span> Descargar Plantilla
                </a>
                <a href="{{ route('callcenter.prospecting') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-black uppercase hover:bg-slate-200 transition-all">Ver Mi Lista de Prospección</a>
            </div>
        </header>

        <!-- Paso 1: Pegar Datos -->
        <div class="bg-white p-8 rounded-[32px] shadow-xl border border-slate-100" x-show="step === 1">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-10 h-10 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black">1</div>
                <h3 class="text-xl font-bold text-slate-800">Pega los datos desde Excel</h3>
            </div>
            
            <p class="text-slate-500 text-sm mb-4 italic">El orden de las columnas debe ser el de Roberitza: <b>NSS, Cédula, Póliza, Nombre, RNC, Empresa, Teléfono, Dirección, Celular</b></p>
            
            <textarea 
                x-model="rawInput"
                class="w-full h-64 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl p-6 font-mono text-sm focus:border-blue-500 focus:ring-0 transition-all"
                placeholder="Pega aquí las filas de Excel..."></textarea>

            <div class="mt-8 flex justify-end">
                <button 
                    @click="checkData()" 
                    :disabled="!rawInput.trim() || loading"
                    class="px-8 py-4 bg-blue-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-blue-700 transition-all disabled:opacity-50">
                    <span x-show="!loading">Validar Listado</span>
                    <span x-show="loading">Procesando...</span>
                </button>
            </div>
        </div>

        <!-- Paso 2: Vista Previa y Resultados -->
        <div class="bg-white p-8 rounded-[32px] shadow-xl border border-slate-100" x-show="step === 2" x-cloak>
             <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-emerald-600 rounded-2xl flex items-center justify-center text-white font-black">2</div>
                    <h3 class="text-xl font-bold text-slate-800">Resultados de Validación</h3>
                </div>
                <button @click="step = 1" class="text-blue-600 text-xs font-bold uppercase hover:underline">Volver a editar</button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">
                            <th class="pb-4 px-4">Estado</th>
                            <th class="pb-4 px-4">Cédula</th>
                            <th class="pb-4 px-4">Nombre</th>
                            <th class="pb-4 px-4">Empresa / RNC</th>
                            <th class="pb-4 px-4">Teléfono</th>
                            <th class="pb-4 px-4">Información del Sistema</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <template x-for="(p, index) in processedData" :key="index">
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-4">
                                    <template x-if="p.status === 'new'">
                                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[9px] font-black uppercase rounded">Nuevo</span>
                                    </template>
                                    <template x-if="p.status === 'exists'">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-[9px] font-black uppercase rounded">Para Actualizar</span>
                                    </template>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-xs font-bold text-slate-500" x-text="p.cedula || '---'"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-slate-800" x-text="p.nombre"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-xs text-slate-600" x-text="p.empresa"></div>
                                    <div class="text-[10px] font-mono text-slate-400" x-text="p.rnc"></div>
                                </td>
                                <td class="py-4 px-4 text-sm text-slate-600" x-text="p.telefono"></td>
                                <td class="py-4 px-4 text-xs italic text-slate-500">
                                    <template x-if="p.status === 'exists' && p.asignado_a">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-rose-600 font-black uppercase text-[9px] tracking-tight">Asignación Activa</span>
                                            <span class="text-slate-700 not-italic font-bold" x-text="'Trabajado por: ' + p.asignado_a"></span>
                                        </div>
                                    </template>
                                    <template x-if="p.status === 'exists' && !p.asignado_a">
                                        <span x-text="'Se actualizará el registro: ' + p.existente_estado"></span>
                                    </template>
                                    <template x-if="p.status === 'new'">
                                        <span class="text-emerald-600 font-bold">Listo para registrar</span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="mt-10 flex justify-between items-center p-6 bg-slate-50 rounded-2xl">
                <div class="text-sm font-medium text-slate-600">
                    Se procesarán <span class="font-black text-slate-900" x-text="processedData.length"></span> registros (Nuevos y Actualizaciones).
                </div>
                <button 
                    @click="storeData()"
                    class="px-8 py-4 bg-emerald-600 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-emerald-700 transition-all">
                    Confirmar e Integrar al Sistema
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function receptionHandler() {
    return {
        step: 1,
        loading: false,
        rawInput: '',
        processedData: [],

        async checkData() {
            this.loading = true;
            try {
                const response = await fetch("{{ route('callcenter.reception.check') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ data: this.rawInput })
                });
                this.processedData = await response.json();
                this.step = 2;
            } catch (error) {
                alert("Error al validar datos");
            } finally {
                this.loading = false;
            }
        },

        async storeData() {
            const allToProcess = this.processedData;
            if (allToProcess.length === 0) {
                alert("No hay datos para procesar");
                return;
            }

            // Barra de progreso moderna con Swal
            Swal.fire({
                title: 'Integrando Registros',
                html: `
                    <div class="p-6">
                        <div class="flex justify-between mb-2">
                            <span class="text-[10px] font-black uppercase text-blue-600 tracking-widest">Motor de Base de Datos</span>
                            <span id="swal-progress-text" class="text-[10px] font-black text-blue-600">0%</span>
                        </div>
                        <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden border border-slate-200">
                            <div id="swal-progress-bar" class="h-full bg-gradient-to-r from-blue-600 to-indigo-600 w-0 transition-all duration-500"></div>
                        </div>
                        <p class="mt-4 text-[11px] text-slate-500 font-medium italic animate-pulse">Actualizando e insertando prospectos...</p>
                    </div>
                `,
                allowOutsideClick: false,
                showConfirmButton: false,
                customClass: { popup: 'rounded-[40px] border-0 shadow-2xl' }
            });

            // Simulamos avance fluido
            let progress = 0;
            const timer = setInterval(() => {
                if (progress < 90) {
                    progress += 5;
                    const pb = document.getElementById('swal-progress-bar');
                    const pt = document.getElementById('swal-progress-text');
                    if (pb) pb.style.width = progress + '%';
                    if (pt) pt.textContent = progress + '%';
                }
            }, 100);

            try {
                const response = await fetch("{{ route('callcenter.reception.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ prospects: allToProcess })
                });
                const result = await response.json();
                
                clearInterval(timer);
                if (result.success) {
                    const pb = document.getElementById('swal-progress-bar');
                    const pt = document.getElementById('swal-progress-text');
                    if (pb) pb.style.width = '100%';
                    if (pt) pt.textContent = '100%';
                    
                    setTimeout(() => {
                        window.location.href = "{{ route('callcenter.prospecting') }}";
                    }, 800);
                }
            } catch (error) {
                clearInterval(timer);
                Swal.fire({ title: 'Error', text: 'No se pudo completar el registro', icon: 'error', customClass: { popup: 'rounded-[40px]' } });
            }
        }
    }
}
</script>
@endsection
