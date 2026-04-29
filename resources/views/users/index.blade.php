@extends('layouts.app')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-on-surface">Usuarios & Roles</h2>
            <p class="text-slate-500 text-sm mt-1">Administra los accesos y roles del personal del sistema.</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="bg-primary text-white px-5 py-2.5 rounded-xl shadow-lg shadow-primary/20 text-sm font-semibold flex items-center gap-2 hover:bg-blue-800 transition-colors">
            <span class="material-symbols-outlined text-lg">person_add</span> Nuevo Usuario
        </a>
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-slate-100">
        <a href="{{ route('usuarios.index') }}" class="px-6 py-3 text-sm font-bold border-b-2 border-primary text-primary transition-all">
            Usuarios
        </a>
        <a href="{{ route('roles.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-all border-b-2 border-transparent">
            Roles & Permisos
        </a>
        <a href="{{ route('admin.audit.index') }}" class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-slate-600 transition-all border-b-2 border-transparent">
            Auditoría
        </a>
    </div>

    <div class="bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50">
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Usuario</th>
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Rol</th>
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Departamento</th>
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400">Responsable</th>
                    <th class="py-4 px-6 text-[0.65rem] font-black uppercase text-slate-400 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50/50 transition-colors group">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-primary font-bold">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-700">{{ $user->name }}</span>
                                <span class="text-xs text-slate-400 font-medium">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        @foreach($user->getRoleNames() as $role)
                        <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[0.65rem] font-bold uppercase tracking-wider">
                            {{ $role }}
                        </span>
                        @endforeach
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-xs font-bold px-3 py-1 rounded-lg {{ $user->departamento ? 'bg-slate-100 text-slate-700' : 'bg-rose-50 text-rose-500' }}">
                            {{ $user->departamento->nombre ?? 'Sin Asignar' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-sm text-slate-600 font-medium">
                        {{ $user->responsable->nombre ?? 'N/A' }}
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-2">
                            @if(auth()->id() !== $user->id && !session()->has('impersonate_original_id'))
                            <form action="{{ route('usuarios.impersonate', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-amber-500 transition-colors" title="Entrar como este usuario">
                                    <span class="material-symbols-outlined text-lg">person_search</span>
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('usuarios.edit', $user) }}" class="p-2 text-slate-400 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('usuarios.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 transition-colors">
                                    <span class="material-symbols-outlined text-lg">delete_forever</span>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
