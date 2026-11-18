<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Game;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function gameForm(array $override = []): array
    {
        return array_merge([
            'name' => 'Free Fire', 'genre' => 'Battle Royale', 'currency_name' => 'Diamonds',
            'account_fields' => [['key' => 'player_id', 'label' => 'Player ID', 'type' => 'numeric', 'min' => 8, 'max' => 12]],
        ], $override);
    }

    public function test_admin_creates_game_with_image_and_unique_slug(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $first = $this->actingAs($admin)->post('/api/admin/games', $this->gameForm(['image' => $this->png()]), ['Accept' => 'application/json'])
            ->assertCreated()->assertJsonPath('slug', 'free-fire');
        Storage::disk('public')->assertExists($first->json('image_path'));

        $this->actingAs($admin)->postJson('/api/admin/games', $this->gameForm())
            ->assertCreated()->assertJsonPath('slug', 'free-fire-2');
    }

    public function test_replacing_game_image_deletes_old_file(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $old = $this->actingAs($admin)->post('/api/admin/games', $this->gameForm(['image' => $this->png()]), ['Accept' => 'application/json'])->json();

        $new = $this->actingAs($admin)->post("/api/admin/games/{$old['id']}", $this->gameForm([
            '_method' => 'PUT', 'name' => 'Free Fire Max', 'image' => $this->png('new.png'),
        ]), ['Accept' => 'application/json'])->assertOk()->assertJsonPath('slug', 'free-fire-max')->json();

        Storage::disk('public')->assertMissing($old['image_path']);
        Storage::disk('public')->assertExists($new['image_path']);
    }

    public function test_game_banner_upload_is_stored_and_exposed(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $game = $this->actingAs($admin)->post('/api/admin/games', $this->gameForm(['banner' => $this->png('wide.png')]), ['Accept' => 'application/json'])
            ->assertCreated()->json();

        Storage::disk('public')->assertExists($game['banner_path']);
        $this->getJson("/api/games/{$game['slug']}")->assertOk()->assertJsonPath('data.banner_url', asset('storage/'.$game['banner_path']));
    }

    public function test_game_validation(): void
    {
        $this->actingAs($this->admin())->postJson('/api/admin/games', $this->gameForm(['account_fields' => []]))
            ->assertStatus(422)->assertJsonValidationErrors('account_fields');
    }

    public function test_game_with_packages_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();

        $this->actingAs($this->admin())->deleteJson("/api/admin/games/{$product->game_id}")->assertStatus(409);
    }

    public function test_product_promo_price_must_be_higher(): void
    {
        $game = Game::factory()->create();

        $this->actingAs($this->admin())->postJson('/api/admin/products', [
            'game_id' => $game->id, 'name' => '86 Diamonds', 'item_amount' => 86,
            'price' => 20000, 'original_price' => 15000, 'stock' => 5,
        ])->assertStatus(422)->assertJsonValidationErrors('original_price');
    }

    public function test_ordered_product_cannot_be_deleted(): void
    {
        $product = Product::factory()->create();
        $order = Order::create(['order_number' => 'TOP-1', 'user_id' => $this->admin()->id, 'status' => 'paid', 'total' => 20000]);
        $order->items()->create([
            'product_id' => $product->id, 'game_name' => 'G', 'product_name' => 'P',
            'price' => 20000, 'quantity' => 1, 'subtotal' => 20000, 'account_data' => ['user_id' => '12345'],
        ]);

        $this->actingAs($this->admin())->deleteJson("/api/admin/products/{$product->id}")
            ->assertStatus(409)->assertJsonPath('message', 'This package has been ordered before. Set it to inactive instead.');
    }

    public function test_banner_crud(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $game = Game::factory()->create();

        $this->actingAs($admin)->postJson('/api/admin/banners', ['game_id' => $game->id, 'title' => 'No image'])
            ->assertStatus(422)->assertJsonValidationErrors('image');

        $banner = $this->actingAs($admin)->post('/api/admin/banners', [
            'game_id' => $game->id, 'title' => 'ML Week', 'subtitle' => 'Save 20%', 'image' => $this->png(),
        ], ['Accept' => 'application/json'])->assertCreated()->json();

        // ubah tanpa gambar baru: gambar lama tetap
        $this->actingAs($admin)->putJson("/api/admin/banners/{$banner['id']}", ['game_id' => $game->id, 'title' => 'ML Mega Week'])
            ->assertOk()->assertJsonPath('title', 'ML Mega Week')->assertJsonPath('image_path', $banner['image_path']);

        $this->actingAs($admin)->getJson('/api/admin/banners')->assertJsonPath('0.game.id', $game->id);

        $this->actingAs($admin)->deleteJson("/api/admin/banners/{$banner['id']}")->assertNoContent();
        Storage::disk('public')->assertMissing($banner['image_path']);
        $this->assertSame(0, Banner::count());
    }
}
