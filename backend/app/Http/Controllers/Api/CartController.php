<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\AddCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use App\Support\AccountData;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(Request $request)
    {
        return $this->cart($request->user());
    }

    public function store(AddCartItemRequest $request)
    {
        $product = Product::with('game')->findOrFail($request->product_id);

        if (! $product->is_active || ! $product->game->is_active) {
            throw ValidationException::withMessages(['product_id' => ['This package is no longer available.']]);
        }

        $accountData = AccountData::validate($product->game, $request->account_data);
        $accountKey = AccountData::key($accountData);

        // paket sama untuk akun sama digabung, jumlahnya ditambah (FR-5.3)
        $item = $request->user()->cartItems()->firstOrNew([
            'product_id'  => $product->id,
            'account_key' => $accountKey,
        ]);
        $quantity = ($item->quantity ?? 0) + $request->quantity;

        $this->ensureStock($product, $quantity);

        $item->fill(['quantity' => $quantity, 'account_data' => $accountData])->save();

        return $this->cart($request->user());
    }

    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        $this->ensureOwner($request, $cartItem);
        $this->ensureStock($cartItem->product, $request->quantity);

        $cartItem->update(['quantity' => $request->quantity]);

        return $this->cart($request->user());
    }

    public function destroy(Request $request, CartItem $cartItem)
    {
        $this->ensureOwner($request, $cartItem);

        $cartItem->delete();

        return $this->cart($request->user());
    }

    private function ensureOwner(Request $request, CartItem $cartItem): void
    {
        // item milik orang lain dianggap tidak ada (404), bukan 403
        abort_if($cartItem->user_id !== $request->user()->id, 404);
    }

    private function ensureStock(Product $product, int $quantity): void
    {
        if ($quantity > $product->stock) {
            throw ValidationException::withMessages([
                'quantity' => [$product->stock > 0 ? "Only {$product->stock} left in stock." : 'This package is out of stock.'],
            ]);
        }
    }

    // Isi keranjang dengan harga dan stok terbaru (FR-5.5, FR-5.6)
    private function cart(User $user): array
    {
        $items = $user->cartItems()->with('product.game')->oldest()->get()->map(function (CartItem $item) {
            $product = $item->product;

            return [
                'id'           => $item->id,
                'game'         => [
                    'name'      => $product->game->name,
                    'slug'      => $product->game->slug,
                    'image_url' => $product->game->image_url,
                ],
                'product'      => [
                    'id'        => $product->id,
                    'name'      => $product->name,
                    'price'     => $product->price,
                    'stock'     => $product->stock,
                    'image_url' => $product->image_url ?? $product->game->image_url,
                ],
                'quantity'     => $item->quantity,
                'account_data' => $item->account_data,
                'subtotal'     => $product->price * $item->quantity,
                'problem'      => $item->problem(),
            ];
        });

        return [
            'items'        => $items,
            'total'        => $items->sum('subtotal'),
            'can_checkout' => $items->isNotEmpty() && $items->every(fn ($i) => $i['problem'] === null),
        ];
    }
}
