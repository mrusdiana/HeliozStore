<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

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

    // Paket bisa dibeli: paketnya aktif dan game-nya aktif (FR-2.5)
    public function scopePurchasable(Builder $query): void {
        $query->where('is_active', true)->whereHas('game', fn ($q) => $q->active());
    }

    // Promo = harga normal diisi (FR-9.4)
    public function scopePromo(Builder $query): void {
        $query->whereNotNull('original_price');
    }

    public function scopeActive(Builder $query) {

        $query->where('is_active', true);
    }

    protected function imageUrl():Attribute {
        return Attribute::get(fn () => $this->image_path ? asset('storage/'.$this->image_path): null);
    }

}
