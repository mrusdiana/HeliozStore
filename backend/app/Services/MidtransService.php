<?php

namespace App\Services;

use App\Jobs\CompleteOrder;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MidtransService
{
    private function http()
    {
        return Http::withBasicAuth((string) config('services.midtrans.server_key'), '')
            ->acceptJson()
            ->timeout(15);
    }

    private function unavailable(): never
    {
        abort(502, 'Payment service is unavailable. Please try again.');
    }

    // Minta token pembayaran ke Midtrans, lalu catat di tabel payments
    public function createSnapToken(Order $order): string
    {
        try {
            $res = $this->http()->post(config('services.midtrans.snap_url'), [
                'transaction_details' => [
                    'order_id'     => $order->order_number,
                    'gross_amount' => $order->total,
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email'      => $order->user->email,
                ],
            ]);
        } catch (ConnectionException) {
            $this->unavailable();
        }

        if ($res->failed() || ! $res->json('token')) {
            $this->unavailable();
        }

        $order->payments()->create([
            'snap_token' => $res->json('token'),
            'status'     => 'pending',
            'amount'     => $order->total,
        ]);

        return $res->json('token');
    }

    // Tanya status ke Midtrans. Array kosong = user belum memilih metode bayar.
    public function getStatus(string $orderNumber): array
    {
        try {
            $res = $this->http()->get(config('services.midtrans.api_url')."/{$orderNumber}/status");
        } catch (ConnectionException) {
            $this->unavailable();
        }

        if ($res->status() === 404 || $res->json('status_code') === '404') {
            return [];
        }

        if ($res->failed()) {
            $this->unavailable();
        }

        return $res->json() ?? [];
    }

    // Samakan status order dengan Midtrans (FR-7)
    public function sync(Order $order): Order
    {
        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return $order;
        }

        $data = $this->getStatus($order->order_number);
        $status = $data['transaction_status'] ?? null;

        if (! $status) {
            return $order;
        }

        DB::transaction(function () use ($order, $data, $status) {
            // kunci lalu cek ulang, supaya dua request bersamaan tidak memproses dua kali (FR-7.5)
            $locked = Order::lockForUpdate()->find($order->id);

            if ($locked->status !== Order::STATUS_PENDING_PAYMENT) {
                return;
            }

            $locked->payments()->latest('id')->first()?->update([
                'status'             => $status,
                'provider_reference' => $data['transaction_id'] ?? null,
                'raw_response'       => $data,
                'paid_at'            => in_array($status, ['settlement', 'capture']) ? now() : null,
            ]);

            if (in_array($status, ['settlement', 'capture'])) {
                $locked->update(['status' => Order::STATUS_PAID, 'paid_at' => now()]);
                CompleteOrder::dispatch($locked)->delay(now()->addSeconds(rand(5, 10)))->afterCommit();
            }

            if (in_array($status, ['expire', 'cancel', 'deny'])) {
                foreach ($locked->items as $item) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
                $locked->update(['status' => Order::STATUS_CANCELLED]);
            }
        });

        return $order->fresh();
    }
}
