{{-- DASHBOARD GLOBAL --}}
<x-nav-link route="carnetizacion.dashboard" icon="ph ph-chart-pie" label="Dashboard Principal" />

{{-- GRUPO: ADMISIÓN --}}
@can('manage_affiliates')
<div class="space-y-1">
    <button @click="activeGroup = activeGroup === 'admision' ? '' : 'admision'" 
            :class="activeGroup === 'admision' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
            class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
        <div class="flex items-center gap-4">
            <i class="ph ph-user-plus text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'admision' ? 'text-blue-600' : ''"></i>
            <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'admision' ? 'text-blue-700' : ''">Admisión</span>
        </div>
        <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'admision' ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="activeGroup === 'admision'" x-collapse class="pl-4 space-y-1">
        <x-nav-link route="carnetizacion.import.index" icon="ph ph-upload-simple" label="Importar Excel" />
        <x-nav-link route="carnetizacion.afiliados.cmd" icon="ph ph-identification-card" label="CMD (Paso 1)" />
        <x-nav-link route="carnetizacion.afiliados.call_center" icon="ph ph-headset" label="Entrada Call Center" />
        <x-nav-link route="carnetizacion.afiliados.otros" icon="ph ph-users" label="Otros (Paso 1)" />
        <x-nav-link route="carnetizacion.afiliados.salida_inmediata" icon="ph ph-lightning" label="Salida Inmediata" />
    </div>
</div>
@endcan

{{-- GRUPO: CALL CENTER --}}
@can('manage_calls')
<div class="space-y-1">
    <button @click="activeGroup = activeGroup === 'callcenter' ? '' : 'callcenter'" 
            :class="activeGroup === 'callcenter' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
            class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
        <div class="flex items-center gap-4">
            <i class="ph ph-headset text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'callcenter' ? 'text-blue-600' : ''"></i>
            <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'callcenter' ? 'text-blue-700' : ''">Monitoreo de Cartera</span>
        </div>
        <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'callcenter' ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="activeGroup === 'callcenter'" x-collapse class="pl-4 space-y-1">
        <x-nav-link route="callcenter.dashboard" icon="ph ph-phone-call" label="Panel de Control" />
        <x-nav-link route="callcenter.worklist" icon="ph ph-list-bullets" label="Bandeja de Llamadas" />
    </div>
</div>
@endcan

{{-- GRUPO: LOGÍSTICA --}}
@can('manage_logistics')
<div class="space-y-1">
    <button @click="activeGroup = activeGroup === 'logistica' ? '' : 'logistica'" 
            :class="activeGroup === 'logistica' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
            class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
        <div class="flex items-center gap-4">
            <i class="ph ph-truck text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'logistica' ? 'text-blue-600' : ''"></i>
            <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'logistica' ? 'text-blue-700' : ''">Logística</span>
        </div>
        <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'logistica' ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="activeGroup === 'logistica'" x-collapse class="pl-4 space-y-1">
        <x-nav-link route="carnetizacion.afiliados.index" icon="ph ph-users-four" label="Expedientes (Carnets)" />
        <x-nav-link route="lotes.index" icon="ph ph-package" label="Control de Lotes" />
        <x-nav-link route="cierre.index" icon="ph ph-lock-key" label="Cierre de Cortes" />
        <x-nav-link route="mensajeros.index" icon="ph ph-moped" label="Mensajeros" />
        <x-nav-link route="rutas.index" icon="ph ph-map-trifold" label="Rutas de Entrega" />
        <x-nav-link route="despachos.index" icon="ph ph-paper-plane-tilt" label="Despachos" />
    </div>
</div>
@endcan

