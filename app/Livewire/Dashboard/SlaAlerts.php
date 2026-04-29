<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class SlaAlerts extends Component
{
    use WithPagination;

    public $search = '';
    public $level = ''; // 'critical', 'warning', or empty for all
    public $sortField = 'days';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'level' => ['except' => ''],
        'sortField' => ['except' => 'days'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        // Optimizamos la consulta con un subselect para obtener la fecha de última actividad
        // Esto permite ordenar y filtrar por "Días de inactividad" a nivel de SQL.
        $query = Empresa::query()
            ->select('empresas.*')
            ->addSelect([
                'ultima_fecha' => DB::table('interaccion_empresas')
                    ->select('fecha_contacto')
                    ->whereColumn('empresa_id', 'empresas.id')
                    ->latest('fecha_contacto')
                    ->limit(1)
            ])
            ->with(['latestInteraccion', 'promotor']);

        // Filtro por Búsqueda (Nombre o RNC)
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('rnc', 'like', '%' . $this->search . '%');
            });
        }

        // Obtener los días de inactividad calculados en SQL (MySQL DATEDIFF)
        $query->addSelect(DB::raw('DATEDIFF(NOW(), (SELECT MAX(fecha_contacto) FROM interaccion_empresas WHERE empresa_id = empresas.id)) as days_inactive'));

        // Conteos globales para las Cards (antes de filtros de búsqueda/nivel)
        $totalCritical = (clone $query)->where(function($q) {
            $q->whereRaw('DATEDIFF(NOW(), (SELECT MAX(fecha_contacto) FROM interaccion_empresas WHERE empresa_id = empresas.id)) >= 15')
              ->orWhereNotExists(function($sq) {
                  $sq->select(DB::raw(1))->from('interaccion_empresas')->whereColumn('empresa_id', 'empresas.id');
              });
        })->count();

        $totalWarning = (clone $query)->whereRaw('DATEDIFF(NOW(), (SELECT MAX(fecha_contacto) FROM interaccion_empresas WHERE empresa_id = empresas.id)) BETWEEN 7 AND 14')->count();

        // Aplicamos Filtros de UI
        if ($this->search) {
            $query->where(function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('rnc', 'like', '%' . $this->search . '%');
            });
        }
        
        // Filtro por Nivel de SLA
        if ($this->level === 'critical') {
            $query->where(function($q) {
                $q->whereRaw('DATEDIFF(NOW(), (SELECT MAX(fecha_contacto) FROM interaccion_empresas WHERE empresa_id = empresas.id)) >= 15')
                  ->orWhereNotExists(function($sq) {
                      $sq->select(DB::raw(1))
                         ->from('interaccion_empresas')
                         ->whereColumn('empresa_id', 'empresas.id');
                  });
            });
        } elseif ($this->level === 'warning') {
            $query->whereRaw('DATEDIFF(NOW(), (SELECT MAX(fecha_contacto) FROM interaccion_empresas WHERE empresa_id = empresas.id)) BETWEEN 7 AND 14');
        }

        // Ordenamiento
        if ($this->sortField === 'nombre') {
            $query->orderBy('nombre', $this->sortDirection);
        } elseif ($this->sortField === 'days') {
            $query->orderByRaw('CASE WHEN days_inactive IS NULL THEN 9999 ELSE days_inactive END ' . $this->sortDirection);
        }

        $empresas = $query->paginate($this->perPage);

        return view('livewire.dashboard.sla-alerts', [
            'empresas' => $empresas,
            'totalCritical' => $totalCritical,
            'totalWarning' => $totalWarning
        ]);
    }
}
