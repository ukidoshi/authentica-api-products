<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Demo User',
                'password' => Hash::make('password'),
            ],
        );

        $categories = collect(['Phones', 'Laptops', 'Accessories'])
            ->map(fn (string $name) => Category::firstOrCreate(['name' => $name]));

        if (Product::count() > 0) {
            return;
        }

        Product::factory()->create([
            'name' => 'Apple iPhone 15',
            'price' => 799.00,
            'category_id' => $categories[0]->id,
        ]);

        Product::factory()->create([
            'name' => 'Samsung Galaxy S24',
            'price' => 699.00,
            'category_id' => $categories[0]->id,
        ]);

        Product::factory()->create([
            'name' => 'Lenovo ThinkPad E14',
            'price' => 950.00,
            'category_id' => $categories[1]->id,
        ]);

        Product::factory()
            ->count(30)
            ->state(fn () => ['category_id' => $categories->random()->id])
            ->create();
    }
}
