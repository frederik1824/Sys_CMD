@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="mb-10 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight">Nuevo Usuario</h1>
            <p class="text-slate-500 font-medium">Registra una nueva identidad en la plataforma central</p>
        </div>
        <a href="{{ route('admin.access.users') }}" class="text-slate-400 hover:text-slate-900 transition-colors">
            <i class="ph ph-x-circle text-4xl"></i>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/50 overflow-hidden">
        <form action="{{ route('admin.access.users.store') }}" method="POST" class="p-10 md:p-16">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Nombre Completo</label>
                    <div class="relative">
                        <i class="ph ph-user absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/20 transition-all"
                               placeholder="Ej. Juan Pérez">
                    </div>
                    @error('name') <p class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Correo Electrónico</label>
                    <div class="relative">
                        <i class="ph ph-envelope-simple absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/20 transition-all"
                               placeholder="usuario@arscmd.com">
                    </div>
                    @error('email') <p class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Departamento</label>
                    <div class="relative">
                        <i class="ph ph-buildings absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <select name="departamento_id" class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/20 transition-all appearance-none">
                            <option value="">Seleccionar Departamento</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id }}" {{ old('departamento_id') == $dep->id ? 'selected' : '' }}>{{ $dep->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Responsable Directo</label>
                    <div class="relative">
                        <i class="ph ph-identification-badge absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <select name="responsable_id" class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/20 transition-all appearance-none">
                            <option value="">Seleccionar Responsable</option>
                            @foreach($responsables as $resp)
                                <option value="{{ $resp->id }}" {{ old('responsable_id') == $resp->id ? 'selected' : '' }}>{{ $resp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Contraseña</label>
                    <div class="relative">
                        <i class="ph ph-lock absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="password" name="password" required
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/20 transition-all"
                               placeholder="••••••••">
                    </div>
                    @error('password') <p class="text-rose-500 text-xs font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 ml-1">Confirmar Contraseña</label>
                    <div class="relative">
                        <i class="ph ph-lock-keyhole absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="password" name="password_confirmation" required
                               class="w-full pl-14 pr-6 py-4 bg-slate-50 border-transparent rounded-2xl text-sm font-bold focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/20 transition-all"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.access.users') }}" class="px-8 py-4 text-sm font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-10 py-4 bg-blue-600 text-white rounded-2xl font-black uppercase text-xs tracking-[0.2em] hover:bg-blue-700 hover:shadow-xl hover:shadow-blue-500/40 transition-all active:scale-95">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
