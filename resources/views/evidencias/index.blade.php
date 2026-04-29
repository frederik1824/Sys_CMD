@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Validación de Evidencias</h2>
            <p class="text-on-surface-variant text-[0.875rem] mt-1">Revisa y aprueba los documentos subidos por los afiliados.</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-100 mb-2">
        <form method="GET" action="{{ route('evidencias.index') }}" class="flex gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Tipo de Documento</label>
                <select name="tipo_documento" class="bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-2.5 text-sm min-w-[200px]">
                    <option value="">Todos</option>
                    <option value="acuse_recibo" {{ request('tipo_documento') == 'acuse_recibo' ? 'selected' : '' }}>Acuse de Recibo</option>
                    <option value="formulario_firmado" {{ request('tipo_documento') == 'formulario_firmado' ? 'selected' : '' }}>Formulario Firmado</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Estado</label>
                <select name="status" class="bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-2.5 text-sm min-w-[150px]">
                    <option value="">Todos</option>
                    <option value="recibido" {{ request('status') == 'recibido' ? 'selected' : '' }}>Recibidos (Pendientes)</option>
                    <option value="valido" {{ request('status') == 'valido' ? 'selected' : '' }}>Válidos</option>
                    <option value="invalido" {{ request('status') == 'invalido' ? 'selected' : '' }}>Inválidos</option>
                </select>
            </div>
            <button type="submit" class="bg-primary text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-blue-800 transition-colors">Filtrar</button>
            <a href="{{ route('evidencias.index') }}" class="text-slate-500 text-sm font-semibold hover:underline ml-2">Limpiar</a>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-high">
                <tr>
                    <th class="px-6 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Afiliado</th>
                    <th class="px-6 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Documento</th>
                    <th class="px-6 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Subido El</th>
                    <th class="px-6 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Estado</th>
                    <th class="px-6 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-low text-sm">
                @forelse($evidencias as $evidencia)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-700">{{ $evidencia->afiliado->nombre_completo ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-500">{{ $evidencia->afiliado->cedula ?? '' }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-slate-600">
                        {{ str_replace('_', ' ', Str::title($evidencia->tipo_documento)) }}
                    </td>
                    <td class="px-6 py-4 text-slate-500">
                        {{ $evidencia->created_at->format('d M Y, h:i A') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($evidencia->status === 'valido')
                            <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full">Válido</span>
                        @elseif($evidencia->status === 'invalido')
                            <span class="px-3 py-1 bg-red-100 text-red-700 font-bold text-xs rounded-full">Inválido</span>
                        @else
                            <span class="px-3 py-1 bg-amber-100 text-amber-700 font-bold text-xs rounded-full">Recibido</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 flex gap-2 justify-center">
                        <!-- Ver Documento -->
                        <a href="{{ asset('storage/' . $evidencia->file_path) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded font-semibold hover:bg-slate-200 transition-colors text-xs flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">visibility</span> Ver
                        </a>

                        <!-- Validar Form -->
                        @if($evidencia->status === 'recibido')
                        <form action="{{ route('evidencias.status', $evidencia) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="status" value="valido">
                            <button type="submit" class="px-3 py-1.5 bg-green-500 text-white rounded font-semibold hover:bg-green-600 transition-colors text-xs flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">check</span> Aprobar
                            </button>
                        </form>
                        <form action="{{ route('evidencias.status', $evidencia) }}" method="POST" class="inline" onsubmit="confirmActionForm(event, '¿Rechazar Documento?', 'El afiliado volverá a su estado anterior.');">
                            @csrf
                            <input type="hidden" name="status" value="invalido">
                            <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 rounded font-semibold hover:bg-red-100 transition-colors text-xs flex items-center gap-1 border border-red-200">
                                <span class="material-symbols-outlined text-[16px]">close</span> Rechazar
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-slate-500 text-sm">No hay evidencias subidas que coincidan con los filtros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $evidencias->links() }}
        </div>
    </div>
</div>
@endsection
