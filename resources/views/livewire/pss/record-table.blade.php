<div class="space-y-6">
    <!-- Header Actions (Moved inside for reactivity) -->
    <div class="flex justify-between items-end gap-4 mb-2">
        <div>
            <!-- Empty div to push buttons to the right if needed, or keep for future left-side actions -->
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pss.import.index') }}" class="bg-white text-slate-700 px-5 py-2.5 rounded-2xl font-bold text-sm shadow-sm border border-slate-200 hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="ph-bold ph-upload-simple text-lg"></i>
                Importar
            </a>
            <button wire:click="openCreateModal" class="bg-primary text-white px-6 py-2.5 rounded-2xl font-bold text-sm shadow-lg shadow-primary/20 hover:bg-secondary transition-all flex items-center gap-2">
                <i class="ph-bold ph-plus text-lg"></i>
                Nuevo {{ $type === 'medicos' ? 'Médico' : 'Centro' }}
            </button>
        </div>
    </div>

    <!-- Filters Header -->
    <div class="glass-card p-6 rounded-[2rem] shadow-sm border border-white/50">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px] relative">
                <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por nombre o teléfono..." 
                       class="w-full bg-slate-50 border-transparent rounded-2xl py-3 pl-12 pr-4 text-sm font-bold focus:ring-primary focus:bg-white transition-all">
            </div>
            
            <select wire:model.live="city_id" class="bg-slate-50 border-transparent rounded-2xl px-4 py-3 text-sm font-bold focus:ring-primary">
                <option value="">Todas las Ciudades</option>
                @foreach($ciudades as $ciudad)
                <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                @endforeach
            </select>

            @if($type === 'medicos')
            <select wire:model.live="specialty_id" class="bg-slate-50 border-transparent rounded-2xl px-4 py-3 text-sm font-bold focus:ring-primary">
                <option value="">Todas las Especialidades</option>
                @foreach($especialidades as $esp)
                <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                @endforeach
            </select>
            @else
            <select wire:model.live="group_id" class="bg-slate-50 border-transparent rounded-2xl px-4 py-3 text-sm font-bold focus:ring-primary">
                <option value="">Todos los Grupos</option>
                @foreach($grupos as $grupo)
                <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                @endforeach
            </select>
            @endif

            <select wire:model.live="status" class="bg-slate-50 border-transparent rounded-2xl px-4 py-3 text-sm font-bold focus:ring-primary">
                <option value="">Cualquier Estatus</option>
                <option value="activo">Activo</option>
                <option value="inactivo">Inactivo</option>
                <option value="depuración">En Depuración</option>
            </select>
        </div>
    </div>

    <!-- Table Content -->
    <div class="glass-card rounded-[2.5rem] overflow-hidden shadow-xl border border-white/50">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] opacity-50">Prestador</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] opacity-50">Contacto</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] opacity-50">Ubicación</th>
                        @if($type === 'medicos')
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] opacity-50">Especialidad</th>
                        @else
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] opacity-50">Grupo</th>
                        @endif
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] opacity-50">Estado</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-[0.2em] opacity-50 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($records as $record)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/5 text-primary rounded-xl flex items-center justify-center font-black text-xs">
                                    {{ substr($record->nombre, 0, 2) }}
                                </div>
                                <span class="text-sm font-black text-slate-800">{{ $record->nombre }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-600">{{ $record->telefono_1 ?: 'N/A' }}</span>
                                @if($record->telefono_2)
                                <span class="text-[10px] text-slate-400 font-medium">{{ $record->telefono_2 }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5">
                                <i class="ph ph-map-pin text-slate-400"></i>
                                <span class="text-xs font-bold text-slate-600">{{ $record->ciudad->nombre ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($type === 'medicos')
                            <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase">
                                {{ $record->especialidad->nombre ?? 'General' }}
                            </span>
                            @else
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-[10px] font-black uppercase">
                                {{ $record->grupo->nombre ?? 'General' }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $color = match($record->estado) {
                                    'activo' => 'bg-emerald-100 text-emerald-700',
                                    'inactivo' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-amber-100 text-amber-700'
                                };
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $color }}">
                                {{ $record->estado }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="editRecord({{ $record->id }})" class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-primary hover:text-white transition-all">
                                    <i class="ph-bold ph-pencil-simple"></i>
                                </button>
                                <button wire:click="deleteRecord({{ $record->id }})" wire:confirm="¿Estás seguro de eliminar este registro?" 
                                        class="w-8 h-8 rounded-lg bg-slate-100 text-rose-600 hover:bg-rose-600 hover:text-white transition-all">
                                    <i class="ph-bold ph-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-3xl">
                                    <i class="ph ph-mask-sad"></i>
                                </div>
                                <p class="text-slate-400 font-bold">No se encontraron registros para esta búsqueda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($records->hasPages())
        <div class="p-6 bg-slate-50/50 border-t border-slate-100">
            {{ $records->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showModal', false)"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div class="inline-block align-bottom bg-white rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white/20 animate-in fade-in zoom-in-95 duration-300">
                <div class="bg-dark p-8 text-white relative overflow-hidden">
                    <i class="ph-fill ph-plus-circle absolute -right-6 -bottom-6 text-8xl opacity-10"></i>
                    <h3 class="text-xl font-black uppercase tracking-tighter">{{ $editingRecordId ? 'Editar' : 'Nuevo' }} {{ $type === 'medicos' ? 'Médico' : 'Centro' }}</h3>
                    <p class="text-white/50 text-xs font-bold uppercase tracking-widest mt-1">Completa la información del prestador</p>
                </div>

                <form wire:submit.prevent="save" class="p-8 space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nombre Completo</label>
                        <input type="text" wire:model="form.nombre" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white @error('form.nombre') border-rose-500 @enderror">
                        @error('form.nombre') <span class="text-[10px] text-rose-500 font-bold ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Teléfono Principal</label>
                            <input type="text" wire:model="form.telefono_1" placeholder="(809) 000-0000" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Teléfono Secundario</label>
                            <input type="text" wire:model="form.telefono_2" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Ciudad</label>
                            <select wire:model="form.ciudad_id" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white">
                                <option value="">Seleccionar...</option>
                                @foreach($ciudades as $ciudad)
                                <option value="{{ $ciudad->id }}">{{ $ciudad->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Estado</label>
                            <select wire:model="form.estado" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                                <option value="depuración">En Depuración</option>
                            </select>
                        </div>
                    </div>

                    @if($type === 'medicos')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Especialidad</label>
                            <select wire:model="form.especialidad_id" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white">
                                <option value="">General</option>
                                @foreach($especialidades as $esp)
                                <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Centro Principal</label>
                            <select wire:model="form.clinica_id" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white">
                                <option value="">Ninguno</option>
                                @foreach($clinicas as $clinica)
                                <option value="{{ $clinica->id }}">{{ $clinica->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Grupo / Categoría</label>
                        <select wire:model="form.grupo_id" class="w-full bg-slate-50 border-transparent rounded-xl py-3 px-4 text-sm font-bold focus:ring-primary focus:bg-white">
                            <option value="">General</option>
                            @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id }}">{{ $grupo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="flex items-center justify-end gap-3 pt-6">
                        <button type="button" wire:click="$set('showModal', false)" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Cancelar</button>
                        <button type="submit" class="bg-primary text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-primary/30 hover:bg-secondary transition-all">
                            {{ $editingRecordId ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>