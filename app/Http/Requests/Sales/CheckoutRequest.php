<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'payment_method' => ['required','in:cash,debt,charity'],
            'total_price' => ['required','integer'],
            'items' => ['required','array'],
            'items.*.type' => ['required','in:,med_package,fast_selling_item'],
            'items.*.product_id' => ['required','integer'],
            'items.*.quantity' => ['required','integer'],
            'items.*.purchase_price'=>['required','integer'],
            'items.*.retail_price' => ['required', 'integer'],
            'items.*.partial_sale' => ['required','boolean']
        ];
    }
}
