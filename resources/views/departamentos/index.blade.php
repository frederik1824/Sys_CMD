@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1200px] mx-auto" x-data="{ showModal: false, editing: false, dept: { nombre: '', codigo: '', id: null } }">
    <!-- HEADER -->
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-4xl font-extrabold tracking-tighter text-slate-900">Estructura Organizacional</h1>
            <p class="text-slate-500 font-medium mt-1 text-lg">Gestiona los departamentos y unidades operativas de la empresa.</p>
        </div>
        <button @click="editing = false; dept = { nombre: '', codigo: '', id: null }; showModal = true" 
            class="bg-slate-900 text-white rounded-2xl px-8 py-4 text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-xl shadow-slate-200 flex items-center gap-3">
            <i class="ph-bold ph-plus text-lg"></i> Nuevo Departamento
        </button>
    </div>

    <!-- GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($departamentos as $d)
        <div class="bg-white p-8 rounded-[40px] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-6">
                <div class="w-14 h-14 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-slate-900 group-hover:text-white transition-all">
                    <i class="ph ph-buildings text-3xl"></i>
                </div>
                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button @click="editing = true; dept = { nombre: '{{ $d->nombre }}', codigo: '{{ $d->codigo }}', id: {{ $d->id }} }; showModal = true" 
                        class="p-2 text-slate-400 hover:text-blue-600"><i class="ph-bold ph-pencil-simple"></i></button>
                    @if($d->users_count == 0)
                    <form action="{{ route('departamentos.destroy', $d) }}" method="POST" onsubmit="return confirm('¿Eliminar departamento?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-600"><i class="ph-bold ph-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>

            <h3 class="text-xl font-black text-slate-900 tracking-tight mb-1">{{ $d->nombre }}</h3>
            <p class="text-[10px] font-black text-blue-600 uppercase tracking-widest mb-6">{{ $d->codigo }}</p>

            <div class="flex items-center gap-3 pt-6 border-t border-slate-50">
                <div class="flex -space-x-2">
                    @for($i = 0; $i < min($d->users_count, 3); $i++)
                    <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[10px] font-black text-slate-500">U</div>
                    @endfor
                </div>
                <span class="text-xs font-bold text-slate-400">{{ $d->users_count }} Usuarios asignados</span>
            </div>
        </div>
        @endforeach
    </div>

    <!-- MODAL -->
    <div x-show="showModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] flex items-center justify-center p-6" x-cloak>
        <div class="bg-white rounded-[40px] w-full max-w-lg p-10 shadow-2xl" @click.away="showModal = false">
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter mb-2" x-text="editing ? 'Editar Departamento' : 'Nuevo Departamento'"></h3>
            <p class="text-slate-500 font-medium mb-8 text-sm">Define el nombre y código único para la unidad operativa.</p>
            
            <form :action="editing ? '/departamentos/' + dept.id : '{{ route('departamentos.store') }}'" method="POST">
                @csrf
                <template x-if="editing">
                    <input type="hidden" name="_method" value="PATCH">
                </template>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Nombre del Departamento</label>
                        <input type="text" name="nombre" x-model="dept.nombre" required placeholder="Ej: Afiliación y Novedades"
                            class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-blue-500/10 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Código (Siglas)</label>
                        <input type="text" name="codigo" x-model="dept.codigo" required placeholder="Ej: AFIL"
                            class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold focus:ring-4 focus:ring-blue-500/10 transition-all uppercase">
                    </div>
                </div>
                
                <div class="flex gap-4 mt-10">
                    <button type="button" @click="showModal = false" class="flex-1 py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 transition-all">Cancelar</button>
                    <button type="submit" class="flex-[2] py-5 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 shadow-xl shadow-slate-200 transition-all" x-text="editing ? 'Guardar Cambios' : 'Crear Departamento'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
