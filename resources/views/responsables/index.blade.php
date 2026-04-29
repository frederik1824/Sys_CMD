@extends('layouts.app')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Responsables</h2>
            <p class="text-on-surface-variant text-[0.875rem] mt-1">Gestión de las empresas operadoras.</p>
        </div>
        <a href="{{ route('responsables.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-lg text-[0.875rem] font-semibold flex items-center gap-2 shadow-lg shadow-primary/20 hover:bg-blue-800 transition-colors">
            <span class="material-symbols-outlined text-lg">add</span>
            Nuevo Responsable
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-surface-container-high">
                <tr>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">ID</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Nombre</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Usuario</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Descripción</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Estado</th>
                    <th class="px-8 py-4 text-[0.6875rem] font-bold uppercase tracking-wider text-on-surface-variant">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-low">
                @forelse($responsables as $resp)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-8 py-5 text-[0.875rem] font-bold text-slate-500">{{ $resp->id }}</td>
                    <td class="px-8 py-5 text-[0.875rem] font-semibold text-on-surface">{{ $resp->nombre }}</td>
                    <td class="px-8 py-5 text-[0.875rem] text-slate-600">
                        @if($resp->user)
                            <span class="font-bold text-primary">{{ $resp->user->name }}</span>
                        @else
                            <span class="text-slate-400 italic">No asignado</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-[0.875rem] text-slate-600">{{ $resp->descripcion ?? 'N/A' }}</td>
                    <td class="px-8 py-5 text-[0.875rem]">
                        @if($resp->activo)
                            <span class="px-3 py-1 bg-green-100 text-green-700 font-bold text-xs rounded-full">Activo</span>
                        @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 font-bold text-xs rounded-full">Inactivo</span>
                        @endif
                    </td>
                    <td class="px-8 py-5 text-[0.875rem]">
                        <a href="{{ route('responsables.edit', $resp) }}" class="text-primary font-bold hover:underline">Editar</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-8 py-5 text-center text-slate-500 text-sm">No hay responsables registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">
            {{ $responsables->links() }}
        </div>
    </div>
</div>
@endsection
