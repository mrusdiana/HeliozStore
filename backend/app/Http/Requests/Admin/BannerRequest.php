<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'game_id'    => ['required', 'integer', 'exists:games,id'],
            'title'      => ['required', 'string', 'max:100'],
            'subtitle'   => ['nullable', 'string', 'max:200'],
            // gambar wajib saat membuat banner baru, opsional saat mengubah
            'image'      => [$this->route('banner') ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['integer', 'min:0'],
            'is_active'  => ['boolean'],
        ];
    }
}
