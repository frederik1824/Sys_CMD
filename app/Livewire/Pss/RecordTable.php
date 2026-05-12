<?php

namespace App\Livewire\Pss;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PssMedico;
use App\Models\PssCentro;
use App\Models\PssCiudad;
use App\Models\PssEspecialidad;
use App\Models\PssGrupo;
use App\Models\PssClinica;

class RecordTable extends Component
{
    use WithPagination;

    public $type = 'medicos'; 
    public $search = '';
    public $city_id = '';
    public $specialty_id = '';
    public $group_id = '';
    public $status = '';

    // Form properties
    public $showModal = false;
    public $editingRecordId = null;
    public $form = [
        'nombre' => '',
        'telefono_1' => '',
        'telefono_2' => '',
        'ciudad_id' => '',
        'especialidad_id' => '',
        'grupo_id' => '',
        'clinica_id' => '',
        'estado' => 'activo',
    ];

    protected $queryString = [
        'search' => ['except' => ''],
        'city_id' => ['except' => ''],
        'specialty_id' => ['except' => ''],
        'group_id' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatedSearch() { $this->resetPage(); }
    public function updatedCityId() { $this->resetPage(); }
    public function updatedSpecialtyId() { $this->resetPage(); }
    public function updatedGroupId() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->editingRecordId = null;
        $this->form = [
            'nombre' => '',
            'telefono_1' => '',
            'telefono_2' => '',
            'ciudad_id' => '',
            'especialidad_id' => '',
            'grupo_id' => '',
            'clinica_id' => '',
            'estado' => 'activo',
        ];
        $this->showModal = true;
    }

    public function editRecord($id)
    {
        $this->resetValidation();
        $this->editingRecordId = $id;
        $record = $this->type === 'medicos' ? PssMedico::find($id) : PssCentro::find($id);
        
        if ($record) {
            $this->form = [
                'nombre' => $record->nombre,
                'telefono_1' => $record->telefono_1,
                'telefono_2' => $record->telefono_2,
                'ciudad_id' => $record->ciudad_id,
                'estado' => $record->estado,
            ];

            if ($this->type === 'medicos') {
                $this->form['especialidad_id'] = $record->especialidad_id;
                $this->form['clinica_id'] = $record->clinica_id;
            } else {
                $this->form['grupo_id'] = $record->grupo_id;
            }

            $this->showModal = true;
        }
    }

    public function save()
    {
        $rules = [
            'form.nombre' => 'required|string|max:255',
            'form.ciudad_id' => 'required|exists:pss_ciudades,id',
            'form.estado' => 'required|in:activo,inactivo,depuración',
        ];

        if ($this->type === 'medicos') {
            $rules['form.especialidad_id'] = 'nullable|exists:pss_especialidades,id';
            $rules['form.clinica_id'] = 'nullable|exists:pss_clinicas,id';
        } else {
            $rules['form.grupo_id'] = 'nullable|exists:pss_grupos,id';
        }

        $this->validate($rules);

        if ($this->editingRecordId) {
            $record = $this->type === 'medicos' ? PssMedico::find($this->editingRecordId) : PssCentro::find($this->editingRecordId);
        } else {
            $record = $this->type === 'medicos' ? new PssMedico() : new PssCentro();
        }

        $record->fill([
            'nombre' => $this->form['nombre'],
            'telefono_1' => $this->form['telefono_1'],
            'telefono_2' => $this->form['telefono_2'],
            'ciudad_id' => $this->form['ciudad_id'],
            'estado' => $this->form['estado'],
        ]);

        if ($this->type === 'medicos') {
            $record->especialidad_id = $this->form['especialidad_id'] ?: null;
            $record->clinica_id = $this->form['clinica_id'] ?: null;
        } else {
            $record->grupo_id = $this->form['grupo_id'] ?: null;
        }

        $isNew = !$this->editingRecordId;
        $record->save();

        // Registrar Auditoría
        $service = app(PssManagementService::class);
        $service->logAction($record, $isNew ? 'create' : 'update', null, null, $this->form);

        $this->showModal = false;
        $this->dispatch('notify', [
            'msg' => $isNew ? 'Nuevo registro creado con éxito.' : 'Registro actualizado correctamente.',
            'type' => 'success'
        ]);
    }

    public function deleteRecord($id)
    {
        $record = $this->type === 'medicos' ? PssMedico::find($id) : PssCentro::find($id);
        if ($record) {
            $record->delete();
            $this->dispatch('notify', ['msg' => 'Registro eliminado con éxito.', 'type' => 'success']);
        }
    }

    public function render()
    {
        $query = $this->type === 'medicos' 
            ? PssMedico::query()->with(['ciudad', 'especialidad', 'clinica'])
            : PssCentro::query()->with(['ciudad', 'grupo']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('telefono_1', 'like', '%' . $this->search . '%')
                  ->orWhere('telefono_2', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->city_id) {
            $query->where('ciudad_id', $this->city_id);
        }

        if ($this->status) {
            $query->where('estado', $this->status);
        }

        if ($this->type === 'medicos' && $this->specialty_id) {
            $query->where('especialidad_id', $this->specialty_id);
        }

        if ($this->type === 'centros' && $this->group_id) {
            $query->where('grupo_id', $this->group_id);
        }

        return view('livewire.pss.record-table', [
            'records' => $query->latest()->paginate(12),
            'ciudades' => PssCiudad::where('activo', true)->orderBy('nombre')->get(),
            'especialidades' => PssEspecialidad::where('activo', true)->orderBy('nombre')->get(),
            'grupos' => PssGrupo::where('activo', true)->orderBy('nombre')->get(),
            'clinicas' => PssClinica::orderBy('nombre')->get(),
        ]);
    }
}
