<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProduccionTraspasosDetalleSheet implements FromCollection, WithTitle, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;

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
        return 'Desglose Nominal';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cédula Afiliado',
            'Solicitud EPBD',
            'Nombre del Afiliado',
            'Agente Proceso',
            'Equipo/Supervisor',
            'Fecha Solicitud',
            'Estado Unipago',
            'Cantidad Dependientes',
            'Estado Auditoría',
            'Fecha Efectiva',
            'Fecha Rechazo',
            'Motivo Rechazo',
            'Efectivo Unipago (Si/No)',
            'Observaciones Auditoría'
        ];
    }

    public function map($traspaso): array
    {
        return [
            $traspaso->id,
            $traspaso->cedula_afiliado,
            $traspaso->numero_solicitud_epbd,
            $traspaso->nombre_afiliado,
            $traspaso->agenteRel->nombre ?? 'N/A',
            $traspaso->agenteRel->supervisor->nombre ?? 'Sin Equipo',
            $traspaso->fecha_solicitud ? $traspaso->fecha_solicitud->format('d/m/Y') : 'N/A',
            $traspaso->estadoRel->nombre ?? 'N/A',
            $traspaso->cantidad_dependientes,
            strtoupper(str_replace('_', ' ', $traspaso->unipago_status)),
            $traspaso->fecha_efectivo ? $traspaso->fecha_efectivo->format('d/m/Y') : '',
            $traspaso->fecha_rechazo ? $traspaso->fecha_rechazo->format('d/m/Y') : '',
            $traspaso->motivos_estado,
            $traspaso->verificado ? 'SÍ' : 'NO',
            $traspaso->unipago_observaciones
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '166534']]],
        ];
    }
}
