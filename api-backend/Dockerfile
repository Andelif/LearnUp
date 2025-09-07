FROM php:8.2-cli

# System deps + Postgres PDO driver
RUN apt-get update && apt-get install -y git unzip libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /app

# Copy the whole app first so composer scripts (which call "artisan") can run
COPY . .

# Install PHP deps (runs Laravel's post-scripts safely because artisan exists now)
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Ensure writable dirs
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache || true

# Render expects your app to bind to PORT
ENV PORT=10000
EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
