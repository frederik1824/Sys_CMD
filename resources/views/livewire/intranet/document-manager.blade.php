<div class="space-y-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12 animate-fade-in">
        <div>
            <h2 class="text-4xl font-black text-on-surface dark:text-white tracking-tight font-headline">Repositorio Digital Maestro</h2>
            <p class="text-[0.7rem] text-outline dark:text-outline-variant font-black uppercase tracking-[0.2em] mt-3">Gobernanza y Control Documental ARS CMD</p>
        </div>
        <div class="flex gap-4">
            <button wire:click="openCreateModal" class="px-7 py-3.5 bg-primary text-white font-black rounded-2xl shadow-lg shadow-primary/20 hover:scale-105 hover:shadow-xl transition-all flex items-center gap-3 text-[0.65rem] uppercase tracking-widest">
                <span class="material-symbols-outlined text-lg">add_circle</span>
                Nuevo Documento
            </button>
        </div>
    </div>

    <!-- Create Document Simple Form/Modal Section -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[100] flex items-center justify-center p-6">
            <div class="bg-surface-container-lowest w-full max-w-2xl rounded-3xl p-8 shadow-2xl border border-outline-variant/10 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl font-bold text-on-surface font-headline">Nuevo Documento</h3>
                    <button wire:click="$set('showCreateModal', false)" class="text-outline hover:text-on-surface transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="store" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-1 md:col-span-2">
                            <label class="label-sm text-outline-variant uppercase tracking-widest block mb-2">Título del Documento</label>
                            <input wire:model="title" type="text" class="w-full bg-surface-container-high border-none rounded-xl text-sm py-3 px-4 focus:ring-2 focus:ring-primary/20 transition-all shadow-sm font-body" placeholder="Ej. Política de Traspasos">
                            @error('title') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="label-sm text-outline-variant uppercase tracking-widest block mb-2">Código Interno</label>
                            <input wire:model="code" type="text" class="w-full bg-surface-container-high border-none rounded-xl text-sm py-3 px-4 focus:ring-2 focus:ring-primary/20 transition-all shadow-sm font-body" placeholder="AR-SD-001">
                            @error('code') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="label-sm text-outline-variant uppercase tracking-widest block mb-2">Fecha de Expiración</label>
                            <input wire:model="expires_at" type="date" class="w-full bg-surface-container-high border-none rounded-xl text-sm py-3 px-4 focus:ring-2 focus:ring-primary/20 transition-all shadow-sm font-body">
                            @error('expires_at') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="label-sm text-outline-variant uppercase tracking-widest block mb-2">Departamento</label>
                            <select wire:model="department_id" class="w-full bg-surface-container-high border-none rounded-xl text-sm py-3 px-4 focus:ring-2 focus:ring-primary/20 transition-all shadow-sm font-body cursor-pointer">
                                <option value="">Seleccione Depto.</option>
                                @foreach(\App\Models\Department::all() as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="label-sm text-outline-variant uppercase tracking-widest block mb-2">Tipo de Documento</label>
                            <select wire:model="document_type_id" class="w-full bg-surface-container-high border-none rounded-xl text-sm py-3 px-4 focus:ring-2 focus:ring-primary/20 transition-all shadow-sm font-body cursor-pointer">
                                <option value="">Seleccione Tipo</option>
                                @foreach(\App\Models\DocumentType::all() as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('document_type_id') <span class="text-xs text-error mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-1 md:col-span-2">
                            <label class="label-sm text-primary uppercase tracking-widest block mb-3 font-bold">Nivel de Privacidad y Acceso</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative flex flex-col p-4 bg-surface-container-high rounded-2xl cursor-pointer hover:bg-primary/5 transition-all {{ $visibility == 'public' ? 'ring-2 ring-primary bg-primary/5' : '' }}">
                                    <input type="radio" wire:model.live="visibility" value="public" class="hidden">
                                    <span class="material-symbols-outlined text-primary mb-2">public</span>
                                    <span class="text-[0.6rem] font-black uppercase tracking-widest">Público</span>
                                    <span class="text-[0.5rem] text-outline mt-1 leading-tight">Todo el personal</span>
                                </label>
                                <label class="relative flex flex-col p-4 bg-surface-container-high rounded-2xl cursor-pointer hover:bg-primary/5 transition-all {{ $visibility == 'department' ? 'ring-2 ring-primary bg-primary/5' : '' }}">
                                    <input type="radio" wire:model.live="visibility" value="department" class="hidden">
                                    <span class="material-symbols-outlined text-primary mb-2">groups</span>
                                    <span class="text-[0.6rem] font-black uppercase tracking-widest">Depto.</span>
                                    <span class="text-[0.5rem] text-outline mt-1 leading-tight">Solo equipo</span>
                                </label>
                                <label class="relative flex flex-col p-4 bg-surface-container-high rounded-2xl cursor-pointer hover:bg-primary/5 transition-all {{ $visibility == 'private' ? 'ring-2 ring-primary bg-primary/5' : '' }}">
                                    <input type="radio" wire:model.live="visibility" value="private" class="hidden">
                                    <span class="material-symbols-outlined text-primary mb-2">lock</span>
                                    <span class="text-[0.6rem] font-black uppercase tracking-widest">Privado</span>
                                    <span class="text-[0.5rem] text-outline mt-1 leading-tight">Solo autor</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 py-4">
                        <input wire:model="is_regulatory" id="regulatory_chk" type="checkbox" class="w-5 h-5 text-primary border-outline-variant rounded focus:ring-primary/20 transition-all cursor-pointer">
                        <label for="regulatory_chk" class="text-sm font-bold text-on-surface-variant cursor-pointer italic">Documento Normativo (Reportable a SISALRIL)</label>
                    </div>

                    <div class="p-6 bg-primary/5 rounded-2xl border-2 border-dashed border-primary/20">
                        <label class="label-sm text-primary uppercase tracking-widest block mb-3 font-bold text-center">Archivo PDF Maestro</label>
                        <div class="flex items-center justify-center">
                            <label class="cursor-pointer flex flex-col items-center">
                                <span class="material-symbols-outlined text-4xl text-primary animate-bounce">cloud_upload</span>
                                <span class="text-xs font-bold text-outline uppercase tracking-wider mt-2">{{ $file ? $file->getClientOriginalName() : 'Examinar Local' }}</span>
                                <input wire:model="file" type="file" class="hidden" accept=".pdf">
                            </label>
                        </div>
                        @error('file') <p class="text-center text-xs text-error mt-2 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-outline-variant/10">
                         <button type="button" wire:click="$set('showCreateModal', false)" class="px-6 py-3 text-outline font-bold text-xs uppercase tracking-widest hover:text-on-surface transition-colors uppercase">Cancelar</button>
                         <button type="submit" wire:loading.attr="disabled" class="px-8 py-3 bg-primary text-white font-bold text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all disabled:opacity-50">
                            <span wire:loading.remove>Crear Documento</span>
                            <span wire:loading>Procesando...</span>
                         </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Active Filters Badge & Status -->
    @if($search || $filterType || $filterDepartment || $filterStatus || $filterRegulatory)
        <div class="flex items-center gap-4 mb-6 animate-fade-in">
            <div class="flex items-center gap-2 bg-primary/5 border border-primary/20 px-5 py-2.5 rounded-2xl shadow-sm">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                <p class="text-[0.6rem] font-black text-primary uppercase tracking-[0.2em]">Filtros Activos</p>
                <div class="w-[1px] h-4 bg-primary/20 mx-2"></div>
                <button wire:click="$set('search', ''); $set('filterType', ''); $set('filterDepartment', ''); $set('filterStatus', ''); $set('filterRegulatory', false);" 
                        class="text-[0.6rem] font-bold text-outline hover:text-error transition-all uppercase tracking-widest flex items-center gap-1 group">
                    <span class="material-symbols-outlined text-[0.95rem] group-hover:rotate-90 transition-transform">close</span>
                    Limpiar Todo
                </button>
            </div>
            
            @if($filterRegulatory)
                <span class="bg-tertiary/10 text-tertiary text-[0.55rem] font-black px-4 py-2 rounded-xl border border-tertiary/20 uppercase tracking-widest flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">gavel</span>
                    Módulo Normativo SISALRIL
                </span>
            @endif
        </div>
    @endif

    <!-- Filters Section -->
    <div wire:loading.remove wire:target="filterType, filterDepartment, filterStatus, filterRegulatory" class="bg-white dark:bg-surface-container-low p-8 rounded-[2rem] mb-10 flex flex-wrap lg:flex-nowrap gap-6 items-end shadow-sm border border-outline-variant/10">
        <div class="flex-1 min-w-[300px]">
            <label class="text-[0.6rem] font-black text-outline dark:text-outline-variant uppercase tracking-widest mb-3 block italic">Consultar Repositorio</label>
            <div class="flex items-center bg-slate-50 dark:bg-surface-container px-6 py-3 rounded-2xl w-full border border-outline-variant/5 focus-within:border-primary/20 transition-all">
                <span class="material-symbols-outlined text-outline text-lg mr-3">search</span>
                <input wire:model.live.debounce.300ms="search" class="bg-transparent border-none focus:ring-0 text-sm font-bold w-full text-on-surface dark:text-white placeholder:text-outline/50" placeholder="Código, título o palabras clave..." type="text">
            </div>
        </div>

        <div class="w-full lg:w-48">
            <label class="text-[0.6rem] font-black text-outline dark:text-outline-variant uppercase tracking-widest mb-3 block italic">Departamento</label>
            <select wire:model.live="filterDepartment" class="w-full bg-slate-50 dark:bg-surface-container border border-outline-variant/5 rounded-2xl text-[0.75rem] font-bold text-on-surface dark:text-white focus:ring-primary/20 py-3.5 px-5 cursor-pointer">
                <option value="">Cualquier Depto.</option>
                @foreach(\App\Models\Department::all() as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full lg:w-48">
            <label class="text-[0.6rem] font-black text-outline dark:text-outline-variant uppercase tracking-widest mb-3 block italic">Estatus</label>
            <select wire:model.live="filterStatus" class="w-full bg-slate-50 dark:bg-surface-container border border-outline-variant/5 rounded-2xl text-[0.75rem] font-bold text-on-surface dark:text-white focus:ring-primary/20 py-3.5 px-5 cursor-pointer">
                <option value="">Cualquier Estatus</option>
                @foreach(\App\Models\DocumentStatus::all() as $st)
                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- SISALRIL Premium Toggle -->
        <div class="flex items-center gap-4 px-6 h-[54px] bg-tertiary/5 dark:bg-tertiary/10 rounded-2xl border border-tertiary/10 group cursor-pointer hover:bg-tertiary/10 transition-all" @click="$wire.set('filterRegulatory', !@js($filterRegulatory))">
            <div class="flex flex-col">
                <p class="text-[0.6rem] font-black text-tertiary uppercase tracking-tighter">SISALRIL</p>
                <p class="text-[0.5rem] font-bold text-outline uppercase tracking-widest">Normativo</p>
            </div>
            <div class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $filterRegulatory ? 'bg-tertiary' : 'bg-slate-300 dark:bg-surface-container-high' }}">
                <span class="inline-block h-3 w-3 transform rounded-full bg-white transition-transform {{ $filterRegulatory ? 'translate-x-5' : 'translate-x-1' }}"></span>
            </div>
        </div>
    </div>

    <!-- Data Table Container -->
    <div class="bg-white dark:bg-surface-container-low rounded-[3rem] overflow-hidden mb-6 border border-outline-variant/10 shadow-sm relative min-h-[400px]">
        <div wire:loading.remove wire:target="search, filterType, filterDepartment, filterStatus, filterRegulatory">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-surface-container-high/50 text-on-surface-variant">
                        <tr>
                            <th class="py-5 px-8 text-[0.6rem] font-black uppercase tracking-widest text-outline">Código</th>
                            <th class="py-5 px-8 text-[0.6rem] font-black uppercase tracking-widest text-outline">Título e Identidad</th>
                            <th class="py-5 px-8 text-[0.6rem] font-black uppercase tracking-widest text-outline">Departamento</th>
                            <th class="py-5 px-8 text-[0.6rem] font-black uppercase tracking-widest text-outline text-center">Estado</th>
                            <th class="py-5 px-8 text-[0.6rem] font-black uppercase tracking-widest text-outline text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/5">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-primary/5 dark:hover:bg-white/5 transition-all group">
                                <td class="py-6 px-8">
                                    <span class="px-3 py-1.5 bg-primary/10 text-primary rounded-lg text-[0.65rem] font-black tracking-widest uppercase">{{ $doc->code }}</span>
                                </td>
                                <td class="py-6 px-8">
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-[0.85rem] text-on-surface dark:text-white">{{ $doc->title }}</span>
                                            @if($doc->is_regulatory)
                                                <span class="w-2 h-2 bg-tertiary rounded-full animate-pulse shadow-sm shadow-tertiary/20" title="SISALRIL Normativo"></span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 mt-1.5">
                                            <span class="text-[0.55rem] font-bold text-outline uppercase tracking-widest bg-slate-100 dark:bg-surface-container px-2 py-0.5 rounded">{{ $doc->documentType->name }}</span>
                                            <span class="text-[0.55rem] font-bold text-outline-variant uppercase tracking-widest">v{{ $doc->versions->last()->version ?? '1.0' }}</span>
                                            <span class="material-symbols-outlined text-[0.8rem] {{ $doc->visibility == 'public' ? 'text-success' : 'text-primary' }}">{{ $doc->visibility == 'public' ? 'public' : 'lock' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-6 px-8">
                                    <span class="text-[0.65rem] font-black text-on-surface dark:text-outline-variant uppercase tracking-widest">{{ $doc->department->name }}</span>
                                </td>
                                <td class="py-6 px-8 text-center">
                                    @php $latestVersion = $doc->versions->last(); @endphp
                                    @if($doc->approval_status === 'pending')
                                        <span class="px-4 py-1.5 bg-warning/10 text-warning rounded-full text-[0.5rem] font-black uppercase tracking-widest animate-pulse border border-warning/20">Por Aprobar</span>
                                    @elseif($doc->approval_status === 'rejected')
                                         <span class="px-4 py-1.5 bg-error/10 text-error rounded-full text-[0.5rem] font-black uppercase tracking-widest border border-error/20">Rechazado</span>
                                    @else
                                        <span class="px-4 py-1.5 rounded-full text-[0.5rem] font-black uppercase tracking-widest shadow-sm {{ 
                                            $doc->status->slug === 'vigente' ? 'bg-success/10 text-success' : 
                                            ($doc->status->slug === 'pendiente' ? 'bg-warning/10 text-warning' : 'bg-error/10 text-error') 
                                        }} transition-all group-hover:px-6">
                                            {{ $doc->status->name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-6 px-8 text-right">
                                    <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all translate-x-4 group-hover:translate-x-0">
                                        @if($latestVersion)
                                            <button wire:click="openViewer('{{ $latestVersion->file_path }}')" class="w-10 h-10 rounded-xl bg-white dark:bg-surface-container-high shadow-lg flex items-center justify-center text-outline hover:text-primary transition-all">
                                                <span class="material-symbols-outlined text-lg">visibility</span>
                                            </button>
                                        @endif
                                        <button wire:click="openHistoryModal({{ $doc->id }})" class="w-10 h-10 rounded-xl bg-white dark:bg-surface-container-high shadow-lg flex items-center justify-center text-outline hover:text-primary transition-all">
                                            <span class="material-symbols-outlined text-lg">history</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-24 text-center">
                                    <img src="{{ asset('brain/895837ba-5cc8-42db-8d8c-0839e111acbc/no_documents_illustration_1775655955050.png') }}" class="w-48 mx-auto mb-8 opacity-90" alt="No results">
                                    <p class="text-[0.7rem] font-black text-outline uppercase tracking-[0.25em] italic">No se encontraron documentos maestros bajo este criterio.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($documents->hasPages())
                <div class="p-8 border-t border-outline-variant/10 bg-slate-50/30 dark:bg-white/5">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>

        <!-- Master Skeleton Loading -->
        <div wire:loading wire:target="search, filterType, filterDepartment, filterStatus, filterRegulatory" class="absolute inset-0 bg-white dark:bg-surface-container-low p-10 z-20 space-y-8">
            @for($i=0; $i<6; $i++)
                <div class="flex items-center justify-between gap-12 py-5 border-b border-outline-variant/10">
                    <div class="flex items-center gap-6 flex-1">
                        <x-skeleton type="avatar" class="w-12 h-12 rounded-2xl" />
                        <div class="flex-1 max-w-md">
                            <x-skeleton type="text" class="w-2/3 mb-3" />
                            <x-skeleton type="text" class="w-1/3" />
                        </div>
                    </div>
                    <x-skeleton type="button" class="w-24 h-10" />
                    <x-skeleton type="button" class="w-10 h-10" />
                </div>
            @endfor
        </div>
    </div>

    <!-- History & Versioning Modal -->
    @if($showHistoryModal && $selectedDocument)
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-[110] flex items-center justify-center p-6">
            <div class="bg-surface-container-lowest w-full max-w-5xl rounded-3xl p-8 shadow-2xl border border-outline-variant/10 max-h-[90vh] overflow-hidden flex flex-col relative transition-all">
                <div class="flex justify-between items-center mb-8 shrink-0">
                    <div>
                        <h3 class="text-2xl font-bold text-on-surface font-headline uppercase tracking-tight">{{ $selectedDocument->code }}</h3>
                        <p class="text-xs text-outline mt-1 font-bold tracking-widest uppercase">{{ $selectedDocument->title }}</p>
                    </div>
                    <button wire:click="$set('showHistoryModal', false)" class="w-10 h-10 rounded-full flex items-center justify-center text-outline hover:text-on-surface hover:bg-surface-container transition-all">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto grid grid-cols-12 gap-8 pr-2">
                    <!-- Horizontal Timeline Panel -->
                    <div class="col-span-12 lg:col-span-7 bg-slate-50/50 dark:bg-white/5 rounded-[2.5rem] p-8 border border-outline-variant/5">
                        <div class="flex items-center justify-between mb-8 px-4">
                            <h4 class="text-[0.625rem] font-black text-primary uppercase tracking-[0.3em]">Timeline Maestro de Ediciones</h4>
                            <span class="text-[0.5rem] font-bold text-outline uppercase tracking-widest italic">Desliza para explorar</span>
                        </div>
                        
                        <div class="flex overflow-x-auto gap-6 snap-x snap-mandatory pb-6 no-scrollbar">
                            @foreach($selectedDocument->versions->sortByDesc('created_at') as $index => $v)
                                <div class="w-72 flex-shrink-0 snap-start bg-white dark:bg-surface-container-high p-6 rounded-3xl border-2 {{ $index === 0 ? 'border-primary shadow-lg shadow-primary/10' : 'border-transparent shadow-sm' }} transition-all hover:scale-[1.02]">
                                    <div class="flex justify-between items-center mb-6">
                                        <div class="w-12 h-12 rounded-2xl bg-primary/5 dark:bg-primary/20 flex items-center justify-center text-primary font-black text-xs">
                                            v{{ $v->version }}
                                        </div>
                                        @if($v->approved_at)
                                            <span class="text-[0.5rem] font-black text-success uppercase tracking-widest flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[0.8rem]">verified</span>
                                            </span>
                                        @else
                                            <span class="text-[0.5rem] font-black text-warning uppercase tracking-widest animate-pulse">Pendiente</span>
                                        @endif
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <p class="text-[0.55rem] font-bold text-outline-variant uppercase tracking-widest">Publicado en</p>
                                            <p class="text-[0.65rem] font-black text-on-surface dark:text-white">{{ $v->created_at->format('d M, Y') }}</p>
                                        </div>

                                        @if(!$v->approved_at)
                                            <button wire:click="approveVersion({{ $v->id }})" class="w-full py-2.5 bg-primary text-white text-[0.6rem] font-black rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20 uppercase tracking-widest flex items-center justify-center gap-2">
                                                <span class="material-symbols-outlined text-sm">check_circle</span> Aprobar
                                            </button>
                                        @else
                                            <div class="flex items-center gap-3 py-2">
                                                <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-[0.5rem] font-black uppercase text-outline">
                                                    {{ substr($v->approver->name ?? 'A', 0, 1) }}
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-[0.5rem] font-black text-outline uppercase truncate">Validado por</p>
                                                    <p class="text-[0.6rem] font-bold text-on-surface dark:text-white truncate">{{ $v->approver->name ?? 'Admin' }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        <a href="{{ Storage::url($v->file_path) }}" target="_blank" class="flex items-center justify-center gap-2 w-full py-2.5 bg-slate-50 dark:bg-white/5 border border-outline-variant/10 rounded-xl text-[0.55rem] font-black text-outline hover:text-primary transition-all uppercase tracking-widest">
                                            <span class="material-symbols-outlined text-sm">picture_as_pdf</span> PDF Maestro
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Upload Sidebar -->
                    <div class="col-span-12 lg:col-span-5 lg:border-l border-outline-variant/10 lg:pl-10">
                        <div class="sticky top-0">
                            <h4 class="text-[0.625rem] font-extrabold text-primary uppercase tracking-[0.2em] mb-8 border-b border-primary/10 pb-4">Audit & New Release</h4>
                            <div class="bg-surface-container-low p-8 rounded-3xl border border-outline-variant/10 shadow-sm">
                                <form wire:submit.prevent="uploadNewVersion" class="space-y-6">
                                    <div>
                                        <label class="label-sm text-outline-variant uppercase tracking-widest block mb-2 font-black text-[0.625rem]">Nuevo Tag de Versión</label>
                                        <input wire:model="newVersionNumber" type="text" class="w-full bg-white p-4 rounded-2xl border border-outline-variant/10 focus:ring-4 focus:ring-primary/5 text-sm font-bold shadow-sm" placeholder="Ej. 2.1">
                                        @error('newVersionNumber') <span class="text-[0.6rem] text-error mt-2 font-bold block">{{ $message }}</span> @enderror
                                    </div>

                                    <div>
                                        <label class="label-sm text-outline-variant uppercase tracking-widest block mb-2 font-black text-[0.625rem]">Archivo Maestro (PDF)</label>
                                        <div class="flex items-center justify-center border-2 border-dashed border-primary/20 rounded-2xl p-8 bg-white hover:bg-primary/[0.02] transition-all cursor-pointer relative group">
                                            <input wire:model="newFile" type="file" class="absolute inset-0 opacity-0 cursor-pointer" accept=".pdf">
                                            <div class="text-center group-hover:scale-105 transition-transform">
                                                <span class="material-symbols-outlined text-4xl text-primary/40 mb-2">upload_file</span>
                                                <span class="block text-[0.6rem] font-black text-outline uppercase tracking-widest px-4 truncate max-w-[180px]">{{ $newFile ? $newFile->getClientOriginalName() : 'Soltar PDF aquí' }}</span>
                                            </div>
                                        </div>
                                        @error('newFile') <span class="text-[0.6rem] text-error mt-2 font-bold block">{{ $message }}</span> @enderror
                                    </div>

                                    <button type="submit" wire:loading.attr="disabled" class="w-full py-5 bg-primary text-white font-black text-[0.625rem] uppercase tracking-[0.15em] rounded-2xl shadow-2xl shadow-primary/30 hover:shadow-primary/40 active:scale-95 transition-all disabled:opacity-50">
                                        <span wire:loading.remove>Publicar Versión</span>
                                        <span wire:loading class="flex items-center justify-center gap-3">
                                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                            Guardando...
                                        </span>
                                    </button>
                                    
                                    <div class="p-5 bg-tertiary-fixed/30 rounded-2xl border border-tertiary/10">
                                        <p class="text-[0.6rem] text-tertiary font-bold leading-relaxed text-center italic">
                                            "Toda nueva versión generará una notificación automática a los validadores autorizados."
                                        </p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Premium Smart Side Drawer (PDF Viewer & Details) -->
    <div x-data="{ open: @entangle('showViewer') }" 
         x-show="open" 
         class="fixed inset-0 z-[200] flex justify-end overflow-hidden" 
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="open" 
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="open = false" 
             class="absolute inset-0 bg-slate-950/40 backdrop-blur-md"></div>

        <!-- Drawer Panel -->
        <div x-show="open" 
             x-transition:enter="transition-transform ease-out duration-500"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition-transform ease-in duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="relative w-full max-w-6xl bg-white dark:bg-surface-container-low shadow-[-20px_0_80px_rgba(0,0,0,0.2)] flex flex-col h-full border-l border-white/10">
            
            <div class="p-8 border-b border-outline-variant/10 flex justify-between items-center bg-slate-50/50 dark:bg-white/5">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-2xl bg-primary text-white flex items-center justify-center shadow-xl shadow-primary/20">
                        <span class="material-symbols-outlined text-3xl">description</span>
                    </div>
                    <div>
                        <p class="text-[0.6rem] font-black text-primary uppercase tracking-[0.25em]">Visor de Expediente Maestro</p>
                        <h3 class="text-xl font-black text-on-surface dark:text-white uppercase tracking-tight">ARS CMD | Gestión de Calidad</h3>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                     <a href="{{ $viewingFilePath }}" download class="px-6 py-3 bg-white dark:bg-surface-container-high text-outline hover:text-primary transition-all rounded-2xl text-[0.6rem] font-black uppercase tracking-widest flex items-center gap-3 border border-outline-variant/10 shadow-sm">
                         <span class="material-symbols-outlined text-lg">download</span> Descargar
                     </a>
                     <button @click="open = false" class="w-12 h-12 rounded-full flex items-center justify-center text-outline hover:text-on-surface hover:bg-slate-100 dark:hover:bg-white/10 transition-all">
                         <span class="material-symbols-outlined text-2xl">close</span>
                     </button>
                </div>
            </div>

            <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">
                <!-- PDF Viewer Area -->
                <div class="flex-1 bg-slate-800 relative group/viewer">
                    <iframe src="{{ $viewingFilePath }}#toolbar=0" class="w-full h-full border-none" loading="lazy"></iframe>
                    
                    <!-- Watermark -->
                    <div class="absolute inset-0 pointer-events-none flex items-center justify-center opacity-[0.03] select-none">
                        <p class="text-[8rem] font-black -rotate-45 uppercase tracking-widest">PROPIEDAD ARS CMD</p>
                    </div>
                </div>

                <!-- Fast Metadata Sidebar (Optional integration) -->
                <div class="w-full lg:w-96 bg-slate-50/30 dark:bg-white/5 border-l border-outline-variant/10 p-8 overflow-y-auto">
                    <h4 class="text-[0.625rem] font-black text-outline uppercase tracking-[0.3em] mb-8 pb-4 border-b border-outline-variant/10">Metadatos Institucionales</h4>
                    
                    <div class="space-y-8">
                        <div class="space-y-3 p-6 bg-white dark:bg-white/5 rounded-3xl border border-outline-variant/5 shadow-sm">
                            <p class="text-[0.55rem] font-black text-primary uppercase tracking-widest">Estado Actual</p>
                            <span class="inline-flex px-4 py-1.5 bg-success/10 text-success rounded-full text-[0.55rem] font-black uppercase tracking-widest border border-success/10">Vigente Autenticado</span>
                        </div>

                        <div class="space-y-3 p-6 bg-white dark:bg-white/5 rounded-3xl border border-outline-variant/5 shadow-sm">
                            <p class="text-[0.55rem] font-black text-outline uppercase tracking-widest">Información de Seguridad</p>
                            <p class="text-[0.7rem] font-bold text-on-surface dark:text-white leading-relaxed">
                                Este documento ha sido validado mediante el flujo de gobernanza institucional. El acceso a su contenido está registrado para fines de cumplimiento (Compliance).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
