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
curl "http://localhost:8000/api/products?q=iphone&price_min=500&price_max=1000"
```

## Фильтры

- `q` — поиск по подстроке в `name`
- `price_min`, `price_min` — фильтр по цене
- `category_id` — фильтр по категории

## Сортировка

Параметр `sort`:

- `price_asc`
- `price_desc`
- `newest`

Если сортировка не передана, используется `newest`.

## Тесты

```bash
php artisan test
```
