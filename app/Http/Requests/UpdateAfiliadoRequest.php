<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAfiliadoRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nombre_completo' => 'required|string|max:255',
            'cedula' => ['required', 'string', 'max:20', new \App\Rules\CedulaDominicana],
            'sexo' => 'nullable|string|in:M,F',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'provincia_id' => 'nullable|exists:provincias,id',
            'municipio_id' => 'nullable|exists:municipios,id',
            'empresa_id' => 'nullable|exists:empresas,id',
            'corte_id' => 'required|exists:cortes,id',
            'estado_id' => 'required|exists:estados,id',
            'responsable_id' => 'nullable|exists:responsables,id',
            'proveedor_id' => 'nullable|exists:proveedors,id',
            'fecha_entrega_proveedor' => 'nullable|date',
            'costo_entrega' => 'nullable|numeric|min:0',
            'liquidado' => 'nullable|boolean',
            'fecha_liquidacion' => 'nullable|date',
            'recibo_liquidacion' => 'nullable|string|max:100',
            'fecha_entrega_safesure' => 'nullable|date',
            'contrato' => 'nullable|string|max:100',
            'poliza' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ];
    }
    
    /**
     * Prepare the data for validation and Optimistic Locking check.
     */
    protected function prepareForValidation()
    {
        if ($this->has('last_updated_at')) {
            $afiliado = $this->route('afiliado');
            $dbTime = $afiliado->updated_at;
            $formTime = \Carbon\Carbon::parse($this->last_updated_at);
            
            if ($dbTime->diffInSeconds($formTime) > 30) {
                abort(409, 'Error de Concurrencia: Los datos del afiliado han sido modificados por otro usuario significativamente. Recarga la página para ver los cambios recientes.');
            }
        }
    }
}
