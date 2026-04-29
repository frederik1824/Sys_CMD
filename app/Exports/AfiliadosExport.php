<?php

namespace App\Exports;

use App\Models\Afiliado;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AfiliadosExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Afiliado::query()->with(['estado', 'responsable', 'corte', 'empresaModel']);

        if (!empty($this->filters['search'])) {
            $query->where(function($q) {
                $q->where('nombre_completo', 'like', '%' . $this->filters['search'] . '%')
                  ->orWhere('cedula', 'like', '%' . $this->filters['search'] . '%');
            });
        }

        if (!empty($this->filters['estado_id'])) {
            $query->where('estado_id', $this->filters['estado_id']);
        }

        if (!empty($this->filters['corte_id'])) {
            $query->where('corte_id', $this->filters['corte_id']);
        }

        if (!empty($this->filters['responsable_id'])) {
            $query->where('responsable_id', $this->filters['responsable_id']);
        }

        if (!empty($this->filters['rnc_empresa'])) {
            $query->where('rnc_empresa', 'like', '%' . $this->filters['rnc_empresa'] . '%');
        }

        if (!empty($this->filters['sexo'])) {
            $query->where('sexo', $this->filters['sexo']);
        }

        if (!empty($this->filters['lote_id'])) {
            $query->where('lote_id', $this->filters['lote_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'Cédula',
            'Sexo',
            'Póliza',
            'Empresa',
            'RNC Empresa',
            'Estado Actual',
            'Corte',
            'Lote',
            'Responsable',
            'Fecha de Creación'
        ];
    }

    public function map($afiliado): array
    {
        return [
            $afiliado->id,
            $afiliado->nombre_completo,
            $afiliado->cedula,
            $afiliado->sexo === 'M' ? 'Masculino' : ($afiliado->sexo === 'F' ? 'Femenino' : 'N/A'),
            $afiliado->poliza,
            $afiliado->empresa,
            $afiliado->rnc_empresa,
            $afiliado->estado->nombre ?? 'N/A',
            $afiliado->corte->nombre ?? 'N/A',
            $afiliado->lote->nombre ?? 'N/A',
            $afiliado->responsable->nombre ?? 'Sin Asignar',
            $afiliado->created_at->format('d/m/Y'),
        ];
    }
}
