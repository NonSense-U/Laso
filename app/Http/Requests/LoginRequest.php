<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => ['required_without_all:email,phone_number', 'exists:users', 'string'],
            'email' => ['required_without_all:username,phone_number', 'exists:users', 'string'],
            'phone_number' => ['required_without_all:username,email', 'exists:users', 'string'],
            'password' => ['required', 'string']
        ];
    }
}
