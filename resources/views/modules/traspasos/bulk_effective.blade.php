@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1000px] mx-auto">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold tracking-tighter text-slate-900">Efectividad Masiva (Altas Unipago)</h1>
        <p class="text-slate-500 font-medium mt-1">Pega el listado de cédulas aprobadas para marcarlas como efectivas en lote.</p>
    </div>

    @if($errors->any())
        <div class="mb-8 bg-rose-50 border border-rose-100 text-rose-600 p-6 rounded-[32px] flex items-start gap-4 animate-in fade-in slide-in-from-top-4">
            <div class="w-10 h-10 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-rose-200 shrink-0">
                <i class="ph ph-warning text-xl"></i>
            </div>
            <div>
                <span class="font-black text-xs uppercase tracking-widest block mb-2">Se ha detenido el proceso</span>
                <ul class="text-sm font-bold opacity-80 list-disc pl-4 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-8 bg-emerald-50 border border-emerald-100 text-emerald-600 p-6 rounded-[32px] flex items-center gap-4 animate-in fade-in slide-in-from-top-4">
            <div class="w-10 h-10 bg-emerald-500 text-white rounded-full flex items-center justify-center shadow-lg shadow-emerald-200">
                <i class="ph ph-check-circle text-xl"></i>
            </div>
            <span class="font-black text-sm uppercase tracking-tight">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-[40px] border border-slate-100 shadow-2xl overflow-hidden p-10">
        <form action="{{ route('traspasos.bulk.effective.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 block">Fecha de Efectividad</label>
                    <div class="relative">
                        <i class="ph ph-calendar absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="date" name="fecha_efectivo" required value="{{ now()->format('Y-m-d') }}"
                               class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 block">Periodo Operativo (Mes)</label>
                    <div class="relative">
                        <i class="ph ph-calendar-check absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-xl"></i>
                        <input type="month" name="periodo_efectivo" required value="{{ now()->format('Y-m') }}"
                               class="w-full bg-slate-50 border-none rounded-2xl pl-14 pr-6 py-4 text-sm font-bold focus:ring-2 focus:ring-primary/20">
                    </div>
                </div>
            </div>

            <div>
                <label class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3 block">Listado de Cédulas (Pega aquí)</label>
                <div class="relative">
                    <textarea name="cedulas" required rows="12" placeholder="Ej: 00100223344&#10;05611223344&#10;..."
                              class="w-full bg-slate-50 border-none rounded-[32px] p-8 text-sm font-bold font-mono focus:ring-2 focus:ring-primary/20 placeholder:text-slate-300"></textarea>
                </div>
                <p class="mt-4 text-xs font-bold text-slate-400 flex items-center gap-2">
                    <i class="ph ph-info text-blue-500"></i> Puedes copiar la columna de cédulas directamente desde Excel.
                </p>
            </div>

            <button type="submit" class="w-full bg-slate-900 text-white rounded-3xl py-6 text-xs font-black uppercase tracking-[0.2em] hover:bg-slate-800 transition-all shadow-2xl shadow-slate-200 flex items-center justify-center gap-4">
                <i class="ph ph-lightning text-xl text-amber-400"></i> Procesar Efectividad Masiva
            </button>
        </form>
    </div>
</div>
@endsection
