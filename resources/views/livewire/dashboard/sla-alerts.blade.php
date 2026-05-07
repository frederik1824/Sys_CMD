<div class="space-y-6">
    <!-- Header con Controles -->
    <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        <div>
            <h3 class="text-xl font-extrabold text-slate-800 tracking-tight">Centro de Alertas SLA</h3>
            <p class="text-xs text-slate-500 font-medium">Gestión dinámica de inactividad operativa.</p>
        </div>

        <div class="flex items-center gap-4">
            <!-- Card Crítico -->
            <div wire:click="$set('level', 'critical')" class="cursor-pointer group flex items-center gap-4 px-6 py-4 bg-white border border-rose-100 rounded-2xl shadow-sm hover:shadow-md hover:bg-rose-50 transition-all {{ $level === 'critical' ? 'ring-2 ring-rose-500 bg-rose-50' : '' }}">
                <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center shadow-lg shadow-rose-200">
                    <span class="material-symbols-outlined">error</span>
                </div>
                <div>
                    <p class="text-[0.6rem] font-black text-rose-400 uppercase tracking-widest">Nivel Crítico</p>
                    <h4 class="text-xl font-black text-rose-600">{{ $totalCritical }} <span class="text-xs font-bold text-slate-400">EMPRESAS</span></h4>
                </div>
            </div>

            <!-- Card Advertencia -->
            <div wire:click="$set('level', 'warning')" class="cursor-pointer group flex items-center gap-4 px-6 py-4 bg-white border border-amber-100 rounded-2xl shadow-sm hover:shadow-md hover:bg-amber-50 transition-all {{ $level === 'warning' ? 'ring-2 ring-amber-500 bg-amber-50' : '' }}">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-lg shadow-amber-200">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div>
                    <p class="text-[0.6rem] font-black text-amber-400 uppercase tracking-widest">Advertencia</p>
                    <h4 class="text-xl font-black text-amber-600">{{ $totalWarning }} <span class="text-xs font-bold text-slate-400">EMPRESAS</span></h4>
                </div>
            </div>
            
            @if($level)
            <button wire:click="$set('level', '')" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center hover:bg-slate-200 transition-colors" title="Limpiar Filtro">
                <span class="material-symbols-outlined">close</span>
            </button>
            @endif
        </div>
        
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <!-- Buscador -->
            <div class="relative flex-1 lg:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                <input wire:model.live="search" type="text" placeholder="Buscar empresa o RNC..." 
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary transition-all">
            </div>

            <!-- Filtro de Nivel -->
            <select wire:model.live="level" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 outline-none focus:ring-2 focus:ring-primary/10 focus:border-primary">
                <option value="">Todos los niveles</option>
                <option value="critical">Crítico (15+ días)</option>
                <option value="warning">Advertencia (7-14 días)</option>
            </select>

            <!-- Per Page -->
            <select wire:model.live="perPage" class="px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
        </div>
    </div>

    <!-- Tabla de Alertas -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4">
                            <button wire:click="sortBy('nombre')" class="flex items-center gap-1 text-[0.65rem] font-black text-slate-400 uppercase tracking-widest hover:text-primary transition-colors">
                                Empresa
                                @if($sortField === 'nombre')
                                    <span class="material-symbols-outlined text-xs">{{ $sortDirection === 'asc' ? 'expand_less' : 'expand_more' }}</span>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <span class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Promotor Asignado</span>
                        </th>
                        <th class="px-6 py-4">
                            <span class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Última Actividad</span>
                        </th>
                        <th class="px-6 py-4">
                            <button wire:click="sortBy('days')" class="flex items-center gap-1 text-[0.65rem] font-black text-slate-400 uppercase tracking-widest hover:text-primary transition-colors">
                                Días Inactivos
                                @if($sortField === 'days')
                                    <span class="material-symbols-outlined text-xs">{{ $sortDirection === 'asc' ? 'expand_less' : 'expand_more' }}</span>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4">
                            <span class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">SLA Status</span>
                        </th>
                        <th class="px-6 py-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($empresas as $empresa)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-800">{{ $empresa->nombre }}</span>
                                <span class="text-[0.6rem] text-slate-400 font-bold uppercase tracking-tighter">{{ $empresa->rnc }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400 border border-white shadow-sm shrink-0">
                                    {{ strtoupper(substr($empresa->promotor->name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-xs font-bold text-slate-600">{{ $empresa->promotor->name ?? 'Sin asignar' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-slate-500">
                            {{ $empresa->ultima_fecha ? \Carbon\Carbon::parse($empresa->ultima_fecha)->format('d M, Y') : 'Sin registros' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-black {{ ($empresa->days_inactive >= 15 || is_null($empresa->days_inactive)) ? 'text-rose-600' : ($empresa->days_inactive >= 7 ? 'text-amber-600' : 'text-emerald-600') }}">
                                {{ $empresa->days_inactive ?? '∞' }} <span class="text-[10px] font-bold text-slate-400">días</span>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-widest border 
                                {{ ($empresa->days_inactive >= 15 || is_null($empresa->days_inactive)) ? 'bg-rose-50 text-rose-600 border-rose-100' : ($empresa->days_inactive >= 7 ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100') }}">
                                {{ $empresa->sla_status->level === 'critical' ? 'Crítico' : ($empresa->sla_status->level === 'warning' ? 'Advertencia' : 'Al día') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('sistema.empresas.show', $empresa) }}" class="p-2 text-slate-300 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined">chevron_right</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <span class="material-symbols-outlined text-slate-100 text-6xl">search_off</span>
                                <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">No se encontraron alertas en este nivel</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($empresas->hasPages())
        <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/30">
            {{ $empresas->links() }}
        </div>
        @endif
    </div>
</div>
