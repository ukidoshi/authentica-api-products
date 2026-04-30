<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Models\Product;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    #[OA\Get(
        path: '/api/products',
        operationId: 'products.index',
        description: 'Поиск по товарам с фильтрами.',
        summary: 'Полчить товары',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'q',
                description: 'поиск по подстроке в name.',
                in: 'query',
                schema: new OA\Schema(type: 'string', example: 'iphone'),
            ),
            new OA\Parameter(
                name: 'price_min',
                description: 'price от',
                in: 'query',
                schema: new OA\Schema(type: 'number', format: 'float', minimum: 0, example: 100),
            ),
            new OA\Parameter(
                name: 'price_max',
                description: 'price до',
                in: 'query',
                schema: new OA\Schema(type: 'number', format: 'float', minimum: 0, example: 1000),
            ),
            new OA\Parameter(
                name: 'category_id',
                description: 'Product category id.',
                in: 'query',
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
            new OA\Parameter(
                name: 'sort_field',
                description: 'Сортировка товаров (поле)',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: ['price', 'created_at'], default: 'price'),
            ),
            new OA\Parameter(
                name: 'sort_type',
                description: 'Сортировка товаров (direction)',
                in: 'query',
                schema: new OA\Schema(type: 'string', enum: ['asc', 'desc'], default: 'asc'),
            ),
            new OA\Parameter(
                name: 'per_page',
                description: '',
                in: 'query',
                schema: new OA\Schema(type: 'integer', default: 15, maximum: 100, minimum: 1),
            ),
            new OA\Parameter(
                name: 'page',
                description: '',
                in: 'query',
                schema: new OA\Schema(type: 'integer', default: 1, minimum: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated product list.',
                content: new OA\JsonContent(ref: '#/components/schemas/ProductPagination'),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
    public function index(ProductIndexRequest $request)
    {
        $validated = $request->validated();

        $products = Product::query()
            ->when($request->filled('q'), fn ($query) =>
                $query->where('name', 'like', '%' . addcslashes($request->input('q'), '%_\\') . '%')
            )
            ->when($request->filled('price_max'), fn ($query) =>
                $query->where('price', '>=', $request->input('price_min'))
            )
            ->when($request->filled('price_min'), fn ($query) =>
                $query->where('price', '<=', $request->input('price_max'))
            )
            ->when($request->filled('category_id'), fn ($query) =>
                $query->where('category_id', $request->input('category_id'))
            );

        match ($validated['sort_field'] ?? null) {
            'price' => $products->orderBy('price', $validated['sort_type'])->orderBy('id'),
            'created_at' => $products->orderBy('created_at', $validated['sort_type'])->orderBy('id'),
            default => $products->latest()->orderByDesc('id'),
        };

        return $products
            ->paginate((int) ($validated['per_page'] ?? 15))
            ->withQueryString();
    }
}
