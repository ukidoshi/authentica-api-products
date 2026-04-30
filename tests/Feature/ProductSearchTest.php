<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_can_be_filtered_by_query_price_and_category(): void
    {
        $category_phones = Category::factory()->create(['name' => 'Phones']);
        $category_laptops = Category::factory()->create(['name' => 'Laptops']);

        Product::factory()->create([
            'name' => 'Budget Phone',
            'price' => 399.99,
            'category_id' => $category_phones->id,
        ]);

        Product::factory()->create([
            'name' => 'Old Phone',
            'price' => 199.99,
            'category_id' => $category_phones->id,
        ]);

        Product::factory()->create([
            'name' => 'Office Laptop',
            'price' => 899.99,
            'category_id' => $category_laptops->id,
        ]);

        $response = $this->getJson('/api/products?q=phone&price_min=300&price_max=500&category_id='.$category_phones->id);

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Budget Phone');
    }

    public function test_products_can_be_sorted_and_paginated(): void
    {
        $category = Category::factory()->create();

        Product::factory()->create([
            'name' => 'Middle Product',
            'price' => 200,
            'category_id' => $category->id,
        ]);

        Product::factory()->create([
            'name' => 'Cheap Product',
            'price' => 100,
            'category_id' => $category->id,
        ]);

        Product::factory()->create([
            'name' => 'Expensive Product',
            'price' => 300,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/products?sort_field=price&sort_type=asc&per_page=2');

        $response
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.name', 'Cheap Product')
            ->assertJsonPath('data.1.name', 'Middle Product')
            ->assertJsonPath('per_page', 2)
            ->assertJsonPath('total', 3);
    }

    public function test_sort_value_must_be_valid(): void
    {
        $response = $this->getJson('/api/products?sort_field=random');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sort_field');
    }
}
