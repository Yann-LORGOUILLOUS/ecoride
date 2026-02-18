FROM php:8.2-cli

# System deps required for MongoDB TLS + zip + CA certs
RUN apt-get update && apt-get install -y \
    ca-certificates \
    openssl \
    libssl-dev \
    pkg-config \
    libsasl2-dev \
    libzip-dev \
    zip \
  && docker-php-ext-install pdo pdo_mysql mysqli zip \
  && rm -rf /var/lib/apt/lists/*

# MongoDB PHP extension (compiled with SSL thanks to libssl-dev)
RUN pecl install mongodb \
  && docker-php-ext-enable mongodb

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./

# Install PHP deps (DO NOT composer update in a container build)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy app
COPY . .

RUN [ -d storage ] && chmod -R 755 storage || true

EXPOSE 80
CMD ["sh", "-lc", "php -S 0.0.0.0:${PORT:-80} -t public"]
