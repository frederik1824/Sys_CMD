@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight font-headline">Gestión de Mensajeros</h1>
            <p class="text-slate-500 font-medium mt-1">Administra el personal de entrega y logística.</p>
        </div>
        <a href="{{ route('mensajeros.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-bold hover:shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
            <span class="material-symbols-outlined">add_circle</span>
            Nuevo Mensajero
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($mensajeros as $mensajero)
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden group hover:border-primary/20 transition-all">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white shadow-lg" style="background-color: {{ $mensajero->color }}">
                            <span class="material-symbols-outlined text-3xl">directions_run</span>
                        </div>
                        <div class="flex gap-2">
                             <a href="{{ route('mensajeros.edit', $mensajero) }}" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition-colors">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <form action="{{ route('mensajeros.destroy', $mensajero) }}" method="POST" onsubmit="return confirm('¿Eliminar mensajero?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                    <span class="material-symbols-outlined text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-900 truncate">{{ $mensajero->nombre }}</h3>
                    <p class="text-[0.7rem] font-bold text-slate-400 uppercase tracking-widest mb-4">Cédula: {{ $mensajero->cedula }}</p>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <span class="material-symbols-outlined text-base opacity-40">phone</span>
                            <span class="font-medium">{{ $mensajero->telefono ?: 'No reg.' }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <span class="material-symbols-outlined text-base opacity-40">local_shipping</span>
                            <span class="font-medium">{{ $mensajero->vehiculo_tipo }} - <span class="uppercase">{{ $mensajero->vehiculo_placa ?: 'Sin placa' }}</span></span>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-wider {{ $mensajero->activo ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $mensajero->activo ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                        {{ $mensajero->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                    <span class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-tighter">ID #{{ $mensajero->id }}</span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 text-center bg-white rounded-[2.5rem] border-2 border-dashed border-slate-100">
                <span class="material-symbols-outlined text-6xl text-slate-100 mb-4">group_off</span>
                <p class="text-slate-400 font-medium">No hay mensajeros registrados.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $mensajeros->links() }}
    </div>
</div>
@endsection
