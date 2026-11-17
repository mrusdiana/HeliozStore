<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'provider', 'snap_token', 'provider_reference',
        'status', 'amount', 'paid_at', 'raw_response',
    ];

    protected $hidden = ['raw_response'];

    protected function casts(): array
    {
        return [
            'order_id'     => 'integer',
            'amount'       => 'integer',
            'paid_at'      => 'datetime',
            'raw_response' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
