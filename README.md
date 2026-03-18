# BashBookmark
personal bash script manager — save, tag, and fast-search reusable snippets

## Stack

- **Laravel 11** — backend framework
- **Livewire 3** — reactive UI without a JS build step
- **Tailwind CSS** — utility-first styling
- **Alpine.js** — lightweight JS for UI interactions
- **SQLite** — zero-config file database (persisted via Docker named volume)

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

---

## Docker Deployment (Windows 11 + Docker Desktop)

### Prerequisites

| Requirement | Minimum version |
|---|---|
| [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/) | 4.x |
| WSL 2 backend | enabled (default in Docker Desktop 4.x) |
| Linux containers mode | enabled (default) |

> **Windows note:** Docker Desktop must be running **Linux containers**, not Windows containers.
> Right-click the Docker tray icon and verify it shows *"Switch to Windows containers…"* (meaning Linux mode is currently active).

### Quick start

```powershell
# 1. Clone
git clone https://github.com/khunthengPRT/BashBookmark.git
cd BashBookmark

# 2. Create your environment file
copy .env.docker .env
# Open .env and set APP_PASSWORD to a value of your choice.
# Leave APP_KEY blank on first run — it is generated automatically.

# 3. Build the image and start the container
docker compose up -d --build

# 4. Open the app
start http://localhost:8080
```

The first `docker compose up --build` takes a few minutes while npm, composer, and
asset compilation run inside the image. Subsequent starts are fast.

### Persisting your data

SQLite and all uploaded files are stored in **Docker named volumes**, not inside
the container image. Your snippets survive:

- `docker compose restart` — normal restart
- `docker compose up -d --build` — image rebuild after a `git pull`

To **wipe all data** and start fresh:

```powershell
docker compose down -v
```

### Changing the port

Edit `docker-compose.yml` and change the left side of the port mapping:

```yaml
ports:
  - "9090:80"   # now reachable at http://localhost:9090
```

Then restart: `docker compose up -d`.

### Updating after a git pull

```powershell
git pull
docker compose up -d --build
```

The entrypoint runs `php artisan migrate --force` automatically on every startup,
so database schema changes are applied without manual steps.

### Useful commands

```powershell
# View live logs
docker compose logs -f

# Open a shell inside the container
docker compose exec app sh

# Run artisan commands
docker compose exec app php artisan tinker

# Generate a fresh APP_KEY (copy the output into your .env)
docker compose run --rm app php artisan key:generate --show

# Stop the container (data is preserved)
docker compose down

# Stop and delete all data volumes
docker compose down -v
```

---

## Local Setup (no Docker)

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

## License

MIT
