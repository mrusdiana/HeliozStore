<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password'],
        );
        $admin->role = 'admin'; 
        $admin->save();
    
        $ml = Game::firstOrCreate(['slug' => 'mobile-legends'], [
            'name' => 'Mobile Legends',
            'genre' => 'MOBA',
            'currency_name' => 'Diamonds',
            'account_instructions' => 'Open your profile in the game to find your User ID and Zone ID.',
            'account_fields' => [
                ['key' => 'user_id', 'label' => 'User ID', 'type' => 'numeric', 'min' => 5, 'max' => 12, 'placeholder' => '123456789'],
                ['key' => 'zone_id', 'label' => 'Zone ID', 'type' => 'numeric', 'min' => 4, 'max' => 5,  'placeholder' => '1234'],
            ],
        ]);
    
        $ml->products()->firstOrCreate(['name' => '86 Diamonds'],  ['item_amount' => 86,  'price' => 20000, 'original_price' => 25000, 'stock' => 50]);
        $ml->products()->firstOrCreate(['name' => '172 Diamonds'], ['item_amount' => 172, 'price' => 45000, 'stock' => 3]);
    }
}
