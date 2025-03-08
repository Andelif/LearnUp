#!/bin/bash

# Wait for MySQL to be available
echo "Waiting for MySQL..."
until mysql -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SELECT 1" &>/dev/null; do
  echo "Waiting for MySQL to be ready..."
  sleep 10
done

# Install Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --prefer-dist

# Run migrations and other necessary commands
echo "Running Laravel migrations..."
php artisan migrate --force

# Start PHP-FPM
exec php-fpm
