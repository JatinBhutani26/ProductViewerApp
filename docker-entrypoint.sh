#!/bin/sh
set -e

echo "🔄 Starting Laravel container..."

php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "🚀 Running migrations..."
php artisan migrate --force || true

echo "🚀 Caching config with Azure runtime env..."
php artisan config:cache || true

exec "$@"
