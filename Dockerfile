# ─────────────────────────────────────────────
# 1. PHP + Composer dependencies (builder stage)
# ─────────────────────────────────────────────
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./

# Install PHP dependencies only
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts

# Copy full project (needed for autoload + artisan)
COPY . .

# Run Laravel optimization safely (no DB)
RUN php artisan package:discover --no-interaction || true


# ─────────────────────────────────────────────
# 2. Node build stage (frontend assets)
# ─────────────────────────────────────────────
FROM node:20 AS node

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

RUN npm run build


# ─────────────────────────────────────────────
# 3. Runtime stage (final image)
# ─────────────────────────────────────────────
FROM php:8.2-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip curl libpng-dev libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring bcmath exif pcntl

WORKDIR /var/www

# Copy PHP vendor from builder
COPY --from=vendor /app /var/www

# Copy built frontend assets
COPY --from=node /app/public/build /var/www/public/build

# Permissions (important for Laravel storage)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
 && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

# Runtime command (Render injects PORT)
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}"]