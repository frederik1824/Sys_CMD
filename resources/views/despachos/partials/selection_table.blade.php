<div class="overflow-x-auto min-h-[400px]">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50/50">
                <th class="w-16 px-8 py-5">
                    <span class="material-symbols-outlined text-slate-300">check_box_outline_blank</span>
                </th>
                <th class="px-4 py-5 text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Afiliado</th>
                <th class="px-4 py-5 text-[0.65rem] font-black text-slate-400 uppercase tracking-widest">Empresa / Ubicación</th>
                <th class="px-4 py-5 text-right text-[0.65rem] font-black text-slate-400 uppercase tracking-widest pr-8">Prioridad</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($afiliados as $afiliado)
                <tr class="affiliate-row hover:bg-primary/5 transition-colors cursor-pointer group" 
                    @click="toggleItem('{{ $afiliado->id }}')">
                    <td class="px-8 py-6">
                        <input type="checkbox" :checked="selectedItems.has('{{ $afiliado->id }}')"
                               class="w-5 h-5 rounded-lg border-2 border-slate-200 text-primary focus:ring-primary/20 cursor-pointer pointer-events-none transition-all">
                    </td>
                    <td class="px-4 py-6">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-800 leading-tight">{{ $afiliado->nombre_completo }}</span>
                            <span class="text-[0.65rem] font-medium text-slate-400 mt-1">Cédula: {{ $afiliado->cedula }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-6">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-600 truncate max-w-[200px]">{{ $afiliado->empresaModel->nombre ?? 'Empresa Genérica' }}</span>
                            <div class="flex items-center gap-1.5 mt-1">
                                <span class="material-symbols-outlined text-[12px] text-slate-300">location_on</span>
                                <span class="text-[0.65rem] font-bold text-slate-400">{{ $afiliado->municipio ?? 'N/A' }}, {{ $afiliado->provincia ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-6 text-right pr-8">
                        @if($afiliado->empresaModel?->es_verificada)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 text-blue-600 text-[0.6rem] font-black uppercase rounded shadow-sm border border-blue-100">
                                <span class="material-symbols-outlined text-[12px]">verified_user</span>
                                Alta
                            </span>
                        @else
                            <span class="text-[0.6rem] font-black uppercase text-slate-300">Normal</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="py-20 text-center">
                        <span class="material-symbols-outlined text-slate-200 text-6xl mb-4">search_off</span>
                        <p class="text-slate-400 font-medium">No se encontraron afiliados pendientes.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($afiliados->hasPages())
    <div class="px-8 py-4 border-t border-slate-50 bg-slate-50/30" id="selection-pagination">
        {{ $afiliados->links('pagination::tailwind') }}
    </div>
@endif
