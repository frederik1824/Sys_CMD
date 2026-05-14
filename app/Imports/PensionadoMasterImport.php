<?php

namespace App\Imports;

use App\Models\Modules\Dispersion\PensionadoMaster;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Str;

class PensionadoMasterImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row)
    {
        // Buscar si ya existe por cédula para actualizar o omitir
        $pensionado = PensionadoMaster::where('cedula', $this->formatCedula($row['cedula']))->first();

        $data = [
            'nss' => $row['nss'] ?? null,
            'nombre_completo' => strtoupper($row['nombre_completo']),
            'fecha_nacimiento' => isset($row['fecha_nacimiento']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['fecha_nacimiento']) : null,
            'genero' => strtoupper($row['genero'] ?? null),
            'tipo_pension' => $row['tipo_pension'] ?? 'Titular',
            'institucion_pension' => $row['institucion_pension'] ?? null,
            'monto_pension' => $row['monto_pension'] ?? 0,
            'estado_sistema' => 'ACTIVO',
            'data_adicional' => [
                'telefono' => $row['telefono'] ?? null,
                'importado_at' => now()->toDateTimeString(),
            ]
        ];

        if ($pensionado) {
            $pensionado->update($data);
            return null; // O retornar el modelo si se quiere registrar como fila procesada
        }

        return new PensionadoMaster(array_merge([
            'uuid' => (string) Str::uuid(),
            'cedula' => $this->formatCedula($row['cedula']),
        ], $data));
    }

    public function rules(): array
    {
        return [
            'cedula' => 'required',
            'nombre_completo' => 'required',
        ];
    }

    private function formatCedula($cedula)
    {
        $clean = preg_replace('/[^0-9]/', '', $cedula);
        if (strlen($clean) === 11) {
            return substr($clean, 0, 3) . '-' . substr($clean, 3, 7) . '-' . substr($clean, 10, 1);
        }
        return $cedula;
    }
}