{{-- GRUPO: GESTIÓN --}}
@can('manage_administration')
<div class="space-y-1">
    <button @click="activeGroup = activeGroup === 'gestion' ? '' : 'gestion'" 
            :class="activeGroup === 'gestion' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
            class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
        <div class="flex items-center gap-4">
            <i class="ph ph-files text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'gestion' ? 'text-blue-600' : ''"></i>
            <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'gestion' ? 'text-blue-700' : ''">Gestión</span>
        </div>
        <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'gestion' ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="activeGroup === 'gestion'" x-collapse class="pl-4 space-y-1">
        <x-nav-link route="evidencias.index" icon="ph ph-camera" label="Evidencias Digitales" />
        <x-nav-link route="liquidacion.index" icon="ph ph-hand-coins" label="Liquidación" />
    </div>
</div>
@endcan

{{-- GRUPO: REPORTES --}}
@can('view_reports')
<div class="space-y-1">
    <button @click="activeGroup = activeGroup === 'reportes' ? '' : 'reportes'" 
            :class="activeGroup === 'reportes' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
            class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
        <div class="flex items-center gap-4">
            <i class="ph ph-chart-pie-slice text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'reportes' ? 'text-blue-600' : ''"></i>
            <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'reportes' ? 'text-blue-700' : ''">Reportes</span>
        </div>
        <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'reportes' ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="activeGroup === 'reportes'" x-collapse class="pl-4 space-y-1">
        <x-nav-link route="reportes.index" icon="ph ph-chart-line-up" label="Estadísticas" />
        <x-nav-link route="reportes.produccion_traspasos" icon="ph ph-chart-bar" label="Producción Traspasos" />
        <x-nav-link route="reportes.supervision" icon="ph ph-eye" label="Supervisión" />
        <x-nav-link route="reportes.export_center" icon="ph ph-file-csv" label="Centro de Exportación" />
        <x-nav-link route="reportes.heatmap" icon="ph ph-globe-hemisphere-west" label="Mapa Global" />
    </div>
</div>
@endcan

{{-- GRUPO: SISTEMA --}}
@can('manage_system')
<div class="space-y-1">
    <button @click="activeGroup = activeGroup === 'sistema' ? '' : 'sistema'" 
            :class="activeGroup === 'sistema' ? 'text-blue-700 bg-slate-50' : 'text-slate-500'"
            class="w-full flex items-center justify-between px-6 py-3 hover:bg-slate-50 rounded-xl transition-colors group">
        <div class="flex items-center gap-4">
            <i class="ph ph-gear text-[22px] group-hover:text-blue-600 transition-colors" :class="activeGroup === 'sistema' ? 'text-blue-600' : ''"></i>
            <span class="text-[0.75rem] tracking-widest uppercase font-black text-slate-500 group-hover:text-blue-700 transition-colors" :class="activeGroup === 'sistema' ? 'text-blue-700' : ''">Sistema</span>
        </div>
        <i class="ph ph-caret-down text-sm transition-transform duration-300" :class="activeGroup === 'sistema' ? 'rotate-180' : ''"></i>
    </button>
    <div x-show="activeGroup === 'sistema'" x-collapse class="pl-4 space-y-1">
        <x-nav-link route="sistema.empresas.index" icon="ph ph-buildings" label="Empresas" />
        <x-nav-link route="sistema.proveedores.index" icon="ph ph-truck" label="Proveedores" />
        <x-nav-link route="intranet.catalogo.index" icon="ph ph-books" label="Catálogo" />
        <x-nav-link route="admin.access.audit" icon="ph ph-clock-counter-clockwise" label="Auditoría" />
        <x-nav-link route="carnetizacion.sync_center.index" icon="ph ph-arrows-clockwise" label="Sync Center" />
        <x-nav-link route="sistema.backups.index" icon="ph ph-database" label="Copias de Seguridad" />
        <x-nav-link route="admin.updates.index" icon="ph ph-rocket-launch" label="Update Manager" />
        <x-nav-link route="admin.access.index" icon="ph ph-shield-checkered" label="Matriz de Accesos" />
    </div>
</div>
@endcan
