<?php

namespace App\Traits;

trait NormalizesData
{
    /**
     * Normaliza los campos de dirección eliminando abreviaturas comunes
     */
    public function normalizeAddressField(string $value): string
    {
        $replacements = [
            '/\bC\/\b/i' => 'Calle ',
            '/\bNo\.\b/i' => '#',
            '/\bEsq\.\b/i' => 'Esquina ',
            '/\bApt\.\b/i' => 'Apartamento ',
            '/\bRes\.\b/i' => 'Residencial ',
            '/\bAut\.\b/i' => 'Autopista ',
        ];

        $value = preg_replace(array_keys($replacements), array_values($replacements), $value);
        return trim(preg_replace('/\s+/', ' ', $value));
    }

    /**
     * Formatea una cédula dominicana (000-0000000-0)
     */
    public static function formatCedula(string $value): string
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        if (strlen($clean) === 11) {
            return substr($clean, 0, 3) . '-' . substr($clean, 3, 7) . '-' . substr($clean, 10, 1);
        }
        return $value;
    }

    /**
     * Convierte texto a Title Case (Nombre Propio)
     */
    public function toTitleCase(?string $value): ?string
    {
        if (!$value) return $value;
        return mb_convert_case(mb_strtolower($value), MB_CASE_TITLE, "UTF-8");
    }
}
