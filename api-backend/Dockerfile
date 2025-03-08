# Use PHP with FPM
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli

# Install Composer (Best Practice)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer    

# Copy Laravel files
COPY . .

# Set permissions
RUN chmod -R 777 storage bootstrap/cache

# Install Composer
RUN composer install --no-dev --prefer-dist



# Expose port 9000
EXPOSE 9000

CMD ["php-fpm"]
