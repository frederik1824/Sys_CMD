<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:empresas',
            'rnc' => ['nullable', 'string', 'max:20', new \App\Rules\RncDominicano],
            'direccion' => 'nullable|string|max:255',
            'provincia_id' => 'nullable|exists:provincias,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'telefono' => 'nullable|string|max:50',
            'es_real' => 'boolean',
            'es_filial' => 'boolean',
            'es_verificada' => 'boolean',
            'contacto_nombre' => 'nullable|string|max:255',
            'contacto_puesto' => 'nullable|string|max:255',
            'contacto_telefono' => 'nullable|string|max:50',
            'contacto_email' => 'nullable|email|max:255',
            'comision_tipo' => 'nullable|string|max:50',
            'comision_valor' => 'nullable|numeric|min:0',
            'promotor_id' => 'nullable|exists:users,id',
            'estado_contacto' => 'nullable|string|in:Nuevo,Contactado,En Negociación,Afiliada,No Contactar',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'google_maps_url' => 'nullable|url|max:500',
        ];
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'es_real' => $this->has('es_real'),
            'es_filial' => $this->has('es_filial'),
            'es_verificada' => $this->has('es_verificada'),
        ]);
    }
}
