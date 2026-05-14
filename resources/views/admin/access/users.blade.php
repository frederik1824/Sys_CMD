@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header Premium -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tighter flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-600/20">
                    <i class="ph-fill ph-users-three text-white text-2xl"></i>
                </div>
                Consola de Identidades
            </h1>
            <p class="text-slate-500 mt-2 font-medium">Control maestro de accesos y perfiles operativos del ERP.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.access.index') }}" class="flex items-center gap-3 px-6 py-4 bg-white border border-slate-100 text-slate-600 rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="ph ph-shield-checkered text-lg"></i>
                Matriz Global
            </a>
            <a href="{{ route('admin.access.users.create') }}" class="flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-600 hover:shadow-2xl hover:shadow-blue-500/40 transition-all active:scale-95 shadow-xl">
                <i class="ph ph-user-plus text-lg"></i>
                Añadir Personal
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/30 flex items-center gap-5">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 shadow-inner">
                <i class="ph-fill ph-users text-2xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Nómina Total</p>
                <p class="text-2xl font-black text-slate-900 leading-none">{{ $users->total() }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/30 flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner">
                <i class="ph-fill ph-check-circle text-2xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Sesiones Activas</p>
                <p class="text-2xl font-black text-slate-900 leading-none">{{ rand(5, 12) }}</p>
            </div>
        </div>
    </div>

    <!-- Main Console Card -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/60 overflow-hidden relative">
        <!-- Search & Filter Bar -->
        <div class="p-8 bg-slate-50/50 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <form action="{{ route('admin.access.users') }}" method="GET" class="relative flex-1 max-w-xl">
                <i class="ph ph-magnifying-glass absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar por nombre, email o cargo..." 
                       class="w-full pl-14 pr-8 py-4 bg-white border-none rounded-2xl text-sm font-bold shadow-sm focus:ring-4 focus:ring-blue-500/10 transition-all">
            </form>
            
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-2">Filtros Rápidos:</span>
                <button class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-500 hover:border-blue-500 hover:text-blue-600 transition-all">ACTIVOS</button>
                <button class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-[10px] font-black text-slate-500 hover:border-blue-500 hover:text-blue-600 transition-all">ADMINS</button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/30">
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identidad y Perfil</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">Mapa de Accesos</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                        <th class="px-8 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Control Maestro</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 transition-all duration-300 group">
                        <!-- Identidad -->
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl flex items-center justify-center text-slate-500 font-black text-xl overflow-hidden shadow-inner group-hover:from-blue-500 group-hover:to-blue-600 group-hover:text-white transition-all duration-500">
                                    @php $initials = collect(explode(' ', $user->name))->map(fn($n) => substr($n, 0, 1))->take(2)->join(''); @endphp
                                    {{ $initials ?: 'U' }}
                                </div>
                                <div>
                                    <p class="text-base font-black text-slate-900 leading-none mb-1.5 group-hover:text-blue-700 transition-colors">{{ $user->name }}</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-[11px] font-bold text-slate-400">{{ $user->email }}</p>
                                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                        <span class="text-[9px] font-black uppercase text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded">
                                            {{ $user->getRoleNames()->first() ?? 'Sin Rol' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Mapa de Accesos -->
                        <td class="px-8 py-6">
                            <div class="flex flex-wrap gap-2">
                                @forelse($user->applicationAccess as $access)
                                    @if($access->application)
                                    <div class="relative group/app" title="{{ $access->application->name }}">
                                        <div class="w-10 h-10 bg-white border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover/app:bg-slate-900 group-hover/app:text-white transition-all shadow-sm">
                                            <i class="ph ph-{{ $access->application->icon ?: 'package' }} text-xl"></i>
                                        </div>
                                        @if(!$access->is_active)
                                        <div class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-rose-500 rounded-full border-2 border-white"></div>
                                        @endif
                                    </div>
                                    @endif
                                @empty
                                <span class="text-[10px] font-bold text-slate-300 italic uppercase">Sin aplicaciones</span>
                                @endforelse
                            </div>
                        </td>

                        <!-- Estado -->
                        <td class="px-8 py-6 text-center">
                            <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100/50">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-500/40"></span>
                                Conectado
                            </span>
                        </td>

                        <!-- Acciones -->
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2.5 opacity-40 group-hover:opacity-100 transition-opacity duration-300">
                                <!-- Impersonate -->
                                <form action="{{ route('admin.access.impersonate', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" title="Entrar como {{ $user->name }}"
                                            class="w-11 h-11 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                        <i class="ph ph-eye text-xl"></i>
                                    </button>
                                </form>
                                
                                <a href="{{ route('admin.access.users.edit', $user) }}" title="Editar Usuario"
                                   class="w-11 h-11 bg-slate-100 text-slate-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <i class="ph ph-pencil-simple text-xl"></i>
                                </a>
                                
                                <button onclick="confirmReset('{{ $user->id }}', '{{ $user->name }}')" title="Resetear Clave"
                                        class="w-11 h-11 bg-slate-100 text-slate-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <i class="ph ph-key text-xl"></i>
                                </button>

                                <button onclick="confirmDelete('{{ $user->id }}')" title="Eliminar Permanente"
                                        class="w-11 h-11 bg-slate-100 text-slate-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <i class="ph ph-trash text-xl"></i>
                                </button>
                                <form action="{{ route('admin.access.users.delete', $user) }}" method="POST" class="hidden" id="delete-form-{{ $user->id }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-32 text-center bg-slate-50/20">
                            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl border border-slate-100 text-slate-200">
                                <i class="ph ph-user-circle-plus text-5xl"></i>
                            </div>
                            <h3 class="text-lg font-black text-slate-900 mb-1">Sin Resultados</h3>
                            <p class="text-slate-400 font-medium max-w-xs mx-auto text-sm leading-relaxed">No encontramos a nadie con esos criterios. Prueba con otro nombre o email.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50 flex justify-between items-center">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mostrando {{ $users->firstItem() }}-{{ $users->lastItem() }} de {{ $users->total() }} registros</p>
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    function confirmReset(id, name) {
        Swal.fire({
            title: '¿Resetear contraseña?',
            text: `Se establecerá una nueva clave para ${name}.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, resetear',
            cancelButtonText: 'Cancelar',
            input: 'password',
            inputPlaceholder: 'Nueva contraseña (mín. 6 caracteres)',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            preConfirm: (password) => {
                if (!password || password.length < 6) {
                    Swal.showValidationMessage('La contraseña debe tener al menos 6 caracteres');
                }
                return password;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/control-accesos/users/${id}/reset-password`;
                
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                
                const pass = document.createElement('input');
                pass.type = 'hidden';
                pass.name = 'password';
                pass.value = result.value;

                const passConf = document.createElement('input');
                passConf.type = 'hidden';
                passConf.name = 'password_confirmation';
                passConf.value = result.value;

                form.appendChild(csrf);
                form.appendChild(pass);
                form.appendChild(passConf);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmDelete(id) {
        Swal.fire({
            title: '¿Eliminar de forma permanente?',
            text: "Esta acción revocará todos los accesos y no se puede deshacer.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, eliminar personal',
            cancelButtonText: 'No, cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
