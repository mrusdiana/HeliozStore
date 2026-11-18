<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\MidtransService;
use Illuminate\Http\Request;

// Lacak pesanan publik. Wajib nomor + email kontak, supaya data akun game tidak bisa dilihat hanya dengan menebak nomor.
class TrackOrderController extends Controller
{
    public function show(Request $request, MidtransService $midtrans)
    {
        $order = $midtrans->refresh($this->find($request));

        return new OrderResource($order->load('items'));
    }

    public function pay(Request $request, MidtransService $midtrans)
    {
        $order = $this->find($request);
        abort_if($order->status !== Order::STATUS_PENDING_PAYMENT, 409, 'This order can no longer be paid.');

        $token = $order->payments()->latest('id')->value('snap_token') ?? $midtrans->createSnapToken($order);

        return ['snap_token' => $token];
    }

    private function find(Request $request): Order
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:30'],
            'email'  => ['required', 'email'],
        ]);

        $order = Order::where('order_number', strtoupper(trim($data['number'])))->first();

        // Pesan 404 sama untuk "nomor salah" dan "email salah", supaya nomor tidak bisa ditebak.
        abort_unless($order && strcasecmp($order->contact_email, $data['email']) === 0, 404, "We couldn't find an order with that number and email.");

        return $order;
    }
}
