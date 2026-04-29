<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AfiliadosTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'Juan Pérez',
                '001-1234567-8',
                'M',
                '809-555-5555',
                'Av. Principal 123',
                'Santo Domingo',
                'Distrito Nacional',
                'Empresa XYZ',
                '123456789',
                '12345',
                '00000-00000-00',
                'Nota opcional aquí'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'NOMBRE COMPLETO',
            'CÉDULA',
            'SEXO',
            'TELÉFONO',
            'DIRECCIÓN',
            'PROVINCIA',
            'MUNICIPIO',
            'EMPRESA',
            'RNC EMPRESA',
            'NÚMERO DE CONTRATO',
            'PÓLIZA / CERTIFICADO',
            'OBSERVACIONES'
        ];
    }
}
