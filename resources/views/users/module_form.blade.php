@extends('layouts.app')

@section('content')
@php
    $color = str_contains($routePrefix, 'traspasos') ? 'amber' : (str_contains($routePrefix, 'afiliacion') ? 'indigo' : 'blue');
    $isEditing = isset($usuario);
@endphp

<div class="max-w-4xl mx-auto p-8" x-data="{ 
    selectedDept: '{{ old('departamento_id', $usuario->departamento_id ?? '') }}',
    selectedRole: '{{ old('role_name', $isEditing ? $usuario->getRoleNames()->first() : '') }}',
    allRoles: {{ $roles->toJson() }},
    mapping: {
        'Afiliación': ['Analista de Afiliación', 'Supervisor de Afiliación', 'Colaborador', 'Representante'],
        'Servicio al Cliente': ['Servicio al Cliente (CSR)', 'Gestor de Llamadas', 'Supervisor de Llamadas', 'Supervisor de Servicio al Cliente'],
        'Autorizaciones Médicas': ['Supervisor de Autorizaciones', 'Analista de Autorizaciones'],
        'Tecnología (IT)': ['Soporte IT', 'Administrador de Sistemas'],
        'Cuentas Médicas': ['Supervisor de Cuentas Médicas', 'Analista de Cuentas Médicas'],
        'Traspasos': ['Supervisor de Traspasos', 'Agente de Traspasos'],
        'Auditoría': ['Auditor', 'Auditoría'],
        'Calidad': ['Calidad'],
        'Recursos Humanos': ['Recursos Humanos']
    },
    get filteredRoles() {
        if (!this.selectedDept) return [];
        let deptName = '';
        const deptSelect = document.getElementById('dept_select');
        if (deptSelect) {
            deptName = deptSelect.options[deptSelect.selectedIndex]?.text.trim() || '';
        }

        let allowed = this.mapping[deptName] || [];
        
        // Si no hay mapeo específico, mostramos todos los roles para no bloquear
        if (allowed.length === 0) return this.allRoles;

        // Retornar solo roles permitidos para el departamento (Supervisor es el tope)
        return this.allRoles.filter(r => allowed.includes(r.name));
    }
}">
    <!-- HEADER -->
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route($routePrefix . '.index') }}" class="w-14 h-14 bg-white border border-slate-100 rounded-2xl flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm hover:shadow-md">
            <i class="ph-bold ph-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-3xl font-black tracking-tighter text-slate-900">{{ $isEditing ? 'Editar Colaborador' : 'Nuevo Colaborador' }}</h1>
            <p class="text-slate-500 font-medium mt-1">Completa los datos para el acceso al módulo de {{ $moduleName }}.</p>
        </div>
    </div>

    @if ($errors->any())
    <div class="bg-rose-50 border border-rose-100 p-6 rounded-[32px] mb-8 animate-shake">
        <div class="flex items-center gap-3 mb-2">
            <i class="ph-fill ph-warning-circle text-rose-500 text-xl"></i>
            <span class="text-sm font-black text-rose-600 uppercase tracking-widest">Errores de Validación</span>
        </div>
        <ul class="space-y-1">
            @foreach ($errors->all() as $error)
                <li class="text-xs font-bold text-rose-500 ml-8">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ $isEditing ? route($routePrefix . '.update', $usuario) : route($routePrefix . '.store') }}" method="POST" class="bg-white p-10 rounded-[40px] border border-slate-100 shadow-sm space-y-10">
        @csrf
        @if($isEditing) @method('PATCH') @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Nombre -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4">Nombre Completo</label>
                <div class="relative group">
                    <i class="ph ph-user absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-{{ $color }}-600 transition-colors"></i>
                    <input type="text" name="name" value="{{ old('name', $usuario->name ?? '') }}" required 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-{{ $color }}-500/10 transition-all outline-none" 
                        placeholder="Ej. Juan Perez">
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4">Correo Electrónico</label>
                <div class="relative group">
                    <i class="ph ph-envelope absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-{{ $color }}-600 transition-colors"></i>
                    <input type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" required 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-{{ $color }}-500/10 transition-all outline-none" 
                        placeholder="juan.perez@arscmd.com">
                </div>
            </div>

            <!-- Departamento (AHORA PRIMERO) -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4">Departamento</label>
                <div class="relative group">
                    <i class="ph ph-buildings absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-{{ $color }}-600 transition-colors"></i>
                    <select name="departamento_id" id="dept_select" required x-model="selectedDept" @change="selectedRole = ''"
                        class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-12 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-{{ $color }}-500/10 transition-all outline-none appearance-none">
                        <option value="">Seleccione Departamento...</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id }}" {{ old('departamento_id', $usuario->departamento_id ?? '') == $dep->id ? 'selected' : '' }}>{{ $dep->nombre }}</option>
                        @endforeach
                    </select>
                    <i class="ph ph-caret-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            <!-- Rol (Dependiente) -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4">Rol del Sistema</label>
                <div class="relative group">
                    <i class="ph ph-shield absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-{{ $color }}-600 transition-colors"></i>
                    <select name="role_name" required x-model="selectedRole"
                        class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-12 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-{{ $color }}-500/10 transition-all outline-none appearance-none disabled:opacity-50"
                        :disabled="!selectedDept">
                        <option value="">Seleccione Rol...</option>
                        <template x-for="rol in filteredRoles" :key="rol.id">
                            <option :value="rol.name" x-text="rol.name" :selected="rol.name == selectedRole"></option>
                        </template>
                    </select>
                    <i class="ph ph-caret-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>

            @if(isset($responsables))
            <!-- Responsable (Solo para CMD) -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4">Vincular con Responsable</label>
                <div class="relative group">
                    <i class="ph ph-identification-card absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-{{ $color }}-600 transition-colors"></i>
                    <select name="responsable_id" 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-12 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-{{ $color }}-500/10 transition-all outline-none appearance-none">
                        <option value="">Ninguno (Opcional)</option>
                        @foreach($responsables as $resp)
                            <option value="{{ $resp->id }}" {{ old('responsable_id', $usuario->responsable_id ?? '') == $resp->id ? 'selected' : '' }}>{{ $resp->nombre }}</option>
                        @endforeach
                    </select>
                    <i class="ph ph-caret-down absolute right-6 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></i>
                </div>
            </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-10 border-t border-slate-50">
            <!-- Password -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4">{{ $isEditing ? 'Cambiar Contraseña' : 'Establecer Contraseña' }}</label>
                <div class="relative group">
                    <i class="ph ph-lock absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-{{ $color }}-600 transition-colors"></i>
                    <input type="password" name="password" {{ $isEditing ? '' : 'required' }} 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-{{ $color }}-500/10 transition-all outline-none" 
                        placeholder="••••••••">
                </div>
                @if($isEditing)
                <p class="text-[10px] text-slate-400 font-bold ml-4">Deja en blanco para mantener la contraseña actual.</p>
                @endif
            </div>

            <!-- Confirm Password -->
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-4">Confirmar Contraseña</label>
                <div class="relative group">
                    <i class="ph ph-lock-key absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-{{ $color }}-600 transition-colors"></i>
                    <input type="password" name="password_confirmation" {{ $isEditing ? '' : 'required' }} 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-{{ $color }}-500/10 transition-all outline-none" 
                        placeholder="••••••••">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-5 rounded-[24px] shadow-2xl shadow-slate-900/20 transition-all flex items-center justify-center gap-4 active:scale-[0.98] group">
            <i class="ph-bold ph-floppy-disk text-xl group-hover:scale-110 transition-transform"></i>
            {{ $isEditing ? 'Actualizar Información' : 'Guardar Nuevo Colaborador' }}
        </button>
    </form>
</div>
@endsection
