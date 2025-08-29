# --- Base PHP image
FROM php:8.2-cli

# Install system deps + Postgres PDO driver
RUN apt-get update && apt-get install -y git unzip libpq-dev \
    && docker-php-ext-install pdo_pgsql

# Install Composer (copy from official composer image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Workdir inside the image
WORKDIR /app

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Copy the rest of the app
COPY . .

# Ensure runtime dirs are writable
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache || true

# Default port Render expects your app to bind to
ENV PORT=10000
EXPOSE 10000

# Default command (Render can override via "Docker Command", but this is fine)
CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
