<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class CedulaDominicana implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cedula = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($cedula) !== 11) {
            $fail('La cédula debe tener 11 dígitos numéricos.');
            return;
        }

        $sum = 0;
        $weight = [1, 2, 1, 2, 1, 2, 1, 2, 1, 2];
        
        for ($i = 0; $i < 10; $i++) {
            $num = $cedula[$i] * $weight[$i];
            $sum += ($num > 9) ? ($num - 9) : $num;
        }

        $checkDigit = (10 - ($sum % 10)) % 10;
        
        if ($checkDigit !== (int)$cedula[10]) {
            $fail('La cédula proporcionada no es válida.');
        }
    }
}
