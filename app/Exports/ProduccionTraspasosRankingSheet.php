<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProduccionTraspasosRankingSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $rank = 1;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Ranking de Agentes';
    }

    public function headings(): array
    {
        return [
            'Rank',
            'Agente',
            'Equipo/Supervisor',
            'Titulares Efectivos',
            'Dependientes Efectivos',
            'Total Traspasos',
            'Pendientes',
            'Rechazados',
            'Total Solicitudes',
            'Hit Rate (%)'
        ];
    }

    public function map($ag): array
    {
        return [
            $this->rank++,
            $ag->agenteRel->nombre ?? 'N/A',
            $ag->agenteRel->supervisor->nombre ?? 'Sin Equipo',
            $ag->efectivos,
            $ag->dependientes_efectivos,
            $ag->total_vidas_efectivas,
            $ag->pendientes,
            $ag->rechazados,
            $ag->total_solicitudes,
            $ag->hit_rate . '%'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F172A']]],
        ];
    }
}
