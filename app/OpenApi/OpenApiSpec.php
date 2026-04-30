<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'Список товаров с возможностью фильтрации и сортровки.',
    title: 'Products API',
)]
#[OA\Server(
    url: 'http://localhost:8000',
    description: '',
)]
#[OA\Schema(
    schema: 'ProductPagination',
    properties: [
        new OA\Property(property: 'current_page', type: 'integer', example: 1),
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Product'),
        ),
        new OA\Property(property: 'first_page_url', type: 'string'),
        new OA\Property(property: 'from', type: 'integer', nullable: true),
        new OA\Property(property: 'last_page', type: 'integer'),
        new OA\Property(property: 'last_page_url', type: 'string'),
        new OA\Property(property: 'links', type: 'array', items: new OA\Items(type: 'object')),
        new OA\Property(property: 'next_page_url', type: 'string', nullable: true),
        new OA\Property(property: 'path', type: 'string'),
        new OA\Property(property: 'per_page', type: 'integer', example: 15),
        new OA\Property(property: 'prev_page_url', type: 'string', nullable: true),
        new OA\Property(property: 'to', type: 'integer', nullable: true),
        new OA\Property(property: 'total', type: 'integer', example: 33),
    ],
    type: 'object',
)]
#[OA\Schema(
    schema: 'ValidationError',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'The sort field is invalid.'),
        new OA\Property(
            property: 'errors',
            type: 'object',
            additionalProperties: new OA\AdditionalProperties(
                type: 'array',
                items: new OA\Items(type: 'string'),
            ),
        ),
    ],
    type: 'object',
)]
class OpenApiSpec {}
