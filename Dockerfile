FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    unzip \
    git \
    mariadb-client \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY backend/ .

RUN composer install --no-interaction --optimize-autoloader
