<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Game;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['price' => 20000, 'stock' => 5]);
    }

    private function add(array $account = ['user_id' => '123456789', 'zone_id' => '1234'], int $quantity = 1)
    {
        return $this->actingAs($this->user)->postJson('/api/cart/items', [
            'product_id' => $this->product->id, 'quantity' => $quantity, 'account_data' => $account,
        ]);
    }

    public function test_cart_requires_login(): void
    {
        $this->getJson('/api/cart')->assertUnauthorized();
    }

    public function test_add_item_returns_cart_with_totals(): void
    {
        $this->add(quantity: 2)
            ->assertOk()
            ->assertJsonPath('items.0.quantity', 2)
            ->assertJsonPath('items.0.subtotal', 40000)
            ->assertJsonPath('items.0.account_data', ['user_id' => '123456789', 'zone_id' => '1234'])
            ->assertJsonPath('items.0.problem', null)
            ->assertJsonPath('total', 40000)
            ->assertJsonPath('can_checkout', true);
    }

    public function test_same_package_same_account_merges_but_different_account_splits(): void
    {
        $this->add();
        $this->add();
        $this->add(['user_id' => '987654321', 'zone_id' => '4321'])->assertJsonCount(2, 'items');

        $this->assertSame(2, CartItem::where('account_key', '123456789|1234')->value('quantity'));
    }

    public function test_account_data_follows_game_fields(): void
    {
        $this->add(['user_id' => '12', 'zone_id' => 'abcd'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['account_data.user_id', 'account_data.zone_id']);

        $this->add(['user_id' => '123456789'])->assertStatus(422)->assertJsonValidationErrors('account_data.zone_id');
    }

    public function test_text_field_game(): void
    {
        $game = Game::factory()->create(['account_fields' => [
            ['key' => 'username', 'label' => 'Username', 'type' => 'text', 'min' => '3', 'max' => '20'],
        ]]);
        $this->product = Product::factory()->for($game)->create();

        $this->add(['username' => 'player_123', 'extra' => 'dropped'])
            ->assertOk()->assertJsonPath('items.0.account_data', ['username' => 'player_123']);
        $this->add(['username' => 'bad name!'])->assertStatus(422);
    }

    public function test_quantity_cannot_exceed_stock(): void
    {
        $this->add(quantity: 6)->assertStatus(422)->assertJsonPath('errors.quantity.0', 'Only 5 left in stock.');

        $this->add(quantity: 4);
        $this->add(quantity: 2)->assertStatus(422); // 4 + 2 > 5
    }

    public function test_inactive_package_cannot_be_added(): void
    {
        $this->product->update(['is_active' => false]);

        $this->add()->assertStatus(422)->assertJsonValidationErrors('product_id');
    }

    public function test_update_and_delete_item(): void
    {
        $id = $this->add()->json('items.0.id');

        $this->actingAs($this->user)->patchJson("/api/cart/items/$id", ['quantity' => 3])
            ->assertOk()->assertJsonPath('items.0.quantity', 3);
        $this->actingAs($this->user)->patchJson("/api/cart/items/$id", ['quantity' => 9])->assertStatus(422);
        $this->actingAs($this->user)->deleteJson("/api/cart/items/$id")->assertOk()->assertJsonCount(0, 'items');
    }

    public function test_cannot_touch_another_users_item(): void
    {
        $id = $this->add()->json('items.0.id');

        $this->actingAs(User::factory()->create())->deleteJson("/api/cart/items/$id")->assertNotFound();
        $this->assertSame(1, CartItem::count());
    }

    public function test_problems_block_checkout(): void
    {
        $this->add(quantity: 3);

        $this->product->update(['stock' => 2]);
        $this->actingAs($this->user)->getJson('/api/cart')
            ->assertJsonPath('items.0.problem', 'insufficient_stock')->assertJsonPath('can_checkout', false);

        $this->product->game->update(['is_active' => false]);
        $this->actingAs($this->user)->getJson('/api/cart')->assertJsonPath('items.0.problem', 'unavailable');
    }
}
