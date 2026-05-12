@extends('layouts.app')
@section('content')
    <div class="p-8 max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-on-surface">Editar Afiliado</h1>
                <p class="text-on-surface-variant text-sm mt-1">Actualizar información de {{ $afiliado->nombre_completo }}</p>
            </div>
            <a href="{{ $returnUrl }}" class="text-slate-500 hover:text-primary transition-colors flex items-center gap-1 text-sm font-semibold">
                <span class="material-symbols-outlined text-[1.25rem]">arrow_back</span> Volver a {{ $returnLabel }}
            </a>
        </div>

        <form action="{{ route('carnetizacion.afiliados.update', $afiliado) }}" method="POST" class="bg-surface-container-lowest p-8 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="last_updated_at" value="{{ $afiliado->updated_at }}">

            <h3 class="text-lg font-bold text-primary border-b border-slate-100 pb-2 mb-4">Datos Personales</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nombre Completo</label>
                    <input type="text" name="nombre_completo" value="{{ old('nombre_completo', $afiliado->nombre_completo) }}" required class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                    @error('nombre_completo') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Cédula</label>
                    <div class="relative">
                        <input type="text" name="cedula" id="cedula_input" value="{{ old('cedula', $afiliado->cedula) }}" required class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3 pr-10">
                        <div id="cedula_status" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                            <!-- Icono dinámico -->
                        </div>
                    </div>
                    <div id="duplicate_alert" class="mt-2 text-[0.7rem] font-bold hidden animate-bounce">
                        <!-- Mensaje -->
                    </div>
                    @error('cedula') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const cedulaInput = document.getElementById('cedula_input');
                        const statusIcon = document.getElementById('cedula_status');
                        const alertBox = document.getElementById('duplicate_alert');
                        const originalCedula = "{{ $afiliado->cedula }}".replace(/[^0-9]/g, '');
                        let timeout = null;

                        cedulaInput.addEventListener('input', function() {
                            clearTimeout(timeout);
                            const cedula = this.value.replace(/[^0-9]/g, '');
                            
                            // Si es la misma cédula que ya tiene, no alertar
                            if (cedula === originalCedula || cedula.length < 9) {
                                statusIcon.classList.add('hidden');
                                alertBox.classList.add('hidden');
                                return;
                            }

                            timeout = setTimeout(() => {
                                statusIcon.innerHTML = '<span class="material-symbols-outlined text-slate-400 animate-spin">sync</span>';
                                statusIcon.classList.remove('hidden');

                                fetch(`{{ route('carnetizacion.afiliados.check_duplicate') }}?cedula=${cedula}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.exists) {
                                            statusIcon.innerHTML = '<span class="material-symbols-outlined text-rose-500">warning</span>';
                                            alertBox.innerHTML = `<span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full border border-rose-200">⚠️ OTRO REGISTRO DETECTADO: ${data.nombre} aparece en la red global.</span>`;
                                            alertBox.classList.remove('hidden');
                                        } else {
                                            statusIcon.innerHTML = '<span class="material-symbols-outlined text-emerald-500">check_circle</span>';
                                            alertBox.classList.add('hidden');
                                        }
                                    });
                            }, 500);
                        });
                    });
                </script>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sexo</label>
                    <select name="sexo" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                        <option value="">-- Seleccionar --</option>
                        <option value="M" {{ old('sexo', $afiliado->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ old('sexo', $afiliado->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Teléfono</label>
                    <input type="text" name="telefono" value="{{ old('telefono', $afiliado->telefono) }}" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                    @error('telefono') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-2xl border border-slate-200">
                    <div class="md:col-span-2 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400">corporate_fare</span>
                        <h4 class="text-xs font-black text-slate-500 uppercase tracking-[0.2em]">Información de la Empresa</h4>
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-2">Empresa Vinculada</label>
                        <input type="text" id="empresa_search" list="empresas_list" 
                               value="{{ old('empresa', $afiliado->empresaModel->nombre ?? $afiliado->empresa) }}" 
                               class="w-full bg-white border border-slate-100 rounded-xl focus:ring-4 focus:ring-primary/5 p-3 text-sm font-bold shadow-sm"
                               placeholder="Escriba para buscar empresa...">
                        <datalist id="empresas_list">
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->nombre }}" data-id="{{ $emp->id }}" data-rnc="{{ $emp->rnc }}" data-dir="{{ $emp->direccion }}">
                            @endforeach
                        </datalist>
                        <input type="hidden" name="empresa_id" id="empresa_id" value="{{ old('empresa_id', $afiliado->empresa_id) }}">
                    </div>
                    <div>
                        <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-2">RNC / Identificación</label>
                        <input type="text" id="rnc_display" readonly 
                               value="{{ $afiliado->empresaModel->rnc ?? $afiliado->rnc_empresa ?? '---' }}" 
                               class="w-full bg-slate-100/50 border-none rounded-xl p-3 text-sm font-mono font-bold text-slate-400 cursor-not-allowed">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[0.65rem] font-black text-slate-400 uppercase tracking-widest mb-2">Dirección de Empresa (Para entrega laboral)</label>
                        <input type="text" id="dir_display" readonly 
                               value="{{ $afiliado->empresaModel->direccion ?? 'DIRECCIÓN NO DISPONIBLE' }}" 
                               class="w-full bg-slate-100/50 border-none rounded-xl p-3 text-xs font-medium text-slate-400 cursor-not-allowed">
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('empresa_search');
                        const idInput = document.getElementById('empresa_id');
                        const rncDisplay = document.getElementById('rnc_display');
                        const dirDisplay = document.getElementById('dir_display');
                        const dataList = document.getElementById('empresas_list');

                        searchInput.addEventListener('change', function() {
                            const val = this.value;
                            const options = dataList.options;
                            let found = false;

                            for (let i = 0; i < options.length; i++) {
                                if (options[i].value === val) {
                                    idInput.value = options[i].getAttribute('data-id');
                                    rncDisplay.value = options[i].getAttribute('data-rnc') || '---';
                                    dirDisplay.value = options[i].getAttribute('data-dir') || 'No disponible';
                                    found = true;
                                    break;
                                }
                            }

                            if (!found) {
                                idInput.value = '';
                                rncDisplay.value = '---';
                                dirDisplay.value = 'No disponible';
                            }
                        });
                    });
                </script>
                <div class="md:col-span-2 bg-primary/5 p-8 rounded-3xl border border-primary/10 space-y-6">
                    <div class="flex items-center justify-between border-b border-primary/10 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined">home</span>
                            </span>
                            <div>
                                <h4 class="text-sm font-black text-primary tracking-tight">📍 Ubicación de Entrega Personal</h4>
                                <p class="text-[0.65rem] font-bold text-slate-500 uppercase tracking-widest mt-1">Se usará para despachos a domicilio</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Provincia</label>
                            <select name="provincia_id" id="provincia_id" 
                                    class="w-full bg-white border border-slate-100 rounded-xl focus:ring-4 focus:ring-primary/5 p-3 text-sm font-bold shadow-sm">
                                <option value="">Seleccione Provincia</option>
                                @foreach($provincias as $p)
                                    <option value="{{ $p->id }}" {{ old('provincia_id', $afiliado->provincia_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Municipio</label>
                            <select name="municipio_id" id="municipio_id" 
                                    class="w-full bg-white border border-slate-100 rounded-xl focus:ring-4 focus:ring-primary/5 p-3 text-sm font-bold shadow-sm">
                                <option value="">Seleccione Municipio</option>
                                @foreach($municipios as $m)
                                    <option value="{{ $m->id }}" {{ old('municipio_id', $afiliado->municipio_id) == $m->id ? 'selected' : '' }}>
                                        {{ $m->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Dirección Residencial Completa</label>
                            <textarea name="direccion" rows="2" 
                                      class="w-full bg-white border border-slate-100 rounded-xl focus:ring-4 focus:ring-primary/5 p-3 text-sm font-medium shadow-sm"
                                      placeholder="Calle, número, residencial, apartamento...">{{ old('direccion', $afiliado->direccion) }}</textarea>
                            <p class="text-[0.65rem] italic text-slate-400 mt-2">Nota: Si dejas este campo vacío, el mensajero usará la dirección de la empresa por defecto.</p>
                        </div>
                    </div>
                </div>

                {{-- Dynamic Location Script with Tom Select --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const config = {
                            create: false,
                            sortField: { field: "text", direction: "asc" },
                            placeholder: 'Escriba para buscar...',
                        };

                        const pSelect = new TomSelect('#provincia_id', config);
                        const mSelect = new TomSelect('#municipio_id', config);

                        pSelect.on('change', function(provinciaId) {
                            if (!provinciaId) {
                                mSelect.clear();
                                mSelect.clearOptions();
                                return;
                            }

                            mSelect.clear();
                            mSelect.clearOptions();

                            fetch(`{{ url('municipios') }}/${provinciaId}`)
                                .then(response => response.json())
                                .then(data => {
                                    const options = data.map(m => ({
                                        value: m.id,
                                        text: m.nombre
                                    }));
                                    mSelect.addOptions(options);
                                    mSelect.refreshOptions(false);
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        });
                    });
                </script>
            </div>

            <h3 class="text-lg font-bold text-primary border-b border-slate-100 pb-2 mb-4 mt-8">Asignación y Estado</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Corte</label>
                    <select name="corte_id" required class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                        @foreach($cortes as $corte)
                            <option value="{{ $corte->id }}" {{ old('corte_id', $afiliado->corte_id) == $corte->id ? 'selected' : '' }}>{{ $corte->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Responsable Asignado</label>
                    <select name="responsable_id" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                        <option value="">-- Sin asignar --</option>
                        @foreach($responsables as $resp)
                            <option value="{{ $resp->id }}" {{ old('responsable_id', $afiliado->responsable_id) == $resp->id ? 'selected' : '' }}>{{ $resp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Estado Actual</label>
                    <select name="estado_id" required class="w-full bg-emerald-50 text-emerald-900 border-none rounded-xl focus:ring-2 focus:ring-primary p-3 font-semibold">
                        @foreach($estados as $estado)
                            <option value="{{ $estado->id }}" {{ old('estado_id', $afiliado->estado_id) == $estado->id ? 'selected' : '' }}>{{ $estado->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Contrato</label>
                    <input type="text" name="contrato" value="{{ old('contrato', $afiliado->contrato) }}" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Póliza</label>
                    <input type="text" name="poliza" value="{{ old('poliza', $afiliado->poliza) }}" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Observaciones</label>
                    <textarea name="observaciones" rows="3" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">{{ old('observaciones', $afiliado->observaciones) }}</textarea>
                </div>
            </div>

            <h3 class="text-lg font-bold text-emerald-600 border-b border-emerald-100 pb-2 mb-4 mt-8">Control SAFESURE y Liquidación</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Fecha Entrega a SAFESURE</label>
                    <input type="date" name="fecha_entrega_safesure" value="{{ old('fecha_entrega_safesure', $afiliado->fecha_entrega_safesure?->format('Y-m-d')) }}" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Costo Entrega (RD$)</label>
                    <input type="number" step="0.01" name="costo_entrega" value="{{ old('costo_entrega', $afiliado->costo_entrega) }}" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                </div>
                <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl md:col-span-2">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="liquidado" value="0">
                        <input type="checkbox" name="liquidado" value="1" id="liquidado" {{ old('liquidado', $afiliado->liquidado) ? 'checked' : '' }} class="rounded text-primary focus:ring-primary border-slate-300 w-5 h-5">
                        <label for="liquidado" class="text-sm font-bold text-slate-700 cursor-pointer">Marcar como Liquidado (Pagado a SAFESURE)</label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Fecha de Liquidación</label>
                    <input type="date" name="fecha_liquidacion" value="{{ old('fecha_liquidacion', $afiliado->fecha_liquidacion?->format('Y-m-d')) }}" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Referencia / Recibo Liquidación</label>
                    <input type="text" name="recibo_liquidacion" value="{{ old('recibo_liquidacion', $afiliado->recibo_liquidacion) }}" placeholder="Ej: CH-1234 o Fact-99" class="w-full bg-surface-container-low border-none rounded-xl focus:ring-2 focus:ring-primary p-3">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                <a href="{{ $returnUrl }}" class="px-6 py-3 hover:bg-slate-100 rounded-xl text-slate-600 font-semibold transition-colors">Cancelar</a>
                <button type="submit" class="px-10 py-3 bg-blue-700 text-white rounded-xl font-bold hover:bg-blue-800 transition-all shadow-lg shadow-blue-500/20 active:scale-95">Guardar Cambios</button>
            </div>
        </form>

        <!-- Loading Overlay -->
        <div id="loading_overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[100] flex flex-col items-center justify-center hidden animate-in fade-in duration-300">
            <div class="bg-white p-10 rounded-[3rem] shadow-2xl flex flex-col items-center max-w-xs text-center border border-white/20">
                <div class="relative w-24 h-24 mb-8">
                    <div class="absolute inset-0 rounded-full border-8 border-slate-100"></div>
                    <div class="absolute inset-0 rounded-full border-8 border-blue-600 border-t-transparent animate-spin"></div>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">Actualizando Registro</h3>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest leading-relaxed">Persistiendo cambios en la red local y encolando sincronización...</p>
            </div>
        </div>

        <script>
            document.querySelector('form').addEventListener('submit', function() {
                const overlay = document.getElementById('loading_overlay');
                overlay.classList.remove('hidden');
            });
        </script>
    </div>
@endsection
