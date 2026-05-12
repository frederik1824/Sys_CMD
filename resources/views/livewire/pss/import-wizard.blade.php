<div class="glass-card rounded-[2.5rem] overflow-hidden shadow-2xl border-white/50 border">
    <!-- Wizard Header -->
    <div class="bg-dark p-8 text-white relative overflow-hidden">
        <i class="ph-fill ph-file-xls absolute -right-10 -bottom-10 text-[10rem] opacity-10"></i>
        <div class="relative z-10">
            <h3 class="text-2xl font-black font-outfit uppercase tracking-tighter">Importador Inteligente de PSS</h3>
            <p class="text-white/50 text-sm font-medium mt-1">Sincroniza tu red de prestadores desde archivos Excel.</p>
            
            <!-- Progress Bar -->
            <div class="mt-8 flex items-center gap-4">
                @for ($i = 1; $i <= 4; $i++)
                <div class="flex-1 h-1.5 rounded-full {{ $step >= $i ? 'bg-primary' : 'bg-white/10' }} transition-all duration-500"></div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Step 1: Upload -->
    @if($step == 1)
    <div class="p-12 text-center space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <div class="w-24 h-24 bg-blue-50 text-primary rounded-3xl flex items-center justify-center text-4xl mx-auto shadow-inner border border-blue-100">
            <i class="ph-duotone ph-cloud-arrow-up"></i>
        </div>
        <div>
            <h4 class="text-xl font-black text-slate-800">Selecciona tu archivo Excel</h4>
            <p class="text-slate-400 font-medium text-sm mt-2 max-w-sm mx-auto leading-relaxed">
                El sistema detectará automáticamente las hojas y columnas para facilitar el mapeo.
            </p>
        </div>
        
        <div class="relative max-w-md mx-auto">
            <input type="file" wire:model="file" class="hidden" id="excel-upload">
            <label for="excel-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-[2rem] cursor-pointer hover:border-primary hover:bg-slate-50 transition-all group">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <span class="text-xs font-black text-slate-400 group-hover:text-primary transition-colors">HAZ CLIC PARA EXPLORAR</span>
                </div>
            </label>
        </div>

        <div wire:loading wire:target="file" class="text-primary font-black text-xs uppercase tracking-widest animate-pulse">
            Procesando archivo...
        </div>

        @error('file')
        <div class="mt-4 p-4 bg-rose-50 border border-rose-100 rounded-2xl">
            <p class="text-xs font-bold text-rose-600">{{ $message }}</p>
        </div>
        @enderror
    </div>
    @endif

    <!-- Step 2: Mapping -->
    @if($step == 2)
    <div class="p-10 space-y-8 animate-in fade-in slide-in-from-right-4 duration-500">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Configuración Inicial</h5>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-tighter mb-2 ml-1">Tipo de Información</label>
                            <select wire:model.live="type" class="w-full bg-white border-slate-200 rounded-xl text-sm font-bold focus:ring-primary focus:border-primary">
                                <option value="medicos">Médicos y Especialistas</option>
                                <option value="centros">Centros y Grupos PSS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-tighter mb-2 ml-1">Hoja de Excel</label>
                            <select wire:model.live="selectedSheet" wire:change="loadHeaders" class="w-full bg-white border-slate-200 rounded-xl text-sm font-bold focus:ring-primary focus:border-primary">
                                @foreach($sheets as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Mapeo de Columnas</h5>
                    <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($expectedFields[$type] as $field => $label)
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-xs font-bold text-slate-600 min-w-[120px]">{{ $label }}</span>
                            <select wire:model="mapping.{{ $field }}" class="flex-1 bg-slate-50 border-transparent rounded-lg text-[11px] font-black focus:ring-primary focus:bg-white">
                                <option value="">No importar</option>
                                @foreach($headers as $idx => $h)
                                <option value="{{ $idx }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center pt-6 border-t border-slate-50">
            <button wire:click="resetWizard" class="text-slate-400 font-bold text-sm hover:text-rose-500 transition-colors">Cancelar y Volver</button>
            <button wire:click="generatePreview" class="bg-primary text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-primary/30 hover:bg-secondary transition-all">
                Generar Previsualización
            </button>
        </div>
    </div>
    @endif

    <!-- Step 3: Preview -->
    @if($step == 3)
    <div class="p-10 space-y-8 animate-in fade-in zoom-in-95 duration-500">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-lg font-black text-slate-800">Vista Previa de Datos</h4>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Se procesarán {{ number_format($totalRows) }} registros en total.</p>
            </div>
            <div class="flex gap-2">
                <div class="px-3 py-1 bg-green-50 text-green-600 rounded-lg text-[10px] font-black uppercase tracking-tighter flex items-center gap-2">
                    <i class="ph-bold ph-check-circle"></i> Datos Normalizados
                </div>
            </div>
        </div>

        <div class="border border-slate-100 rounded-[2rem] overflow-hidden shadow-inner">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        @foreach($expectedFields[$type] as $field => $label)
                        @if(isset($mapping[$field]) && $mapping[$field] !== '')
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</th>
                        @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($previewData as $row)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        @foreach($expectedFields[$type] as $field => $label)
                        @if(isset($mapping[$field]) && $mapping[$field] !== '')
                        <td class="px-6 py-3 text-xs font-bold text-slate-600">{{ $row[$field] ?? '-' }}</td>
                        @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-center pt-6 border-t border-slate-50">
            <button wire:click="$set('step', 2)" class="text-slate-400 font-bold text-sm">Atrás</button>
            <button wire:click="processImport" class="bg-green-600 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-green-600/30 hover:bg-green-700 transition-all flex items-center gap-3">
                @if($isImporting)
                <i class="ph-bold ph-spinner animate-spin text-lg"></i> Procesando...
                @else
                <i class="ph-bold ph-rocket-launch text-lg"></i> Iniciar Importación Final
                @endif
            </button>
        </div>
    </div>
    @endif

    <!-- Step 4: Finished -->
    @if($step == 4)
    <div class="p-12 text-center space-y-8 animate-in fade-in zoom-in-95 duration-700">
        <div class="w-24 h-24 bg-green-50 text-green-600 rounded-full flex items-center justify-center text-5xl mx-auto shadow-inner border border-green-100">
            <i class="ph-fill ph-check-circle"></i>
        </div>
        <div>
            <h4 class="text-2xl font-black text-slate-800 tracking-tighter">¡Importación Exitosa!</h4>
            <p class="text-slate-400 font-medium text-sm mt-2 max-w-sm mx-auto leading-relaxed">
                Los datos han sido normalizados y cargados correctamente en la base de datos de PSS.
            </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-2xl mx-auto">
            <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                <span class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest">Éxito</span>
                <span class="text-2xl font-black text-emerald-700">{{ number_format($stats['procesados']) }}</span>
            </div>
            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-100">
                <span class="block text-[10px] font-black text-amber-600 uppercase tracking-widest">Duplicados</span>
                <span class="text-2xl font-black text-amber-700">{{ number_format($stats['duplicados']) }}</span>
            </div>
            <div class="bg-rose-50 p-4 rounded-2xl border border-rose-100">
                <span class="block text-[10px] font-black text-rose-600 uppercase tracking-widest">Errores</span>
                <span class="text-2xl font-black text-rose-700">{{ number_format($stats['errores']) }}</span>
            </div>
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100">
                <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Omitidos</span>
                <span class="text-2xl font-black text-slate-600">{{ number_format($stats['omitidos']) }}</span>
            </div>
        </div>

        <div class="pt-6">
            <button wire:click="resetWizard" class="bg-primary text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-secondary transition-all">
                Finalizar y Cerrar
            </button>
        </div>
    </div>
    @endif
</div>