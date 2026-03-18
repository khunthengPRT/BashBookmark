# BashBookmark
personal bash script manager — save, tag, and fast-search reusable snippets

## Stack

- **Laravel 11** — backend framework
- **Livewire 3** — reactive UI without a JS build step
- **Tailwind CSS** — utility-first styling
- **Alpine.js** — lightweight JS for UI interactions
- **SQLite** (local) / **PostgreSQL** (production)

## Features

- Create, edit, and delete bash snippets
- Tag snippets with comma-separated tags
- Full-text search (LIKE on SQLite, fulltext index on MySQL/PostgreSQL)
- Single-page feel via Livewire — no full reloads
- Single-user, password-protected via `APP_PASSWORD` in `.env`

## Database

| Column | Type | Notes |
|---|---|---|
| id | bigint | primary key |
| title | string | |
| body | text | the bash snippet |
| description | text | nullable |
| tags | JSON | nullable array of strings |
| created_at | timestamp | auto-set on insert |

## Local Setup

```bash
git clone https://github.com/khunthengPRT/BashBookmark.git
cd BashBookmark

composer install
npm install

cp .env.example .env
php artisan key:generate

# SQLite is the default — no extra DB config needed locally
touch database/database.sqlite

php artisan migrate

npm run dev
php artisan serve
```

Visit `http://localhost:8000`.

## Environment Variables

```dotenv
APP_PASSWORD=your-secret-password   # used by the auth middleware
DB_CONNECTION=sqlite                 # or pgsql for production
```

## Docker Deployment

### Prerequisites

- [Docker](https://docs.docker.com/get-docker/) and [Docker Compose](https://docs.docker.com/compose/install/) installed

### Quick start

```bash
git clone https://github.com/khunthengPRT/BashBookmark.git
cd BashBookmark

cp .env.example .env
# Edit .env — set APP_PASSWORD, APP_KEY, and DB_* values (see below)

docker compose up -d
```

The app will be available at `http://localhost:8080`.

On first run, run migrations inside the container:

```bash
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
```

### `docker-compose.yml` (example)

```yaml
services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8080:80"
    environment:
      APP_ENV: production
      APP_KEY: "${APP_KEY}"
      APP_PASSWORD: "${APP_PASSWORD}"
      DB_CONNECTION: pgsql
      DB_HOST: db
      DB_PORT: 5432
      DB_DATABASE: bashbookmark
      DB_USERNAME: "${DB_USERNAME}"
      DB_PASSWORD: "${DB_PASSWORD}"
    depends_on:
      db:
        condition: service_healthy

  db:
    image: postgres:16-alpine
    environment:
      POSTGRES_DB: bashbookmark
      POSTGRES_USER: "${DB_USERNAME}"
      POSTGRES_PASSWORD: "${DB_PASSWORD}"
    volumes:
      - pgdata:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U ${DB_USERNAME}"]
      interval: 5s
      retries: 5

volumes:
  pgdata:
```

### `Dockerfile` (example)

```dockerfile
FROM php:8.4-fpm-alpine AS base

RUN apk add --no-cache nginx nodejs npm postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql opcache

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader \
    && npm ci && npm run build && rm -rf node_modules \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 80
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
```

### Useful commands

```bash
# View logs
docker compose logs -f app

# Run artisan commands
docker compose exec app php artisan migrate --force
docker compose exec app php artisan cache:clear

# Stop and remove containers (data volume is preserved)
docker compose down

# Full teardown including the database volume
docker compose down -v
```

## License

MIT
