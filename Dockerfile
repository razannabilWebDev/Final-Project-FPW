# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1 — build aset frontend (Vite + Tailwind)
# ---------------------------------------------------------------------------
FROM node:20-alpine AS node-build

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2 — install dependency PHP (tanpa dev)
# ---------------------------------------------------------------------------
FROM php:8.3-cli-alpine AS php-deps

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
        icu-dev libzip-dev libpng-dev oniguruma-dev \
    && apk add --no-cache git unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql mbstring bcmath intl zip exif gd pcntl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev --no-scripts --no-interaction --prefer-dist \
        --optimize-autoloader

# ---------------------------------------------------------------------------
# Stage final — PHP-FPM
# ---------------------------------------------------------------------------
FROM php:8.3-fpm-alpine

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
        icu-dev libzip-dev libpng-dev oniguruma-dev \
    && apk add --no-cache git unzip nginx \
        libicu libzip libpng libjpeg-turbo freetype oniguruma \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql mbstring bcmath intl zip exif gd pcntl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html

COPY . .
COPY --from=php-deps /app/vendor ./vendor
COPY --from=node-build /app/public/build ./public/build

COPY docker/php.ini /usr/local/etc/php/conf.d/zz-settings.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
