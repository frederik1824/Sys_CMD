<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">save</span>
                    Copias de Seguridad (Backups)
                </h1>
                <p class="text-slate-500 mt-1 text-sm">Gestiona y configura políticas de copias de seguridad de la base de datos MySQL.</p>
            </div>
            
            <button id="btn-create-backup" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-xl shadow-lg shadow-blue-200 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">database</span>
                Generar Respaldo
            </button>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-rose-50 text-rose-700 border border-rose-200 rounded-xl p-4 flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Panel de Configuración -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden h-fit">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-500 text-[20px]">settings</span>
                    <h2 class="font-bold text-slate-700">Políticas de Respaldo</h2>
                </div>
                
                <form action="{{ route('sistema.backups.settings') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    
                    <!-- Automatización -->
                    <div class="flex items-center justify-between bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div>
                            <p class="font-semibold text-slate-700 text-sm">Respaldo Automático</p>
                            <p class="text-xs text-slate-500">Programar ejecución recurrente</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_automated" value="1" class="sr-only peer" {{ $settings->is_automated ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Frecuencia</label>
                            <select name="schedule_frequency" class="w-full border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="daily" {{ $settings->schedule_frequency === 'daily' ? 'selected' : '' }}>Diario</option>
                                <option value="weekly" {{ $settings->schedule_frequency === 'weekly' ? 'selected' : '' }}>Semanal</option>
                                <option value="monthly" {{ $settings->schedule_frequency === 'monthly' ? 'selected' : '' }}>Mensual</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Hora de Ejecución</label>
                            <input type="time" name="schedule_time" value="{{ $settings->schedule_time }}" class="w-full border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-[11px] text-slate-400 mt-1">Formato 24h</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Límite de Retención</label>
                            <input type="number" name="max_backups" value="{{ $settings->max_backups }}" min="1" max="50" class="w-full border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-[11px] text-slate-400 mt-1">Nro. de copias a conservar antes de borrar las más antiguas</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Ruta Personalizada (Opcional)</label>
                            <input type="text" name="custom_path" value="{{ $settings->custom_path }}" placeholder="Ej: D:\Backups\SysCarnet" class="w-full border-slate-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-[11px] text-slate-400 mt-1">Si se deja en blanco, usará la carpeta storage interna.</p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>

            <!-- Listado de Backups -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-500 text-[20px]">history</span>
                    <h2 class="font-bold text-slate-700">Historial de Respaldo</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-slate-50/50 border-b border-slate-100 text-slate-500">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Archivo</th>
                                <th class="px-6 py-3 font-semibold">Fecha</th>
                                <th class="px-6 py-3 font-semibold">Tamaño</th>
                                <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse($backups as $backup)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[18px]">dataset</span>
                                        </div>
                                        <span class="font-medium text-slate-800">{{ $backup['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3">{{ \Carbon\Carbon::parse($backup['date'])->format('d M, Y - h:i A') }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-md font-medium text-xs">
                                        {{ $backup['size'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('sistema.backups.download', $backup['name']) }}" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded-lg transition-colors tooltip" title="Descargar">
                                            <span class="material-symbols-outlined text-[18px]">download</span>
                                        </a>
                                        <button onclick="deleteBackup('{{ $backup['name'] }}')" class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition-colors tooltip" title="Eliminar">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    <div class="w-14 h-14 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <span class="material-symbols-outlined text-2xl">cloud_off</span>
                                    </div>
                                    <p class="font-medium">No hay copias de seguridad disponibles</p>
                                    <p class="text-sm mt-1">Genera un respaldo manualmente con el botón superior.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('btn-create-backup').addEventListener('click', function() {
            Swal.fire({
                title: 'Generando Respaldo',
                text: 'Comprimiendo base de datos y aplicando políticas de retención. Por favor espera...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('sistema.backups.create') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Listo!',
                        text: 'La copia de seguridad ha sido generada y el límite aplicado.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message || 'Ocurrió un problema inesperado', 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error de Conexión', 'No se pudo comunicar con el servidor.', 'error');
            });
        });

        function deleteBackup(name) {
            Swal.fire({
                title: '¿Eliminar respaldo?',
                text: "Esta acción es irreversible.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Eliminando...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                    
                    fetch(`{{ url('sistema/backups') }}/${name}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('Eliminado', data.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'No se pudo eliminar el archivo', 'error');
                    });
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
