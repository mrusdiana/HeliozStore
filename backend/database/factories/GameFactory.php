<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name'           => Str::title($name),
            'slug'           => Str::slug($name),
            'genre'          => 'MOBA',
            'currency_name'  => 'Diamonds',
            'account_fields' => [
                ['key' => 'user_id', 'label' => 'User ID', 'type' => 'numeric', 'min' => 5, 'max' => 12, 'placeholder' => null],
                ['key' => 'zone_id', 'label' => 'Zone ID', 'type' => 'numeric', 'min' => 4, 'max' => 5, 'placeholder' => null],
            ],
            'is_active'      => true,
            'sort_order'     => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
