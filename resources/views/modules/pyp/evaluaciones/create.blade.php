@extends('layouts.app')

@section('content')
<div class="p-8 page-transition" x-data="{ peso: 0, talla: 0, get imc() { return (this.peso && this.talla) ? (this.peso / ((this.talla/100) * (this.talla/100))).toFixed(2) : 0 } }">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-10">
        <a href="{{ route('pyp.afiliados.show', $afiliado->uuid) }}" class="w-12 h-12 bg-white border border-slate-200 rounded-2xl flex items-center justify-center text-slate-400 hover:text-slate-900 hover:shadow-md transition-all">
            <i class="ph ph-x font-bold text-xl"></i>
        </a>
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600">Nueva Evaluación Clínica</p>
            <h1 class="text-3xl font-black text-slate-900">{{ $afiliado->nombre_completo }}</h1>
        </div>
    </div>

    <form action="{{ route('pyp.evaluaciones.store', $afiliado->uuid) }}" method="POST" class="max-w-5xl">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Medical Data -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Section: Biometrics -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
                    <h4 class="text-lg font-black text-slate-900 mb-8 flex items-center gap-3">
                        <i class="ph-fill ph-heartbeat text-rose-500"></i> Parámetros Biométricos
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Presión Sistólica (mmHg)</label>
                            <input type="number" name="presion_sistolica" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-4 focus:ring-rose-500/10 transition-all outline-none" placeholder="Ej. 120">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Presión Diastólica (mmHg)</label>
                            <input type="number" name="presion_diastolica" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-4 focus:ring-rose-500/10 transition-all outline-none" placeholder="Ej. 80">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Glucosa en Ayunas (mg/dL)</label>
                            <input type="number" name="glucosa" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-4 focus:ring-amber-500/10 transition-all outline-none" placeholder="Ej. 95">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Frecuencia Cardiaca (LPM)</label>
                            <input type="number" name="frecuencia_cardiaca" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none" placeholder="Ej. 72">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-6 mt-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Peso (kg)</label>
                            <input type="number" step="0.1" name="peso" x-model="peso" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Talla (cm)</label>
                            <input type="number" name="talla" x-model="talla" required class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-bold text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">IMC Calculado</label>
                            <div class="w-full px-6 py-4 bg-indigo-50 border-none rounded-2xl font-black text-indigo-600 flex items-center justify-center">
                                <span x-text="imc"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Diagnosis -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
                    <h4 class="text-lg font-black text-slate-900 mb-8 flex items-center gap-3">
                        <i class="ph-fill ph-file-text text-indigo-600"></i> Evaluación Diagnóstica
                    </h4>
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Diagnóstico Médico</label>
                            <textarea name="diagnostico" required rows="4" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none" placeholder="Describa el estado actual del paciente..."></textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-2">Plan de Acción / Tratamiento</label>
                            <textarea name="plan_accion" rows="3" class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl font-medium text-slate-700 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none resize-none" placeholder="Pasos a seguir para el control del riesgo..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Clinical Choices -->
            <div class="space-y-8">
                <!-- Comorbidities -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Comorbilidades Activas</p>
                    <div class="space-y-3">
                        @foreach(['Diabetes Mellitus', 'Hipertensión Arterial', 'Cardiopatía Isquémica', 'Obesidad Grado I+', 'Dislipidemia', 'ERC'] as $disease)
                            <label class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 cursor-pointer transition-all border border-transparent hover:border-slate-100">
                                <input type="checkbox" name="comorbilidades[]" value="{{ $disease }}" class="w-5 h-5 rounded-lg border-slate-200 text-indigo-600 focus:ring-indigo-500/20">
                                <span class="text-xs font-bold text-slate-700">{{ $disease }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Status & Save -->
                <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>
                    <div class="relative z-10 space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Estado de Compensación</label>
                            <select name="estado_clinico" class="w-full px-4 py-3 bg-white/10 border-none rounded-xl text-white font-bold text-xs outline-none focus:ring-2 focus:ring-white/20">
                                <option value="Compensado" class="bg-slate-800">Compensado</option>
                                <option value="Controlado" class="bg-slate-800">Controlado</option>
                                <option value="Descompensado" class="bg-slate-800">Descompensado</option>
                                <option value="Inestable" class="bg-slate-800">Inestable</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-3 pt-4">
                            <label class="flex items-center gap-3 text-white/70">
                                <input type="checkbox" name="fumador" value="1" class="w-4 h-4 rounded border-none bg-white/20 text-indigo-500">
                                <span class="text-[10px] font-black uppercase tracking-widest">Fumador Activo</span>
                            </label>
                            <label class="flex items-center gap-3 text-white/70">
                                <input type="checkbox" name="sedentarismo" value="1" class="w-4 h-4 rounded border-none bg-white/20 text-indigo-500">
                                <span class="text-[10px] font-black uppercase tracking-widest">Sedentarismo</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-5 bg-indigo-500 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-indigo-400 transition-all shadow-xl shadow-indigo-900/40">
                            Finalizar Evaluación
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
