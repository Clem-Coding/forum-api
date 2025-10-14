# Base image with PHP 8.4 CLI, minimal setup for running Symfony
FROM php:8.4-cli


# Install system dependencies and PHP extensions required by Symfony
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    git \
    mariadb-client \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy dependency files first to leverage Docker layer caching
COPY composer.json composer.lock ./

# Install dependencies without scripts or autoload to maximize cache efficiency
RUN composer install --no-interaction --no-scripts --no-autoloader

# Copy application code
COPY . .

# Generate optimized autoload files
RUN composer dump-autoload --optimize
