<div class="space-y-10">
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-10">
        {{-- COLUMNA PRINCIPAL: TABLA DISPERSIÓN (70%) --}}
        <div class="xl:col-span-8 space-y-8">
            <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
                <div class="px-8 py-6 border-b border-slate-100 bg-emerald-50/30 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-500/10 rounded-xl">
                            <i class="ph ph-arrows-merge text-xl text-emerald-600"></i>
                        </div>
                        <h4 class="text-xs font-black uppercase tracking-[0.3em] text-slate-900">REPORTE DISPERSIÓN</h4>
                    </div>
                    @if(session()->has('message'))
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 animate-pulse bg-emerald-100 px-3 py-1 rounded-full">
                            {{ session('message') }}
                        </span>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                <th class="px-8 py-5">INDICADOR DE DISPERSIÓN</th>
                                <th class="px-8 py-5 text-right">CANTIDAD</th>
                                <th class="px-8 py-5 text-right">MONTO (RD$)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($indicators as $indicator)
                            @php 
                                $isMonto = $indicator->category === 'Montos'; 
                                // Filtrar indicadores específicos de corte
                                if (str_contains($indicator->name, '1er Corte') && $corteNumber == 2) continue;
                                if (str_contains($indicator->name, '2do Corte') && $corteNumber == 1) continue;

                                // Excluir redundantes solicitados por el usuario
                                if (str_contains($indicator->name, 'Pérdida de Empleo') || str_contains($indicator->name, 'Separación o Divorcio')) continue;
                            @endphp
                            <tr class="{{ $indicator->is_total ? 'bg-sky-50/60 font-black border-l-4 border-l-sky-500' : 'hover:bg-slate-50/50' }} transition-colors">
                                <td class="px-8 py-4">
                                    <span class="text-xs {{ $indicator->is_total ? 'text-sky-900' : 'text-slate-600' }}">
                                        {{ $indicator->name }}
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    @if(!$isMonto)
                                        <input type="number" 
                                               wire:model.live="values.id_{{ $indicator->id }}.quantity"
                                               {{ $indicator->is_total || $isClosed ? 'disabled' : '' }}
                                               class="w-full text-right bg-transparent border-none focus:ring-0 p-0 text-sm font-black tabular-nums {{ $indicator->is_total ? 'text-sky-900' : 'text-slate-900' }} placeholder-slate-200"
                                               placeholder="0">
                                    @else
                                        <span class="block w-full text-right text-slate-300">---</span>
                                    @endif
                                </td>
                                <td class="px-8 py-4">
                                    @if($isMonto)
                                        <input type="number" step="0.01"
                                               wire:model.live="values.id_{{ $indicator->id }}.amount"
                                               {{ $indicator->is_total || $isClosed ? 'disabled' : '' }}
                                               class="w-full text-right bg-transparent border-none focus:ring-0 p-0 text-sm font-black tabular-nums {{ $indicator->is_total ? 'text-sky-900' : 'text-slate-900' }} placeholder-slate-200"
                                               placeholder="0.00">
                                    @else
                                        <span class="block w-full text-right text-slate-300">---</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- COLUMNA LATERAL: BAJAS Y CONTROL (30%) --}}
        <div class="xl:col-span-4 space-y-8">
            {{-- TABLA BAJAS --}}
            <div class="bg-white border border-slate-200 rounded-[2.5rem] overflow-hidden shadow-sm">
                <div class="px-8 py-6 border-b border-slate-100 bg-rose-50/30 flex items-center gap-3">
                    <div class="p-2 bg-rose-500/10 rounded-xl">
                        <i class="ph ph-user-minus text-xl text-rose-600"></i>
                    </div>
                    <h4 class="text-xs font-black uppercase tracking-[0.3em] text-slate-900">BAJAS DEL CORTE</h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-widest text-slate-400 border-b border-slate-100">
                                <th class="px-8 py-5">TIPO DE BAJA</th>
                                <th class="px-8 py-5 text-right">CANTIDAD</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($bajaTypes as $type)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-8 py-4 text-xs text-slate-600 font-medium">
                                    {{ $type->name }}
                                </td>
                                <td class="px-8 py-4">
                                    <input type="number" 
                                           wire:model.live="bajaValues.id_{{ $type->id }}.quantity"
                                           {{ $isClosed ? 'disabled' : '' }}
                                           class="w-full text-right bg-transparent border-none focus:ring-0 p-0 text-sm font-black tabular-nums text-slate-900 placeholder-slate-200"
                                           placeholder="0">
                                </td>
                            </tr>
                            @endforeach
                            <tr class="bg-rose-50/40 font-black">
                                <td class="px-8 py-5 text-xs text-rose-900 uppercase tracking-widest">TOTAL BAJAS</td>
                                <td class="px-8 py-5 text-right text-sm text-rose-900 tabular-nums">
                                    {{ number_format($totals['bajas'] ?? 0) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PANEL DE CONTROL DE CORTE --}}
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white space-y-6 shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
                
                <div class="flex items-center justify-between relative z-10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.4em] text-emerald-400">Estado del Registro</h4>
                    <div class="h-2 w-2 rounded-full {{ $isClosed ? 'bg-slate-500' : 'bg-emerald-400 animate-pulse' }}"></div>
                </div>

                @if(session()->has('error'))
                    <div class="p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl text-rose-400 text-[10px] font-bold relative z-10">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 relative z-10">
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 text-center block">Fecha Recepción</label>
                        <input type="date" wire:model="receptionDate" {{ $isClosed ? 'disabled' : '' }}
                               class="w-full bg-slate-800 border-none rounded-xl px-4 py-2 text-xs font-bold focus:ring-1 focus:ring-emerald-500 transition-all text-white">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[9px] font-black uppercase tracking-widest text-slate-400 text-center block">Modo</label>
                        <div class="w-full bg-slate-800 rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest text-emerald-400 flex items-center justify-center gap-2">
                            <i class="ph ph-lightning-fill"></i>
                            Auto-Save
                        </div>
                    </div>
                </div>

                <div class="space-y-1.5 relative z-10">
                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Observaciones</label>
                    <textarea wire:model="notes" {{ $isClosed ? 'disabled' : '' }}
                              rows="2" 
                              placeholder="Notas institucionales..."
                              class="w-full bg-slate-800 border-none rounded-2xl px-4 py-3 text-xs font-bold focus:ring-1 focus:ring-emerald-500 transition-all placeholder:text-slate-600 text-white"></textarea>
                </div>

                {{-- IMPORTADOR EXCEL --}}
                <div class="space-y-3 pt-4 border-t border-slate-800 relative z-10">
                    <label class="text-[9px] font-black uppercase tracking-widest text-slate-400">Importar desde Excel</label>
                    <div class="relative group/upload">
                        <input type="file" wire:model="excelFile" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                               accept=".xlsx,.xls">
                        <div class="w-full bg-slate-800 border-2 border-dashed border-slate-700 rounded-2xl px-4 py-6 flex flex-col items-center gap-2 group-hover/upload:border-emerald-500/50 transition-all">
                            <i class="ph ph-file-xls text-3xl {{ $excelFile ? 'text-emerald-400' : 'text-slate-600' }}"></i>
                            <span class="text-[9px] font-black uppercase tracking-widest text-center px-4 {{ $excelFile ? 'text-white' : 'text-slate-500' }}">
                                {{ $excelFile ? $excelFile->getClientOriginalName() : 'Arrastre archivo Excel aquí' }}
                            </span>
                        </div>
                    </div>
                    @if($excelFile)
                        <button wire:click="importExcel" 
                                wire:loading.attr="disabled"
                                class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-black text-[9px] uppercase tracking-widest transition-all flex items-center justify-center gap-2">
                            <i class="ph ph-lightning" wire:loading.remove></i>
                            <i class="ph ph-circle-notch animate-spin" wire:loading></i>
                            <span wire:loading.remove>Procesar e Importar</span>
                            <span wire:loading>Procesando...</span>
                        </button>
                    @endif
                </div>

                @if(!$isClosed)
                <button wire:click="save" 
                        wire:loading.attr="disabled"
                        class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-slate-900 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] transition-all flex items-center justify-center gap-3 shadow-lg shadow-emerald-500/20 disabled:opacity-50 disabled:cursor-not-allowed relative z-10">
                    <i class="ph ph-floppy-disk text-lg" wire:loading.remove></i>
                    <i class="ph ph-circle-notch text-lg animate-spin" wire:loading></i>
                    <span wire:loading.remove>GUARDAR CAMBIOS</span>
                    <span wire:loading>PROCESANDO...</span>
                </button>
                @else
                <div class="w-full py-4 bg-slate-800 text-slate-500 rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] text-center border border-slate-700 relative z-10">
                    REGISTRO CERRADO
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
