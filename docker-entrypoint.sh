#!/bin/sh
set -e

echo "🔄 Starting Laravel container..."

# Clear caches so runtime env vars are always used
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Run migrations (idempotent — safe to run every startup)
echo "🚀 Running migrations..."
php artisan migrate --force || true

echo "✅ Laravel startup complete. Handing over to CMD..."

echo "🚀 Caching new Configs"
php artisan config:cache || true

exec "$@"
