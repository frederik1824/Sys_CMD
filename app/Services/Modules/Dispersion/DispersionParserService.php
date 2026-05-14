<?php

namespace App\Services\Modules\Dispersion;

use Exception;

class DispersionParserService
{
    /**
     * Layout Oficial: Archivo de Dispersión de Pensionados
     * Fuente: Instructivo Envío y Recepción de Archivos Módulo de Pensionados
     */
    protected $layout = [
        'E' => [ // Encabezado
            'tipo_registro'   => [0, 1], // E
            'proceso'         => [1, 2], // PD
            'entidad_carga'   => [3, 2], // 01, 02
            'id_ars'          => [5, 2], // ID ARS
            'periodo'         => [7, 6], // MMAAAA
        ],
        'D' => [ // Detalle (Unificado para T, D, A)
            'tipo_registro'      => [0, 1], // D
            'tipo_afiliado'      => [1, 1], // T, D, A
            'nss_titular'        => [2, 9],
            'cedula_titular'     => [11, 11],
            'codigo_pensionado'  => [22, 10],
            'nss_dependiente'    => [32, 9],
            'cedula_dependiente' => [41, 11],
            'tipo_pensionado'    => [52, 1], // O, S
            'origen_pension'     => [53, 2], // 01, 02, 03
        ],
        'S' => [ // Sumario
            'tipo_registro'    => [0, 1], // S
            'total_registros'  => [1, 6],
        ],
        // Aliases para compatibilidad con variaciones (1=E, 2=D, 3=S)
        '1' => 'E',
        '2' => 'D',
        '3' => 'S'
    ];

    public function parseLine(string $line)
    {
        // Remove UTF-8 BOM
        $bom = pack('H*', 'EFBBBF');
        $line = preg_replace("/^$bom/", '', $line);

        $type = $this->detectType($line);
        if (!$type) return null;

        $actualType = is_string($this->layout[$type]) ? $this->layout[$type] : $type;
        $mapping = $this->layout[$actualType];
        $data = [];

        foreach ($mapping as $field => $pos) {
            $value = substr($line, $pos[0], $pos[1]);
            $data[$field] = trim($value);
        }

        return [
            'type' => $actualType,
            'data' => $data,
            'raw'  => $line
        ];
    }

    protected function detectType(string $line)
    {
        $line = ltrim($line);
        if (empty($line)) return null;

        $prefix1 = substr($line, 0, 1);
        if (isset($this->layout[$prefix1])) return $prefix1;

        return null;
    }

    public function formatAmount($value)
    {
        return 0; // Este layout no contempla montos financieros
    }
}
