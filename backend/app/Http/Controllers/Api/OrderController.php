<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest()->paginate(10);

        return [
            'data'      => OrderResource::collection($orders->items()),
            'page'      => $orders->currentPage(),
            'last_page' => $orders->lastPage(),
            'total'     => $orders->total(),
        ];
    }

    public function show(Request $request, Order $order)
    {
        // pesanan milik orang lain dianggap tidak ada
        abort_if($order->user_id !== $request->user()->id, 404);

        // TODO (Tahap 7-8, dikerjakan sendiri):
        // - status pending_payment: cek status ke Midtrans dulu (FR-7.2)
        // - status paid lebih dari 10 detik: selesaikan (cadangan FR-8.2)

        return new OrderResource($order->load('items'));
    }

    // TODO (Tahap 7, dikerjakan sendiri): store(), pay(), checkPayment()
}
