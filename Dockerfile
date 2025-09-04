# Stage 1: Base image with PHP and Apache
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip curl libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev libonig-dev nano \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd mbstring xml sockets

RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf


# Enable mod_rewrite
RUN a2enmod rewrite

# Git safe directory
RUN git config --global --add safe.directory /var/www/html

# Set working directory
WORKDIR /var/www/html

# COPY source files
COPY . .
RUN ls -la /var/www/html
RUN cat /var/www/html/.env

# Install Composer manually
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts


# Set proper permissions
RUN chown -R www-data:www-data /var/www/html /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html

CMD ["sh", "-c", "php artisan package:discover && apache2-foreground"]

