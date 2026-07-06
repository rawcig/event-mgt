FROM php:8.2-cli

# Install system packages
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    bcmath \
    exif \
    pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy composer files first
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Copy the rest of the project
COPY . .

# Cache Laravel
RUN php artisan config:clear || true

EXPOSE 10000

CMD sh -c "\
php artisan migrate --force && \
php artisan storage:link || true && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
php artisan serve --host=0.0.0.0 --port=${PORT}"