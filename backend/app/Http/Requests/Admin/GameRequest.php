<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GameRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:100'],
            'genre' => ['required', 'string', 'max:50'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'currency_name' => ['required', 'string', 'max:50'],
            'account_instructions'   => ['nullable', 'string', 'max:500'],
            'account_fields'         => ['required', 'array', 'min:1'],
            'account_fields.*.key'   => ['required', 'alpha_dash', 'max:30', 'distinct'],
            'account_fields.*.label' => ['required', 'string', 'max:50'],
            'account_fields.*.type'  => ['required', 'in:numeric,text'],
            'account_fields.*.min'   => ['required', 'integer', 'min:1'],
            'account_fields.*.max'   => ['required', 'integer', 'gte:account_fields.*.min'],
            'account_fields.*.placeholder' => ['nullable', 'string', 'max:50'],
            'is_active'              => ['boolean'],
            'sort_order'             => ['integer', 'min:0'],   
        ];
    }
}
