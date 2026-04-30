<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductService
{
    public function __construct(
        private readonly ProductCacheService $cache,
    ) {}

    public function paginate(array $filters): array
    {
        return $this->cache->rememberList($filters, function () use ($filters) {
            $products = Product::query();

            if ($this->filled($filters, 'q')) {
                $products->where('name', 'like', '%'.addcslashes($filters['q'], '%_\\').'%');
            }

            if ($this->filled($filters, 'price_min')) {
                $products->where('price', '>=', $filters['price_min']);
            }

            if ($this->filled($filters, 'price_max')) {
                $products->where('price', '<=', $filters['price_max']);
            }

            if ($this->filled($filters, 'category_id')) {
                $products->where('category_id', $filters['category_id']);
            }

            $this->applySorting($products, $filters);

            return $products
                ->paginate((int) ($filters['per_page'] ?? 15))
                ->withQueryString()
                ->toArray();
        });
    }

    public function create(array $data): Product
    {
        $product = Product::query()->create($data);

        $this->cache->forgetList();

        return $product->refresh();
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);

        $this->cache->forgetList();

        return $product->refresh();
    }

    public function delete(Product $product): void
    {
        $product->delete();

        $this->cache->forgetList();
    }

    private function applySorting(Builder $products, array $filters): void
    {
        $sortDirection = $filters['sort_type'] ?? 'asc';

        match ($filters['sort_field'] ?? null) {
            'price' => $products->orderBy('price', $sortDirection)->orderBy('id'),
            'created_at' => $products->orderBy('created_at', $sortDirection)->orderBy('id'),
            default => $products->latest()->orderByDesc('id'),
        };
    }

    private function filled(array $filters, string $key): bool
    {
        return array_key_exists($key, $filters) && $filters[$key] !== null && $filters[$key] !== '';
    }
}
