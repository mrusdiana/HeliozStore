<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_a_buyer_even_if_role_is_sent(): void
    {
        $this->postJson('/api/auth/register', [
            'name' => 'Rina', 'email' => 'rina@example.com', 'password' => 'password123', 'role' => 'admin',
        ])->assertCreated()->assertJsonStructure(['user' => ['id', 'email'], 'token']);

        $this->assertSame('buyer', User::where('email', 'rina@example.com')->first()->role);
    }

    public function test_register_validates_input(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/auth/register', ['name' => 'X', 'email' => 'taken@example.com', 'password' => 'short'])
            ->assertStatus(422)->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_and_wrong_password_message(): void
    {
        User::factory()->create(['email' => 'a@example.com', 'password' => 'password123']);

        $this->postJson('/api/auth/login', ['email' => 'a@example.com', 'password' => 'password123'])
            ->assertOk()->assertJsonStructure(['token']);

        $this->postJson('/api/auth/login', ['email' => 'a@example.com', 'password' => 'wrong-pass'])
            ->assertStatus(422)->assertJsonPath('errors.email.0', 'Incorrect email or password.');
    }

    public function test_me_requires_token(): void
    {
        $this->getJson('/api/auth/me')->assertUnauthorized();

        $user = User::factory()->create();
        $this->actingAs($user)->getJson('/api/auth/me')->assertOk()->assertJsonPath('user.id', $user->id);
    }

    public function test_admin_routes_reject_buyers(): void
    {
        $this->actingAs(User::factory()->create())->getJson('/api/admin/games')->assertForbidden();
        $this->actingAs($this->admin())->getJson('/api/admin/games')->assertOk();
    }
}
