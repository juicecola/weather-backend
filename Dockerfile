# Base image
FROM php:8.1-fpm

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    curl \
    && docker-php-ext-install pdo pdo_mysql mbstring zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application code
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www

# Install PHP dependencies
RUN composer install --optimize-autoloader --no-dev

# Expose port 8000 for Laravel development server
EXPOSE 8000

# Start Laravel development server
CMD php artisan serve --host=0.0.0.0 --port=8000
