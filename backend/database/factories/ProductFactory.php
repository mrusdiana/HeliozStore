<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $amount = fake()->numberBetween(10, 500);

        return [
            'game_id'     => Game::factory(),
            'name'        => "$amount Diamonds",
            'item_amount' => $amount,
            'price'       => 20000,
            'stock'       => 10,
            'is_active'   => true,
        ];
    }

    public function promo(): static
    {
        return $this->state(['original_price' => 25000]);
    }
}
