# =============================================================================
# Stage 1 — Frontend asset build
# =============================================================================
FROM node:20-alpine AS frontend-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# =============================================================================
# Stage 2 — Production image (PHP-FPM + Nginx via Supervisord)
# =============================================================================
FROM php:8.2-fpm-alpine AS app

# System packages: nginx, supervisor, and PHP extension dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite \
    sqlite-dev

# PHP extensions required by Laravel
RUN docker-php-ext-install \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application source (vendor and node_modules excluded via .dockerignore)
COPY . .

# Copy compiled frontend assets from the builder stage
COPY --from=frontend-builder /app/public/build ./public/build

# Install PHP production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --quiet

# Nginx site config
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Supervisord config
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Prepare writable directories and SQLite database file
RUN mkdir -p \
        database \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/logs \
        bootstrap/cache \
    && touch database/database.sqlite \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
