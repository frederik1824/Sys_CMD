@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@600;800&display=swap');
    body { background-color: #F4F7FA; font-family: 'Inter', sans-serif; }
    .premium-card { background: white; border: 1px solid #DFE1E6; border-radius: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .btn-premium { padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; font-size: 0.8rem; transition: all 0.2s ease; }
    .conflict-card { border: 1px solid #EAE6FF; background: white; border-radius: 20px; transition: all 0.3s; }
    .conflict-card:hover { border-color: #403294; transform: translateY(-2px); box-shadow: 0 12px 20px rgba(64, 50, 148, 0.05); }
    .version-panel { padding: 1.5rem; border-radius: 16px; background: #F8F9FA; border: 1px solid #F1F2F4; }
    .version-panel.active { border-color: #403294; background: #F5F3FF; }
    .diff-badge { padding: 2px 8px; border-radius: 6px; font-size: 0.6rem; font-weight: 800; text-transform: uppercase; }
</style>

<div class="p-6 lg:p-10 max-w-[1400px] mx-auto">
    <header class="flex justify-between items-center mb-12">
        <div class="flex items-center gap-6">
            <a href="{{ route('carnetizacion.sync_center.index') }}" class="w-12 h-12 bg-white border border-slate-200 rounded-xl flex items-center justify-center text-slate-400 hover:text-blue-600 transition-all">
                <i class="ph ph-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black tracking-tight text-slate-900 font-outfit">Resolución de <span class="text-indigo-600">Conflictos</span></h1>
                <p class="text-xs text-slate-500 mt-1">Identifica y resuelve discrepancias entre la base local y Firebase.</p>
            </div>
        </div>
        <div class="bg-indigo-50 px-6 py-3 rounded-2xl border border-indigo-100 flex items-center gap-3">
            <i class="ph-fill ph-warning-diamond text-indigo-600 text-xl"></i>
            <span class="text-sm font-black text-indigo-900">{{ $conflicts->total() }} Pendientes</span>
        </div>
    </header>

    <div class="space-y-8">
        @forelse($conflicts as $conflict)
            <div class="premium-card p-8 group">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6">
                    <div class="flex items-center gap-6">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center">
                            <i class="ph ph-scales text-3xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">{{ $conflict->nombre_completo }}</h3>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-xs font-bold text-slate-400 font-mono">{{ $conflict->cedula }}</span>
                                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Conflicto detectado hace {{ $conflict->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button class="btn-premium bg-slate-900 text-white hover:bg-slate-800 shadow-lg">Resolver Automáticamente</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Local Version -->
                    <div class="version-panel relative overflow-hidden">
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Versión Local (CMD)</span>
                            <span class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-[9px] font-bold text-slate-500">v1.2</span>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-slate-200/50 pb-2">
                                <span class="text-xs text-slate-500">Estado:</span>
                                <span class="text-xs font-bold text-slate-900">{{ $conflict->estado?->nombre ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200/50 pb-2">
                                <span class="text-xs text-slate-500">Dirección:</span>
                                <span class="text-xs font-bold text-slate-900 text-right max-w-[200px]">{{ $conflict->direccion ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <button class="w-full mt-8 py-3 bg-white border border-slate-200 rounded-xl text-xs font-black text-slate-700 hover:bg-indigo-50 hover:border-indigo-200 transition-all">Conservar Local</button>
                    </div>

                    <!-- Remote Version -->
                    <div class="version-panel active relative overflow-hidden">
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Versión Nube (Firebase)</span>
                            <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[9px] font-bold">LATEST</span>
                        </div>
                        <div class="space-y-4">
                            <div class="flex justify-between border-b border-indigo-200/50 pb-2">
                                <span class="text-xs text-slate-500">Estado:</span>
                                <span class="text-xs font-black text-indigo-600">COMPLETADO <span class="diff-badge bg-indigo-100 text-indigo-600 ml-2">DIFF</span></span>
                            </div>
                            <div class="flex justify-between border-b border-indigo-200/50 pb-2">
                                <span class="text-xs text-slate-500">Dirección:</span>
                                <span class="text-xs font-bold text-slate-900 text-right max-w-[200px]">{{ $conflict->direccion ?? 'Mismo valor' }}</span>
                            </div>
                        </div>
                        <button class="w-full mt-8 py-3 bg-indigo-600 text-white rounded-xl text-xs font-black hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition-all">Conservar Nube</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="premium-card p-20 text-center">
                <div class="w-24 h-24 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-8">
                    <i class="ph ph-check-circle text-5xl"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight">Sin Conflictos</h3>
                <p class="text-sm text-slate-500 mt-2">No se han detectado discrepancias de datos en la última sincronización.</p>
                <a href="{{ route('carnetizacion.sync_center.index') }}" class="mt-8 inline-block px-8 py-3 bg-slate-900 text-white rounded-xl text-xs font-black uppercase tracking-widest">Volver al Dashboard</a>
            </div>
        @endforelse
    </div>

    <div class="mt-10">
        {{ $conflicts->links() }}
    </div>
</div>

<script src="https://unpkg.com/@phosphor-icons/web"></script>
@endsection
