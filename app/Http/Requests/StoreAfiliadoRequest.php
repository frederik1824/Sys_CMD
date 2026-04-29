<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAfiliadoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Set to true for now; restrict based on roles later
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string|max:255',
            'cedula' => [
                'required', 
                'string', 
                'max:20', 
                new \App\Rules\CedulaDominicana,
                \Illuminate\Validation\Rule::unique('afiliados')->where(fn ($q) => $q->where('corte_id', $this->corte_id))
            ],
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'provincia_id' => 'nullable|exists:provincias,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'empresa_id' => 'nullable|exists:empresas,id',
            'corte_id' => 'required|exists:cortes,id',
            'estado_id' => 'required|exists:estados,id',
            'responsable_id' => 'nullable|exists:responsables,id',
            'poliza' => 'nullable|string|max:100',
            'contrato' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string|max:1000',
            'fecha_entrega_safesure' => 'nullable|date',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'cedula.unique' => 'Error: El afiliado con esta cédula ya existe dentro del corte seleccionado.',
        ];
    }
}
