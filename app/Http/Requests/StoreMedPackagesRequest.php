<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMedPackagesRequest extends FormRequest
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
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'total_price' => ['required','numeric'],
            'paid_amount' => ['required','numeric'],
            'packages-order' => ['required', 'array'],
            'packages-order.*.medication_id' => ['required', 'exists:medications,id'],
            'packages-order.*.quantity' => ['required', 'integer'],
            'packages-order.*.production_date' => ['required', 'date'],
            'packages-order.*.expiration_date' => ['required', 'date'],
            'packages-order.*.purchase_price' => ['required', 'numeric'],
        ];
    }
}
