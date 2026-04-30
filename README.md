# Products API

Тестовое задание на Laravel: поиск товаров с фильтрами, сортировкой, пагинацией, Docker и Swagger.

Сарыглар Начын

## Запуск через Docker

```bash
cp .env.example .env
docker compose up --build
```

После старта приложение само выполнит миграции и сиды.

Адреса:

- API: `http://localhost:8000/api/products`
- Swagger: `http://localhost:8000/docs`
- OpenAPI JSON: `http://localhost:8000/api/docs`

Swagger собирается из PHP attributes в контроллере и схемах:

```bash
php artisan l5-swagger:generate
```

## Примеры запросов

```bash
curl "http://localhost:8000/api/products"
curl "http://localhost:8000/api/products?q=iphone&price_from=500&price_to=1000&in_stock=true"
curl "http://localhost:8000/api/products?category_id=1&rating_from=4.5&sort=price_asc&per_page=10"
```

## Фильтры

- `q` — поиск по подстроке в `name`
- `price_from`, `price_to` — фильтр по цене
- `category_id` — фильтр по категории
- `in_stock` — `true`, `false`, `1` или `0`
- `rating_from` — минимальный рейтинг от `0` до `5`

## Сортировка

Параметр `sort`:

- `price_asc`
- `price_desc`
- `rating_desc`
- `newest`

Если сортировка не передана, используется `newest`.

## Тесты

```bash
php artisan test
```
