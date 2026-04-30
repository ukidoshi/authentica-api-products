<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = collect(['Phones', 'Laptops', 'Accessories'])
            ->map(fn (string $name) => Category::firstOrCreate(['name' => $name]));

        if (Product::count() > 0) {
            return;
        }

        Product::factory()->create([
            'name' => 'Apple iPhone 15',
            'price' => 799.00,
            'category_id' => $categories[0]->id,
            'in_stock' => true,
            'rating' => 4.8,
        ]);

        Product::factory()->create([
            'name' => 'Samsung Galaxy S24',
            'price' => 699.00,
            'category_id' => $categories[0]->id,
            'in_stock' => true,
            'rating' => 4.6,
        ]);

        Product::factory()->create([
            'name' => 'Lenovo ThinkPad E14',
            'price' => 950.00,
            'category_id' => $categories[1]->id,
            'in_stock' => false,
            'rating' => 4.4,
        ]);

        Product::factory()
            ->count(30)
            ->state(fn () => ['category_id' => $categories->random()->id])
            ->create();
    }
}
