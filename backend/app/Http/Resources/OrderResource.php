<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'order_number' => $this->order_number,
            'status'       => $this->status,
            'total'        => $this->total,
            'created_at'   => $this->created_at,
            'paid_at'      => $this->paid_at,
            'completed_at' => $this->completed_at,
            'items'        => $this->items->map(fn ($item) => [
                'id'           => $item->id,
                'game_name'    => $item->game_name,
                'product_name' => $item->product_name,
                'price'        => $item->price,
                'quantity'     => $item->quantity,
                'subtotal'     => $item->subtotal,
                'account_data' => $item->account_data,
            ]),
            // hanya untuk admin: pembeli dan riwayat pembayaran (FR-10.2)
            'customer'     => $this->whenLoaded('user', fn () => [
                'id'    => $this->user->id,
                'name'  => $this->user->name,
                'email' => $this->user->email,
            ]),
            'payments'     => $this->whenLoaded('payments', fn () => $this->payments->map(fn ($p) => [
                'id'                 => $p->id,
                'provider'           => $p->provider,
                'provider_reference' => $p->provider_reference,
                'status'             => $p->status,
                'amount'             => $p->amount,
                'paid_at'            => $p->paid_at,
                'created_at'         => $p->created_at,
            ])),
        ];
    }
}
