<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Services\MidtransService;
use App\Support\AccountData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

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

    public function show(Request $request, Order $order, MidtransService $midtrans)
    {
        abort_if($order->user_id !== $request->user()->id, 404);

        // cek status ke Midtrans; kalau Midtrans mati, tampilkan order apa adanya (FR-7.2)
        $order = rescue(fn () => $midtrans->sync($order), $order, false);

        // cadangan kalau queue worker mati (FR-8.2)
        if ($order->status === Order::STATUS_PAID && $order->paid_at?->lt(now()->subSeconds(10))) {
            $order->update(['status' => Order::STATUS_COMPLETED, 'completed_at' => now()]);
        }

        return new OrderResource($order->load('items'));
    }

    public function store(StoreOrderRequest $request, MidtransService $midtrans)
    {
        $user = $request->user();

        if($request->source === 'cart')
        {
            $lines = $user->cartItems()->get()->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'account_data' => $item->account_data,
            ]);

            if($lines->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Your cart is Empty.'
                ]);
            }
        } else {

            $product = Product::with('game')->findOrFail($request->product_id);

            $lines = collect([[
                'product_id' => $product->id,
                'quantity' => $request->integer('quantity'),
                'account_data' => AccountData::validate($product->game, $request->account_data),
            ]]);
        }

        $order = DB::transaction(function () use ($user, $lines, $request) {
            
            $products = Product::with('game')->whereIn('id', $lines->pluck('product_id'))->lockForUpdate()->get()->keyBy('id');

            foreach ($lines as $line) {
                $product = $products[$line['product_id']];
                $needed = $lines->where('product_id', $product->id)->sum('quantity');

                if(!$product->is_active || !$product->game->is_active) {
                    throw ValidationException::withMessages(['items' => "{$product->name} is no longer available."]);
                }

                if($product->stock < $needed) {
                    throw ValidationException::withMessages(['items' => "Only {$product->stock} left for {$product->name}."]);
                }
            }

            $order = $user->orders()->create([
                'order_number' => $this->newOrderNumber(),
                'status' => Order::STATUS_PENDING_PAYMENT,
                'total' => $lines->sum(fn ($l) => (int) $products[$l['product_id']]->price * $l['quantity']),
            ]);

            foreach ($lines as $line) {
                $product = $products[$line['product_id']];

                $order->items()->create([
                    'product_id' => $product->id,
                    'game_name' => $product->game->name,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $line['quantity'],
                    'subtotal' => (int) $product->price * $line['quantity'],
                    'account_data' => $line['account_data'],
                ]);

                $product->decrement('stock', $line['quantity']);
            }

            if($request->source === 'cart') {
                $user->cartItems()->delete();
            }

            return $order;
        });

        return response()->json([
            'order' => new OrderResource($order->load('items')),
            'snap_token' => $midtrans->createSnapToken($order),
        ], 201);
        
    }

    private function newOrderNumber():string{
        do{
            $number = 'TOP-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }

    public function pay(Request $request, Order $order, MidtransService $midtrans)
    {
        abort_if($order->user_id !== $request->user()->id, 404);
        abort_if($order->status !== Order::STATUS_PENDING_PAYMENT, 409, 'This order can no longer be paid.');

        // Midtrans menolak order_id yang sama dua kali, jadi pakai ulang token lama
        $token = $order->payments()->latest('id')->value('snap_token')
            ?? $midtrans->createSnapToken($order);

        return ['snap_token' => $token];
    }

    public function checkPayment(Request $request, Order $order, MidtransService $midtrans)
    {
        abort_if($order->user_id !== $request->user()->id, 404);

        return new OrderResource($midtrans->sync($order)->load('items'));
    }
}
