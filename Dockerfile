FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpq-dev \
    libzip-dev \
    unzip && \
    docker-php-ext-install pdo pdo_pgsql zip && \
    a2enmod rewrite && \
    rm -rf /var/lib/apt/lists/*

# Increase memory limit for composer
RUN echo "memory_limit=2G" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini

# Set working directory
WORKDIR /var/www/html

# Copy composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html/storage && \
    mkdir -p /var/www/html/bootstrap/cache && \
    chmod -R 755 /var/www/html/bootstrap/cache

# Configure Apache
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Expose port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
