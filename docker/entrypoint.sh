#!/bin/sh
set -e

# ---------------------------------------------------------------------------
# Generate APP_KEY if none is provided
# ---------------------------------------------------------------------------
if [ -z "$APP_KEY" ]; then
    echo "[entrypoint] APP_KEY not set — generating one now..."
    php artisan key:generate --no-interaction
fi

# ---------------------------------------------------------------------------
# Ensure the SQLite database file exists (persisted volume may be empty)
# ---------------------------------------------------------------------------
DB_FILE="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
if [ ! -f "$DB_FILE" ]; then
    echo "[entrypoint] Creating SQLite database at $DB_FILE"
    mkdir -p "$(dirname "$DB_FILE")"
    touch "$DB_FILE"
    chown www-data:www-data "$DB_FILE"
fi

# ---------------------------------------------------------------------------
# Run migrations (idempotent — safe to run on every startup)
# ---------------------------------------------------------------------------
echo "[entrypoint] Running database migrations..."
php artisan migrate --force --no-interaction

# ---------------------------------------------------------------------------
# Recreate storage symlink (public/storage -> storage/app/public)
# ---------------------------------------------------------------------------
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link --no-interaction || true
fi

# ---------------------------------------------------------------------------
# Fix ownership in case volumes were mounted as root
# ---------------------------------------------------------------------------
chown -R www-data:www-data \
    /var/www/html/database \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

echo "[entrypoint] Starting supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
