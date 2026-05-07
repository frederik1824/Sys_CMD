@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 max-w-7xl mx-auto space-y-10 animate-in fade-in duration-700">
    <!-- Header de Contexto -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-6">
            <div class="w-16 h-16 bg-slate-900 text-white rounded-[24px] flex items-center justify-center shadow-2xl shadow-slate-900/20">
                <i class="ph-bold ph-file-arrow-up text-3xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-1">Importación de Prospectos</h1>
                <p class="text-slate-500 font-medium italic">Motor de ingesta inteligente para campañas de Call Center.</p>
            </div>
        </div>
        <a href="{{ route('call-center.worklist') }}" class="inline-flex items-center gap-3 bg-white border border-slate-200 hover:border-slate-300 text-slate-600 px-6 py-3 rounded-2xl font-black uppercase text-xs tracking-widest transition-all shadow-sm">
            <i class="ph-bold ph-arrow-left text-lg"></i>
            Volver a Bandeja
        </a>
    </div>

    <!-- Card de Importación Premium -->
    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden relative">
        <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/5 rounded-full blur-3xl -mr-48 -mt-48"></div>
        
        <div class="p-8 lg:p-12 relative z-10">
            <form id="importForm" class="space-y-10">
                @csrf
                
                <div class="grid grid-cols-1 gap-10">
                    <!-- Nombre de la Carga -->
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Identificador de la Operación</label>
                        <input type="text" name="nombre_carga" id="nombre_carga" class="w-full bg-slate-50 border-transparent rounded-[24px] py-5 px-8 text-lg font-black text-slate-900 placeholder:text-slate-300 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white transition-all shadow-inner" 
                               placeholder="Ej: Campaña ARS - Segmento Premium - Mayo 2026" required>
                    </div>

                    <!-- Área de Pegado de Datos -->
                    <div class="space-y-4">
                        <div class="bg-slate-900/50 rounded-2xl p-6 border border-white/10 backdrop-blur-md">
                            <h3 class="text-xs font-black text-emerald-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                                <i class="ph-bold ph-table"></i>
                                Estructura de Columnas (Excel / CSV)
                            </h3>
                            <div class="overflow-x-auto">
                                <table class="w-full text-[10px] text-slate-300 border-collapse">
                                    <thead>
                                        <tr class="text-white/40 border-b border-white/5">
                                            <th class="py-2 pr-4 text-left font-black uppercase">Col 1</th>
                                            <th class="py-2 px-4 text-left font-black uppercase">Col 2</th>
                                            <th class="py-2 px-4 text-left font-black uppercase">Col 3</th>
                                            <th class="py-2 px-4 text-left font-black uppercase">Col 4</th>
                                            <th class="py-2 px-4 text-left font-black uppercase">Col 5</th>
                                            <th class="py-2 px-4 text-left font-black uppercase">Col 6</th>
                                            <th class="py-2 px-4 text-left font-black uppercase">Col 7</th>
                                            <th class="py-2 pl-4 text-left font-black uppercase">Col 8</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        <tr class="text-emerald-300 font-bold">
                                            <td class="py-3 pr-4">CEDULA</td>
                                            <td class="py-3 px-4">POLIZA</td>
                                            <td class="py-3 px-4">NOMBRE</td>
                                            <td class="py-3 px-4">RNC_EMPRESA</td>
                                            <td class="py-3 px-4">NOMBRE_EMPRESA</td>
                                            <td class="py-3 px-4">TELEFONO</td>
                                            <td class="py-3 px-4">DIRECCION</td>
                                            <td class="py-3 pl-4">CELULAR</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="relative group">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-focus-within:opacity-100 transition-opacity rounded-[32px]"></div>
                            <textarea name="data_raw" id="data_raw" rows="12" class="relative z-10 w-full bg-slate-50 border-transparent rounded-[32px] p-8 text-sm font-mono font-bold text-emerald-800 placeholder:text-slate-300 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white transition-all shadow-inner resize-none overflow-y-auto custom-scrollbar" 
                                      placeholder="Pega las filas de tu hoja de cálculo aquí..." required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Progress Container -->
                <div id="progressContainer" class="hidden space-y-4 animate-in slide-in-from-top-4 duration-500 bg-slate-50 p-8 rounded-[32px] border border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="ph-bold ph-lightning text-amber-500 animate-pulse text-xl"></i>
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Procesando Data en Tiempo Real...</span>
                        </div>
                        <span id="progressPercent" class="text-sm font-black text-indigo-600">0%</span>
                    </div>
                    <div class="w-full h-4 bg-white rounded-full overflow-hidden border border-slate-200">
                        <div id="progressBar" class="h-full bg-gradient-to-r from-indigo-500 to-emerald-500 transition-all duration-300 w-0"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white p-4 rounded-2xl border border-indigo-100 text-center shadow-sm">
                            <p id="countNuevos" class="text-2xl font-black text-indigo-600 leading-none">0</p>
                            <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mt-1">Nuevos</p>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-emerald-100 text-center shadow-sm">
                            <p id="countActualizados" class="text-2xl font-black text-emerald-600 leading-none">0</p>
                            <p class="text-[8px] font-black text-emerald-400 uppercase tracking-widest mt-1">Actualizados</p>
                        </div>
                        <div class="bg-white p-4 rounded-2xl border border-rose-100 text-center shadow-sm">
                            <p id="countOmitidos" class="text-2xl font-black text-rose-600 leading-none">0</p>
                            <p class="text-[8px] font-black text-rose-400 uppercase tracking-widest mt-1">Omitidos</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 pt-4">
                    <div class="flex items-center gap-4 text-slate-400">
                        <i class="ph-bold ph-shield-check text-2xl text-emerald-500"></i>
                        <p class="text-xs font-bold italic leading-relaxed">El sistema validará automáticamente duplicados y normalizará formatos de cédula y teléfono durante el proceso.</p>
                    </div>
                    <button type="submit" id="submitBtn" class="w-full sm:w-auto inline-flex items-center justify-center gap-4 bg-slate-900 hover:bg-black text-white px-12 py-5 rounded-[24px] font-black uppercase text-xs tracking-[0.2em] transition-all shadow-2xl active:scale-95 group">
                        <i class="ph-bold ph-lightning text-xl group-hover:animate-pulse"></i>
                        Iniciar Importación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('importForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const dataRaw = document.getElementById('data_raw').value;
    const nombreCarga = document.getElementById('nombre_carga').value;
    const btn = document.getElementById('submitBtn');
    const progressContainer = document.getElementById('progressContainer');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    
    const countNuevos = document.getElementById('countNuevos');
    const countActualizados = document.getElementById('countActualizados');
    const countOmitidos = document.getElementById('countOmitidos');

    const lines = dataRaw.split('\n').filter(line => line.trim() !== '');
    if (lines.length === 0) return;

    btn.disabled = true;
    btn.innerHTML = '<i class="ph-bold ph-circle-notch animate-spin text-xl mr-2"></i>PREPARANDO...';
    progressContainer.classList.remove('hidden');

    try {
        const startResponse = await fetch("{{ route('call-center.import.start') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ nombre_carga: nombreCarga })
        });
        const startData = await startResponse.json();
        
        if (!startData.success) throw new Error(startData.message);

        const cargaId = startData.carga_id;
        const chunkSize = 10;
        let processed = 0;
        let totalNuevos = 0;
        let totalActualizados = 0;
        let totalOmitidos = 0;

        btn.innerHTML = '<i class="ph-bold ph-lightning animate-pulse text-xl mr-2"></i>IMPORTANDO...';

        for (let i = 0; i < lines.length; i += chunkSize) {
            const chunk = lines.slice(i, i + chunkSize);
            
            const chunkResponse = await fetch("{{ route('call-center.import.chunk') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    carga_id: cargaId,
                    lines: chunk
                })
            });
            const chunkData = await chunkResponse.json();

            if (chunkData.success) {
                totalNuevos += chunkData.stats.nuevos;
                totalActualizados += chunkData.stats.actualizados;
                totalOmitidos += chunkData.stats.omitidos;
                
                countNuevos.innerText = totalNuevos;
                countActualizados.innerText = totalActualizados;
                countOmitidos.innerText = totalOmitidos;

                processed += chunk.length;
                const percent = Math.round((processed / lines.length) * 100);
                progressBar.style.width = percent + '%';
                progressPercent.innerText = percent + '%';
            }
        }

        Swal.fire({
            title: '¡Importación Finalizada!',
            html: `<div class="text-left space-y-2 text-sm">
                    <p>Total procesados: <b>${lines.length}</b></p>
                    <p class="text-indigo-600">Nuevos registros: <b>${totalNuevos}</b></p>
                    <p class="text-emerald-600">Actualizados: <b>${totalActualizados}</b></p>
                    <p class="text-rose-600">Omitidos: <b>${totalOmitidos}</b></p>
                   </div>`,
            icon: 'success',
            confirmButtonText: 'Ir a la Bandeja',
            customClass: { popup: 'rounded-[40px]', confirmButton: 'rounded-2xl font-black uppercase text-xs px-8 py-4' }
        }).then(() => {
            window.location.href = "{{ route('call-center.worklist') }}";
        });

    } catch (error) {
        console.error(error);
        Swal.fire('Error Crítico', error.message || 'Error en el proceso de importación', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ph-bold ph-lightning text-xl group-hover:animate-pulse"></i>Iniciar Importación';
    }
});
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection
