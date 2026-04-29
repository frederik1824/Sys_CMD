@extends('layouts.app')

@section('content')
<div class="p-8" x-data="prospectingHandler()">
    <div class="max-w-6xl mx-auto space-y-8">
        <header class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Bandeja de Prospección</h1>
                <p class="text-slate-500 text-sm font-medium uppercase tracking-widest">Investigación de Empresas y Captura de Cédulas</p>
            </div>
            <a href="{{ route('callcenter.reception') }}" class="px-6 py-3 bg-blue-600 text-white rounded-2xl text-xs font-black uppercase hover:bg-blue-700 shadow-lg shadow-blue-500/20 transition-all">Recibir Nuevo Listado</a>
        </header>

        @if($prospectos->isEmpty())
            <div class="bg-white p-20 rounded-[40px] text-center border border-slate-100 shadow-xl">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="ph ph-magnifying-glass text-4xl text-slate-300"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-400 uppercase tracking-widest">No tienes prospectos asignados</h3>
                <p class="text-slate-400 text-sm mt-2">Usa el botón de "Recibir Nuevo Listado" para cargar datos desde Excel.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($prospectos as $rnc => $afiliados)
                    @php $first = $afiliados->first(); @endphp
                    <div class="bg-white rounded-[32px] shadow-xl border border-slate-100 overflow-hidden group hover:border-blue-200 transition-all">
                        <!-- Header de Empresa -->
                        <div class="p-8 bg-slate-50/50 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex items-center gap-6">
                                <div class="w-14 h-14 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center text-blue-600">
                                    <i class="ph ph-buildings text-3xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight">{{ $first->empresa }}</h3>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] font-mono text-slate-400 bg-white px-2 py-0.5 rounded border border-slate-100">RNC: {{ $rnc }}</span>
                                        <span class="text-[10px] font-bold text-blue-600 uppercase">{{ $afiliados->count() }} Afiliados</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="https://www.google.com/search?q={{ urlencode($first->empresa . ' ' . $rnc . ' contacto republica dominicana') }}" 
                                   target="_blank"
                                   class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase hover:bg-blue-50 hover:text-blue-600 transition-all flex items-center gap-2">
                                    <i class="ph ph-google-logo text-lg"></i>
                                    Investigar Empresa
                                </a>
                                <button @click="openCompanyNote('{{ $rnc }}', '{{ $first->empresa }}')"
                                        class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-50 hover:text-indigo-600 transition-all flex items-center gap-2">
                                    <i class="ph ph-phone text-lg"></i>
                                    Guardar Contacto
                                </button>
                            </div>
                        </div>

                        <!-- Lista de Afiliados -->
                        <div class="p-4 bg-white">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-50">
                                        <th class="pb-3 px-4">Nombre del Afiliado</th>
                                        <th class="pb-3 px-4 w-64">Cédula (Formato: 000-0000000-0)</th>
                                        <th class="pb-3 px-4 text-right">Acción</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($afiliados as $af)
                                        <tr class="hover:bg-slate-50/30 transition-colors">
                                            <td class="py-4 px-4 font-bold text-slate-700">{{ $af->nombre_completo }}</td>
                                            <td class="py-4 px-4">
                                                <input type="text" 
                                                       x-mask="999-9999999-9"
                                                       placeholder="000-0000000-0"
                                                       value="{{ $af->cedula }}"
                                                       @keyup.enter="promote('{{ $af->uuid }}', $event.target.value)"
                                                       class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2 text-sm font-mono focus:ring-blue-500 focus:border-blue-500 transition-all">
                                            </td>
                                            <td class="py-4 px-4 text-right">
                                                <button @click="promote('{{ $af->uuid }}', $el.closest('tr').querySelector('input').value)"
                                                        class="p-2 bg-emerald-100 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all">
                                                    <i class="ph ph-check-bold"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal para Guardar Contacto de Empresa (Quick Info) -->
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
        <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-md p-8">
            <h3 class="text-xl font-black text-slate-800 uppercase tracking-tight mb-2" x-text="modalEmpresa"></h3>
            <p class="text-slate-500 text-xs mb-6 uppercase tracking-widest font-bold">Guardar Información de Contacto</p>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Teléfono Encontrado</label>
                    <input type="text" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-blue-500 transition-all" placeholder="Eje: 809-555-0000">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Extensión / Contacto</label>
                    <input type="text" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-blue-500 transition-all" placeholder="Eje: Ext 201 - Maria Perez">
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <button @click="showModal = false" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-2xl font-black uppercase text-[10px]">Cancelar</button>
                <button @click="showModal = false" class="flex-1 py-3 bg-blue-600 text-white rounded-2xl font-black uppercase text-[10px]">Guardar Info</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js" defer></script>
<script>
function prospectingHandler() {
    return {
        showModal: false,
        modalEmpresa: '',
        modalRnc: '',

        openCompanyNote(rnc, nombre) {
            this.modalRnc = rnc;
            this.modalEmpresa = nombre;
            this.showModal = true;
        },

        async promote(uuid, cedula) {
            if (!cedula || cedula.length < 13) {
                alert("Por favor ingresa una cédula válida");
                return;
            }

            try {
                const response = await fetch(`/admin/callcenter/prospecting/promote/${uuid}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ cedula: cedula })
                });
                
                const result = await response.json();
                if (result.success) {
                    // Animación de éxito y recarga
                    location.reload();
                } else {
                    alert(result.message || "Error al procesar");
                }
            } catch (error) {
                alert("Error de conexión");
            }
        }
    }
}
</script>
@endsection
