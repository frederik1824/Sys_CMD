@extends('layouts.app')

@section('content')
<div class="p-8 max-w-[1600px] mx-auto">
    <!-- Header: Strategic Control -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-4 mb-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center text-white shadow-2xl shadow-slate-200">
                    <i class="ph-fill ph-bank text-2xl"></i>
                </div>
                <h1 class="text-3xl font-black tracking-tighter text-slate-900 leading-none">Gestión de Dispersión</h1>
            </div>
            <p class="text-slate-500 font-medium max-w-2xl text-lg">Control operativo de archivos TSS/SUIRPLUS para el Plan Especial de Pensionados y Jubilados.</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                    class="px-8 py-4 bg-indigo-600 text-white rounded-[20px] text-xs font-black uppercase tracking-[0.2em] hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100 flex items-center gap-3 group">
                <i class="ph-bold ph-plus text-lg group-hover:rotate-90 transition-transform"></i> Nueva Dispersión
            </button>
        </div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group hover:-translate-y-1 transition-all">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4 relative z-10">Monto Total Dispersado</p>
            <div class="flex items-end gap-2 relative z-10">
                <span class="text-3xl font-black text-slate-900 tracking-tighter">RD$ {{ number_format($stats['total_dispersado'], 2) }}</span>
            </div>
            <div class="mt-6 flex items-center gap-2 relative z-10">
                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Fondos Conciliados</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group hover:-translate-y-1 transition-all">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4 relative z-10">Titulares Pensionados</p>
            <div class="flex items-end gap-2 relative z-10">
                <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ number_format($stats['total_pensionados']) }}</span>
                <span class="text-xs font-bold text-slate-400 mb-1.5 uppercase">Vidas</span>
            </div>
            <div class="mt-6 h-1.5 bg-slate-50 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" style="width: 85%"></div>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group hover:-translate-y-1 transition-all">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4 relative z-10">Dependientes Adicionales</p>
            <div class="flex items-end gap-2 relative z-10">
                <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ number_format($stats['total_dependientes']) }}</span>
            </div>
            <p class="mt-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest italic">Cápitas Adicionales Activas</p>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 relative overflow-hidden group hover:-translate-y-1 transition-all">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4 relative z-10">Cargas Pendientes</p>
            <div class="flex items-end gap-2 relative z-10">
                <span class="text-3xl font-black text-slate-900 tracking-tighter">{{ $stats['cargas_pendientes'] }}</span>
                <i class="ph ph-clock-countdown text-amber-500 text-2xl mb-1.5"></i>
            </div>
            <p class="mt-6 text-[10px] font-bold text-amber-600 uppercase tracking-widest">Requieren Revisión</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Main Activity: Recent Loads -->
        <div class="lg:col-span-2 bg-white rounded-[3rem] border border-slate-100 shadow-2xl shadow-slate-200/30 overflow-hidden">
            <div class="p-10 border-b border-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight leading-none mb-2">Historial Reciente de Cargas</h3>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Últimos archivos procesados en el sistema</p>
                </div>
                <a href="{{ route('dispersion.pensionados.history') }}" class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline">Ver Todo</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-50/50 text-left">
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Periodo</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Archivo</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Titulares</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Monto</th>
                            <th class="px-10 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Estado</th>
                            <th class="px-10 py-5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($ultimasCargas as $carga)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-10 py-8">
                                <span class="text-sm font-black text-slate-900">{{ $carga->periodo }}</span>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                                        <i class="ph ph-file-text text-xl"></i>
                                    </div>
                                    <div class="max-w-[200px]">
                                        <p class="text-[13px] font-black text-slate-900 truncate">{{ $carga->nombre_archivo }}</p>
                                        <p class="text-[10px] font-bold text-slate-400">{{ $carga->fecha_carga->format('d/m/Y h:i A') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <span class="text-sm font-black text-slate-900">{{ number_format($carga->total_titulares) }}</span>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <span class="text-sm font-black text-indigo-600">RD$ {{ number_format($carga->monto_total_dispersado, 2) }}</span>
                            </td>
                            <td class="px-10 py-8 text-center">
                                <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest {{ $carga->estado == 'Completado' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $carga->estado }}
                                </span>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <a href="{{ route('dispersion.pensionados.show', $carga->uuid) }}" 
                                   class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center hover:bg-slate-800 transition-all inline-flex">
                                    <i class="ph ph-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sidebar: Info & Actions -->
        <div class="space-y-10">
            <div class="bg-slate-900 rounded-[3rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-indigo-200">
                <i class="ph ph-info text-8xl absolute -right-6 -bottom-6 text-white/5"></i>
                <h4 class="text-xl font-black tracking-tight mb-6 relative z-10">Importante: Layout TSS</h4>
                <p class="text-slate-400 text-sm leading-relaxed mb-8 relative z-10">
                    Asegúrese de que el archivo TXT siga la estructura de posiciones fijas definida por la TSS para el Plan de Pensionados. 
                </p>
                <div class="space-y-4 relative z-10">
                    <div class="flex items-center gap-3 text-[11px] font-bold uppercase tracking-widest text-emerald-400">
                        <i class="ph ph-check-circle text-lg"></i> Validar Sumario Final
                    </div>
                    <div class="flex items-center gap-3 text-[11px] font-bold uppercase tracking-widest text-emerald-400">
                        <i class="ph ph-check-circle text-lg"></i> Sin líneas en blanco
                    </div>
                    <div class="flex items-center gap-3 text-[11px] font-bold uppercase tracking-widest text-emerald-400">
                        <i class="ph ph-check-circle text-lg"></i> Formato UTF-8
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-xl p-10">
                <h4 class="text-lg font-black text-slate-900 tracking-tight mb-6">Próximos Pasos</h4>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                            <i class="ph ph-arrows-merge text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800 leading-tight">Cruce con Afiliación</p>
                            <p class="text-[11px] font-medium text-slate-400">Validar solicitudes vs dispersión real.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                            <i class="ph ph-warning-octagon text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800 leading-tight">Detección de Fugas</p>
                            <p class="text-[11px] font-medium text-slate-400">Identificar pensionados sin carnet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- UPLOAD MODAL -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-slate-900/90 backdrop-blur-md z-[100] flex items-center justify-center p-6">
        <div class="bg-white rounded-[4rem] w-full max-w-2xl p-12 shadow-2xl relative overflow-hidden">
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')" class="absolute top-10 right-10 text-slate-400 hover:text-slate-900">
                <i class="ph ph-x text-2xl"></i>
            </button>

            <div class="mb-10">
                <h3 class="text-3xl font-black text-slate-900 tracking-tighter mb-2">Cargar Dispersión</h3>
                <p class="text-slate-500 font-medium">Seleccione el archivo TXT oficial enviado por la TSS.</p>
            </div>

            <form action="{{ route('dispersion.pensionados.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-8">
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-4 block">Periodo de Dispersión</label>
                        <input type="month" name="periodo" required 
                            class="w-full bg-slate-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white rounded-[24px] px-8 py-5 text-lg font-bold text-slate-800 transition-all shadow-sm">
                    </div>

                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-4 block">Archivo TXT (Layout Pensionados)</label>
                        <div class="relative group">
                            <input type="file" name="archivo_txt" id="file_input" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="border-4 border-dashed border-slate-100 rounded-[40px] p-12 text-center group-hover:border-indigo-100 group-hover:bg-indigo-50/30 transition-all">
                                <div class="w-16 h-16 rounded-3xl bg-slate-50 text-slate-400 mx-auto flex items-center justify-center mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-all shadow-sm">
                                    <i class="ph ph-cloud-arrow-up text-3xl"></i>
                                </div>
                                <p class="text-slate-900 font-black text-lg mb-1" id="file_name">Seleccionar archivo</p>
                                <p class="text-slate-400 text-xs font-medium">Arrastra y suelta o haz clic para buscar</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-6 rounded-[28px] bg-slate-900 text-white text-sm font-black uppercase tracking-[0.2em] hover:bg-black shadow-2xl shadow-slate-200 transition-all flex items-center justify-center gap-4">
                            Iniciar Procesamiento Técnico <i class="ph ph-arrow-right text-xl"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('file_input').onchange = function() {
        document.getElementById('file_name').innerText = this.files[0].name;
    };
</script>
@endsection
