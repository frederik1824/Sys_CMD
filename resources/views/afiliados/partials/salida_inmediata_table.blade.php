<div class="overflow-x-auto custom-scrollbar">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-surface-container-high dark:bg-slate-800/50">
                <th class="py-4 px-6 border-b border-slate-200 dark:border-slate-700">
                    <input id="selectAll" class="rounded text-primary focus:ring-primary border-slate-300 w-4 h-4 cursor-pointer" type="checkbox"/>
                </th>
                <th class="py-4 px-2 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">
                    <a href="javascript:void(0)" onclick="handleSort('nombre')" class="flex items-center gap-1 hover:text-primary transition-colors">
                        Afiliado @if(request('sort') === 'nombre') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                    </a>
                </th>
                <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">
                    <a href="javascript:void(0)" onclick="handleSort('contrato')" class="flex items-center gap-1 hover:text-primary transition-colors">
                        Contrato @if(request('sort') === 'contrato') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                    </a>
                </th>
                <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400">
                    <a href="javascript:void(0)" onclick="handleSort('empresa')" class="flex items-center gap-1 hover:text-primary transition-colors">
                        Empresa @if(request('sort') === 'empresa') <span class="material-symbols-outlined text-xs">{{ request('direction') === 'asc' ? 'expand_less' : 'expand_more' }}</span> @endif
                    </a>
                </th>
                <th class="py-4 px-4 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400 text-center">Estado</th>
                <th class="py-4 px-6 border-b border-slate-200 dark:border-slate-700 text-[0.6875rem] font-medium tracking-wider uppercase text-on-surface-variant dark:text-slate-400 text-right">Acciones</th>
            </tr>
        </thead>
        <tbody id="tableBody" class="divide-y divide-slate-50 dark:divide-slate-800/50">
            @forelse($afiliados as $afiliado)
            <tr class="hover:bg-slate-50/80 transition-all group border-b border-slate-100 last:border-0 dark:border-slate-800">
                <td class="py-4 px-6">
                    <input name="selected[]" value="{{ $afiliado->uuid }}" class="rounded text-primary focus:ring-primary border-slate-300 w-4 h-4 cursor-pointer affiliate-checkbox" type="checkbox"/>
                </td>
                <td class="py-4 px-2">
                    <div class="flex flex-col">
                        <a href="{{ route('carnetizacion.afiliados.show', $afiliado) }}" class="flex items-center gap-1 group/name">
                            <span class="text-sm font-bold text-on-surface group-hover/name:text-primary transition-colors">{{ $afiliado->nombre_completo }}</span>
                            @if($afiliado->sexo)
                                <span class="material-symbols-outlined text-[14px] {{ $afiliado->sexo === 'M' ? 'text-blue-500' : 'text-pink-500' }}">
                                    {{ $afiliado->sexo === 'M' ? 'male' : 'female' }}
                                </span>
                            @endif
                        </a>
                        <span class="text-[0.75rem] text-slate-500">{{ $afiliado->cedula }}</span>
                    </div>
                </td>
                <td class="py-4 px-4 text-xs font-bold text-on-surface">{{ $afiliado->contrato ?? 'N/A' }}</td>
                <td class="py-4 px-4">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-primary flex items-center gap-1">
                            {{ $afiliado->empresaModel->nombre ?? $afiliado->empresa }}
                            <span class="material-symbols-outlined text-blue-500 text-[14px]">verified_user</span>
                        </span>
                        <span class="text-[0.65rem] text-slate-400">{{ $afiliado->rnc_empresa ?? $afiliado->empresaModel?->rnc }}</span>
                    </div>
                </td>
                <td class="py-4 px-4 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[0.6875rem] font-bold border {{ $afiliado->status_color_class }} uppercase">
                        {{ $afiliado->estado->nombre ?? 'Pendiente' }}
                    </span>
                </td>
                <td class="py-4 px-6 text-right">
                    <div class="flex items-center justify-end gap-1">
                        @if(strtolower($afiliado->estado?->nombre) !== 'completado')
                        <button type="button" onclick="confirmAcuse('{{ $afiliado->uuid }}', '{{ $afiliado->nombre_completo }}')" class="p-2 text-blue-600 hover:text-blue-700 transition-colors bg-blue-50 rounded-lg" title="Marcar Acuse de Recibo">
                            <span class="material-symbols-outlined text-[1.25rem]">assignment_late</span>
                        </button>
                        @endif

                        @if($afiliado->estado_id == 7)
                        <button type="button" onclick="confirmReceipt('{{ $afiliado->uuid }}', '{{ $afiliado->nombre_completo }}')" class="p-2 text-amber-500 hover:text-amber-600 transition-colors animate-pulse" title="Confirmar Recepción Final">
                            <span class="material-symbols-outlined text-[1.25rem]">assignment_return</span>
                        </button>
                        @endif

                        <a href="{{ route('carnetizacion.afiliados.edit', $afiliado) }}" class="p-2 text-slate-400 hover:text-primary transition-colors" title="Editar"><span class="material-symbols-outlined text-[1.25rem]">edit</span></a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-12 text-center text-slate-400 italic text-sm">
                    <div class="flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-4xl">inventory_2</span>
                        No se encontraron resultados con los filtros aplicados.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-6 py-4 border-t border-slate-50 flex justify-between items-center bg-surface-container-low/30" id="pagination-container">
    {{ $afiliados->links() }}
</div>
