<?php

namespace App\Exports;

use App\Models\Modules\Dispersion\PensionadoMaster;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PensionadoMasterExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return PensionadoMaster::all();
    }

    public function headings(): array
    {
        return [
            'cedula',
            'nss',
            'nombre_completo',
            'fecha_nacimiento',
            'genero',
            'tipo_pension',
            'institucion_pension',
            'monto_pension',
            'telefono',
            'estado_sistema'
        ];
    }

    public function map($pensionado): array
    {
        return [
            $pensionado->cedula,
            $pensionado->nss,
            $pensionado->nombre_completo,
            $pensionado->fecha_nacimiento ? $pensionado->fecha_nacimiento->format('Y-m-d') : '',
            $pensionado->genero,
            $pensionado->tipo_pension,
            $pensionado->institucion_pension,
            $pensionado->monto_pension,
            $pensionado->data_adicional['telefono'] ?? '',
            $pensionado->estado_sistema
        ];
    }
}
