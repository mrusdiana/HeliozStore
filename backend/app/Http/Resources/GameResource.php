<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'genre' => $this->genre,
            'image_url' => $this->image_path ? asset('storage/', $this->image_path) : null,
            $this->mergeWhen($this->relationLoaded('products'), fn() => [
                'currency_name' => $this->currency_name,
                'account_instructions' => $this->account_instructions,
                'account_fields' => $this->account_fields,
                'packages' => ProductResource::collection($this->products)
            ]),
        ];
    }
}
