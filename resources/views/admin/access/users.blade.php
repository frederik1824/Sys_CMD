@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                <i class="ph ph-users-three text-blue-600"></i>
                Gestión de Nómina
            </h1>
            <p class="text-slate-500 mt-1 font-medium italic">Control centralizado de identidades y credenciales del ERP</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.access.index') }}" class="flex items-center gap-2 px-5 py-2.5 bg-slate-100 text-slate-600 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-slate-200 transition-all">
                <i class="ph ph-shield-checkered text-lg"></i>
                Matriz de Accesos
            </a>
            <a href="{{ route('admin.access.users.create') }}" class="flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all active:scale-95">
                <i class="ph ph-user-plus text-lg"></i>
                Nuevo Usuario
            </a>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center">
                <i class="ph ph-users text-2xl text-blue-600"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Usuarios</p>
                <p class="text-2xl font-black text-slate-900">{{ $users->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
        <!-- Search & Filter -->
        <div class="p-8 border-b border-slate-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form action="{{ route('admin.access.users') }}" method="GET" class="relative flex-1 max-w-md">
                <i class="ph ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar por nombre o correo electrónico..." 
                       class="w-full pl-12 pr-6 py-3 bg-slate-50 border-transparent rounded-2xl text-sm font-medium focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500/20 transition-all">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Usuario</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl flex items-center justify-center text-slate-500 font-black text-lg overflow-hidden shadow-sm">
                                    @php $initials = collect(explode(' ', $user->name))->map(fn($n) => substr($n, 0, 1))->take(2)->join(''); @endphp
                                    {{ $initials ?: 'U' }}
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 leading-none mb-1 group-hover:text-blue-600 transition-colors">{{ $user->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">ID: #{{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-medium text-slate-600">{{ $user->email }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                Activo
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.access.users.edit', $user) }}" title="Editar Perfil"
                                   class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl hover:bg-blue-600 hover:text-white transition-all flex items-center justify-center group/btn">
                                    <i class="ph ph-pencil-simple text-lg group-hover/btn:scale-110 transition-transform"></i>
                                </a>
                                
                                <button onclick="confirmReset('{{ $user->id }}', '{{ $user->name }}')" title="Resetear Contraseña"
                                        class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl hover:bg-amber-500 hover:text-white transition-all flex items-center justify-center group/btn">
                                    <i class="ph ph-key text-lg group-hover/btn:scale-110 transition-transform"></i>
                                </button>

                                <form action="{{ route('admin.access.users.delete', $user) }}" method="POST" class="inline" id="delete-form-{{ $user->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('{{ $user->id }}')" title="Eliminar Usuario"
                                            class="w-10 h-10 bg-slate-100 text-slate-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center group/btn">
                                        <i class="ph ph-trash text-lg group-hover/btn:scale-110 transition-transform"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="ph ph-user-minus text-4xl text-slate-200"></i>
                            </div>
                            <p class="text-slate-400 font-medium">No se encontraron usuarios que coincidan con la búsqueda.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
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
            inputPlaceholder: 'Nueva contraseña',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            preConfirm: (password) => {
                if (!password || password.length < 8) {
                    Swal.showValidationMessage('La contraseña debe tener al menos 8 caracteres');
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
            title: '¿Eliminar usuario?',
            text: "Esta acción no se puede deshacer y revocará todos los accesos.",
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection
