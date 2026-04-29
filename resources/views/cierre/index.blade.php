@extends('layouts.app')

@section('content')
<div class="p-8 space-y-8">
    <header class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-on-surface tracking-tight">Cierre de Documentos</h1>
            <p class="text-on-surface-variant text-sm mt-1 font-medium">Validación física de acuses y formularios recibidos en oficina.</p>
        </div>
        <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-400">
            <span class="material-symbols-outlined text-sm">info</span>
            Confirmar recepción de documentos físicos para completar expedientes
        </div>
    </header>

    <!-- Filtros de Búsqueda -->
    <section class="bg-surface-container-low p-6 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <form action="{{ route('cierre.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cédula / Nombre / Póliza..." class="w-full bg-white border-none rounded-xl focus:ring-2 focus:ring-primary p-3 text-sm shadow-sm pl-10">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            </div>
            
            <div class="relative">
                <select name="responsable_id" class="w-full bg-white border-none rounded-xl focus:ring-2 focus:ring-primary p-3 text-sm shadow-sm appearance-none">
                    <option value="">-- Todos los Responsables --</option>
                    @foreach($responsables as $resp)
                        <option value="{{ $resp->id }}" {{ request('responsable_id') == $resp->id ? 'selected' : '' }}>{{ $resp->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <div class="relative">
                <select name="corte_id" class="w-full bg-white border-none rounded-xl focus:ring-2 focus:ring-primary p-3 text-sm shadow-sm appearance-none">
                    <option value="">-- Todos los Cortes --</option>
                    @foreach($cortes as $c)
                        <option value="{{ $c->id }}" {{ request('corte_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-primary text-white font-bold rounded-xl hover:bg-primary-container transition-colors shadow-lg shadow-primary/10">Filtrar</button>
                <a href="{{ route('cierre.index') }}" class="p-3 bg-white text-slate-400 hover:text-rose-500 rounded-xl transition-colors border border-slate-100 flex items-center justify-center" title="Limpiar"><span class="material-symbols-outlined">backspace</span></a>
            </div>
        </form>
    </section>

    <!-- Listado de Afiliados para Cierre -->
    <div class="bg-surface-container-lowest rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-800 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="py-5 px-6 text-[0.65rem] font-black uppercase tracking-[0.1em] text-slate-400">Expediente / Afiliado</th>
                        <th class="py-5 px-4 text-[0.65rem] font-black uppercase tracking-[0.1em] text-slate-400">Responsable</th>
                        <th class="py-5 px-4 text-[0.65rem] font-black uppercase tracking-[0.1em] text-slate-400 text-center">Acuse Recibo</th>
                        <th class="py-5 px-4 text-[0.65rem] font-black uppercase tracking-[0.1em] text-slate-400 text-center">Form. Firmado</th>
                        <th class="py-5 px-6 text-[0.65rem] font-black uppercase tracking-[0.1em] text-slate-400 text-right">Aciones Rápidas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($afiliados as $af)
                        @php
                            $hasAcuse = $af->evidenciasAfiliado->where('tipo_documento', 'acuse_recibo')->where('status', 'validado')->count() > 0;
                            $hasForm  = $af->evidenciasAfiliado->where('tipo_documento', 'formulario_firmado')->where('status', 'validado')->count() > 0;
                        @endphp
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="py-5 px-6">
                                <div class="font-bold text-slate-800">{{ $af->nombre_completo }}</div>
                                <div class="text-[0.7rem] text-slate-400 font-mono tracking-tighter">{{ $af->cedula }}</div>
                            </td>
                            <td class="py-5 px-4">
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 rounded text-[0.65rem] font-black uppercase tracking-wider">{{ $af->responsable->nombre ?? 'Sin Asignar' }}</span>
                            </td>
                            <td class="py-5 px-4 text-center">
                                <div class="flex justify-center">
                                    @if($hasAcuse)
                                        <span class="material-symbols-outlined text-emerald-500 bg-emerald-50 rounded-full p-1" title="Recibido y Validado">verified</span>
                                    @else
                                        <input type="checkbox" class="doc-check w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary transition-all cursor-pointer" data-id="{{ $af->id }}" data-tipo="acuse_recibo">
                                    @endif
                                </div>
                            </td>
                            <td class="py-5 px-4 text-center">
                                <div class="flex justify-center">
                                    @if($hasForm)
                                        <span class="material-symbols-outlined text-emerald-500 bg-emerald-50 rounded-full p-1" title="Recibido y Validado">verified</span>
                                    @else
                                        <input type="checkbox" class="doc-check w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary transition-all cursor-pointer" data-id="{{ $af->id }}" data-tipo="formulario_firmado">
                                    @endif
                                </div>
                            </td>
                            <td class="py-5 px-6 text-right">
                                <button type="button" 
                                        onclick="processCierre({{ $af->id }})" 
                                        id="btn-cierre-{{ $af->id }}"
                                        class="opacity-0 group-hover:opacity-100 px-4 py-2 bg-slate-900 text-white text-[0.65rem] font-bold rounded-lg hover:bg-emerald-600 transition-all shadow-md disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed disabled:shadow-none"
                                        {{ ($hasAcuse && $hasForm) ? 'disabled' : '' }}>
                                    Confirmar Entrega
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-sm">No se encontraron expedientes pendenientes de cierre físico.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
            {{ $afiliados->links() }}
        </div>
    </div>
</div>

<script>
    function processCierre(afiliadoId) {
        const checks = document.querySelectorAll(`.doc-check[data-id="${afiliadoId}"]:checked`);
        if (checks.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Selección vacía',
                text: 'Debe marcar al menos un documento para confirmar su recepción física.',
                confirmButtonColor: '#00346f'
            });
            return;
        }

        const documentos = Array.from(checks).map(c => c.dataset.tipo);
        const btn = document.getElementById(`btn-cierre-${afiliadoId}`);
        const originalText = btn.innerText;

        btn.disabled = true;
        btn.innerText = 'Procesando...';

        fetch("{{ route('cierre.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                afiliado_id: afiliadoId,
                documentos: documentos,
                observaciones: 'Cierre físico confirmado desde el módulo de mensajería.'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Notificación mini
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
                
                // Recargar para ver los checks actualizados
                setTimeout(() => window.location.reload(), 1500);
            } else {
                Swal.fire('Error', data.error || 'Ocurrió un error al procesar.', 'error');
                btn.disabled = false;
                btn.innerText = originalText;
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerText = originalText;
        });
    }
</script>
@endsection
