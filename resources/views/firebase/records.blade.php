@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;800&display=swap');

    body { background-color: #F4F7FA; font-family: 'Inter', sans-serif; }
    .premium-card { background: white; border: 1px solid #DFE1E6; border-radius: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .btn-premium { padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; font-size: 0.8rem; transition: all 0.2s ease; }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; }
    .status-synced { background: #E3FCEF; color: #006644; }
    .status-pending { background: #FFF0B3; color: #826331; }
    .status-error { background: #FFEBE6; color: #BF2600; }
    .status-conflict { background: #EAE6FF; color: #403294; }
    
    .table-row:hover { background-color: #F8F9FA; cursor: pointer; }
    .search-input { border-radius: 14px; border: 1px solid #DFE1E6; padding: 0.7rem 1rem 0.7rem 2.5rem; font-size: 0.85rem; width: 100%; transition: all 0.2s; }
    .search-input:focus { border-color: #0066FF; ring: 2px rgba(0,102,255,0.1); outline: none; }
</style>

<div class="p-6 lg:p-10 max-w-[1600px] mx-auto">
    <header class="flex justify-between items-center mb-10">
        <div class="flex items-center gap-6">
            <a href="{{ route('carnetizacion.sync_center.index') }}" class="w-12 h-12 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 font-outfit">Registros <span class="text-blue-600">Sincronizados</span></h1>
                <p class="text-xs text-slate-500 mt-1">Explora y gestiona los datos vinculados con la nube Firebase.</p>
            </div>
        </div>
        
        <div class="flex gap-3">
            <button class="btn-premium bg-white border border-slate-200 text-slate-700 hover:bg-slate-50">
                <i class="ph ph-download-simple mr-2"></i> Exportar
            </button>
        </div>
    </header>

    <!-- Filters & Search -->
    <div class="premium-card p-6 mb-10">
        <form action="{{ route('carnetizacion.sync_center.records') }}" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-6 relative">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="q" value="{{ request('q') }}" class="search-input" placeholder="Buscar por nombre o cédula...">
            </div>
            <div class="md:col-span-3">
                <select name="status" class="search-input !pl-4">
                    <option value="">Todos los estados</option>
                    <option value="synced" {{ request('status') == 'synced' ? 'selected' : '' }}>Sincronizados</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                    <option value="error" {{ request('status') == 'error' ? 'selected' : '' }}>Con Error</option>
                    <option value="conflict" {{ request('status') == 'conflict' ? 'selected' : '' }}>En Conflicto</option>
                </select>
            </div>
            <div class="md:col-span-3 flex gap-3">
                <button type="submit" class="flex-1 btn-premium bg-blue-600 text-white hover:bg-blue-700">Filtrar Resultados</button>
                <a href="{{ route('carnetizacion.sync_center.records') }}" class="px-4 py-2 flex items-center justify-center bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Records Table -->
    <div class="premium-card overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 text-[10px] font-black uppercase text-slate-400 tracking-[0.2em]">
                <tr>
                    <th class="px-8 py-5">Afiliado</th>
                    <th class="px-8 py-5">Identificación</th>
                    <th class="px-8 py-5">Estado Operativo</th>
                    <th class="px-8 py-5">Sincronización</th>
                    <th class="px-8 py-5">Último Cambio</th>
                    <th class="px-8 py-5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($records as $record)
                    <tr class="table-row">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold">
                                    {{ substr($record->nombre_completo, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $record->nombre_completo }}</p>
                                    <p class="text-[10px] text-slate-500 uppercase font-medium">{{ $record->empresa?->nombre ?? 'Sin Empresa' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="font-mono text-xs text-slate-600">{{ $record->cedula }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                <span class="text-xs font-semibold text-slate-700">{{ $record->estado?->nombre ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="status-badge status-{{ $record->sync_status ?? 'pending' }}">
                                {{ $record->sync_status ?? 'pending' }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-slate-800">{{ $record->firebase_synced_at ? $record->firebase_synced_at->diffForHumans() : 'Nunca' }}</p>
                            <p class="text-[10px] text-slate-400 uppercase font-medium">{{ $record->updated_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('carnetizacion.afiliados.show', $record->uuid) }}" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Ver Perfil">
                                    <i class="ph ph-user-focus text-lg"></i>
                                </a>
                                <button class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Comparar con Nube" onclick="compareRecord('{{ $record->cedula }}')">
                                    <i class="ph ph-scales text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center text-slate-400">
                            <i class="ph ph-magnifying-glass text-5xl mb-4"></i>
                            <p class="font-bold uppercase tracking-widest text-xs">No se encontraron registros</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 bg-slate-50/50 border-t border-slate-100">
            {{ $records->links() }}
        </div>
    </div>
</div>

<script src="https://unpkg.com/@phosphor-icons/web"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function compareRecord(id) {
        Swal.fire({
            title: 'Consultando Nube...',
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const response = await fetch(`{{ route('carnetizacion.sync_center.compare') }}?id=${id}&type=afiliado`);
            const data = await response.json();
            
            if (data.success) {
                // Simplified comparison for now
                Swal.fire({
                    title: 'Comparación de Datos',
                    html: `
                        <div class="text-left space-y-4 p-4 text-xs">
                            <div class="grid grid-cols-2 gap-4 border-b pb-2 font-bold uppercase text-slate-400 tracking-widest">
                                <div>LOCAL (CMD)</div>
                                <div>NUBE (FIREBASE)</div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-slate-500 uppercase text-[9px] font-black">Estado</p>
                                    <p class="font-bold">${data.local.estado_id || 'N/A'}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500 uppercase text-[9px] font-black">Estado</p>
                                    <p class="font-bold text-blue-600">${data.remote.estado_id || 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                    `,
                    icon: 'info',
                    customClass: { popup: 'rounded-[32px]' }
                });
            } else {
                Swal.fire('Error', 'No se encontró el registro en la nube', 'warning');
            }
        } catch (e) {
            Swal.fire('Error', 'Fallo al conectar con el servicio', 'error');
        }
    }
</script>
@endsection
