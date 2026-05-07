@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#FDFDFD] pb-20" x-data="managementTray()">
    <!-- Header -->
    <div class="bg-white border-b border-slate-100 pt-12 pb-16 mb-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div>
                    <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">
                        <a href="{{ route('callcenter.worklist') }}" class="hover:text-blue-600 transition-colors">Lista de Trabajo</a>
                        <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                        <span class="text-emerald-600">Gestión de Documentos</span>
                    </nav>
                    <h1 class="text-4xl font-black text-slate-900 tracking-tight leading-none">
                        Bandeja de <span class="text-emerald-600">Validación</span>
                    </h1>
                    <p class="text-slate-500 font-medium mt-3">Confirma la recepción de cédulas para finalizar la gestión.</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="bg-emerald-50 border border-emerald-100 px-8 py-4 rounded-[28px] flex flex-col items-center">
                        <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-1">Pendientes de Cédula</span>
                        <span class="text-3xl font-black text-emerald-700 leading-none">{{ $afiliados->total() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtros Rápidos -->
        @php
            $idPendiente = $estadosCc->get('Cédula Pendiente')?->id;
            $idRecibida  = $estadosCc->get('Cédula Recibida')?->id;
        @endphp
        <div class="flex items-center gap-4 mb-8">
            <a href="{{ route('callcenter.management') }}" class="px-6 py-3 {{ !request('estado') ? 'bg-slate-900 text-white' : 'bg-white text-slate-500 border border-slate-100' }} rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Todos</a>
            <a href="{{ route('callcenter.management', ['estado' => $idPendiente]) }}" class="px-6 py-3 {{ request('estado') == $idPendiente ? 'bg-amber-500 text-white' : 'bg-white text-slate-500 border border-slate-100' }} rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Solo Pendientes</a>
            <a href="{{ route('callcenter.management', ['estado' => $idRecibida]) }}" class="px-6 py-3 {{ request('estado') == $idRecibida ? 'bg-emerald-600 text-white' : 'bg-white text-slate-500 border border-slate-100' }} rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all">Solo Recibidas</a>
        </div>

        <!-- Lista de Gestión -->
        <div class="grid grid-cols-1 gap-4">
            @forelse($afiliados as $a)
                <div id="row-{{ $a->uuid }}" class="bg-white border border-slate-100 rounded-[36px] p-7 flex flex-col lg:flex-row lg:items-center justify-between gap-10 hover:shadow-2xl hover:shadow-emerald-500/5 transition-all">
                    
                    <div class="flex items-center gap-6 flex-1">
                        <div class="w-16 h-16 rounded-[24px] {{ $a->estado->nombre == 'Cédula Recibida' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center transition-all">
                            <span class="material-symbols-outlined text-3xl">{{ $a->estado->nombre == 'Cédula Recibida' ? 'check_circle' : 'pending' }}</span>
                        </div>

                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <h3 class="text-xl font-black text-slate-900 tracking-tighter">{{ $a->nombre_completo }}</h3>
                                <span class="px-2.5 py-1 {{ $a->estado->nombre == 'Cédula Recibida' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} text-[8px] font-black uppercase rounded-lg tracking-widest">
                                    {{ $a->estado->nombre }}
                                </span>
                            </div>
                            <div class="flex items-center gap-4 text-slate-400 font-bold uppercase text-[9px] tracking-widest">
                                <span>Cédula: {{ $a->cedula }}</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                                <span>{{ $a->empresaModel->nombre ?? $a->empresa ?? 'Corporativo' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex items-center gap-3">
                        @if($a->estado->nombre == 'Cédula Pendiente')
                            <button @click="confirmStatus('{{ $a->uuid }}', 'recibida')" class="h-14 px-8 bg-emerald-600 text-white rounded-[20px] text-[10px] font-black uppercase tracking-widest shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 hover:-translate-y-1 transition-all flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">upload_file</span>
                                Marcar Recibida
                            </button>
                        @else
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-black text-emerald-600 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">verified</span>
                                    Documento Validado
                                </span>
                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1">Listo para emisión de carnet</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-40 text-center">
                    <div class="w-32 h-32 bg-slate-50 rounded-[40px] flex items-center justify-center mx-auto mb-8 text-slate-200">
                        <span class="material-symbols-outlined text-6xl">fact_check</span>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 tracking-tight">Bandeja Vacía</h3>
                    <p class="text-slate-400 font-bold uppercase tracking-widest text-[9px] mt-2">No hay documentos pendientes de validación</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $afiliados->links() }}
        </div>
    </div>
</div>

<script>
    function managementTray() {
        return {
            async confirmStatus(uuid, status) {
                const result = await Swal.fire({
                    title: '¿Confirmar Recepción?',
                    text: "El afiliado pasará a la etapa de emisión de carnet.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, recibida',
                    cancelButtonText: 'Cancelar',
                    customClass: { popup: 'rounded-[40px]' }
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/callcenter/documents/${uuid}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ status: status })
                        });
                        const data = await response.json();
                        if (data.success) {
                            const row = document.getElementById('row-' + uuid);
                            if (row) {
                                row.style.opacity = '0';
                                row.style.transform = 'scale(0.9)';
                                setTimeout(() => row.remove(), 500);
                            }
                            Swal.fire({
                                title: '¡Validado!',
                                text: 'El caso ha sido cerrado en call center y enviado a logística.',
                                icon: 'success',
                                customClass: { popup: 'rounded-[40px]' }
                            });
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo actualizar el estado', 'error');
                    }
                }
            }
        }
    }
</script>
@endsection
