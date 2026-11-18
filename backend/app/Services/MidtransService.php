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
    // Pilihan metode di frontend → nama channel Snap untuk `enabled_payments`.
    public const PAYMENT_METHODS = [
        'qris'       => 'other_qris',
        'gopay'      => 'gopay',
        'shopeepay'  => 'shopeepay',
        'bca_va'     => 'bca_va',
        'bni_va'     => 'bni_va',
        'bri_va'     => 'bri_va',
        'mandiri_va' => 'echannel',
        'permata_va' => 'permata_va',
        'indomaret'  => 'indomaret',
        'alfamart'   => 'alfamart',
    ];

    // Cek status ke Midtrans (kalau Midtrans mati, pakai data apa adanya), lalu selesaikan order Paid > 10 detik
    // sebagai cadangan kalau queue worker mati. Dipakai halaman detail order dan lacak pesanan.
    public function refresh(Order $order): Order
    {
        $order = rescue(fn () => $this->sync($order), $order, false);

        if ($order->status === Order::STATUS_PAID && $order->paid_at?->lt(now()->subSeconds(10))) {
            $order->update(['status' => Order::STATUS_COMPLETED, 'completed_at' => now()]);
        }

        return $order;
    }

    public function createSnapToken(Order $order, ?string $method = null): string
    {
        $payload = [
            'transaction_details' => [
                'order_id'     => $order->order_number,
                'gross_amount' => $order->total,
            ],
            'customer_details' => [
                'first_name' => $order->user?->name ?? 'Guest',
                'email'      => $order->contact_email ?? $order->user?->email,
            ],
        ];

        // Kalau pembeli sudah memilih metode, popup Snap langsung membuka metode itu.
        if ($method && isset(self::PAYMENT_METHODS[$method])) {
            $payload['enabled_payments'] = [self::PAYMENT_METHODS[$method]];
        }

        try {
            $res = $this->http()->post(config('services.midtrans.snap_url'), $payload);
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
