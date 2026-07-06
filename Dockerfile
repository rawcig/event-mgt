FROM php:8.2-cli

# ── System dependencies ────────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
 && rm -rf /var/lib/apt/lists/*

# ── Node.js 20 ─────────────────────────────────────────────────────────────────
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs \
 && rm -rf /var/lib/apt/lists/*

# ── PHP extensions ─────────────────────────────────────────────────────────────
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# ── Composer ───────────────────────────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ── Install PHP deps first (better layer caching) ──────────────────────────────
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# ── Install JS deps & build assets ────────────────────────────────────────────
COPY package.json package-lock.json ./
RUN npm ci

# ── Copy the rest of the app ───────────────────────────────────────────────────
COPY . .

# ── Build frontend after full copy (needs resources/ etc.) ────────────────────
RUN npm run build

# ── Permissions ───────────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
 && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 10000

# ── Startup ───────────────────────────────────────────────────────────────────
# key:generate and db:seed are intentionally REMOVED:
#   - APP_KEY must be set as a static env var in Render's dashboard
#   - db:seed should be run manually via Render Shell when actually needed
CMD ["sh", "-c", \
  "php artisan migrate --force && \
   php artisan storage:link && \
   php artisan config:cache && \
   php artisan route:cache && \
   php artisan view:cache && \
   php -S 0.0.0.0:${PORT:-10000} -t public"]