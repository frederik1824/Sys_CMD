@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <header class="mb-10">
        <h1 class="text-[2.75rem] font-bold text-primary leading-tight mb-2 font-headline">Módulo de Carga Masiva</h1>
        <p class="text-on-surface-variant max-w-2xl">Procesa de forma masiva los registros de afiliados nuevos usando el formato estándar. La validación se realiza al importar.</p>
    </header>

    @if(session('success'))
        <div class="mb-6 px-6 py-4 bg-emerald-50 text-emerald-800 rounded-xl flex items-center gap-3 border border-emerald-100">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            <div class="text-sm font-semibold">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div class="mb-6 p-6 bg-red-50 text-red-800 rounded-xl border border-red-100">
            <div class="flex items-center gap-3 mb-2 font-bold">
                <span class="material-symbols-outlined text-red-500">warning</span> Errores de Validación
            </div>
            @if(session('error'))
                <p class="text-sm ml-8">{{ session('error') }}</p>
            @endif
            @if($errors->any())
            <ul class="list-disc ml-10 text-sm space-y-1 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            @endif
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 px-6 py-4 bg-amber-50 text-amber-800 rounded-xl flex items-center gap-3 border border-amber-100">
            <span class="material-symbols-outlined text-amber-500">warning</span>
            <div class="text-sm font-semibold">{{ session('warning') }}</div>
        </div>
    @endif

    @if(session('import_duplicated'))
        <div class="mb-6 p-6 bg-amber-50 text-amber-800 rounded-xl border border-amber-100 max-h-96 overflow-y-auto shadow-sm">
            <div class="flex items-center gap-3 mb-2 font-bold">
                <span class="material-symbols-outlined text-amber-500">sync</span> Resumen de Registros Actualizados / Existentes
            </div>
            <p class="text-xs mb-4 ml-8 font-medium">Estos registros ya se encontraban en el corte seleccionado. El sistema ha actualizado sus datos según el archivo cargado.</p>
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-amber-700 uppercase bg-amber-100">
                        <tr>
                            <th class="px-4 py-2">Fila (Excel)</th>
                            <th class="px-4 py-2">Cédula</th>
                            <th class="px-4 py-2">Nombre Completo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(session('import_duplicated') as $dup)
                            <tr class="border-b border-amber-100/50">
                                <td class="px-4 py-2 font-bold">{{ $dup['fila'] }}</td>
                                <td class="px-4 py-2">{{ $dup['cedula'] }}</td>
                                <td class="px-4 py-2 capitalize">{{ strtolower($dup['nombre']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if(session('import_failures'))
        <div class="mb-6 p-6 bg-red-50 text-red-800 rounded-xl border border-red-100 max-h-96 overflow-y-auto">
            <div class="flex items-center gap-3 mb-2 font-bold">
                <span class="material-symbols-outlined text-red-500">error</span> Detalles de Errores por Fila
            </div>
            <div class="overflow-x-auto mt-4">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-red-700 uppercase bg-red-100">
                        <tr>
                            <th class="px-4 py-2">Fila (Excel)</th>
                            <th class="px-4 py-2">Columna</th>
                            <th class="px-4 py-2">Errores</th>
                            <th class="px-4 py-2">Valor Leído</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(session('import_failures') as $failure)
                            <tr class="border-b border-red-100/50">
                                <td class="px-4 py-2 font-bold text-center">{{ $failure->row() }}</td>
                                <td class="px-4 py-2">{{ $failure->attribute() }}</td>
                                <td class="px-4 py-2">
                                    <ul class="list-disc ml-4">
                                        @foreach($failure->errors() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="px-4 py-2 font-mono text-xs">
                                    {{ $failure->values()[$failure->attribute()] ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    <div class="grid grid-cols-12 gap-8">
        <!-- Left Column: Upload -->
        <div class="col-span-12 lg:col-span-5 space-y-8">
            <section class="bg-surface-container-lowest p-8 rounded-xl relative overflow-hidden shadow-sm border border-slate-100">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -mr-16 -mt-16"></div>
                <div class="relative z-10 text-center">
                    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-4xl">cloud_upload</span>
                    </div>
                    
                    <form id="import-form" action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div>
                            <h3 class="text-xl font-semibold mb-2 text-slate-800">Cargar Archivo de Datos</h3>
                            <p class="text-sm text-slate-500 mb-6">Soportados: .xlsx, .csv (Máx. 10MB)</p>
                            
                            <div class="text-left space-y-5">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Período de Corte</label>
                                    <select name="corte_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                                        <option value="">Seleccione el corte de facturación...</option>
                                        @foreach($cortes as $corte)
                                            <option value="{{ $corte->id }}">{{ $corte->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-primary mb-2 uppercase tracking-tighter">Destino de los Datos</label>
                                        <select name="empresa_tipo" required class="w-full bg-white border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm font-bold text-slate-700 shadow-sm">
                                            <option value="CMD">Afiliados ARS CMD</option>
                                            <option value="OTRAS">Otras Empresas (Externos)</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-[0.65rem] font-bold text-slate-500 mb-1 uppercase tracking-wider">Asignar Responsable (Opcional)</label>
                                        <select name="responsable_id" class="w-full bg-white border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm font-medium text-slate-600 shadow-sm">
                                            <option value="">-- No asignar responsable aún --</option>
                                            @foreach($responsables as $resp)
                                                <option value="{{ $resp->id }}">{{ $resp->nombre }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-[0.6rem] text-slate-400 mt-1">Los registros se asignarán automáticamente a esta persona.</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Archivo a procesar</label>
                                    <input type="file" name="file" accept=".xlsx, .csv, .xls" required class="w-full text-sm text-slate-500
                                      file:mr-4 file:py-3 file:px-6
                                      file:rounded-xl file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-primary/5 file:text-primary
                                      hover:file:bg-primary/10 file:transition-colors
                                      cursor-pointer
                                    "/>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4 space-y-4">
                            <button type="submit" class="w-full bg-gradient-to-br from-primary to-primary-container text-white py-4 rounded-xl font-semibold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:opacity-90 transition-opacity">
                                <span class="material-symbols-outlined">upload</span> Comenzar Carga
                            </button>
                            <a href="{{ route('import.template') }}" class="w-full bg-transparent border border-slate-200 py-3 rounded-xl text-primary font-medium flex items-center justify-center gap-2 hover:bg-slate-50 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">download</span> Descargar Plantilla Modelo
                            </a>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Rescate de Sistema -->
            <section class="bg-rose-50 p-6 rounded-xl border border-rose-100">
                <div class="flex items-center gap-3 mb-4">
                    <span class="material-symbols-outlined text-rose-500">emergency</span>
                    <h4 class="text-sm font-bold text-rose-900 uppercase tracking-tight">Zona de Rescate</h4>
                </div>
                <p class="text-xs text-rose-700 mb-4 leading-relaxed">Si la carga parece detenida (0% por mucho tiempo) o el sistema está lento, es posible que la cola de tareas esté saturada.</p>
                <button onclick="purgeQueueFromImport()" class="w-full py-3 bg-white border border-rose-200 text-rose-600 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all shadow-sm">
                    Limpiar todas las tareas pendientes
                </button>
            </section>
        </div>

        <!-- Right Column: Instrucciones e Historial Breve -->
        <div class="col-span-12 lg:col-span-7 space-y-8">
            <section class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm border border-slate-100">
                <div class="px-8 py-6 bg-amber-50/50 border-b border-amber-100 flex items-center gap-3">
                    <span class="material-symbols-outlined text-amber-500 text-2xl">info</span> 
                    <h3 class="text-lg font-bold text-amber-900">Reglas de Formato y Columnas</h3>
                </div>
                <div class="p-8 space-y-6 text-sm text-slate-600">
                    <p>El sistema utiliza <strong>Mapeo por Nombre de Columna (Heading Row)</strong>. La primera fila de su archivo Excel debe contener los siguientes títulos escritos de manera exacta (en minúsculas y sin espacios extras):</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <h4 class="font-bold text-slate-800 text-xs tracking-wider uppercase">Requeridos</h4>
                            <ul class="space-y-2 font-medium">
                                <li class="flex items-center gap-2"><code class="bg-red-50 text-red-600 px-2 py-0.5 rounded text-xs">nombre_completo</code></li>
                                <li class="flex items-center gap-2"><code class="bg-red-50 text-red-600 px-2 py-0.5 rounded text-xs">cedula</code> <span class="text-[0.65rem] text-slate-400 font-normal">Formato xxx-xxxxxxx-x</span></li>
                            </ul>
                        </div>
                        <div class="space-y-3">
                            <h4 class="font-bold text-slate-800 text-xs tracking-wider uppercase">Opcionales</h4>
                            <ul class="space-y-2 font-medium grid grid-cols-2 gap-x-2">
                                <li><code class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs block truncate" title="telefono">telefono</code></li>
                                <li><code class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs block truncate" title="direccion">direccion</code></li>
                                <li><code class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs block truncate" title="provincia">provincia</code></li>
                                <li><code class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs block truncate" title="municipio">municipio</code></li>
                                <li><code class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs block truncate" title="empresa">empresa</code></li>
                                <li><code class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs block truncate" title="contrato">contrato</code></li>
                                <li><code class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded text-xs block truncate" title="poliza">poliza</code></li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100 text-xs text-slate-500">
                        <p><strong>Comportamiento ante Errores:</strong> Los registros que carezcan de cédula o nombre_completo, o que no cumplan con el formato, generarán una advertencia y la fila entera será omitida. Verifique que el estado inicial por defecto se haya configurado (generalmente "Pendiente").</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.getElementById('import-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!this.checkValidity()) {
            return;
        }

        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;

        // Mostrar Swal inicial con barra de progreso
        Swal.fire({
            title: 'Procesando Archivo...',
            html: `
                <div class="space-y-6">
                    <p class="text-sm text-slate-600">Estamos validando y cargando los registros. Este proceso puede tardar unos segundos según el tamaño del archivo.</p>
                    
                    <div class="relative pt-1">
                        <div class="flex mb-2 items-center justify-between">
                            <div>
                                <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full text-primary bg-primary/10">
                                    Cargando al sistema
                                </span>
                            </div>
                            <div class="text-right">
                                <span id="percent-text" class="text-xs font-bold inline-block text-primary">
                                    0%
                                </span>
                            </div>
                        </div>
                        <div class="overflow-hidden h-2 mb-4 text-xs flex rounded-full bg-slate-100">
                            <div id="progress-bar" style="width:0%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary transition-all duration-700"></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-3 py-4">
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                        <div class="w-2 h-2 bg-primary rounded-full animate-bounce"></div>
                    </div>

                    <div id="import-log-console" class="hidden mt-6 text-left">
                        <div class="bg-slate-900 rounded-lg p-3 font-mono text-[0.65rem] text-blue-400 space-y-1 max-h-32 overflow-y-auto custom-scrollbar border border-slate-800">
                            <!-- Los logs aparecerán aquí -->
                            <div class="opacity-50 italic">Iniciando bitácora de eventos...</div>
                        </div>
                    </div>

                    <p class="text-[0.7rem] text-slate-400 italic">No cierre esta ventana ni recargue la página.</p>
                </div>
            `,
            allowOutsideClick: false,
            showConfirmButton: false,
            background: '#ffffff',
            customClass: {
                popup: 'rounded-3xl border-0 shadow-2xl p-8',
                title: 'text-2xl font-bold text-slate-800',
            }
        });

        // Simulación de progreso visual para feedback (en modo síncrono)
        let visualProgress = 0;
        const progressTimer = setInterval(() => {
            if (visualProgress < 95) {
                // Incremento gradual para dar sensación de vida
                visualProgress += (95 - visualProgress) * 0.05; 
                const pb = document.getElementById('progress-bar');
                const pt = document.getElementById('percent-text');
                if (pb) pb.style.width = Math.round(visualProgress) + '%';
                if (pt) pt.textContent = Math.round(visualProgress) + '%';
            }
        }, 800);

        // Enviar por AJAX
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(progressTimer); // Detener timer visual
            if (data.success) {
                if (data.status === 'completed') {
                    // Si el servidor respondió que ya terminó (proceso síncrono)
                    finishImport(data.lote_id, data.total, data.duplicados_count);
                } else {
                    // Si es un proceso asíncrono, iniciamos el sondeo
                    const loteId = data.lote_id;
                    startPolling(loteId);
                }
            } else {
                Swal.fire('Error', data.error || 'Ocurrió un error al cargar el archivo', 'error');
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            submitBtn.disabled = false;
        });

        function finishImport(loteId, total = 0, duplicates = 0) {
            // Llenar la barra al 100% visualmente
            const progressBar = document.getElementById('progress-bar');
            const percentText = document.getElementById('percent-text');
            const processedEl = document.getElementById('processed-count');
            const duplicatedEl = document.getElementById('duplicated-count');

            if (progressBar) progressBar.style.width = '100%';
            if (percentText) percentText.textContent = '100%';
            if (processedEl) processedEl.textContent = total;
            if (duplicatedEl) duplicatedEl.textContent = duplicates;

            Swal.fire({
                title: '¡Carga Finalizada!',
                html: `
                    <div class="text-left space-y-4">
                        <p class="text-sm text-slate-600">El proceso se ha completado satisfactoriamente.</p>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">Filas Procesadas</div>
                                <div class="text-lg font-bold text-slate-800">${total}</div>
                            </div>
                            <div>
                                <div class="text-[0.6rem] font-bold text-amber-500 uppercase tracking-widest">Duplicados Omitidos</div>
                                <div class="text-lg font-bold text-amber-600">${duplicates}</div>
                            </div>
                        </div>
                        <p class="text-[0.7rem] text-slate-400 italic">Haga clic en el botón inferior para refrescar la página y ver el detalle de los duplicados.</p>
                    </div>
                `,
                icon: 'success',
                confirmButtonColor: '#00346f',
                confirmButtonText: 'Refrescar y Ver Detalles',
                allowOutsideClick: false
            }).then(() => {
                window.location.reload();
            });
        }

        function startPolling(loteId) {
            const interval = setInterval(() => {
                fetch(`{{ url('importar/progreso') }}/${loteId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(status => {
                    const progressBar = document.getElementById('progress-bar');
                    const percentText = document.getElementById('percent-text');
                    const processedEl = document.getElementById('processed-count');
                    const duplicatedEl = document.getElementById('duplicated-count');

                    if (progressBar) progressBar.style.width = status.percentage + '%';
                    if (percentText) percentText.textContent = status.percentage + '%';
                    
                    // Mostrar y actualizar logs en tiempo real
                    const consoleEl = document.getElementById('import-log-console');
                    if (consoleEl) {
                        consoleEl.classList.remove('hidden');
                        const logContainer = consoleEl.querySelector('div');
                        if (status.logs && status.logs.length > 0) {
                            logContainer.innerHTML = status.logs.map(log => `<div>${log}</div>`).join('');
                        }
                    }

                    if (status.status === 'completed') {
                        clearInterval(interval);
                        finishImport(loteId, status.total, status.duplicados_count);
                    }
                });
            }, 1500);
        }
    }
    
    async function purgeQueueFromImport() {
        if (!confirm('¿Estás seguro de que deseas eliminar TODAS las tareas pendientes? Se detendrán todas las cargas activas.')) return;
        
        try {
            const res = await fetch('{{ route("firebase.purge_queue") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();
            if (data.success) {
                alert('Se han eliminado ' + data.count + ' tareas de la cola. Ya puede intentar subir el archivo nuevamente.');
                location.reload();
            }
        } catch (e) {
            console.error(e);
            alert('Error al purgar la cola.');
        }
    }
</script>

@endpush
