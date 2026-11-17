<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Game;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_only_visible_banners_promos_and_games(): void
    {
        $game = Game::factory()->create(['name' => 'Mobile Legends', 'slug' => 'mobile-legends']);
        $hidden = Game::factory()->inactive()->create();

        Banner::create(['game_id' => $game->id, 'title' => 'ML Week', 'image_path' => 'banners/a.png']);
        Banner::create(['game_id' => $hidden->id, 'title' => 'Hidden game', 'image_path' => 'banners/b.png']);
        Banner::create(['game_id' => $game->id, 'title' => 'Off', 'image_path' => 'banners/c.png', 'is_active' => false]);

        Product::factory()->promo()->for($game)->create(['name' => '86 Diamonds']);
        Product::factory()->for($game)->create();                          // bukan promo
        Product::factory()->promo()->for($game)->create(['stock' => 0]);   // habis
        Product::factory()->promo()->for($hidden)->create();              // game nonaktif

        $this->getJson('/api/home')
            ->assertOk()
            ->assertJsonCount(1, 'banners')
            ->assertJsonPath('banners.0.game_slug', 'mobile-legends')
            ->assertJsonCount(1, 'promos')
            ->assertJsonPath('promos.0.name', '86 Diamonds')
            ->assertJsonPath('promos.0.original_price', 25000)
            ->assertJsonPath('promos.0.game.slug', 'mobile-legends')
            ->assertJsonCount(1, 'games');
    }

    public function test_games_search_genre_and_genres_list(): void
    {
        Game::factory()->create(['name' => 'Mobile Legends', 'genre' => 'MOBA']);
        Game::factory()->create(['name' => 'PUBG Mobile', 'genre' => 'Battle Royale']);
        Game::factory()->inactive()->create(['genre' => 'RPG']);

        $this->getJson('/api/games?search=mobile')->assertJsonCount(2, 'data');
        $this->getJson('/api/games?search=LEGENDS')->assertJsonCount(1, 'data');
        $this->getJson('/api/games?genre=MOBA')->assertJsonCount(1, 'data');
        $this->getJson('/api/genres')->assertExactJson(['Battle Royale', 'MOBA']);
    }

    public function test_game_detail_lists_active_packages_cheapest_first(): void
    {
        $game = Game::factory()->create(['slug' => 'ml']);
        Product::factory()->for($game)->create(['price' => 50000]);
        Product::factory()->for($game)->create(['price' => 10000]);
        Product::factory()->for($game)->create(['is_active' => false]);

        $this->getJson('/api/games/ml')
            ->assertOk()
            ->assertJsonCount(2, 'data.packages')
            ->assertJsonPath('data.packages.0.price', 10000)
            ->assertJsonPath('data.account_fields.0.key', 'user_id');
    }

    public function test_inactive_or_missing_game_is_404(): void
    {
        Game::factory()->inactive()->create(['slug' => 'off']);

        $this->getJson('/api/games/off')->assertNotFound();
        $this->getJson('/api/games/nope')->assertNotFound();
    }
}
