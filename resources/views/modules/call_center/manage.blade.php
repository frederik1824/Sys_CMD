@extends('layouts.app')

@section('content')
<div class="p-4 lg:p-8 space-y-8 animate-in fade-in duration-700 bg-slate-50/50 min-h-screen" x-data="managementConsole()">
    <!-- Header Minimalista y Elegante -->
    <div class="flex items-center justify-between bg-white px-8 py-6 rounded-[32px] border border-slate-100 shadow-sm">
        <div class="flex items-center gap-6">
            <a href="{{ route('call-center.worklist') }}" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-slate-50 text-slate-400 hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                <i class="ph-bold ph-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $registro->nombre }}</h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Gestión de Prospecto • {{ $registro->cedula }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="px-6 py-2 rounded-2xl border font-black uppercase text-[10px] tracking-widest"
                 style="background-color: {{ $registro->estado->color }}10; color: {{ $registro->estado->color }}; border-color: {{ $registro->estado->color }}30;">
                <i class="ph-bold ph-{{ $registro->estado->icono ?: 'info' }} mr-2"></i>
                {{ $registro->estado->nombre }}
            </div>
            
            @if($registro->estado->nombre == 'Enviado a carnetización')
                <button type="button" disabled class="bg-slate-100 text-slate-400 px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest flex items-center gap-3 cursor-not-allowed">
                    <i class="ph-bold ph-check-circle text-lg text-emerald-500"></i>
                    Ya Promovido
                </button>
            @else
                <button type="button" onclick="confirmPromote()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-black uppercase text-[10px] tracking-widest transition-all shadow-lg shadow-emerald-500/20 active:scale-95 flex items-center gap-3">
                    <i class="ph-bold ph-rocket-launch text-lg"></i>
                    Promover
                </button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">
        
        <!-- COLUMNA IZQUIERDA: Perfil y Acciones Rápidas -->
        <div class="xl:col-span-3 space-y-8">
            <!-- Card de Perfil -->
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-xl shadow-slate-200/40 p-8 space-y-6">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-24 h-24 bg-slate-100 rounded-[32px] flex items-center justify-center text-slate-400 relative">
                        <i class="ph ph-user text-5xl"></i>
                        <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 text-white rounded-2xl flex items-center justify-center border-4 border-white shadow-lg">
                            <i class="ph-bold ph-check"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 tracking-tight">{{ $registro->nombre }}</h3>
                        <p class="text-xs font-bold text-slate-400">ID: {{ $registro->uuid }}</p>
                    </div>
                </div>

                <div class="space-y-4 pt-4 border-t border-slate-50">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-black text-slate-400 uppercase tracking-widest">Póliza</span>
                        <span class="font-black text-slate-700">{{ $registro->poliza ?: 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-black text-slate-400 uppercase tracking-widest">Empresa</span>
                        <span class="font-black text-indigo-600 truncate max-w-[150px] text-right">{{ $registro->empresa_nombre ?? 'Independiente' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-black text-slate-400 uppercase tracking-widest">Intentos</span>
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg font-black">{{ $registro->intentos_llamada }}</span>
                    </div>
                </div>

                <!-- Quick Actions Grid -->
                <div class="grid grid-cols-1 gap-3 pt-4">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest text-center mb-1">Acciones de Un Clic</p>
                    <button @click="quickAction('No contesta')" 
                            {{ $registro->estado->nombre == 'Enviado a carnetización' ? 'disabled' : '' }}
                            class="{{ $registro->estado->nombre == 'Enviado a carnetización' ? 'opacity-50 cursor-not-allowed' : '' }} w-full py-3 bg-rose-50 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center gap-2 border border-rose-100">
                        <i class="ph-bold ph-phone-slash text-base"></i> No contestó
                    </button>
                    <button @click="quickAction('Ocupado')" 
                            {{ $registro->estado->nombre == 'Enviado a carnetización' ? 'disabled' : '' }}
                            class="{{ $registro->estado->nombre == 'Enviado a carnetización' ? 'opacity-50 cursor-not-allowed' : '' }} w-full py-3 bg-amber-50 text-amber-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 hover:text-white transition-all flex items-center justify-center gap-2 border border-amber-100">
                        <i class="ph-bold ph-hourglass text-base"></i> Ocupado / Llamar luego
                    </button>
                </div>
            </div>

            <!-- Datos de Contacto Directo -->
            <div class="bg-slate-900 rounded-[32px] p-8 text-white space-y-6 shadow-2xl shadow-slate-900/20">
                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Canales de Contacto</h4>
                <div class="space-y-4">
                    <div class="group flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10 hover:bg-white/10 transition-all cursor-pointer" onclick="copyToClipboard('{{ $registro->telefono }}')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-500/20 text-indigo-400 rounded-xl flex items-center justify-center">
                                <i class="ph-bold ph-phone text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Teléfono</p>
                                <p class="text-sm font-black">{{ $registro->telefono }}</p>
                            </div>
                        </div>
                        <i class="ph ph-copy text-slate-600 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </div>
                    @if($registro->celular)
                    <div class="group flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10 hover:bg-white/10 transition-all cursor-pointer" onclick="copyToClipboard('{{ $registro->celular }}')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-500/20 text-emerald-400 rounded-xl flex items-center justify-center">
                                <i class="ph-bold ph-device-mobile text-xl"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Celular</p>
                                <p class="text-sm font-black">{{ $registro->celular }}</p>
                            </div>
                        </div>
                        <i class="ph ph-copy text-slate-600 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    </div>
                    @endif
                </div>
                <button class="w-full py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.1em] transition-all flex items-center justify-center gap-3 shadow-xl shadow-emerald-500/20">
                    <i class="ph-bold ph-whatsapp-logo text-2xl"></i>
                    Contactar por WhatsApp
                </button>
            </div>
        </div>

        <!-- COLUMNA CENTRAL: Gestión y Timeline -->
        <div class="xl:col-span-6 space-y-8">
            <!-- Formulario de Gestión -->
            <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl shadow-slate-200/40 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                            <i class="ph-bold ph-note-pencil text-xl"></i>
                        </div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Nueva Interacción</h2>
                    </div>
                </div>

                <div class="p-8">
                    <form id="gestionForm" action="{{ route('call-center.gestion.store', $registro->uuid) }}" method="POST" class="space-y-8">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Cambiar Estado</label>
                                <select name="estado_nuevo_id" id="form_estado_id" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                    @foreach($estados as $est)
                                        <option value="{{ $est->id }}" {{ $registro->estado_id == $est->id ? 'selected' : '' }}>
                                            {{ $est->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Resultado del Contacto</label>
                                <input type="text" name="resultado_contacto" id="form_resultado" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all" 
                                       placeholder="Resumen rápido de la llamada" required>
                            </div>
                            
                            <!-- Campos de Enriquecimiento (Compactos) -->
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/50 p-6 rounded-[32px] border border-dashed border-slate-200">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Celular Actualizado</label>
                                    <input type="text" name="celular_contactado" value="{{ $registro->celular }}" class="w-full bg-white border-slate-100 rounded-xl py-3 px-5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Dirección Actualizada</label>
                                    <input type="text" name="direccion_contactada" value="{{ $registro->empresa_direccion }}" class="w-full bg-white border-slate-100 rounded-xl py-3 px-5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 transition-all">
                                </div>
                                @if($registro->empresa_id)
                                <div class="md:col-span-2 flex items-center gap-3 mt-2">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="actualizar_empresa" value="1" class="sr-only peer">
                                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        <span class="ml-3 text-[10px] font-black text-slate-500 uppercase tracking-tight">Sincronizar cambios con la Empresa Maestra</span>
                                    </label>
                                </div>
                                @endif
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Persona Contactada</label>
                                <input type="text" name="persona_contactada" value="{{ $registro->nombre }}" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Próximo Seguimiento</label>
                                <input type="date" name="fecha_proximo_contacto" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700">
                            </div>
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Observaciones Detalladas</label>
                                <textarea name="observacion" rows="3" class="w-full bg-slate-50 border-slate-100 rounded-2xl py-4 px-6 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all"></textarea>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            @if($registro->estado->nombre == 'Enviado a carnetización')
                                <div class="bg-emerald-50 text-emerald-600 px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest flex items-center gap-3 border border-emerald-100">
                                    <i class="ph-bold ph-check-circle text-lg"></i>
                                    Gestión Finalizada
                                </div>
                            @else
                                <button type="submit" class="bg-slate-900 hover:bg-black text-white px-12 py-5 rounded-[24px] font-black uppercase text-xs tracking-[0.2em] transition-all shadow-2xl active:scale-95 flex items-center gap-3">
                                    <i class="ph-bold ph-floppy-disk text-xl"></i>
                                    Guardar Gestión
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Timeline Compacta -->
            <div class="space-y-6">
                <div class="flex items-center justify-between px-4">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-3">
                        <i class="ph-bold ph-clock-counter-clockwise text-indigo-500"></i>
                        Historial Reciente
                    </h2>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $registro->gestiones->count() }} Interacciones</span>
                </div>

                <div class="space-y-4">
                    @forelse($registro->gestiones->sortByDesc('created_at') as $gestion)
                    <div class="bg-white p-6 rounded-[28px] border border-slate-100 shadow-sm hover:shadow-md transition-all">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full border-2 border-slate-50 shadow-sm overflow-hidden">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($gestion->operador->name) }}&background=0f172a&color=fff" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-900">{{ $gestion->resultado_contacto }}</p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase">{{ $gestion->operador->name }} • {{ $gestion->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-1 bg-slate-50 rounded-lg border border-slate-100">
                                <span class="text-[9px] font-black text-emerald-600 uppercase">{{ $gestion->estadoNuevo->nombre }}</span>
                            </div>
                        </div>
                        @if($gestion->observacion)
                        <p class="text-[11px] text-slate-500 italic bg-slate-50/50 p-3 rounded-xl border border-dashed border-slate-100">
                            "{{ $gestion->observacion }}"
                        </p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-12 bg-white rounded-[40px] border border-dashed border-slate-200">
                        <p class="text-slate-400 font-bold text-sm">Sin gestiones previas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Apoyo y Documentos -->
        <div class="xl:col-span-3 space-y-8">
            <!-- Scripts (Sticky) -->
            <div class="sticky top-8 space-y-8">
                @include('modules.call_center.partials.scripts')

                <!-- Gestión Documental -->
                <div class="bg-white rounded-[32px] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="p-6 bg-amber-50/30 border-b border-amber-100 flex items-center gap-3">
                        <i class="ph-bold ph-files text-amber-600 text-xl"></i>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Expediente</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach(['Copia de cédula', 'Formulario firmado', 'Evidencia'] as $doc)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i class="ph ph-file-text text-slate-400 text-lg"></i>
                                <span class="text-[10px] font-black text-slate-600 uppercase">{{ $doc }}</span>
                            </div>
                            <div class="w-3 h-3 rounded-full bg-slate-300"></div>
                        </div>
                        @endforeach
                        <button class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                            Gestionar Documentos
                        </button>
                    </div>
                </div>

                <!-- Compañeros de Empresa -->
                @if(count($companeros) > 0)
                <div class="bg-white rounded-[32px] border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden">
                    <div class="p-6 bg-indigo-50/30 border-b border-indigo-100 flex items-center gap-3">
                        <i class="ph-bold ph-users-three text-indigo-600 text-xl"></i>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Otros en esta Empresa</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @foreach($companeros as $comp)
                        <a href="{{ route('call-center.manage', $comp->uuid) }}" class="flex items-center justify-between p-3 rounded-2xl hover:bg-slate-50 transition-all border border-transparent hover:border-slate-100 group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center text-[10px] font-black group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                    {{ substr($comp->nombre, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-700 truncate max-w-[120px]">{{ $comp->nombre }}</p>
                                    <p class="text-[8px] font-bold text-slate-400 uppercase">{{ $comp->estado->nombre }}</p>
                                </div>
                            </div>
                            <i class="ph ph-caret-right text-slate-300"></i>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function managementConsole() {
    return {
        quickAction(type) {
            const form = document.getElementById('gestionForm');
            const resultInput = document.getElementById('form_resultado');
            const estadoSelect = document.getElementById('form_estado_id');

            if (type === 'No contesta') {
                resultInput.value = 'Llamada no contestada / Buzón';
                // Asumiendo que existe un estado "No contestó" o similar. 
                // Buscaremos por texto o ID si lo tenemos.
                Array.from(estadoSelect.options).forEach(opt => {
                    if (opt.text.includes('No contestó') || opt.text.includes('Pendiente')) {
                        estadoSelect.value = opt.value;
                    }
                });
            } else if (type === 'Ocupado') {
                resultInput.value = 'Línea ocupada / Cliente solicita llamar luego';
            }

            // Enviar automáticamente
            Swal.fire({
                title: '¿Registrar acción rápida?',
                text: `Se marcará como: ${resultInput.value}`,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar',
                customClass: { popup: 'rounded-[32px]' }
            }).then((res) => {
                if (res.isConfirmed) {
                    form.dispatchEvent(new Event('submit'));
                }
            });
        }
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    Toast.fire({ icon: 'success', title: 'Copiado al portapapeles' });
}

// Reutilizar lógica de envío AJAX anterior
document.getElementById('gestionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: '¡Éxito!',
                text: data.message,
                icon: 'success',
                customClass: { popup: 'rounded-[32px]' }
            }).then(() => location.reload());
        }
    })
    .finally(() => btn.disabled = false);
});

function confirmPromote() {
    Swal.fire({
        title: '¿Promover a Carnetización?',
        text: "El caso pasará al flujo operativo maestro.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        confirmButtonText: 'Sí, promover',
        customClass: { popup: 'rounded-[32px]' }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch("{{ route('call-center.promote', $registro->uuid) }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ title: '¡Promovido!', icon: 'success', customClass: { popup: 'rounded-[32px]' } })
                    .then(() => window.location.href = "{{ route('call-center.worklist') }}");
                }
            });
        }
    });
}

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true
});
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
@endsection
