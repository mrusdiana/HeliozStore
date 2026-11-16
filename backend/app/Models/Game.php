<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    protected $fillable = [
        'name', 'slug', 'genre', 'image_path', 'currency_name',
        'account_instructions', 'account_fields', 'is_active', 'sort_order',
    ];

    protected function casts()
    {
        return [
            'account_fields' => 'array',   
            'is_active'      => 'boolean',
            'sort_order'     => 'integer',
        ];
    }

    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): void {
        $query->where('is_active', true);
    }
}
