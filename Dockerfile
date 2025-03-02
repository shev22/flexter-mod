FROM php:8.2-fpm

# Install dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    default-mysql-client \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql zip exif pcntl gd \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli

# Install Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    chmod +x /usr/local/bin/composer

# Create working directories
RUN mkdir -p /var/www/html
WORKDIR /var/www/html

# Create MySQL config directory
RUN mkdir -p /var/www/html/mysql

# Wait for MySQL script (for better container startup coordination)
COPY ./docker-scripts/wait-for-mysql.sh /wait-for-mysql.sh
RUN chmod +x /wait-for-mysql.sh

# Set up entrypoint script
COPY ./docker-scripts/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Create storage directory structure if it doesn't exist
RUN mkdir -p /var/www/html/storage/logs /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views /var/www/html/storage/framework/cache \
    /var/www/html/bootstrap/cache

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
