@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto" x-data="{ activeTab: 'titulares' }">
    <!-- Header: Operational Detail -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-12">
        <div class="flex items-start gap-6">
            <a href="{{ route('dispersion.pensionados.index') }}" class="w-14 h-14 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shadow-sm group">
                <i class="ph ph-arrow-left text-2xl group-hover:-translate-x-1 transition-transform"></i>
            </a>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-4 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $carga->periodo }}</span>
                    <h1 class="text-4xl font-black tracking-tighter text-slate-900 leading-none">{{ $carga->nombre_archivo }}</h1>
                </div>
                <p class="text-slate-400 font-bold text-sm uppercase tracking-widest">Hash de Integridad: <span class="text-slate-300 font-mono text-[10px]">{{ substr($carga->hash_archivo, 0, 16) }}...</span></p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <form action="{{ route('dispersion.pensionados.reprocess', $carga->uuid) }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-8 py-4 bg-slate-900 text-white rounded-[2rem] font-black text-[11px] uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-2xl shadow-slate-900/10">
                    <i class="ph ph-arrows-counter-clockwise text-xl"></i>
                    Reprocesar Lote
                </button>
            </form>

            <form action="{{ route('dispersion.pensionados.destroy', $carga->uuid) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar este lote permanentemente?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center gap-3 px-8 py-4 bg-white text-rose-600 border border-rose-100 rounded-[2rem] font-black text-[11px] uppercase tracking-widest hover:bg-rose-50 transition-all shadow-sm">
                    <i class="ph ph-trash text-xl"></i>
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    <!-- Stats Summary Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Total Dispersado</p>
            <p class="text-2xl font-black text-slate-900 tracking-tighter">RD$ {{ number_format($carga->monto_total_dispersado, 2) }}</p>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Cápitas Salud</p>
            <p class="text-2xl font-black text-slate-900 tracking-tighter">RD$ {{ number_format($carga->monto_total_salud, 2) }}</p>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Cápitas Adicionales</p>
            <p class="text-2xl font-black text-slate-900 tracking-tighter">RD$ {{ number_format($carga->monto_total_capita, 2) }}</p>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Registros Procesados</p>
            <p class="text-2xl font-black text-slate-900 tracking-tighter">{{ number_format($carga->total_registros) }}</p>
        </div>
    </div>

    <!-- Main Navigation Tabs -->
    <div class="flex items-center gap-6 mb-8 border-b border-slate-100">
        <button @click="activeTab = 'titulares'" 
                :class="activeTab === 'titulares' ? 'border-b-4 border-indigo-600 text-indigo-900' : 'text-slate-400'"
                class="px-8 py-5 text-sm font-black uppercase tracking-[0.15em] transition-all">
            Titulares ({{ number_format($carga->total_titulares) }})
        </button>
        <button @click="activeTab = 'dependientes'" 
                :class="activeTab === 'dependientes' ? 'border-b-4 border-indigo-600 text-indigo-900' : 'text-slate-400'"
                class="px-8 py-5 text-sm font-black uppercase tracking-[0.15em] transition-all">
            Dependientes ({{ number_format($carga->total_dependientes) }})
        </button>
        <button @click="activeTab = 'logs'" 
                :class="activeTab === 'logs' ? 'border-b-4 border-indigo-600 text-indigo-900' : 'text-slate-400'"
                class="px-8 py-5 text-sm font-black uppercase tracking-[0.15em] transition-all">
            Logs del Sistema
        </button>
    </div>

    <!-- CONTENT AREAS -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-slate-200/30 overflow-hidden min-h-[600px]">
        
        <!-- TAB: TITULARES -->
        <div x-show="activeTab === 'titulares'" x-transition>
            <div class="p-10 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <div class="relative w-full max-w-md">
                    <i class="ph ph-magnifying-glass absolute left-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" placeholder="Buscar por Cédula o Código..." class="w-full bg-white border border-slate-200 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-slate-700 shadow-sm focus:ring-4 focus:ring-indigo-500/10 transition-all">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50 text-left">
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Afiliado</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cédula</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">NSS</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Cód. Pensionado</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Origen</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Verificado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($carga->titulares->take(100) as $t)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-10 py-6">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[9px] font-black uppercase tracking-tighter">TITULAR</span>
                            </td>
                            <td class="px-10 py-6 text-sm font-black text-slate-900">{{ $t->cedula }}</td>
                            <td class="px-10 py-6 text-sm font-bold text-slate-500">{{ $t->nss ?? 'N/A' }}</td>
                            <td class="px-10 py-6 text-sm font-bold text-slate-400 uppercase">{{ $t->codigo_pensionado }}</td>
                            <td class="px-10 py-6 text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $t->origen_pension }}</td>
                            <td class="px-10 py-6 text-right">
                                <i class="ph-fill ph-check-circle text-emerald-500 text-xl"></i>
                            </td>
                        </tr>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($carga->total_titulares > 100)
            <div class="p-10 text-center bg-slate-50/50">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Mostrando los primeros 100 registros. Use filtros para búsquedas específicas.</p>
            </div>
            @endif
        </div>

        <!-- TAB: DEPENDIENTES -->
        <div x-show="activeTab === 'dependientes'" x-transition x-cloak>
             <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="px-10 py-6">Relación</th>
                            <th class="px-10 py-6">Cédula Titular</th>
                            <th class="px-10 py-6">NSS Titular</th>
                            <th class="px-10 py-6">Cédula Dep.</th>
                            <th class="px-10 py-6">NSS Dep.</th>
                            <th class="px-10 py-6 text-right">Estatus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($carga->dependientes->take(100) as $d)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-10 py-6">
                                <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-lg text-[9px] font-black uppercase tracking-tighter">DEPENDIENTE</span>
                            </td>
                            <td class="px-10 py-6 text-sm font-black text-slate-900">{{ $d->cedula_titular }}</td>
                            <td class="px-10 py-6 text-sm font-bold text-slate-400">{{ $d->nss_titular ?? 'N/A' }}</td>
                            <td class="px-10 py-6 text-sm font-black text-indigo-600">{{ $d->cedula_dependiente }}</td>
                            <td class="px-10 py-6 text-sm font-bold text-slate-400">{{ $d->nss_dependiente ?? 'N/A' }}</td>
                            <td class="px-10 py-6 text-right">
                                <i class="ph-fill ph-check-circle text-emerald-500 text-xl"></i>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB: LOGS -->
        <div x-show="activeTab === 'logs'" x-transition x-cloak>
            @if($carga->logs->count() > 0)
                <div class="p-10 space-y-4">
                    @foreach($carga->logs as $log)
                        <div class="flex items-start gap-6 p-6 rounded-[2rem] border {{ $log->tipo === 'error' ? 'bg-rose-50 border-rose-100' : 'bg-amber-50 border-amber-100' }}">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $log->tipo === 'error' ? 'bg-rose-500' : 'bg-amber-500' }} text-white shadow-lg">
                                <i class="ph ph-{{ $log->tipo === 'error' ? 'warning-octagon' : 'warning' }} text-xl"></i>
                            </div>
                            <div>
                                <h5 class="font-black text-slate-900 uppercase tracking-widest text-[10px] mb-1">{{ $log->mensaje }}</h5>
                                <p class="text-slate-600 text-sm font-medium">{{ $log->detalles }}</p>
                                <span class="text-[9px] font-black text-slate-400 mt-2 block">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-20 text-center">
                    <div class="w-24 h-24 rounded-full bg-emerald-50 text-emerald-500 mx-auto flex items-center justify-center mb-8 shadow-inner">
                        <i class="ph ph-check-circle text-5xl"></i>
                    </div>
                    <h4 class="text-2xl font-black text-slate-900 tracking-tight mb-2 uppercase">Procesamiento Íntegro</h4>
                    <p class="text-slate-500 font-medium max-w-md mx-auto">La estructura del archivo TSS fue interpretada al 100% sin anomalías detectadas en los offsets ni en la codificación.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
