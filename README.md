# Laravel POS API

A learning-focused Point of Sale REST API built with Laravel. The project covers product management, stock tracking, restocks, carts, checkout, sales, and simple reports.

## Features

- Product CRUD with initial stock creation
- Stock list with product data
- Restock flow with stock movement history
- Cart flow: active cart, add item, update item, remove item
- Checkout flow that creates sales and sale items
- Stock decrease on checkout
- Low-stock notification job dispatch
- Sales and stock reports
- Docker setup with PHP-FPM, Nginx, PostgreSQL, and Redis

## Tech Stack

- PHP 8.4
- Laravel 13
- Laravel Sanctum
- PostgreSQL
- Redis
- Docker Compose
- PHPUnit

## Getting Started

Copy the environment file and adjust values if needed:

```bash
cp .env.example .env
```

Start the application:

```bash
docker compose up --build
```

The API is available at:

```text
http://localhost:8000
```

The app container runs package discovery and migrations automatically on startup.

## Useful Commands

Run migrations manually:

```bash
docker exec laravel_app php artisan migrate
```

Run tests:

```bash
docker exec laravel_app php artisan test
```

Clear Laravel cache:

```bash
docker exec laravel_app php artisan optimize:clear
```

Open a shell in the app container:

```bash
docker exec -it laravel_app bash
```

## API Endpoints

### Products

```text
GET     /api/products
POST    /api/products
GET     /api/products/{product}
PUT     /api/products/{product}
PATCH   /api/products/{product}
DELETE  /api/products/{product}
```

Product payload:

```json
{
  "sku": "PRD001",
  "name": "Coffee",
  "price": 15000,
  "minimum_stock": 10
}
```

### Stocks

```text
GET /api/stocks
```

### Restocks

```text
POST /api/restocks
```

Restock payload:

```json
{
  "product_id": 1,
  "quantity": 10,
  "note": "Initial stock"
}
```

### Cart

```text
GET     /api/cart
POST    /api/cart/items
PATCH   /api/cart/items/{item}
DELETE  /api/cart/items/{item}
POST    /api/cart/checkout
```

Add item payload:

```json
{
  "product_id": 1,
  "quantity": 2
}
```

Update item payload:

```json
{
  "quantity": 5
}
```

### Reports

```text
GET /api/reports/sales
GET /api/reports/stocks
```

## Database Overview

Main tables:

- `products`
- `stocks`
- `stock_movements`
- `restocks`
- `carts`
- `cart_items`
- `sales`
- `sale_items`

Stock movements use:

- `type`: `IN`, `OUT`
- `reference_type`: `RESTOCK`, `SALE`

Cart status uses:

- `ACTIVE`
- `CHECKOUT`

## Testing

Feature tests cover the core service flows:

- Stock service
- Restock service
- Cart service
- Sale service
- Report service

Run the suite:

```bash
docker exec laravel_app php artisan test
```

## Notes

This project is configured for learning and local development. The Docker image installs dev dependencies so test and debugging commands are available inside the container.
