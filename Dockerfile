FROM php:8.2-cli

# Install extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_mysql mysqli zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first (better caching)
COPY composer.json composer.lock* ./

# Install dependencies
RUN pecl install mongodb \
    &&  echo "extension=mongodb.so" > $PHP_INI_DIR/conf.d/mongo.ini
RUN composer update
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application files
COPY . .

# Set permissions
RUN chmod -R 755 storage

EXPOSE 80

# Use PHP built-in server (simpler, works well on Railway)
CMD php -S 0.0.0.0:${PORT:-80} -t public
