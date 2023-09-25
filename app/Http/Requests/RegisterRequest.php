<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class RegisterRequest extends FormRequest
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
            'name'      => 'required',
            'email'     => 'required|unique:users|max:255',
            'password'  => 'required|min:6',
            'role_name'  => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'El nombre del usuario es requerido',
            'email.required'    => 'El correo electrónico es requerido',
            'email.unique'      => 'Este correo ya existe en la base de datos',
            'password.required'     => 'La contraseña es requerida',
            'password.min'          => 'La contraseña debe contener 6 o mas caracteres',
            'role_name.required'     => 'El nombre del rol es requerido'

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422));
    }
}
