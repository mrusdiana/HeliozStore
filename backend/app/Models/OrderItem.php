<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'game_name', 'product_name',
        'price', 'quantity', 'subtotal', 'account_data',
    ];

    protected function casts(): array
    {
        return [
            'order_id'     => 'integer',
            'product_id'   => 'integer',
            'price'        => 'integer',
            'quantity'     => 'integer',
            'subtotal'     => 'integer',
            'account_data' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
