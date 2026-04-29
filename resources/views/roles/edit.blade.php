@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('roles.index') }}" class="w-10 h-10 bg-white shadow-sm border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
        </a>
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Configurar Rol: {{ $role->name }}</h2>
            <p class="text-slate-500 text-sm mt-1">Activa o desactiva permisos específicos para este nivel de acceso.</p>
        </div>
    </div>

    <form action="{{ route('roles.update', $role->id) }}" method="POST" class="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 space-y-8">
        @csrf
        @method('PUT')

        @if($role->name === 'Admin')
            <div class="p-6 bg-amber-50 border border-amber-100 rounded-2xl text-amber-900 flex items-center gap-4">
                <span class="material-symbols-outlined text-3xl">lock</span>
                <div>
                    <h3 class="font-black text-xs uppercase tracking-widest text-amber-700">Acceso Maestro</h3>
                    <p class="text-sm font-medium">El rol <strong>Admin</strong> hereda todos los permisos por defecto y no puede ser modificado manualmente.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($permissions as $permission)
                <label class="flex items-center justify-between p-4 bg-slate-50 border border-transparent hover:border-primary/20 rounded-2xl cursor-pointer group transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-lg">
                                @if(str_contains($permission->name, 'manage')) manage_accounts
                                @elseif(str_contains($permission->name, 'view')) analytics
                                @elseif(str_contains($permission->name, 'delete')) delete_forever
                                @else task_alt @endif
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-slate-700">{{ ucwords(str_replace('_', ' ', $permission->name)) }}</span>
                            <span class="text-[0.6rem] font-bold text-slate-300 uppercase tracking-widest">Capacidad operacional</span>
                        </div>
                    </div>
                    <div class="relative inline-flex items-center">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-colors"></div>
                    </div>
                </label>
                @endforeach
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-slate-800 text-white font-black py-4 rounded-2xl shadow-xl shadow-primary/20 transition-all flex items-center justify-center gap-3 active:scale-[0.98]">
                <span class="material-symbols-outlined text-xl">verified_user</span>
                Sincronizar Permisos
            </button>
        @endif
    </form>
</div>
@endsection
