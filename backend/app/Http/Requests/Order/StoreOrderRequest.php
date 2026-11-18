<?php

namespace App\Http\Requests\Order;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'source' => ['required', 'in:cart,buy_now'],
            'product_id' => ['required_if:source,buy_now', 'integer', 'exists:products,id'],
            'quantity' => ['required_if:source,buy_now', 'integer', 'between:1,100'],
            'account_data' => ['required_if:source,buy_now', 'array']
        ];
    }
}
