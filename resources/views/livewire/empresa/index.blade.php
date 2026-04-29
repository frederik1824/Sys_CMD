<div class="space-y-6">
    {{-- TAB TOGGLES & FILTERS (Fusionado con la tabla debajo) --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shadow-inner">
            <button wire:click="$set('activeTab', 'list')" @class(['px-6 py-2.5 rounded-xl text-[0.65rem] font-black uppercase tracking-widest transition-all flex items-center gap-2', 'bg-white text-primary shadow-sm' => $activeTab === 'list', 'text-slate-400 hover:text-slate-600' => $activeTab !== 'list'])>
                <span class="material-symbols-outlined text-base">format_list_bulleted</span>
                Listado de Registros
            </button>
            <button wire:click="$set('activeTab', 'map')" @class(['px-6 py-2.5 rounded-xl text-[0.65rem] font-black uppercase tracking-widest transition-all flex items-center gap-2', 'bg-white text-secondary shadow-sm' => $activeTab === 'map', 'text-slate-400 hover:text-slate-600' => $activeTab !== 'map'])>
                <span class="material-symbols-outlined text-base">map</span>
                Vista Geográfica
            </button>
        </div>
        
        <div class="text-[0.65rem] font-black text-slate-400 uppercase tracking-widest bg-slate-50 px-4 py-2 rounded-xl border border-slate-100">
            Mostrando: <span class="text-slate-800">{{ $empresas->total() }} Entidades</span>
        </div>
    </div>

    {{-- Filter Toolbar --}}
    <div class="bg-white shadow-sm rounded-[2.5rem] border border-slate-100 overflow-hidden">
        <div class="p-4 lg:p-6 flex flex-col lg:flex-row gap-4 items-center bg-slate-50/20">
            {{-- Unified Search --}}
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por Nombre Corporativo o RNC..." 
                    class="w-full pl-14 pr-6 py-4 bg-white border-slate-100 rounded-[1.5rem] shadow-sm focus:ring-4 focus:ring-primary/5 focus:border-primary text-sm font-bold text-slate-600 transition-all">
            </div>

            <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
                {{-- Geo Filters --}}
                <div class="flex gap-2 w-full sm:w-auto">
                    <select wire:model.live="provincia" class="h-14 bg-white border-slate-100 rounded-2xl shadow-sm focus:ring-primary/5 focus:border-primary text-xs font-black text-slate-600 px-5 min-w-[130px] appearance-none cursor-pointer">
                        <option value="">Provincia</option>
                        @foreach($provincias as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="municipio" class="h-14 bg-white border-slate-100 rounded-2xl shadow-sm focus:ring-primary/5 focus:border-primary text-xs font-black text-slate-600 px-5 min-w-[130px] appearance-none cursor-pointer">
                        <option value="">Municipio</option>
                        @foreach($municipios as $m)
                            <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="status" class="h-14 bg-white border-slate-100 rounded-2xl shadow-sm focus:ring-primary/5 focus:border-primary text-xs font-black text-slate-600 px-5 min-w-[130px] appearance-none cursor-pointer">
                        <option value="">Clasificación</option>
                        <option value="reales">Verificadas</option>
                        <option value="filiales">Sucursales</option>
                        <option value="no_verificadas">No Verificadas</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- LIST TAB CONTENT --}}
        <div x-show="$wire.activeTab === 'list'" class="overflow-x-auto min-h-[500px] animate-fade-in">
            <table class="w-full text-left border-separate border-spacing-y-0">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-6 text-[0.65rem] font-black text-slate-400 uppercase tracking-widest border-b border-slate-50">Identificación Corporativa</th>
                        <th class="px-8 py-6 text-[0.65rem] font-black text-slate-400 uppercase tracking-widest text-center border-b border-slate-50">Nómina Activa</th>
                        <th class="px-8 py-6 text-[0.65rem] font-black text-slate-400 uppercase tracking-widest text-right border-b border-slate-50">Gestión</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($empresas as $empresa)
                        <tr class="hover:bg-slate-50/40 transition-all duration-300 group cursor-pointer" onclick="window.location='{{ route('empresas.show', $empresa) }}'">
                            <td class="px-8 py-7">
                                <div class="flex items-center gap-5">
                                    <div class="relative">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-500 font-black text-xl shadow-inner group-hover:from-primary/10 group-hover:to-primary/5 group-hover:text-primary transition-all duration-500">
                                            {{ strtoupper(substr($empresa->nombre, 0, 1)) }}
                                        </div>
                                        @if($empresa->es_verificada)
                                        <div class="absolute -right-1 -bottom-1 w-6 h-6 bg-blue-500 border-4 border-white rounded-full flex items-center justify-center text-white shadow-sm">
                                            <span class="material-symbols-outlined text-[10px] font-bold">verified</span>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="space-y-1">
                                        <h5 class="text-sm font-black text-slate-800 leading-tight group-hover:text-primary transition-colors">{{ $empresa->nombre }}</h5>
                                        <div class="flex items-center gap-3">
                                            <span class="text-[0.65rem] font-bold text-slate-400 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">fingerprint</span>
                                                {{ $empresa->rnc ?? 'S/RNC' }}
                                            </span>
                                            <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                            <span class="text-[0.65rem] font-bold text-slate-500 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[12px]">map</span>
                                                {{ $empresa->provinciaRel->nombre ?? 'N/A' }}
                                            </span>
                                            
                                            {{-- Data Quality Indicator --}}
                                            @php $quality = $empresa->data_quality; @endphp
                                            <div class="flex items-center gap-2 min-w-[100px] mt-1" title="Calidad de Datos: {{ $quality->score }}%">
                                                <div class="flex-1 h-1 bg-slate-100 rounded-full overflow-hidden border border-slate-200/50">
                                                    <div class="h-full bg-{{ $quality->color }}-500 shadow-[0_0_8px_rgba(var(--tw-color-{{ $quality->color }}-500),0.4)]" style="width: {{ $quality->score }}%"></div>
                                                </div>
                                                <span class="text-[0.55rem] font-black text-{{ $quality->color }}-600 uppercase tracking-tighter">{{ $quality->score }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-7 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="text-lg font-black text-slate-800 tracking-tighter">{{ number_format($empresa->afiliados_count) }}</span>
                                    <span class="text-[0.55rem] font-black text-slate-400 uppercase tracking-widest italic">Afiliados</span>
                                </div>
                            </td>
                            <td class="px-8 py-7 text-right">
                                <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 text-slate-400 group-hover:bg-primary group-hover:text-white transition-all shadow-sm">
                                    <span class="material-symbols-outlined text-lg">chevron_right</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-20 text-center opacity-50">
                                <span class="material-symbols-outlined text-6xl mb-4">search_off</span>
                                <p class="text-sm font-bold uppercase tracking-widest">No se encontraron entidades con estos filtros</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-8 py-4 border-t border-slate-50">
                {{ $empresas->links() }}
            </div>
        </div>

        {{-- MAP TAB CONTENT --}}
        <div x-show="$wire.activeTab === 'map'" class="animate-fade-in relative" x-cloak>
            <div id="map" wire:ignore class="h-[600px] w-full rounded-b-[2.5rem] bg-slate-100 z-10"></div>
            
            <div class="absolute top-6 right-6 z-20 space-y-2 pointer-events-none">
                <div class="bg-white/90 backdrop-blur-md p-4 rounded-3xl border border-slate-100 shadow-2xl pointer-events-auto">
                    <h4 class="text-[0.65rem] font-black text-slate-600 uppercase tracking-widest mb-3">Distribución Geográfica</h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-primary"></span>
                            <span class="text-[0.65rem] font-bold text-slate-500">Entidad Registrada</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let map;
            let markers = [];

            const initMap = () => {
                const mapEl = document.getElementById('map');
                if (!mapEl || map) return;

                map = L.map('map').setView([18.7357, -70.1627], 8); // Rep. Dom.
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                updateMarkers();
            };

            const updateMarkers = () => {
                if (!map) return;
                
                // Clear existing markers
                markers.forEach(m => map.removeLayer(m));
                markers = [];

                const mapMarkers = @json($mapMarkers);
                
                mapMarkers.forEach(data => {
                    if (data.latitude && data.longitude) {
                        // Determine marker type and class
                        let markerClass = 'pulse-default';
                        let markerColor = '#64748b'; // Slate default
                        
                        if (data.es_verificada) {
                            markerClass = 'pulse-green';
                            markerColor = '#10b981'; // Emerald/Green
                        } else if (data.es_filial) {
                            markerClass = 'pulse-yellow';
                            markerColor = '#f59e0b'; // Amber/Yellow
                        }

                        const icon = L.divIcon({
                            className: 'custom-marker-wrapper',
                            html: `<div class="marker-pin ${markerClass}" style="background-color: ${markerColor}; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);"></div>`,
                            iconSize: [16, 16],
                            iconAnchor: [8, 8]
                        });

                        const marker = L.marker([data.latitude, data.longitude], { icon: icon })
                            .bindPopup(`
                                <div class="min-w-[180px]">
                                    <p class="text-[0.6rem] font-black uppercase text-slate-400 tracking-widest mb-1">
                                        ${data.es_verificada ? 'Verificada' : (data.es_filial ? 'Sucursal Filial' : 'Estándar')}
                                    </p>
                                    <h4 class="text-sm font-black text-slate-800 mb-2 leading-tight">${data.nombre}</h4>
                                    <p class="text-[0.65rem] font-medium text-slate-500 mb-3 line-clamp-2">${data.direccion}</p>
                                    <a href="/empresas/${data.uuid}" class="block text-center py-2 bg-primary text-white rounded-lg text-[0.65rem] font-black uppercase tracking-widest hover:bg-slate-900 transition-colors">Ver Perfil</a>
                                </div>
                            `)
                            .addTo(map);
                        markers.push(marker);
                    }
                });

                if (markers.length > 0) {
                    const group = new L.featureGroup(markers);
                    map.fitBounds(group.getBounds().pad(0.1));
                }
            };

            Livewire.on('active-tab-updated', (tab) => {
                if (tab === 'map') {
                    setTimeout(initMap, 100);
                }
            });

            // Re-render markers when filters change
            Livewire.on('markers-updated', () => {
                updateMarkers();
            });

            // Watch specific wire:click
            setInterval(() => {
                if (Livewire.find('{{ $this->getId() }}').activeTab === 'map') {
                    initMap();
                }
            }, 500);
        });
    </script>
    <style>
        .leaflet-container { font-family: inherit; }
        .leaflet-popup-content-wrapper { 
            background-color: #ffffff !important; 
            border-radius: 20px !important; 
            border: 1px solid #e2e8f0 !important; 
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1) !important;
            padding: 2px !important;
        }
        .leaflet-popup-content { margin: 12px 16px !important; }
        .leaflet-popup-tip { background-color: #ffffff !important; }

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

        .pulse-green { animation: pulse-green-anim 2s infinite; }
        .pulse-yellow { animation: pulse-yellow-anim 2s infinite; }
        .marker-pin { border-radius: 50%; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    </style>
    @endpush
</div>
