<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductIndexRequest;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
    ) {}

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
        return $this->products->paginate($request->validated());
    }

    #[OA\Post(
        path: '/api/products',
        operationId: 'products.store',
        description: 'Создание товара. Авторизация.',
        summary: 'Создать товар',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductStorePayload'),
        ),
        tags: ['Products'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created product.',
                content: new OA\JsonContent(ref: '#/components/schemas/Product'),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
    public function store(ProductStoreRequest $request): JsonResponse
    {
        $product = $this->products->create($request->validated());

        return response()->json($product, 201);
    }

    #[OA\Put(
        path: '/api/products/{id}',
        operationId: 'products.update',
        description: 'Обновление товара. Авторизация.',
        summary: 'Обновить товар',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ProductUpdatePayload'),
        ),
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Product id.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated product.',
                content: new OA\JsonContent(ref: '#/components/schemas/Product'),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found.',
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error.',
                content: new OA\JsonContent(ref: '#/components/schemas/ValidationError'),
            ),
        ],
    )]
    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        $product = $this->products->update($product, $request->validated());

        return response()->json($product);
    }

    #[OA\Delete(
        path: '/api/products/{id}',
        operationId: 'products.destroy',
        description: 'Удаление товара. Авторизация.',
        summary: 'Удалить товар',
        security: [['bearerAuth' => []]],
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Product id.',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1),
            ),
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Product deleted.',
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated.',
            ),
            new OA\Response(
                response: 404,
                description: 'Product not found.',
            ),
        ],
    )]
    public function destroy(Product $product): Response
    {
        $this->products->delete($product);

        return response()->noContent();
    }
}
