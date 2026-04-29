@extends('layouts.app')
@section('content')
    <div class="p-8 space-y-6">
        <!-- Page Header & Bulk Actions -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-500 text-3xl">verified_user</span>
                    Salida Inmediata (Empresas Verificadas)
                </h1>
                <p class="text-on-surface-variant text-[0.875rem] mt-1">Listado priorizado de afiliados listos para despacho inmediato por pertenecer a empresas verificadas.</p>
            </div>
            <div class="flex items-center gap-3">
                <div id="bulk-actions-wrapper" class="hidden animate-in fade-in zoom-in duration-300">
                    <div class="flex items-center bg-primary/5 p-1 rounded-xl border border-primary/20 shadow-sm">
                        <button type="button" onclick="openAssignModal()" class="px-4 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white rounded-lg transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">person_add</span> Asignar
                        </button>
                        <div class="w-[1px] h-4 bg-primary/20 mx-1"></div>
                        <button type="button" onclick="openStatusModal()" class="px-4 py-2 text-xs font-bold text-primary hover:bg-primary hover:text-white rounded-lg transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">sync</span> Estado
                        </button>
                    </div>
                </div>
                <a href="{{ route('afiliados.export', array_merge(request()->all(), ['segment' => 'SalidaInmediata'])) }}" class="px-5 py-2.5 bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg hover:bg-slate-800 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Exportar XLSX
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <form id="filterForm" method="GET" action="{{ route('afiliados.salida_inmediata') }}" class="bg-surface-container-low p-4 rounded-xl flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por Nombre / Cédula / Expediente" class="w-full appearance-none bg-surface-container-lowest border-none rounded-lg text-xs font-medium px-4 py-2.5 focus:ring-2 ring-blue-500/10 shadow-sm border border-slate-100">
            </div>

            <div class="w-full md:w-auto min-w-[250px] relative">
                <select name="empresa_id" id="filter_empresa" class="w-full appearance-none bg-surface-container-lowest border border-slate-100 rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10 shadow-sm">
                    <option value="">Empresa: Todas</option>
                    @foreach(\App\Models\Empresa::where('es_verificada', true)->orderBy('nombre')->get() as $e)
                        <option value="{{ $e->id }}" {{ request('empresa_id') == $e->id ? 'selected' : '' }}>{{ $e->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-12 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">corporate_fare</span>
            </div>

            <div class="w-full md:w-auto min-w-[160px] relative">
                <select name="estado_id" class="w-full appearance-none bg-surface-container-lowest border border-slate-100 rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10 shadow-sm">
                    <option value="">Estado: Todos</option>
                    @foreach(\App\Models\Estado::all() as $est)
                        <option value="{{ $est->id }}" {{ request('estado_id') == $est->id ? 'selected' : '' }}>{{ $est->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">sync</span>
            </div>

            <div class="w-full md:w-auto min-w-[150px] relative">
                <select name="corte_id" class="w-full appearance-none bg-surface-container-lowest border border-slate-100 rounded-lg text-xs font-medium px-4 py-2.5 pr-10 focus:ring-2 ring-blue-500/10 shadow-sm">
                    <option value="">Corte: Todos</option>
                    @foreach(\App\Models\Corte::all() as $c)
                        <option value="{{ $c->id }}" {{ request('corte_id') == $c->id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                    @endforeach
                </select>
                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
            </div>

            <button type="submit" class="bg-primary text-white p-2.5 rounded-lg hover:bg-primary-container transition-colors shadow-sm">
                <span class="material-symbols-outlined text-xl">search</span>
            </button>
            <a href="{{ route('afiliados.salida_inmediata') }}" class="bg-surface-container-high text-on-surface-variant p-2.5 rounded-lg hover:bg-slate-200 transition-colors shadow-sm border border-slate-200" title="Limpiar Filtros">
                <span class="material-symbols-outlined text-xl">clear_all</span>
            </a>
        </form>

        <!-- Table Container -->
        <div id="tableContainer" class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm border border-slate-100 transition-opacity duration-300">
            @include('afiliados.partials.salida_inmediata_table')
        </div>
    </div>

    <!-- Modals (Reusing from index for consistency) -->
    <div id="assignModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form method="POST" action="{{ route('afiliados.bulk_assign') }}" id="assignForm" class="bg-surface-container-lowest p-6 rounded-2xl shadow-lg w-full max-w-md border border-slate-100">
            @csrf
            <input type="hidden" name="segment" value="SalidaInmediata">
            <h3 class="text-xl font-bold mb-4 text-on-surface">Asignar Responsable</h3>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-2">Responsable</label>
                <select name="responsable_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Seleccione uno...</option>
                    @foreach(\App\Models\Responsable::all() as $resp)
                        <option value="{{ $resp->id }}">{{ $resp->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div id="hiddenSelectedInputs"></div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeAssignModal()" class="px-4 py-2 hover:bg-slate-100 rounded-lg text-slate-600 font-semibold text-sm">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-container transition-colors">Confirmar</button>
            </div>
        </form>
    </div>

    <div id="statusModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <form method="POST" action="{{ route('afiliados.bulk_status') }}" id="statusForm" class="bg-surface-container-lowest p-6 rounded-2xl shadow-lg w-full max-w-md border border-slate-100">
            @csrf
            <input type="hidden" name="segment" value="SalidaInmediata">
            <h3 class="text-xl font-bold mb-4 text-on-surface">Cambiar Estado Masivo</h3>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-2">Nuevo Estado</label>
                <select name="estado_id" required class="w-full bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-primary p-3 text-sm">
                    <option value="">Seleccione uno...</option>
                    @foreach(\App\Models\Estado::all() as $est)
                        <option value="{{ $est->id }}" {{ $est->id == 9 ? 'selected' : '' }}>{{ $est->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div id="hiddenStatusSelectedInputs"></div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 hover:bg-slate-100 rounded-lg text-slate-600 font-semibold text-sm">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary-container transition-colors">Confirmar</button>
            </div>
        </form>
    </div>

    <script>
        const selectedIds = new Set();
        const bulkActionsWrapper = document.getElementById('bulk-actions-wrapper');
        const filterForm = document.getElementById('filterForm');
        const tableContainer = document.getElementById('tableContainer');
        let currentSort = "{{ request('sort', 'nombre') }}";
        let currentDirection = "{{ request('direction', 'asc') }}";

        // Initialize Tom Select for filters
        new TomSelect('#filter_empresa', {
            create: false,
            sortField: { field: "text", direction: "asc" },
            placeholder: 'Buscar empresa...',
            onChange: () => fetchResults()
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => { clearTimeout(timeout); func(...args); };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        async function fetchResults() {
            tableContainer.style.opacity = '0.5';
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            params.set('sort', currentSort);
            params.set('direction', currentDirection);

            const url = `${window.location.pathname}?${params.toString()}`;
            
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                tableContainer.innerHTML = html;
                window.history.pushState({}, '', url);
                
                // Re-bind select all
                const selectAll = document.getElementById('selectAll');
                if (selectAll) {
                    selectAll.checked = Array.from(document.querySelectorAll('.affiliate-checkbox')).every(cb => selectedIds.has(cb.value)) && document.querySelectorAll('.affiliate-checkbox').length > 0;
                }
            } catch (error) {
                console.error('Error fetching results:', error);
            } finally {
                tableContainer.style.opacity = '1';
                updateBulkActionsVisibility();
            }
        }

        const debouncedFetch = debounce(fetchResults, 300);

        filterForm.addEventListener('input', (e) => {
            if (e.target.name === 'search') debouncedFetch();
        });

        filterForm.addEventListener('change', (e) => {
            if (e.target.tagName === 'SELECT') fetchResults();
        });

        filterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            fetchResults();
        });

        function handleSort(column) {
            if (currentSort === column) {
                currentDirection = currentDirection === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort = column;
                currentDirection = 'asc';
            }
            fetchResults();
        }

        function updateBulkActionsVisibility() {
            const checkboxes = document.querySelectorAll('.affiliate-checkbox');
            checkboxes.forEach(cb => {
                if (selectedIds.has(cb.value)) cb.checked = true;
            });

            if (selectedIds.size > 0) bulkActionsWrapper.classList.remove('hidden');
            else bulkActionsWrapper.classList.add('hidden');
        }

        document.addEventListener('change', function(e) {
            if (e.target && e.target.id === 'selectAll') {
                const checkboxes = document.querySelectorAll('.affiliate-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = e.target.checked;
                    if (cb.checked) selectedIds.add(cb.value);
                    else selectedIds.delete(cb.value);
                });
                updateBulkActionsVisibility();
            }
            if (e.target && e.target.classList.contains('affiliate-checkbox')) {
                const id = e.target.value;
                if (e.target.checked) selectedIds.add(id);
                else selectedIds.delete(id);
                updateBulkActionsVisibility();
            }
        });

        function openAssignModal() {
            const hiddenInputsContainer = document.getElementById('hiddenSelectedInputs');
            hiddenInputsContainer.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });
            document.getElementById('assignModal').classList.remove('hidden');
            document.getElementById('assignModal').classList.add('flex');
        }

        function openStatusModal() {
            const hiddenInputsContainer = document.getElementById('hiddenStatusSelectedInputs');
            hiddenInputsContainer.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'selected[]';
                input.value = id;
                hiddenInputsContainer.appendChild(input);
            });
            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusModal').classList.add('flex');
        }

        function closeAssignModal() { document.getElementById('assignModal').classList.add('hidden'); document.getElementById('assignModal').classList.remove('flex'); }
        function closeStatusModal() { document.getElementById('statusModal').classList.add('hidden'); document.getElementById('statusModal').classList.remove('flex'); }
        
        // Handle pagination clicks
        document.addEventListener('click', function(e) {
            const paginationLink = e.target.closest('#pagination-container a');
            if (paginationLink) {
                e.preventDefault();
                const url = new URL(paginationLink.href);
                const params = new URLSearchParams(url.search);
                
                // Add current filters to pagination link
                const formData = new FormData(filterForm);
                for (let [key, value] of formData.entries()) {
                    if (value && !params.has(key)) params.set(key, value);
                }
                if (!params.has('sort')) params.set('sort', currentSort);
                if (!params.has('direction')) params.set('direction', currentDirection);

                const finalUrl = `${window.location.pathname}?${params.toString()}`;
                
                fetch(finalUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                    window.history.pushState({}, '', finalUrl);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }
        });
    </script>
@endsection
