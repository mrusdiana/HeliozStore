<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Banner extends Model
{
    protected $fillable = ['game_id', 'title', 'subtitle', 'image_path', 'sort_order', 'is_active'];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'game_id'    => 'integer',
            'sort_order' => 'integer',
            'is_active'  => 'boolean',
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function scopeVisible(Builder $query): void
    {
        $query->where('is_active', true)->whereHas('game', fn ($q) => $q->active());
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->image_path ? asset('storage/'.$this->image_path) : null);
    }
}
