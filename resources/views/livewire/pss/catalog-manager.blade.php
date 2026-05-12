<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            @foreach(['ciudades' => 'Ciudades', 'especialidades' => 'Especialidades', 'grupos' => 'Grupos PSS', 'clinicas' => 'Clínicas'] as $type => $label)
            <button wire:click="$set('catalogType', '{{ $type }}')" 
                    class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all {{ $catalogType === $type ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white text-slate-400 hover:text-slate-600 border border-slate-100' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
        <button wire:click="openCreateModal" class="bg-dark text-white px-6 py-2.5 rounded-xl font-bold text-sm flex items-center gap-2 hover:bg-slate-800 transition-all">
            <i class="ph-bold ph-plus"></i>
            Nuevo Registro
        </button>
    </div>

    <!-- Filters -->
    <div class="glass-card p-4 rounded-3xl shadow-sm border border-white/50">
        <div class="relative">
            <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar en {{ $catalogType }}..." 
                   class="w-full bg-slate-50 border-transparent rounded-2xl py-3 pl-12 pr-4 text-sm font-bold focus:ring-primary focus:bg-white transition-all">
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-[2rem] overflow-hidden shadow-xl border border-slate-100">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Nombre</th>
                    @if($catalogType === 'clinicas')
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Ciudad</th>
                    @endif
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Estado</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($records as $record)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="text-sm font-black text-slate-800">{{ $record->nombre }}</span>
                    </td>
                    @if($catalogType === 'clinicas')
                    <td class="px-6 py-4">
                        <span class="text-xs font-bold text-slate-600">{{ $record->ciudad->nombre ?? 'N/A' }}</span>
                    </td>
                    @endif
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $record->activo ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $record->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="edit({{ $record->id }})" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-primary hover:text-white transition-all">
                            <i class="ph-bold ph-pencil-simple"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-400 font-bold">No hay registros.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($records->hasPages())
        <div class="p-4 border-t border-slate-50">
            {{ $records->links() }}
        </div>
        @endif
    </div>

    <!-- Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showModal', false)"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-dark p-6 text-white">
                    <h3 class="text-lg font-black uppercase tracking-tighter">{{ $editingId ? 'Editar' : 'Nuevo' }} {{ ucfirst(rtrim($catalogType, 'es')) }}</h3>
                </div>
                <form wire:submit.prevent="save" class="p-8 space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nombre</label>
                        <input type="text" wire:model="form.nombre" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white">
                        @error('form.nombre') <span class="text-[10px] text-rose-500 font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    @if($catalogType === 'clinicas')
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Ciudad</label>
                        <select wire:model="form.ciudad_id" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary">
                            <option value="">Seleccionar Ciudad...</option>
                            @foreach($ciudades as $ciudad)
                            <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                            @endforeach
                        </select>
                        @error('form.ciudad_id') <span class="text-[10px] text-rose-500 font-bold ml-1">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    <div class="flex items-center gap-3 bg-slate-50 p-4 rounded-2xl">
                        <input type="checkbox" wire:model="form.activo" class="w-5 h-5 text-primary border-slate-300 rounded focus:ring-primary">
                        <span class="text-sm font-bold text-slate-700">Registro Activo</span>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6">
                        <button type="button" wire:click="$set('showModal', false)" class="px-6 py-3 text-sm font-bold text-slate-400">Cancelar</button>
                        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-primary/30">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
