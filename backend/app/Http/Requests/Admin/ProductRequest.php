<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'game_id'        => ['required', 'integer', 'exists:games,id'],
            'name'           => ['required', 'string', 'max:100'],
            'image'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'item_amount'    => ['required', 'integer', 'min:1'],
            'price'          => ['required', 'integer', 'min:1000'],
            'original_price' => ['nullable', 'integer', 'gt:price'], 
            'stock'          => ['required', 'integer', 'min:0'],
            'is_active'      => ['boolean'],
        ];
    }
}
