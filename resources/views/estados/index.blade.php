@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Estados</h2>
            <p class="text-on-surface-variant text-[0.875rem] mt-1">Gestión de estados del flujo de los afiliados.</p>
        </div>
        <a href="{{ route('sistema.estados.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-[0.875rem] font-semibold flex items-center gap-2 shadow-lg shadow-primary/20 hover:bg-blue-800 transition-colors">
            <span class="material-symbols-outlined text-lg">add</span>
            Nuevo Estado
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-high">
                <tr>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">ID</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Nombre</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Descripción</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-low">
                @forelse($estados as $estado)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-8 py-5 text-[0.875rem] font-bold text-slate-500">{{ $estado->id }}</td>
                    <td class="px-8 py-5 text-[0.875rem] font-semibold text-on-surface">
                        <span class="px-3 py-1 bg-slate-100 text-slate-700 font-bold text-xs rounded-full">{{ $estado->nombre }}</span>
                    </td>
                    <td class="px-8 py-5 text-[0.875rem] text-slate-600">{{ $estado->descripcion ?? 'N/A' }}</td>
                    <td class="px-8 py-5 text-[0.875rem] flex gap-3">
                        <a href="{{ route('sistema.estados.edit', $estado) }}" class="text-primary font-bold hover:underline">Editar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-5 text-center text-slate-500 text-sm">No hay estados registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $estados->links() }}
        </div>
    </div>
</div>
@endsection
