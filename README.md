### testVOVO — Laravel Products API

#### Overview
This repository contains a Laravel 10 application that exposes a simple Products API with filtering, sorting, pagination, and category relations. It includes database migrations for `categories` and `products`, Eloquent scopes for expressive filtering/sorting, API resources for consistent responses, and seeders for demo data.

Key features:
- GET `/api/products` endpoint with filters: search by name, price range, category, in‑stock flag, minimum rating
- Sorting: newest (default), price asc/desc, rating desc
- Pagination with configurable `per_page`
- Category relation eager‑loading


#### Stack
- Language: PHP 8.1+
- Framework: Laravel 10.x
- Package manager: Composer (PHP), npm (Node) for assets via Vite
- Testing: PHPUnit 10
- Optional local dev with Docker: Laravel Sail (MySQL 8)


#### Requirements
Local (without Docker):
- PHP 8.1+
- Composer 2.x
- MySQL 8 (or compatible) or SQLite

With Docker (Laravel Sail):
- Docker 20.10+
- Docker Compose plugin


#### Project structure (selected)
```
app/
  Http/
    Controllers/Api/ProductController.php   # GET /api/products
    Requests/ProductSearchRequest.php       # Validates filters/sort/pagination
    Resources/ProductResource.php           # Shapes API responses
  Models/
    Product.php                             # Scopes for filters/sorting
    Category.php                            # Category model
bootstrap/
config/
database/
  migrations/                               # Categories & Products tables
  seeders/                                  # CategorySeeder, ProductSeeder
public/
  index.php                                 # HTTP entry point
resources/
routes/
  api.php                                   # API route definitions
tests/
  Feature/, Unit/                           # PHPUnit tests
vite.config.js
composer.json
package.json
compose.yaml                                # Sail services (Docker)
```


#### Environment variables
Typical Laravel `.env` variables used by this app include (non‑exhaustive):
- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
- Database: `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Sail/MySQL (from compose.yaml): `FORWARD_DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `MYSQL_EXTRA_OPTIONS`
- Vite: `VITE_PORT`

Notes:
- On first install, `APP_KEY` must be set via `php artisan key:generate` (Composer script may also do this on create).
- If using Laravel Sail, environment variables are consumed from `.env` by Docker services in `compose.yaml`.


#### Installation & setup
1) Clone repository
```
git clone <your-fork-or-origin-url> testVOVO
cd testVOVO
```

2) Configure environment
```
cp .env.example .env   # if .env does not exist
```
Edit `.env` to set database credentials and app URL.

3) Install PHP dependencies
```
composer install
```

4) Generate app key
```
php artisan key:generate
```

5) Run migrations and (optionally) seed demo data
```
php artisan migrate
php artisan db:seed    # seeds categories/products if seeders are wired
```

6) Run the application (without Docker)
```
php artisan serve
# App will be available at http://127.0.0.1:8000
```


#### Running with Docker (Laravel Sail)
This repo includes a `compose.yaml` configured for Laravel Sail with MySQL 8.

Setup (first time):
```
cp .env.example .env
composer install
php artisan key:generate
```

Start services:
```
# If Sail is not installed globally, use vendor binary
./vendor/bin/sail up -d
```

Run migrations and seeds inside Sail:
```
./vendor/bin/sail artisan migrate --seed
```

Stop services:
```
./vendor/bin/sail down
```

TODO:
- Verify `compose.yaml` healthcheck array syntax with current Docker Compose (there may be minor schema differences). If issues arise, consider using the Sail‑generated `docker-compose.yml` via `php artisan sail:install`.


#### API usage
Base URL for API routes: `/api`.

Products index
```
GET /api/products
```

Query parameters (validated by `ProductSearchRequest`):
- `q` — search substring in product name
- `price_from` — minimum price (>= 0)
- `price_to` — maximum price (>= `price_from`)
- `category_id` — category ID existing in `categories`
- `in_stock` — boolean (1/0, true/false)
- `rating_from` — minimum rating (0..5)
- `sort` — one of: `newest` (default), `price_asc`, `price_desc`, `rating_desc`
- `per_page` — items per page (1..100, default 15)
- `page` — page number (>=1)

Response shape (`ProductResource`):
```
{
  "data": [
    {
      "id": 1,
      "name": "...",
      "price": 123.45,
      "category_id": 2,
      "in_stock": true,
      "rating": 4.5,
      "created_at": "YYYY-MM-DD HH:MM:SS",
      "updated_at": "YYYY-MM-DD HH:MM:SS",
      "category": {"id": 2, "name": "..."}  // present when eager loaded
    }
  ],
  "meta": {
    "filters_applied": ["q", "price_from", ...],
    "sort_applied": "newest",
    "available_sorts": ["price_asc", "price_desc", "rating_desc", "newest"]
  },
  "links": { /* Laravel pagination links */ }
}
```

Example request:
```
GET /api/products?q=phone&price_from=100&price_to=1000&in_stock=1&rating_from=4&sort=price_desc&per_page=10
```


#### Database
Migrations create the following tables:
- `categories` (id, name unique, slug unique, description nullable, timestamps)
- `products` (id, name, price decimal(10,2), category_id FK, in_stock boolean, rating float(3,1), timestamps)

Seeders (see `database/seeders`):
- `CategorySeeder`
- `ProductSeeder`

Run all seeds:
```
php artisan db:seed
# or inside Sail
./vendor/bin/sail artisan db:seed
```


