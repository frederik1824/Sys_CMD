@extends('layouts.app')

@section('content')
<div class="p-10 page-transition">
    <!-- Top Bar: Title & Stats -->
    <div class="flex justify-between items-start mb-12">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-slate-900 rounded-[1.5rem] flex items-center justify-center text-white shadow-2xl shadow-slate-200">
                <i class="ph-fill ph-users-three text-3xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tight">Buscador Clínico</h1>
                <p class="text-slate-400 font-medium italic text-sm">Gestión y localización de población bajo seguimiento preventivo.</p>
            </div>
        </div>
        <div class="bg-white px-8 py-4 rounded-3xl border border-slate-100 shadow-xl flex items-center gap-6">
            <div class="text-center">
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1">Población Total</p>
                <p class="text-xl font-black text-slate-900 leading-none">{{ number_format($stats['total'] ?? 0) }}</p>
            </div>
        </div>
    </div>

    <!-- Search Command Bar -->
    <div class="bg-white p-4 rounded-[2.5rem] border border-slate-100 shadow-2xl mb-12 flex flex-col md:flex-row gap-4 items-center">
        <div class="relative flex-1 group w-full">
            <form action="{{ route('pyp.afiliados.index') }}" method="GET" class="relative">
                <i class="ph ph-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-2xl text-slate-300 group-focus-within:text-indigo-600 transition-colors"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Buscar por Cédula, Nombre o Póliza..." 
                    class="w-full pl-16 pr-8 py-6 bg-slate-50 border-none rounded-[1.8rem] font-bold text-sm text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none"
                >
            </form>
        </div>
        <div class="flex gap-3 w-full md:w-auto">
            <a href="{{ route('pyp.afiliados.create') }}" class="flex-1 md:flex-none px-8 py-6 bg-emerald-50 text-emerald-600 rounded-[1.8rem] text-[11px] font-black uppercase tracking-widest hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center gap-3">
                <i class="ph ph-user-plus text-xl"></i> Matriculación
            </a>
            @if(request('search'))
                <a href="{{ route('pyp.afiliados.index') }}" class="px-8 py-6 bg-slate-100 text-slate-400 rounded-[1.8rem] text-[11px] font-black uppercase tracking-widest hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <i class="ph ph-x text-xl"></i>
                </a>
            @endif
        </div>
    </div>

    <!-- Results Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($afiliados as $afiliado)
            @php
                $exp = $afiliado->pypExpediente;
                $riskColor = match($exp?->riesgo_nivel) {
                    'Alto' => 'rose',
                    'Moderado' => 'amber',
                    'Bajo' => 'emerald',
                    default => 'slate'
                };
            @endphp
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl overflow-hidden group hover:scale-[1.03] transition-all duration-300">
                <div class="p-8">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                            <i class="ph-fill ph-user-circle text-4xl"></i>
                        </div>
                        @if($exp && $exp->estado_clinico !== 'Pendiente')
                            <div class="px-3 py-1.5 bg-{{ $riskColor }}-50 border border-{{ $riskColor }}-100 rounded-xl">
                                <span class="text-[9px] font-black uppercase tracking-widest text-{{ $riskColor }}-600">{{ $exp->riesgo_nivel }}</span>
                            </div>
                        @else
                            <div class="px-3 py-1.5 bg-slate-50 border border-slate-100 rounded-xl">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 italic">Pendiente</span>
                            </div>
                        @endif
                    </div>

                    <div class="mb-6">
                        <h3 class="text-base font-black text-slate-900 mb-1 truncate">{{ $afiliado->nombre_completo }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $afiliado->cedula }}</p>
                    </div>

                    <div class="space-y-4 mb-8">
                        <div class="flex items-center gap-3">
                            <i class="ph ph-phone text-slate-300"></i>
                            <span class="text-[11px] font-bold text-slate-500">{{ $afiliado->telefono ?? 'Sin Teléfono' }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="ph ph-stethoscope text-slate-300"></i>
                            <span class="text-[11px] font-bold text-slate-500">{{ $exp?->estado_clinico ?? 'Sin Evaluar' }}</span>
                        </div>
                    </div>
                    
                    <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex -space-x-2">
                            @foreach($exp?->programas ?? [] as $prog)
                                <div class="w-7 h-7 rounded-full border-2 border-white flex items-center justify-center text-[10px] text-white shadow-sm" style="background-color: {{ $prog->color }}" title="{{ $prog->nombre }}">
                                    <i class="ph-fill {{ $prog->icon }}"></i>
                                </div>
                            @endforeach
                            @if(!$exp || $exp->programas->isEmpty())
                                <span class="text-[10px] font-bold text-slate-300 italic">Sin Programas</span>
                            @endif
                        </div>
                        <a href="{{ route('pyp.afiliados.show', $afiliado->uuid) }}" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-lg shadow-slate-200">
                            Ver Ficha
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 bg-white rounded-[4rem] border border-slate-100 shadow-xl flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 mb-8">
                    <i class="ph ph-magnifying-glass text-6xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 mb-2">No encontramos al afiliado</h3>
                <p class="text-slate-400 text-sm font-medium max-w-sm mb-10 italic">Verifique que la cédula sea correcta o registre al paciente en el programa.</p>
                <a href="{{ route('pyp.afiliados.create') }}" class="px-10 py-5 bg-slate-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-2xl shadow-slate-200 flex items-center gap-3">
                    <i class="ph ph-plus-circle text-xl"></i> Registrar Nuevo Afiliado
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $afiliados->links() }}
    </div>
</div>
@endsection
