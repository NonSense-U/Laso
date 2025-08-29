<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationRequest extends FormRequest
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
            'serial_number'   => ['required', 'string', 'max:255', 'unique:medications,serial_number'],
            'name'            => ['required', 'string', 'max:255'],
            'scientific_name' => ['required', 'string', 'max:255'],
            'strength'        => ['required', 'string', 'max:255'],
            'entities'        => ['nullable', 'integer', 'min:1'],
            'retail_price'    => ['required', 'integer', 'min:0'],
            'manufacturer_id' => ['required', 'exists:manufacturers,id'],
        ];
    }
}
