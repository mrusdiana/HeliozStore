<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

// Data demo: harga perkiraan pasar top-up Indonesia, admin tinggal menyesuaikan.
// Gambar opsional: taruh di database/seeders/images/games/{slug}.png|jpg dan images/banners/{slug}.jpg
class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->games() as $sort => $data) {
            $game = Game::updateOrCreate(['slug' => $data['slug']], [
                'name'                 => $data['name'],
                'genre'                => $data['genre'],
                'currency_name'        => $data['currency'],
                'account_instructions' => $data['instructions'],
                'account_fields'       => $data['fields'],
                'image_path'           => $this->image('games', $data['slug']),
                'is_active'            => true,
                'sort_order'           => $sort,
            ]);

            foreach ($data['packages'] as [$amount, $price, $original, $stock]) {
                $game->products()->updateOrCreate(['name' => "$amount {$data['currency']}"], [
                    'item_amount'    => $amount,
                    'price'          => $price,
                    'original_price' => $original,
                    'stock'          => $stock,
                    'is_active'      => true,
                ]);
            }

            if (isset($data['banner']) && $banner = $this->image('banners', $data['slug'])) {
                $game->banners()->updateOrCreate(['title' => $data['banner'][0]], [
                    'subtitle'   => $data['banner'][1],
                    'image_path' => $banner,
                    'sort_order' => $sort,
                    'is_active'  => true,
                ]);
            }
        }
    }

    // Salin gambar dari folder seeder ke storage publik, null kalau tidak ada
    private function image(string $folder, string $slug): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $source = database_path("seeders/images/$folder/$slug.$ext");
            if (File::exists($source)) {
                $path = "$folder/$slug.$ext";
                Storage::disk('public')->put($path, File::get($source));

                return $path;
            }
        }

        return null;
    }

    private function numeric(string $key, string $label, int $min, int $max, string $placeholder): array
    {
        return compact('key', 'label', 'min', 'max', 'placeholder') + ['type' => 'numeric'];
    }

    private function text(string $key, string $label, int $min, int $max, string $placeholder): array
    {
        return compact('key', 'label', 'min', 'max', 'placeholder') + ['type' => 'text'];
    }

    // packages: [jumlah, harga, harga coret (null = bukan promo), stok]
    private function games(): array
    {
        return [
            [
                'slug' => 'mobile-legends', 'name' => 'Mobile Legends', 'genre' => 'MOBA', 'currency' => 'Diamonds',
                'instructions' => 'Open your profile in the game to find your User ID and Zone ID.',
                'fields' => [
                    $this->numeric('user_id', 'User ID', 5, 12, '123456789'),
                    $this->numeric('zone_id', 'Zone ID', 4, 5, '1234'),
                ],
                'banner' => ['Mobile Legends Week', 'Save up to 20% on Diamonds'],
                'packages' => [
                    [5, 1500, null, 999], [12, 3500, null, 999], [28, 8000, null, 999], [59, 16000, null, 999],
                    [86, 20000, 25000, 999], [172, 40000, 48000, 999], [257, 66000, null, 999], [344, 88000, null, 999],
                    [429, 110000, null, 999], [514, 132000, null, 999], [706, 176000, null, 999], [1050, 264000, null, 999],
                    [2195, 528000, null, 3], [3688, 880000, null, 0],
                ],
            ],
            [
                'slug' => 'free-fire', 'name' => 'Free Fire', 'genre' => 'Battle Royale', 'currency' => 'Diamonds',
                'instructions' => 'Tap your avatar in the top left corner of the lobby to find your Player ID.',
                'fields' => [$this->numeric('player_id', 'Player ID', 8, 12, '123456789')],
                'banner' => ['Free Fire Booyah Deals', 'Diamonds from Rp 1.000'],
                'packages' => [
                    [5, 1000, null, 999], [12, 2000, null, 999], [50, 7500, null, 999], [70, 10000, null, 999],
                    [140, 18000, 20000, 999], [355, 50000, null, 999], [720, 100000, null, 999], [1450, 200000, null, 999],
                    [2180, 300000, null, 999], [3640, 500000, null, 999],
                ],
            ],
            [
                'slug' => 'pubg-mobile', 'name' => 'PUBG Mobile', 'genre' => 'Battle Royale', 'currency' => 'UC',
                'instructions' => 'Open your profile in the game. Your Character ID is shown under your name.',
                'fields' => [$this->numeric('player_id', 'Character ID', 8, 12, '5123456789')],
                'banner' => ['PUBG Mobile UC Sale', 'Get more UC for less'],
                'packages' => [
                    [60, 15000, null, 999], [325, 72000, 75000, 999], [660, 150000, null, 999],
                    [1800, 375000, null, 999], [3850, 750000, null, 999], [8100, 1500000, null, 999],
                ],
            ],
            [
                'slug' => 'genshin-impact', 'name' => 'Genshin Impact', 'genre' => 'RPG', 'currency' => 'Genesis Crystals',
                'instructions' => 'Open the Paimon menu. Your UID is shown in the bottom right corner.',
                'fields' => [$this->numeric('uid', 'UID', 9, 10, '812345678')],
                'banner' => ['Genshin Impact Crystals', 'Genesis Crystals delivered instantly'],
                'packages' => [
                    [60, 16000, null, 999], [330, 79000, null, 999], [1090, 249000, null, 999],
                    [2240, 479000, null, 999], [3880, 799000, null, 999], [8080, 1599000, null, 999],
                ],
            ],
            [
                'slug' => 'honor-of-kings', 'name' => 'Honor of Kings', 'genre' => 'MOBA', 'currency' => 'Tokens',
                'instructions' => 'Tap your avatar in the lobby to find your Player ID.',
                'fields' => [$this->numeric('player_id', 'Player ID', 8, 20, '123456789012')],
                'banner' => ['Honor of Kings Anniversary', 'Celebrate with bonus Tokens'],
                'packages' => [
                    [16, 3500, null, 999], [80, 16000, null, 999], [240, 48000, null, 999], [400, 80000, null, 999],
                    [800, 160000, null, 999], [1200, 240000, null, 999], [2400, 480000, null, 999],
                ],
            ],
            [
                'slug' => 'roblox', 'name' => 'Roblox', 'genre' => 'Sandbox', 'currency' => 'Robux',
                'instructions' => 'Enter the username you use to sign in to Roblox.',
                'fields' => [$this->text('username', 'Username', 3, 20, 'player123')],
                'banner' => ['Roblox Robux Bonus', 'Top up Robux in seconds'],
                'packages' => [
                    [80, 16000, null, 999], [400, 72000, 75000, 999], [800, 150000, null, 999],
                    [1700, 300000, null, 999], [4500, 750000, null, 999], [10000, 1500000, null, 999],
                ],
            ],
            [
                'slug' => 'valorant', 'name' => 'Valorant', 'genre' => 'FPS', 'currency' => 'VP',
                'instructions' => 'Your Riot ID is shown in the top right of the client, for example PlayerName#1234.',
                'fields' => [
                    $this->text('riot_name', 'Riot name', 3, 16, 'PlayerName'),
                    $this->text('tagline', 'Tagline', 3, 5, '1234'),
                ],
                'packages' => [
                    [125, 15000, null, 999], [420, 50000, null, 999], [700, 80000, null, 999], [1375, 150000, null, 999],
                    [2400, 250000, null, 999], [4000, 400000, null, 999], [8150, 800000, null, 999],
                ],
            ],
            [
                'slug' => 'call-of-duty-mobile', 'name' => 'Call of Duty Mobile', 'genre' => 'FPS', 'currency' => 'CP',
                'instructions' => 'Open your profile in the game to find your Player UID.',
                'fields' => [$this->numeric('player_id', 'Player UID', 8, 20, '6712345678901234567')],
                'packages' => [
                    [31, 5000, null, 999], [63, 10000, null, 999], [128, 20000, null, 999], [321, 50000, null, 999],
                    [645, 100000, null, 999], [1373, 200000, null, 999], [3564, 500000, null, 999],
                ],
            ],
        ];
    }
}
