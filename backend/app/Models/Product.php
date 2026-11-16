<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'game_id', 'name', 'image_path', 'item_amount',
        'price', 'original_price', 'stock', 'is_active',
    ];

    protected $appends = ['image_url'];

    protected function casts():array {
        return [
            'game_id'        => 'integer',  
            'item_amount'    => 'integer',
            'price'          => 'integer',   
            'original_price' => 'integer',
            'stock'          => 'integer',
            'is_active'      => 'boolean',
        ];
    }

    public function game(): BelongsTo {

      return $this->belongsTo(Game::class);

    }

    public function scopeActive(Builder $query) {

        $query->where('is_active', true);
    }

    protected function imageUrl():Attribute {
        return Attribute::get(fn () => $this->image_path ? asset('storage/'.$this->image_path): null);
    }

}
