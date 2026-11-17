<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'product_id', 'quantity', 'account_data', 'account_key'];

    protected function casts(): array
    {
        return [
            'user_id'      => 'integer',
            'product_id'   => 'integer',
            'quantity'     => 'integer',
            'account_data' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function problem(): ?string
    {
        if (! $this->product->is_active || ! $this->product->game->is_active) {
            return 'unavailable';
        }

        return $this->quantity > $this->product->stock ? 'insufficient_stock' : null;
    }
}
