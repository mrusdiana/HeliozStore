<?php

namespace App\Support;

use App\Models\Game;
use Illuminate\Support\Facades\Validator;

class AccountData
{
    public static function validate(Game $game, mixed $input): array
    {
        $rules = ['account_data' => ['required', 'array']];
        $attributes = [];

        foreach ($game->account_fields as $field) {
            $key = 'account_data.'.$field['key'];
            $min = (int) $field['min']; 
            $max = (int) $field['max'];

            $rules[$key] = $field['type'] === 'numeric'
                ? ['required', "digits_between:$min,$max"]
                : ['required', 'string', "between:$min,$max", 'regex:/^[A-Za-z0-9_.\-]+$/'];

            $attributes[$key] = $field['label'];
        }

        $valid = Validator::make(['account_data' => $input], $rules, [], $attributes)->validate();

        $data = [];
        foreach ($game->account_fields as $field) {
            $data[$field['key']] = (string) $valid['account_data'][$field['key']];
        }

        return $data;
    }

    public static function key(array $data): string
    {
        return mb_substr(implode('|', $data), 0, 150);
    }
}
