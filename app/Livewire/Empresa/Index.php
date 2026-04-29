<?php

namespace App\Livewire\Empresa;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Empresa;
use App\Models\Provincia;
use App\Models\Municipio;

class Index extends Component
{
    use WithPagination;

    // Filter properties
    public $search = '';
    public $provincia = '';
    public $municipio = '';
    public $status = '';
    public $cedula = ''; // Filter by affiliate ID
    
    // UI state
    public $activeTab = 'list';
    
    // Pagination reset on search
    public function updatingSearch() { $this->resetPage(); }
    public function updatingProvincia() { $this->resetPage(); $this->municipio = ''; }

    public function render()
    {
        $query = Empresa::query()->with(['provinciaRel', 'municipioRel'])
            ->withCount('afiliados');

        if (auth()->check() && auth()->user()->responsable_id && !auth()->user()->hasRole(['Admin'])) {
            $assignedProvincias = \Illuminate\Support\Facades\DB::table('provincia_responsable')
                ->where('responsable_id', auth()->user()->responsable_id)
                ->pluck('provincia_id');
            $query->whereIn('provincia_id', $assignedProvincias);
        }

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('rnc', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->cedula) {
            $query->whereHas('afiliados', function($q) {
                $q->where('cedula', 'like', '%' . $this->cedula . '%');
            });
        }

        if ($this->provincia) $query->where('provincia_id', $this->provincia);
        if ($this->municipio) $query->where('municipio_id', $this->municipio);
        
        if ($this->status === 'reales') $query->where('es_verificada', true);
        elseif ($this->status === 'filiales') $query->where('es_filial', true);
        elseif ($this->status === 'no_verificadas') $query->where('es_verificada', false);

        $empresas = $query->paginate(12);

        // Stats for cards
        $baseStatsQuery = clone $query;
        // Stats for cards based on filtered query avoiding double conditions
        $stats = [
            'total' => (clone $baseStatsQuery)->count(),
            'reales' => (clone $baseStatsQuery)->where('es_verificada', true)->count(),
            'filiales' => (clone $baseStatsQuery)->where('es_filial', true)->count(),
        ];

        return view('livewire.empresa.index', [
            'empresas' => $empresas,
            'provincias' => Provincia::orderBy('nombre')->get(),
            'municipios' => $this->provincia ? Municipio::where('provincia_id', $this->provincia)->orderBy('nombre')->get() : [],
            'stats' => $stats,
            'mapMarkers' => $this->activeTab === 'map' ? $query->get(['uuid', 'nombre', 'latitude', 'longitude', 'direccion']) : collect([])
        ]);
    }
}
