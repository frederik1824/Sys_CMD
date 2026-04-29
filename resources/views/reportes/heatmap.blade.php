@extends('layouts.app')

@section('content')
<div class="p-6 lg:p-10 space-y-10 animate-fade-in pb-20">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div class="space-y-2">
            <nav class="flex items-center gap-2 text-[0.65rem] font-black uppercase tracking-[0.2em] text-slate-400">
                <span class="text-primary/60">Análisis Inteligente</span>
                <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                <span class="text-primary text-bold">Monitor Geográfico Global</span>
            </nav>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight leading-none">Mapa de Densidad Logística</h2>
            <p class="text-slate-500 text-sm font-medium">Visualización de cobertura nacional y concentración de afiliados.</p>
        </div>
        
        <div class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-100 shadow-sm">
            <div class="px-4 py-2 text-right">
                <p class="text-[0.6rem] font-black text-slate-400 uppercase">Puntos Geocodificados</p>
                <p class="text-lg font-black text-primary">{{ $puntosMapa->count() }}</p>
            </div>
            <div class="w-px h-8 bg-slate-100"></div>
            <div class="px-4 py-2">
                <p class="text-[0.6rem] font-black text-slate-400 uppercase">Mayor Concentración</p>
                <p class="text-sm font-black text-slate-700">{{ $densidadProvincia->first()->provinciaRel->nombre ?? 'N/D' }}</p>
            </div>
        </div>
    </div>

    {{-- GLOBAL MAP CONTAINER --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden relative group">
        {{-- Map Overlay Legend --}}
        <div class="absolute top-6 left-6 z-[999] space-y-3 pointer-events-none">
            <div class="bg-white/90 backdrop-blur-md p-5 rounded-3xl border border-slate-100 shadow-xl max-w-[240px]">
                <h4 class="text-[0.65rem] font-black text-slate-800 uppercase tracking-widest mb-4">Referencias de Red</h4>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-primary shadow-lg shadow-primary/20"></div>
                        <span class="text-[0.65rem] font-bold text-slate-600 uppercase">Empresa Verificada</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 rounded-full bg-secondary shadow-lg shadow-secondary/20"></div>
                        <span class="text-[0.65rem] font-bold text-slate-600 uppercase">Filial Corporativa</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="global-map" class="h-[700px] w-full z-10"></div>
        
        {{-- Progress Stats Bar --}}
        <div class="absolute bottom-6 left-6 right-6 z-[999] flex gap-4 overflow-x-auto pb-4 no-scrollbar pr-10">
            @foreach($densidadProvincia->take(6) as $p)
            <div class="bg-slate-900/95 backdrop-blur-md p-4 rounded-2xl min-w-[190px] border border-white/10 shadow-2xl hover:bg-slate-800 transition-all cursor-all-scroll">
                <div class="flex justify-between items-center mb-2 gap-4">
                    <span class="text-[0.65rem] font-black text-slate-300 uppercase tracking-widest truncate">{{ $p->provinciaRel->nombre ?? 'N/D' }}</span>
                    <span class="text-[0.8rem] font-black text-white bg-primary/40 px-2 py-0.5 rounded-md min-w-[40px] text-center border border-white/5">{{ $p->total }}</span>
                </div>
                <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden border border-white/5">
                    <div class="bg-gradient-to-r from-primary to-blue-400 h-full shadow-[0_0_10px_rgba(0,100,255,0.4)]" 
                         style="width: {{ ($p->total / $densidadProvincia->max('total')) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Hotspot Lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-sm">
            <h4 class="text-lg font-black text-slate-800 mb-8 flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">analytics</span>
                Densidad por Provincia
            </h4>
            <div class="space-y-6">
                @foreach($densidadProvincia as $p)
                <div class="flex items-center justify-between group cursor-pointer" onclick="focusProvincia('{{ $p->provinciaRel->nombre }}')">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-primary group-hover:text-white transition-all">
                            <span class="text-xs font-black">{{ $loop->iteration }}</span>
                        </div>
                        <span class="text-sm font-black text-slate-700 uppercase tracking-tight">{{ $p->provinciaRel->nombre ?? 'N/D' }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-black text-slate-800">{{ number_format($p->total) }}</p>
                        <p class="text-[0.6rem] font-bold text-slate-400 uppercase">Afiliados</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-sm">
            <h4 class="text-lg font-black text-slate-800 mb-8 flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-500">local_fire_department</span>
                Municipios Hotspots
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($densidadMunicipio as $m)
                <div class="p-4 bg-slate-50 rounded-2xl border border-transparent hover:border-rose-100 transition-all group">
                    <div class="flex justify-between items-start mb-2">
                        <p class="text-[0.7rem] font-black text-slate-800 uppercase leading-none">{{ $m->municipioRel->nombre ?? 'N/D' }}</p>
                        <span class="text-[0.6rem] font-black text-rose-500 bg-rose-50 px-2 py-0.5 rounded-lg">{{ $m->total }}</span>
                    </div>
                    <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-widest">{{ $m->provinciaRel->nombre ?? 'N/D' }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const map = L.map('global-map').setView([18.7357, -70.1627], 8);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markers = L.markerClusterGroup({
            showCoverageOnHover: false,
            zoomToBoundsOnClick: true,
            maxClusterRadius: 50
        });

        const puntos = @json($puntosMapa);
        
        puntos.forEach(p => {
            // Determine marker type and class
            let markerClass = 'pulse-default';
            let markerColor = '#64748b'; // Slate
            
            if (p.es_verificada) {
                markerClass = 'pulse-green';
                markerColor = '#10b981'; // Emerald/Green
            } else if (p.es_filial) {
                markerClass = 'pulse-yellow';
                markerColor = '#f59e0b'; // Amber/Yellow
            }

            const icon = L.divIcon({
                className: 'custom-marker-wrapper',
                html: `<div class="marker-pin ${markerClass}" style="background-color: ${markerColor};"></div>`,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            const marker = L.marker([p.latitude, p.longitude], { icon: icon })
                .bindPopup(`
                    <div class="p-5 min-w-[240px]">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-2.5 h-2.5 rounded-full ${markerClass}" style="background-color: ${markerColor}"></span>
                            <p class="text-[0.65rem] font-black uppercase text-slate-500 tracking-[0.2em]">
                                ${p.es_verificada ? 'Verificada' : (p.es_filial ? 'Sucursal Filial' : 'Estándar')}
                            </p>
                        </div>
                        <h4 class="text-base font-black text-slate-900 mb-3 tracking-tight leading-tight">${p.nombre}</h4>
                        <div class="space-y-2 py-4 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <span class="text-[0.7rem] font-bold text-slate-500 uppercase tracking-wider">Afiliados:</span>
                                <span class="text-sm font-black text-primary">${p.afiliados_count}</span>
                            </div>
                        </div>
                        <a href="/empresas/${p.rnc || p.uuid}?context=executive" class="block w-full text-center py-3 bg-slate-900 text-white rounded-xl text-[0.7rem] font-black uppercase tracking-[0.15em] hover:bg-primary transition-all shadow-lg shadow-slate-200">Ver Ficha CRM</a>
                    </div>
                `);
            markers.addLayer(marker);
        });

        map.addLayer(markers);
        
        if (puntos.length > 0) {
            map.fitBounds(markers.getBounds().pad(0.1));
        }

        window.focusProvincia = (nombre) => {
            // Lógica para filtrar o centrar en provincia si es necesario
            console.log('Centrando en: ' + nombre);
        };
    });
</script>

<style>
    .leaflet-container { background: #f8f9fa; font-family: 'Inter', sans-serif; }
    .leaflet-popup-content-wrapper { 
        background-color: #ffffff !important; 
        border-radius: 24px !important; 
        border: 1px solid #e2e8f0 !important; 
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important; 
        padding: 4px !important;
    }
    .leaflet-popup-content { margin: 0 !important; }
    .leaflet-popup-tip { background-color: #ffffff !important; border-radius: 4px; }
    .marker-cluster-small { background-color: rgba(0, 52, 111, 0.6); }
    .marker-cluster-small div { background-color: rgba(0, 52, 111, 1); color: white; font-weight: 800; font-size: 11px; }
    .marker-cluster-medium { background-color: rgba(0, 96, 172, 0.6); }
    .marker-cluster-medium div { background-color: rgba(0, 96, 172, 1); color: white; font-weight: 800; }
    .no-scrollbar::-webkit-scrollbar { display: none; }

    /* Custom Marker Styles */
    .marker-pin {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    
    .pulse-green {
        animation: pulse-green-anim 2s infinite;
    }
    
    .pulse-yellow {
        animation: pulse-yellow-anim 2s infinite;
    }

    @keyframes pulse-green-anim {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); transform: scale(0.95); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); transform: scale(1.1); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); transform: scale(0.95); }
    }

    @keyframes pulse-yellow-anim {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); transform: scale(0.95); }
        70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); transform: scale(1.1); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); transform: scale(0.95); }
    }
</style>
@endpush
