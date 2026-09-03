#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Creating admin user..."
php artisan tinker --execute="
\$user = \App\Models\User::where('email', 'admin@unesa.ac.id')->first();
if (!\$user) {
  \App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@unesa.ac.id',
    'password' => bcrypt('admin123456'),
    'role' => 'admin'
  ]);
  echo 'Admin user created';
} else {
  echo 'Admin user already exists';
}
"

echo "Linking storage..."
php artisan storage:link --force || true

echo "Setting permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "Starting Apache..."
exec apache2-foreground
