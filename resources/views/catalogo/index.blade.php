@extends('layouts.app')

@section('content')
<div class="space-y-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div class="animate-in fade-in slide-in-from-left-4 duration-500">
            <h2 class="text-4xl font-headline font-black text-on-surface tracking-tighter">Catálogos Maestros</h2>
            <p class="text-on-surface-variant text-sm mt-2 font-medium">Gestión y configuración global de los parámetros del sistema.</p>
        </div>
        <div class="bg-primary-container/20 px-6 py-3 rounded-2xl border border-primary/10 animate-in fade-in slide-in-from-right-4 duration-500">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-3xl font-light">settings_suggest</span>
                <div>
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-400">Estado del Sistema</h4>
                    <p class="text-xs font-bold text-on-surface">Base de Datos Optimizada</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog Hub Bento Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        
        <!-- Corte (Periodos) -->
        <a href="{{ route('cortes.index') }}" class="group bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:border-primary/20 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-primary/5 rounded-full group-hover:scale-125 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-3xl">calendar_month</span>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Cortes / Períodos</h3>
                <p class="text-sm text-slate-500 leading-relaxed font-medium mb-12">Administración de los cierres mensuales y generación de cortes operativos.</p>
                <div class="flex items-center justify-between mt-auto">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">{{ \App\Models\Corte::count() }} Registros</span>
                    <span class="material-symbols-outlined text-indigo-300 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </div>
        </a>

        <!-- Responsables -->
        <a href="{{ route('responsables.index') }}" class="group bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:border-primary/20 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-orange-500/5 rounded-full group-hover:scale-125 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-orange-600 group-hover:text-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-3xl">engineering</span>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Responsables</h3>
                <p class="text-sm text-slate-500 leading-relaxed font-medium mb-12">Entidades o personal encargado de la distribución (ARS-CMD, Safesure).</p>
                <div class="flex items-center justify-between mt-auto">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">{{ \App\Models\Responsable::count() }} Registros</span>
                    <span class="material-symbols-outlined text-orange-300 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </div>
        </a>

        <!-- Proveedores -->
        <a href="{{ route('proveedores.index') }}" class="group bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:border-primary/20 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-blue-500/5 rounded-full group-hover:scale-125 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-3xl">local_shipping</span>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Proveedores</h3>
                <p class="text-sm text-slate-500 leading-relaxed font-medium mb-12">Empresas externas para servicios de courier y entrega final.</p>
                <div class="flex items-center justify-between mt-auto">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">{{ \App\Models\Proveedor::count() }} Registros</span>
                    <span class="material-symbols-outlined text-blue-300 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </div>
        </a>

        <!-- Estados -->
        <a href="{{ route('estados.index') }}" class="group bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:border-primary/20 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-emerald-500/5 rounded-full group-hover:scale-125 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-3xl">sync</span>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Estados de Flujo</h3>
                <p class="text-sm text-slate-500 leading-relaxed font-medium mb-12">Definición de los estados del carnet en su ciclo de vida.</p>
                <div class="flex items-center justify-between mt-auto">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">{{ \App\Models\Estado::count() }} Registros</span>
                    <span class="material-symbols-outlined text-emerald-300 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </div>
        </a>

        <!-- Empresas -->
        <a href="{{ route('empresas.index') }}" class="group bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:border-primary/20 hover:shadow-2xl hover:shadow-primary/5 transition-all duration-300 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-amber-500/5 rounded-full group-hover:scale-125 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-600 group-hover:text-white transition-colors shadow-sm">
                    <span class="material-symbols-outlined text-3xl">apartment</span>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Empresas</h3>
                <p class="text-sm text-slate-500 leading-relaxed font-medium mb-12">Directorio maestro de clientes institucionales y corporativos.</p>
                <div class="flex items-center justify-between mt-auto">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-400">{{ \App\Models\Empresa::count() }} Registros</span>
                    <span class="material-symbols-outlined text-amber-300 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </div>
        </a>

        <!-- Usuarios (Solo Administrador) -->
        @hasanyrole('Admin')
        <a href="{{ route('usuarios.index') }}" class="group bg-slate-900 rounded-3xl p-8 shadow-xl border border-slate-800 hover:shadow-2xl hover:shadow-slate-500/10 transition-all duration-300 relative overflow-hidden text-white">
            <div class="absolute -top-4 -right-4 w-32 h-32 bg-white/5 rounded-full group-hover:scale-125 transition-transform"></div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-white/10 text-white rounded-2xl flex items-center justify-center mb-6 group-hover:bg-white group-hover:text-slate-900 transition-colors shadow-sm backdrop-blur-md">
                    <span class="material-symbols-outlined text-3xl">verified_user</span>
                </div>
                <h3 class="text-xl font-black mb-2">Usuarios y Roles</h3>
                <p class="text-sm text-slate-400 leading-relaxed font-medium mb-12">Control de acceso, perfiles de seguridad y gestión de personal.</p>
                <div class="flex items-center justify-between mt-auto">
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-slate-500">{{ \App\Models\User::count() }} Cuentas</span>
                    <span class="material-symbols-outlined text-slate-600 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </div>
            </div>
        </a>
        @endhasanyrole

    </div>

    <!-- Quick Maintenance Panel -->
    <div class="bg-surface-container-low p-8 rounded-[40px] border border-slate-200/50">
        <h3 class="text-lg font-black text-slate-800 mb-8 uppercase tracking-tighter flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">analytics</span> Limpieza y Optimizaciones
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-6 rounded-3xl border border-slate-100 flex items-center justify-between group hover:border-primary/20 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">location_off</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-800">Normalizar Direcciones</h4>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Ejecutar Corrector de Texto</p>
                    </div>
                </div>
                <form action="{{ route('afiliados.sanitize') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">Ejecutar</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-100 flex items-center justify-between group hover:border-primary/20 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">database</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-800">Enriquecer Empresas</h4>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Cruzar geolocalización</p>
                    </div>
                </div>
                <a href="{{ route('empresas.enrich') }}" class="bg-slate-100 text-slate-600 px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">Ir a Módulo</a>
            </div>

            <div class="bg-primary/5 p-6 rounded-3xl border border-primary/10 flex items-center justify-between group hover:border-primary/30 transition-all">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary/10 text-primary rounded-full flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">cloud_sync</span>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-slate-800">Sincronizar Cloud</h4>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Traer catálogos de Firebase</p>
                    </div>
                </div>
                <form action="{{ route('catalogo.sync_firebase') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-slate-900 transition-all">Sincronizar Todo</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
