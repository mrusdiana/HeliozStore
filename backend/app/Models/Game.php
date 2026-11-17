<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'genre', 'image_path', 'currency_name',
        'account_instructions', 'account_fields', 'is_active', 'sort_order',
    ];

    protected $appends = ['image_url'];

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

    public function banners(): HasMany {
        return $this->hasMany(Banner::class);
    }

    public function scopeActive(Builder $query): void {
        $query->where('is_active', true);
    }

    protected function imageUrl():Attribute {
        return Attribute::get(fn () => $this->image_path ? asset('storage/'.$this->image_path) : null);
    }
}
