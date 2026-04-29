<?php

namespace App\Rules;

use App\Helpers\ValidationHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RncDominicano implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!ValidationHelper::isValidDocument($value)) {
            $fail('El RNC proporcionado no es válido.');
        }
    }
}
