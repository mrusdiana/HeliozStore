<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Console\Command;

class SyncPendingOrders extends Command
{
    protected $signature = 'orders:sync-pending';

    protected $description = 'Check unpaid orders against Midtrans';

    public function handle(MidtransService $midtrans): void
    {
        Order::where('status', Order::STATUS_PENDING_PAYMENT)
            ->where('created_at', '>=', now()->subDay())
            ->each(fn (Order $order) => rescue(fn () => $midtrans->sync($order), report: false));
    }
}
