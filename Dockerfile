# Use official PHP image with FPM
FROM php:8.2-fpm

# Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    git curl libpq-dev unzip nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend assets
RUN npm install && npm run build

# Cache config
RUN php artisan config:cache

# Expose port 8000
EXPOSE 8000

# Start Laravel with Artisan
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]