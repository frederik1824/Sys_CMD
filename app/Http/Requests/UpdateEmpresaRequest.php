<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmpresaRequest extends FormRequest
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
        $empresaId = $this->route('empresa')->id ?? $this->route('empresa');

        return [
            'nombre' => 'required|string|max:255|unique:empresas,nombre,' . $empresaId,
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
        
        // Optimistic Locking Check
        if ($this->has('last_updated_at')) {
            $empresa = $this->route('empresa');
            $dbTime = $empresa->updated_at;
            $formTime = \Carbon\Carbon::parse($this->last_updated_at);
            
            // Permitir un margen de 30 segundos para actualizaciones automáticas (Sync Cloud) o guardados rápidos
            if ($dbTime->diffInSeconds($formTime) > 30) {
                abort(409, 'Error de Concurrencia: Los datos han sido actualizados por otro usuario significativamente. Por favor, recarga la página para ver los últimos cambios antes de guardar.');
            }
        }
    }
}
