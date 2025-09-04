# Use official PHP image with FPM
FROM php:8.2-fpm

# Install system dependencies & PHP extensions
# - libssl-dev is needed so the phpredis extension supports TLS (Azure Redis uses 6380/TLS)
RUN apt-get update && apt-get install -y \
    git curl unzip nodejs npm libpq-dev libssl-dev \
 && docker-php-ext-install pdo pdo_pgsql \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . .

# Install PHP dependencies (no dev, optimized autoloader)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Build frontend assets (deterministic install)
RUN npm ci && npm run build

# Expose port 8000
EXPOSE 8000

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Use entrypoint (good place to run config cache at runtime if you want)
ENTRYPOINT ["docker-entrypoint.sh"]

# Start Laravel with Artisan (kept as-is for your current setup)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]