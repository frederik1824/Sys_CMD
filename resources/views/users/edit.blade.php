@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('usuarios.index') }}" class="w-10 h-10 bg-white shadow-sm border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Editar Usuario</h2>
            <p class="text-slate-500 text-sm mt-1">Actualiza los permisos y perfil de {{ $usuario->name }}.</p>
        </div>
    </div>

    @if ($errors->any())
    <div class="bg-rose-50 border border-rose-100 p-4 rounded-2xl flex items-center gap-3 animate-shake">
        <span class="material-symbols-outlined text-rose-500">error</span>
        <div class="text-xs font-bold text-rose-600">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    <form action="{{ route('usuarios.update', $usuario) }}" method="POST" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-8">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre -->
            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-4">Nombre Completo</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">person</span>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}" required 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none">
                </div>
            </div>

            <!-- Email -->
            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-4">Correo Electrónico</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">alternate_email</span>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none">
                </div>
            </div>

            <!-- Rol -->
            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-4">Rol en el Sistema</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">shield</span>
                    <select name="role_name" required 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none appearance-none">
                        @foreach($roles as $rol)
                            <option value="{{ $rol->name }}" {{ $usuario->hasRole($rol->name) ? 'selected' : '' }}>{{ $rol->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Departamento -->
            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-4">Departamento Operativo</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">business_center</span>
                    <select name="departamento_id" required 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none appearance-none">
                        <option value="">Seleccione Departamento...</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id }}" {{ $usuario->departamento_id == $dep->id ? 'selected' : '' }}>{{ $dep->nombre }} ({{ $dep->codigo }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Responsable -->
            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-4">Enlace con Responsable</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">corporate_fare</span>
                    <select name="responsable_id" 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none appearance-none">
                        <option value="">Ninguno</option>
                        @foreach($responsables as $resp)
                            <option value="{{ $resp->id }}" {{ $usuario->responsable_id == $resp->id ? 'selected' : '' }}>{{ $resp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-50 relative overflow-hidden">
            <div class="col-span-1 md:col-span-2 space-y-2 mb-4">
                <div class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-100 rounded-xl text-[0.7rem] text-amber-700 font-bold uppercase italic">
                    <span class="material-symbols-outlined text-sm">info</span>
                    Deja los campos de contraseña vacíos si no deseas cambiarla.
                </div>
            </div>
            
            <!-- Password -->
            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-4">Nueva Contraseña</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">lock</span>
                    <input type="password" name="password" 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none" 
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
                <label class="text-xs font-black uppercase tracking-widest text-slate-400 ml-4">Repetir Contraseña</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm">lock_person</span>
                    <input type="password" name="password_confirmation" 
                        class="w-full bg-slate-50 border-none rounded-2xl pl-12 pr-4 py-3 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-primary/10 transition-all outline-none" 
                        placeholder="••••••••">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-primary hover:bg-slate-800 text-white font-black py-4 rounded-2xl shadow-xl shadow-primary/20 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
            <span class="material-symbols-outlined text-xl">update</span>
            Actualizar Usuario
        </button>
    </form>
</div>
@endsection
