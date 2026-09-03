#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Setting permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "Starting Apache..."
exec apache2-foreground
