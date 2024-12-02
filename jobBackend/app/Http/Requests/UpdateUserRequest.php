<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        'name' => 'string|max:255',
        'email' => 'email|string|max:255|unique:users',
        'password' => 'confirmed|string|min:8|max:255',
        'phoneNumber' => 'string|max:255',
        'address' => 'string|max:255',
        ];
    }
}
