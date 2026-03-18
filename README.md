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

## License

MIT
