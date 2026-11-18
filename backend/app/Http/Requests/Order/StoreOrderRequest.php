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
            'account_data' => ['required_if:source,buy_now', 'array'],
            // Tamu wajib isi email kontak; akun yang masuk boleh kosong (pakai email akun).
            'contact_email' => [\Illuminate\Validation\Rule::requiredIf(! $this->user('sanctum') && $this->source === 'buy_now'), 'nullable', 'email', 'max:255'],
            'contact_whatsapp' => ['nullable', 'regex:/^\+?[0-9]{9,15}$/'],
            'payment_method' => ['nullable', 'in:'.implode(',', array_keys(\App\Services\MidtransService::PAYMENT_METHODS))],
        ];
    }
}
