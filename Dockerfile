FROM php:8.3-apache

# Install MySQLi extension for PHP
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files to Apache web root
COPY . /var/www/html/

# Set ownership permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
