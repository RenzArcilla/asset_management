# --- Stage 1: Composer dependencies ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

# --- Stage 2: Node build (Vite/Tailwind assets) ---
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- Stage 3: Application image ---
FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    unzip \
    git \
    autoconf g++ make \
    && docker-php-ext-configure intl \
    && docker-php-ext-install pcntl pdo pdo_mysql gd bcmath zip opcache mbstring exif intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get purge -y autoconf g++ make \
    && apt-get autoremove -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Match container's www-data UID/GID to host user, so bind-mounted volumes are writable
ARG UID=1000
ARG GID=1000
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

# Install Composer binary
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /usr/share/nginx/html/

# Copy dependency manifests first for cache reuse
COPY composer.json composer.lock ./

# Bring in vendor from build stage (already resolved)
COPY --from=vendor /app/vendor ./vendor

# Copy application code
COPY . .

# Bring in compiled frontend assets
COPY --from=assets /app/public/build ./public/build

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && sed 's_@php artisan package:discover_/bin/true_;' -i composer.json \
    && composer dump-autoload --optimize --no-dev \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache /usr/share/nginx/html \
    && chmod -R 775 storage bootstrap/cache

COPY ./scripts/php-fpm-entrypoint /usr/local/bin/php-entrypoint
RUN chmod a+x /usr/local/bin/php-entrypoint

USER www-data

ENTRYPOINT ["/usr/local/bin/php-entrypoint"]

CMD ["php-fpm"]