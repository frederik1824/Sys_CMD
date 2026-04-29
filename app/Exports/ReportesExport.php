<?php

namespace App\Exports;

use App\Models\Afiliado;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filtros;

    public function __construct(array $filtros)
    {
        $this->filtros = $filtros;
    }

    public function query()
    {
        $query = Afiliado::query()->with(['corte', 'estado', 'responsable']);

        if (!empty($this->filtros['corte_id'])) {
            $query->where('corte_id', $this->filtros['corte_id']);
        }
        if (!empty($this->filtros['estado_id'])) {
            $query->where('estado_id', $this->filtros['estado_id']);
        }
        if (!empty($this->filtros['responsable_id'])) {
            $query->where('responsable_id', $this->filtros['responsable_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cédula',
            'Nombre Completo',
            'Teléfono',
            'Corte',
            'Estado',
            'Responsable',
            'Empresa',
            'Provincia',
            'Municipio',
            'Fecha Registro'
        ];
    }

    public function map($afiliado): array
    {
        return [
            $afiliado->id,
            $afiliado->cedula,
            $afiliado->nombre_completo,
            $afiliado->telefono,
            $afiliado->corte->nombre ?? 'N/A',
            $afiliado->estado->nombre ?? 'N/A',
            $afiliado->responsable->nombre ?? 'N/A',
            $afiliado->empresa,
            $afiliado->provincia,
            $afiliado->municipio,
            $afiliado->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
