<?php

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // account_data divalidasi di controller, karena aturannya bergantung pada game paket ini
    public function rules(): array
    {
        return [
            'product_id'   => ['required', 'integer', 'exists:products,id'],
            'quantity'     => ['required', 'integer', 'min:1', 'max:100'],
            'account_data' => ['required', 'array'],
        ];
    }
}
