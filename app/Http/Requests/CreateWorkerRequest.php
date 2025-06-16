<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CreateWorkerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(Request $request): bool
    {
        return $request->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'unique:users'],
            'email' => ['required_without:phone_number', 'email'],
            'phone_number' => ['required_without:email', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['nullable', 'string'],
            "password" => ['required','confirmed','string'],
        ];
    }
}
