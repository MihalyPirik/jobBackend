<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|max:255|unique:users',
            'password' => 'required|confirmed|string|min:8|max:255',
            'phone_number' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+?[0-9\s\-]+$/',
            ],
            'address' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'A név mező megadása kötelező.',
            'email.required' => 'Az e-mail mező megadása kötelező.',
            'email.email' => 'Az e-mail cím nem megfelelő formátumú.',
            'email.unique' => 'Ez az e-mail cím már használatban van.',
            'password.required' => 'A jelszó megadása kötelező.',
            'password.confirmed' => 'A jelszavak nem egyeznek meg.',
            'password.min' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
            'phone_number.required' => 'A telefonszám megadása kötelező.',
            'phone_number.regex' => 'A telefonszám nem megfelelő formátumú.',
            'address.required' => 'A cím mező megadása kötelező.',
        ];
    }

}
