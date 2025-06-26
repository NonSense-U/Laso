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
            'worker.username' => ['required', 'string', 'unique:users,username'],
            'worker.email' => ['required_without:worker.phone_number', 'email','unique:users,email'],
            'worker.phone_number' => ['required_without:worker.email', 'string','unique:users,phone_number'],
            'worker.first_name' => ['required', 'string'],
            'worker.last_name' => ['nullable', 'string'],
            "worker.password" => ['required','confirmed','string'],
            'login' => ['required','boolean']
        ];
    }
}
