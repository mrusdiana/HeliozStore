<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    // Dibuat langsung, karena checkout (POST /orders) belum ada
    private function order(User $user, string $status = Order::STATUS_PAID, string $number = 'TOP-20261116-AAAA'): Order
    {
        $order = Order::create(['order_number' => $number, 'user_id' => $user->id, 'status' => $status, 'total' => 20000]);
        $order->items()->create([
            'product_id' => \App\Models\Product::factory()->create()->id,
            'game_name' => 'Mobile Legends', 'product_name' => '86 Diamonds',
            'price' => 20000, 'quantity' => 1, 'subtotal' => 20000,
            'account_data' => ['user_id' => '123456789', 'zone_id' => '1234'],
        ]);

        return $order;
    }

    public function test_buyer_sees_only_own_orders(): void
    {
        $me = User::factory()->create();
        $this->order($me);
        $this->order(User::factory()->create(), number: 'TOP-OTHER');

        $this->actingAs($me)->getJson('/api/orders')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.order_number', 'TOP-20261116-AAAA')
            ->assertJsonPath('data.0.items.0.game_name', 'Mobile Legends')
            ->assertJsonPath('total', 1)
            ->assertJsonPath('page', 1);
    }

    public function test_order_detail_by_number_and_hidden_from_others(): void
    {
        $me = User::factory()->create();
        $this->order($me);

        $this->actingAs($me)->getJson('/api/orders/TOP-20261116-AAAA')
            ->assertOk()->assertJsonPath('data.items.0.account_data.zone_id', '1234')
            ->assertJsonMissingPath('data.customer');
        $this->actingAs(User::factory()->create())->getJson('/api/orders/TOP-20261116-AAAA')->assertNotFound();
    }

    public function test_admin_lists_filters_and_views_orders(): void
    {
        $buyer = User::factory()->create(['name' => 'Rina']);
        $this->order($buyer, Order::STATUS_PAID, 'TOP-A');
        $this->order($buyer, Order::STATUS_CANCELLED, 'TOP-B');
        $admin = $this->admin();

        $this->actingAs($admin)->getJson('/api/admin/orders?status=paid')->assertJsonCount(1, 'data');
        $this->actingAs($admin)->getJson('/api/admin/orders?search=TOP-B')->assertJsonPath('data.0.status', 'cancelled');
        $this->actingAs($admin)->getJson('/api/admin/orders?status=bogus')->assertStatus(422);
        $this->actingAs($admin)->getJson('/api/admin/orders/TOP-A')
            ->assertOk()->assertJsonPath('data.customer.name', 'Rina')->assertJsonPath('data.payments', []);
    }

    public function test_admin_can_complete_only_paid_orders(): void
    {
        $buyer = User::factory()->create();
        $this->order($buyer, Order::STATUS_PAID, 'TOP-PAID');
        $this->order($buyer, Order::STATUS_PENDING_PAYMENT, 'TOP-UNPAID');
        $admin = $this->admin();

        $this->actingAs($admin)->postJson('/api/admin/orders/TOP-PAID/complete')
            ->assertOk()->assertJsonPath('data.status', 'completed');
        $this->assertNotNull(Order::where('order_number', 'TOP-PAID')->value('completed_at'));

        $this->actingAs($admin)->postJson('/api/admin/orders/TOP-UNPAID/complete')->assertStatus(409);
        $this->actingAs(User::factory()->create())->postJson('/api/admin/orders/TOP-PAID/complete')->assertForbidden();
    }

    public function test_admin_dashboard_summarises_sales(): void
    {
        $buyer = User::factory()->create();
        $paid = $this->order($buyer, Order::STATUS_PAID, 'TOP-D1');
        $paid->update(['paid_at' => now()]);
        $this->order($buyer, Order::STATUS_PENDING_PAYMENT, 'TOP-D2');

        $res = $this->actingAs($this->admin())->getJson('/api/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('today.revenue', 20000)
            ->assertJsonPath('today.orders', 2)
            ->assertJsonPath('pending_payment', 1)
            ->assertJsonPath('to_process', 1)
            ->assertJsonCount(7, 'revenue_7d')
            ->assertJsonCount(2, 'recent_orders');

        $this->assertSame(100.0, (float) $res->json('top_games.0.percent'));
        $this->actingAs($buyer)->getJson('/api/admin/dashboard')->assertForbidden();
    }
}
