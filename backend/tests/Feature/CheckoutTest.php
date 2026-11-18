<?php

namespace Tests\Feature;

use App\Jobs\CompleteOrder;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;
    private array $account = ['user_id' => '123456789', 'zone_id' => '1234'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['price' => 20000, 'stock' => 5]);
    }

    private function fakeMidtrans(?string $status = null): void
    {
        Http::fake([
            'app.sandbox.midtrans.com/*' => Http::response(['token' => 'snap-123']),
            'api.sandbox.midtrans.com/*' => $status
                ? Http::response(['transaction_status' => $status, 'transaction_id' => 'trx-1', 'status_code' => '200'])
                : Http::response(['status_code' => '404'], 404),
        ]);
    }

    private function buyNow(int $quantity = 2)
    {
        return $this->actingAs($this->user)->postJson('/api/orders', [
            'source' => 'buy_now', 'product_id' => $this->product->id,
            'quantity' => $quantity, 'account_data' => $this->account,
        ]);
    }

    public function test_checkout_from_cart_creates_order_reduces_stock_and_empties_cart(): void
    {
        $this->fakeMidtrans();
        CartItem::create([
            'user_id' => $this->user->id, 'product_id' => $this->product->id, 'quantity' => 3,
            'account_data' => $this->account, 'account_key' => '123456789|1234',
        ]);

        $res = $this->actingAs($this->user)->postJson('/api/orders', ['source' => 'cart'])
            ->assertCreated()
            ->assertJsonPath('snap_token', 'snap-123')
            ->assertJsonPath('order.status', 'pending_payment')
            ->assertJsonPath('order.total', 60000);

        $this->assertMatchesRegularExpression('/^TOP-\d{8}-[A-Z0-9]{4}$/', $res->json('order.order_number'));
        $this->assertSame(2, $this->product->fresh()->stock);
        $this->assertSame(0, $this->user->cartItems()->count());
        $this->assertDatabaseHas('order_items', ['product_name' => $this->product->name, 'quantity' => 3]);
        $this->assertDatabaseHas('payments', ['snap_token' => 'snap-123', 'status' => 'pending']);
    }

    public function test_payment_method_is_sent_to_snap_and_validated(): void
    {
        $this->fakeMidtrans();

        $this->actingAs($this->user)->postJson('/api/orders', [
            'source' => 'buy_now', 'product_id' => $this->product->id, 'quantity' => 1,
            'account_data' => $this->account, 'payment_method' => 'qris',
        ])->assertCreated();

        Http::assertSent(fn ($req) => str_contains($req->url(), 'app.sandbox')
            && $req['enabled_payments'] === ['other_qris']);

        $this->actingAs($this->user)->postJson('/api/orders', [
            'source' => 'buy_now', 'product_id' => $this->product->id, 'quantity' => 1,
            'account_data' => $this->account, 'payment_method' => 'bitcoin',
        ])->assertUnprocessable()->assertJsonValidationErrors('payment_method');
    }

    public function test_buy_now_keeps_cart_untouched(): void
    {
        $this->fakeMidtrans();
        CartItem::create([
            'user_id' => $this->user->id, 'product_id' => $this->product->id, 'quantity' => 1,
            'account_data' => $this->account, 'account_key' => '123456789|1234',
        ]);

        $this->buyNow()->assertCreated();

        $this->assertSame(1, $this->user->cartItems()->count());
        $this->assertSame(3, $this->product->fresh()->stock);
    }

    public function test_empty_cart_and_missing_buy_now_fields_are_rejected(): void
    {
        $this->actingAs($this->user)->postJson('/api/orders', ['source' => 'cart'])
            ->assertUnprocessable()->assertJsonValidationErrors('cart');

        $this->actingAs($this->user)->postJson('/api/orders', ['source' => 'buy_now'])
            ->assertUnprocessable()->assertJsonValidationErrors(['product_id', 'quantity', 'account_data']);
    }

    public function test_not_enough_stock_rolls_back_and_creates_no_order(): void
    {
        $this->buyNow(quantity: 6)->assertUnprocessable()->assertJsonValidationErrors('items');

        $this->assertSame(0, Order::count());
        $this->assertSame(5, $this->product->fresh()->stock);
    }

    public function test_midtrans_down_returns_502(): void
    {
        Http::fake(['*' => Http::response([], 500)]);

        $this->buyNow()->assertStatus(502)
            ->assertJsonPath('message', 'Payment service is unavailable. Please try again.');
    }

    public function test_settlement_marks_paid_and_queues_completion(): void
    {
        Queue::fake();
        // checkout tidak memanggil endpoint status, jadi satu fake cukup untuk keduanya
        $this->fakeMidtrans('settlement');
        $number = $this->buyNow()->json('order.order_number');
        $this->actingAs($this->user)->postJson("/api/orders/$number/check-payment")
            ->assertOk()->assertJsonPath('data.status', 'paid');

        Queue::assertPushed(CompleteOrder::class);
        $this->assertDatabaseHas('payments', ['provider_reference' => 'trx-1', 'status' => 'settlement']);
    }

    public function test_expire_restores_stock_only_once(): void
    {
        $this->fakeMidtrans('expire');
        $number = $this->buyNow()->json('order.order_number');
        $this->assertSame(3, $this->product->fresh()->stock);

        $this->actingAs($this->user)->postJson("/api/orders/$number/check-payment")
            ->assertJsonPath('data.status', 'cancelled');
        $this->actingAs($this->user)->postJson("/api/orders/$number/check-payment");

        $this->assertSame(5, $this->product->fresh()->stock);
    }

    public function test_pay_reuses_token_and_rejects_others_and_paid_orders(): void
    {
        $this->fakeMidtrans();
        $number = $this->buyNow()->json('order.order_number');

        $this->actingAs($this->user)->postJson("/api/orders/$number/pay")
            ->assertOk()->assertJsonPath('snap_token', 'snap-123');
        $this->assertSame(1, Order::first()->payments()->count());

        $this->actingAs(User::factory()->create())->postJson("/api/orders/$number/pay")->assertNotFound();

        Order::first()->update(['status' => Order::STATUS_PAID, 'paid_at' => now()]);
        $this->actingAs($this->user)->postJson("/api/orders/$number/pay")->assertStatus(409);
    }

    public function test_job_completes_paid_order(): void
    {
        $order = Order::create([
            'order_number' => 'TOP-20261117-AAAA', 'user_id' => $this->user->id,
            'status' => Order::STATUS_PAID, 'total' => 20000, 'paid_at' => now(),
        ]);

        (new CompleteOrder($order))->handle();

        $this->assertSame('completed', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->completed_at);
    }

    public function test_show_completes_paid_order_after_10_seconds(): void
    {
        $order = Order::create([
            'order_number' => 'TOP-20261117-BBBB', 'user_id' => $this->user->id,
            'status' => Order::STATUS_PAID, 'total' => 20000, 'paid_at' => now()->subSeconds(11),
        ]);

        $this->actingAs($this->user)->getJson("/api/orders/{$order->order_number}")
            ->assertJsonPath('data.status', 'completed');
    }

    public function test_scheduled_command_syncs_pending_orders(): void
    {
        Queue::fake();
        $this->fakeMidtrans('settlement');
        $this->buyNow();

        $this->artisan('orders:sync-pending')->assertSuccessful();

        $this->assertSame('paid', Order::first()->status);
    }

    public function test_guest_can_buy_now_with_contact_email_and_track_the_order(): void
    {
        $this->fakeMidtrans();
        $payload = [
            'source' => 'buy_now', 'product_id' => $this->product->id, 'quantity' => 1,
            'account_data' => $this->account, 'payment_method' => 'qris',
        ];

        $this->postJson('/api/orders', $payload)->assertUnprocessable()->assertJsonValidationErrors('contact_email');
        $this->postJson('/api/orders', ['source' => 'cart'])->assertUnauthorized();

        $number = $this->postJson('/api/orders', $payload + ['contact_email' => 'guest@example.com', 'contact_whatsapp' => '081234567890'])
            ->assertCreated()
            ->assertJsonPath('order.contact.email', 'guest@example.com')
            ->json('order.order_number');

        $this->assertDatabaseHas('orders', ['order_number' => $number, 'user_id' => null]);
        Http::assertSent(fn ($req) => str_contains($req->url(), 'app.sandbox') && $req['customer_details']['email'] === 'guest@example.com');

        $this->getJson("/api/orders/track?number={$number}&email=GUEST@example.com")
            ->assertOk()->assertJsonPath('data.order_number', $number);
        $this->getJson("/api/orders/track?number={$number}&email=other@example.com")->assertNotFound();
        $this->getJson('/api/orders/track?number=TOP-NOPE&email=guest@example.com')->assertNotFound();

        $this->postJson('/api/orders/track/pay', ['number' => $number, 'email' => 'guest@example.com'])
            ->assertOk()->assertJsonPath('snap_token', 'snap-123');
    }

    public function test_signed_in_order_uses_account_email_as_contact(): void
    {
        $this->fakeMidtrans();
        $this->buyNow(1)->assertCreated()->assertJsonPath('order.contact.email', $this->user->email);
    }
}
