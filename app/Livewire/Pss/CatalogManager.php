<?php

namespace App\Livewire\Pss;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PssCiudad;
use App\Models\PssEspecialidad;
use App\Models\PssGrupo;
use App\Models\PssClinica;
use App\Services\PssManagementService;

class CatalogManager extends Component
{
    use WithPagination;

    public $catalogType = 'ciudades'; // ciudades, especialidades, grupos, clinicas
    public $search = '';
    public $showModal = false;
    public $editingId = null;
    
    public $form = [
        'nombre' => '',
        'ciudad_id' => '',
        'activo' => true
    ];

    protected $queryString = ['catalogType', 'search'];

    public function updatedCatalogType() { $this->resetPage(); }
    public function updatedSearch() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->form = ['nombre' => '', 'ciudad_id' => '', 'activo' => true];
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->editingId = $id;
        $model = $this->getModel();
        $record = $model::find($id);
        
        if ($record) {
            $this->form = [
                'nombre' => $record->nombre,
                'activo' => (bool)$record->activo,
                'ciudad_id' => $record->ciudad_id ?? ''
            ];
            $this->showModal = true;
        }
    }

    public function save()
    {
        $this->validate([
            'form.nombre' => 'required|string|max:255',
            'form.ciudad_id' => $this->catalogType === 'clinicas' ? 'required|exists:pss_ciudades,id' : 'nullable',
        ]);

        $model = $this->getModel();
        
        if ($this->editingId) {
            $record = $model::find($this->editingId);
            $action = 'update';
        } else {
            $record = new $model();
            $action = 'create';
        }

        $record->nombre = $this->form['nombre'];
        $record->activo = $this->form['activo'];
        if ($this->catalogType === 'clinicas') {
            $record->ciudad_id = $this->form['ciudad_id'];
        }
        $record->save();

        // Auditoría
        $service = app(PssManagementService::class);
        $service->logAction($record, $action, null, null, $this->form);

        $this->showModal = false;
        $this->dispatch('notify', ['msg' => 'Catálogo actualizado.', 'type' => 'success']);
    }

    private function getModel()
    {
        return match($this->catalogType) {
            'ciudades' => PssCiudad::class,
            'especialidades' => PssEspecialidad::class,
            'grupos' => PssGrupo::class,
            'clinicas' => PssClinica::class,
        };
    }

    public function render()
    {
        $model = $this->getModel();
        $query = $model::query();

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        if ($this->catalogType === 'clinicas') {
            $query->with('ciudad');
        }

        return view('livewire.pss.catalog-manager', [
            'records' => $query->latest()->paginate(10),
            'ciudades' => PssCiudad::orderBy('nombre')->get()
        ]);
    }
}
