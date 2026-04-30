<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_use_bearer_token(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $token = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->json('access_token');

        $this
            ->withToken($token)
            ->postJson('/api/products', [
                'name' => 'Authorized Product',
                'price' => 10,
                'category_id' => $category->id,
            ])
            ->assertCreated()
            ->assertJsonPath('name', 'Authorized Product');
    }

    public function test_product_mutations_require_authentication(): void
    {
        $this->postJson('/api/products', [
            'name' => 'Unauthorized Product',
            'price' => 10,
            'category_id' => 1,
        ])->assertUnauthorized();
    }
}
