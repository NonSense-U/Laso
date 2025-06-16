<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
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
            'admin.username' => ['required', 'string', 'unique:users,username'],
            'admin.email' => ['required_without:admin.phone_number', 'email','unique:users,email'],
            'admin.phone_number' => ['required_without:admin.email', 'string','unique:users,phone_number'],
            'admin.first_name' => ['required', 'string'],
            'admin.last_name' => ['nullable', 'string'],
            'admin.password' => ['required','confirmed','string'],
            'pharmacy.name' => ['required','string'],
            'pharmacy.location' => ['required','string'],
            'login' => ['required','boolean']

        ];
    }
}
