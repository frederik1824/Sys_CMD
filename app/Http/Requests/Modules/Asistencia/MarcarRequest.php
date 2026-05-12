<?php

namespace App\Http\Requests\Modules\Asistencia;

use Illuminate\Foundation\Http\FormRequest;

class MarcarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo' => 'required|in:entrada,inicio_almuerzo,fin_almuerzo,salida'
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'El tipo de marca es obligatorio.',
            'tipo.in' => 'El tipo de marca seleccionado no es válido.'
        ];
    }
}
