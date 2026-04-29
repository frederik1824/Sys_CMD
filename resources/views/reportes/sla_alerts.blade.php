@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto space-y-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Centro de Alertas SLA</h2>
            <p class="text-slate-500 text-sm mt-1">Monitoreo de inactividad operativa por empresa y promotor.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50 transition-all flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-lg">dashboard</span>
            Volver al Tablero
        </a>
    </div>

    <!-- Livewire Component -->
    <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
        <livewire:dashboard.sla-alerts />
    </div>
</div>
@endsection
