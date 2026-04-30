<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_list_is_cached_and_invalidated_after_creating_product(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'name' => 'Cached Product',
            'price' => 100,
            'category_id' => $category->id,
        ]);

        $this->getJson('/api/products?sort_field=price&sort_type=asc')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        Product::factory()->create([
            'name' => 'Hidden Until Cache Flush',
            'price' => 200,
            'category_id' => $category->id,
        ]);

        $this->getJson('/api/products?sort_field=price&sort_type=asc')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this
            ->withToken($this->login(User::factory()->create()))
            ->postJson('/api/products', [
                'name' => 'Created Through API',
                'price' => 300,
                'category_id' => $category->id,
            ])
            ->assertCreated()
            ->assertJsonPath('name', 'Created Through API');

        $this->getJson('/api/products?sort_field=price&sort_type=asc')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('data.2.name', 'Created Through API');
    }

    private function login(User $user): string
    {
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();

        return $response->json('access_token');
    }
}
