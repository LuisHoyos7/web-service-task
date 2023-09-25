<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class TaskRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        //Validaciones para registar un usuario nuevo en el sistema
        return [
            'title'             => 'required',
            'description'       => 'required',
            'state_id'          => 'required',
            'priority_id'       => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'El titulo de la tarea es requerido',
            'description.required'  => 'La descripción de la tarea es requerido',
            'state_id.required'        => 'El estado de la tarea es requerido',
            'priority_id.required'        => 'La prioridad de la tarea es requerida',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message'   => 'Validation error',
            'errors'    => $validator->errors(),
        ], 422));
    }
}
