<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;

class DashboardController extends Controller
{
    private const SOLD = [Order::STATUS_PAID, Order::STATUS_COMPLETED];

    public function __invoke()
    {
        $today = today();

        // Pendapatan dihitung dari tanggal bayar (paid_at), bukan tanggal order dibuat.
        $paid = Order::whereIn('status', self::SOLD)
            ->where('paid_at', '>=', $today->copy()->subDays(6))
            ->get(['total', 'paid_at']);

        $byDay = $paid->groupBy(fn ($o) => $o->paid_at->toDateString());
        $revenue7d = collect(range(6, 0))->map(function ($ago) use ($today, $byDay) {
            $date = $today->copy()->subDays($ago)->toDateString();

            return ['date' => $date, 'revenue' => (int) ($byDay->get($date)?->sum('total') ?? 0)];
        });

        // shortcut: dikelompokkan per game_name (snapshot di order_items); cukup untuk demo, pakai game_id kalau nama game sering diganti.
        $sold = OrderItem::whereHas('order', fn ($q) => $q->whereIn('status', self::SOLD)
                ->where('paid_at', '>=', $today->copy()->subDays(29)))
            ->selectRaw('game_name as name, SUM(quantity) as items_sold')
            ->groupBy('game_name')
            ->orderByDesc('items_sold')
            ->get();
        $totalSold = max(1, $sold->sum('items_sold'));

        return [
            'today' => [
                'revenue' => (int) ($byDay->get($today->toDateString())?->sum('total') ?? 0),
                'orders'  => Order::whereDate('created_at', $today)->count(),
            ],
            'yesterday' => [
                'revenue' => (int) ($byDay->get($today->copy()->subDay()->toDateString())?->sum('total') ?? 0),
                'orders'  => Order::whereDate('created_at', $today->copy()->subDay())->count(),
            ],
            'pending_payment' => Order::where('status', Order::STATUS_PENDING_PAYMENT)->count(),
            'to_process'      => Order::where('status', Order::STATUS_PAID)->count(),
            'revenue_7d'      => $revenue7d,
            'status_counts'   => Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'recent_orders'   => OrderResource::collection(Order::with('user', 'items')->latest()->limit(5)->get()),
            'top_games'       => $sold->map(fn ($g) => [
                'name'       => $g->name,
                'items_sold' => (int) $g->items_sold,
                'percent'    => round($g->items_sold / $totalSold * 100, 1),
            ]),
        ];
    }
}
