@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Proveedores de Entrega</h2>
            <p class="text-on-surface-variant text-[0.875rem] mt-1">Gestión de empresas o couriers encargados de entregar carnets (ej. SAFESURE).</p>
        </div>
        <a href="{{ route('sistema.proveedores.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-[0.875rem] font-semibold flex items-center gap-2 shadow-lg shadow-primary/20 hover:bg-blue-800 transition-colors">
            <span class="material-symbols-outlined text-lg">add</span>
            Nuevo Proveedor
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-high">
                <tr>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">ID</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Nombre</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Precio Base (RD$)</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Estado</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-low">
                @forelse($proveedores as $p)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-8 py-5 text-[0.875rem] font-bold text-slate-500">{{ $p->id }}</td>
                    <td class="px-8 py-5 text-[0.875rem] font-semibold text-on-surface">{{ $p->nombre }}</td>
                    <td class="px-8 py-5 text-[0.875rem] text-emerald-600 font-bold">RD$ {{ number_format($p->precio_base, 2) }}</td>
                    <td class="px-8 py-5 text-[0.875rem]">
                        @if($p->activo)
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 font-bold text-xs rounded-full">Activo</span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 font-bold text-xs rounded-full">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-[0.875rem] flex gap-3">
                        <a href="{{ route('sistema.proveedores.edit', $p) }}" class="text-primary font-bold hover:underline">Editar</a>
                        <form action="{{ route('sistema.proveedores.destroy', $p) }}" method="POST" onsubmit="confirmActionForm(event, '¿Eliminar proveedor?', 'Esta acción no se puede deshacer y podría afectar el historial si el proveedor tiene afiliados.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 font-bold hover:underline">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-5 text-center text-slate-500 text-sm">No hay proveedores registrados. Haga clic en Nuevo Proveedor para agregar uno (ej. SAFESURE).</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
