<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'status' => ['nullable', Rule::in([
                Order::STATUS_PENDING_PAYMENT, Order::STATUS_PAID, Order::STATUS_COMPLETED, Order::STATUS_CANCELLED,
            ])],
            'search' => ['nullable', 'string', 'max:30'],
        ]);

        $orders = Order::with(['items', 'user'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, fn ($q, $search) => $q->where('order_number', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20);

        return [
            'data'      => OrderResource::collection($orders->items()),
            'page'      => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'total'     => $orders->total(),
        ];
    }

    public function show(Order $order)
    {
        return new OrderResource($order->load(['items', 'user', 'payments']));
    }

    // Cadangan bila penyelesaian otomatis gagal: hanya dari Paid (FR-10.3)
    public function complete(Order $order)
    {
        if ($order->status !== Order::STATUS_PAID) {
            return response()->json(['message' => 'Only paid orders can be marked as completed.'], 409);
        }

        $order->update(['status' => Order::STATUS_COMPLETED, 'completed_at' => now()]);

        return new OrderResource($order->load(['items', 'user', 'payments']));
    }
}
