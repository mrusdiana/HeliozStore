<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

// Menandai order paid sebagai completed beberapa detik setelah dibayar (FR-8.1)
class CompleteOrder implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        $this->order->refresh();

        // admin mungkin sudah menyelesaikannya lebih dulu
        if ($this->order->status === Order::STATUS_PAID) {
            $this->order->update([
                'status'       => Order::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        }
    }
}
