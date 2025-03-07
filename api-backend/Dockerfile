# Use PHP with FPM
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Copy Laravel files
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --prefer-dist

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]
