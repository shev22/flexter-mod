#!/bin/bash
set -e

# Create this file in ./docker-scripts/entrypoint.sh

# Wait for database to be ready
/wait-for-mysql.sh

# Install dependencies if needed
if [ ! -d "vendor" ]; then
    echo "Installing Composer dependencies..."
    composer install --no-interaction --no-progress
fi

# Generate application key if not set
if [ ! -s ".env" ] || ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    echo "Generating application key..."
    php artisan key:generate
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Cache configuration and routes in production
if [ "$APP_ENV" = "production" ]; then
    echo "Caching configuration and routes..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Set correct permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Execute the command passed to the container
exec "$@"
