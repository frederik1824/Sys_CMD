@extends('layouts.app')

@section('content')
<div class="space-y-8 animate-in fade-in duration-500">
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-white p-8 rounded-2xl border border-slate-200 shadow-sm">
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <div class="w-1.5 h-8 bg-slate-900 rounded-full"></div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Logs de <span class="text-slate-400">Auditoría</span></h2>
            </div>
            <p class="text-slate-500 font-bold text-[10px] uppercase tracking-widest pl-5">Trazabilidad de Acciones • Seguridad Maestra</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest border border-slate-200">
                Total Registros: {{ $logs->total() }}
            </span>
        </div>
    </div>

    <!-- FILTERS -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.access.audit') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Usuario</label>
                <select name="user_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500 outline-none transition-all">
                    <option value="">Todos los usuarios</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Evento</label>
                <select name="event" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500 outline-none transition-all">
                    <option value="">Todos los eventos</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Creado</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Actualizado</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Eliminado</option>
                    <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>Inicio Sesión</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 px-1">Módulo/Modelo</label>
                <input type="text" name="model" value="{{ request('model') }}" placeholder="Ej: User, Role..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500 outline-none transition-all">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-slate-900 text-white py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                    Filtrar
                </button>
                <a href="{{ route('admin.access.audit') }}" class="px-4 py-3 bg-slate-100 text-slate-400 rounded-xl hover:bg-slate-200 transition-all">
                    <i class="ph ph-arrow-counter-clockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- LOGS TABLE -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Fecha / Hora</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Usuario</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Evento</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Modelo / ID</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Cambios</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-slate-900">{{ $log->created_at->format('d/m/Y') }}</span>
                                <span class="text-[10px] font-bold text-slate-400">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-500 border border-slate-200">
                                    {{ substr($log->user->name ?? 'SYS', 0, 2) }}
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ $log->user->name ?? 'Sistema' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border {{ 
                                match($log->event) {
                                    'created' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'updated' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'deleted' => 'bg-rose-50 text-rose-700 border-rose-100',
                                    'login' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    default => 'bg-slate-50 text-slate-700 border-slate-100'
                                }
                            }}">
                                {{ $log->event }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-900 uppercase tracking-tighter">{{ class_basename($log->model_type) }}</span>
                                <span class="text-[9px] font-bold text-slate-400">ID: {{ $log->model_id }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="max-w-xs overflow-hidden">
                                <p class="text-[9px] font-medium text-slate-500 italic truncate" title="{{ json_encode($log->new_values) }}">
                                    {{ is_array($log->new_values) ? json_encode($log->new_values) : $log->new_values }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <i class="ph ph-list-magnifying-glass text-4xl mb-4"></i>
                                <p class="text-[10px] font-black uppercase tracking-widest">No se encontraron registros de auditoría</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-200">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
