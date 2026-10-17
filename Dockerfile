# Use official PHP with Apache image
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    mariadb-client \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html/ \
    && chmod -R 755 /var/www/html/ \
    && chmod -R 777 /var/www/html/config/ \
    && chmod -R 777 /var/www/html/assets/

# Configure Apache to listen on port 3001 and serve from /var/www/html
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    sed -i 's/Listen [0-9]*/Listen 3001/' /etc/apache2/ports.conf && \
    sed -i 's/<VirtualHost \*:[0-9]*>/<VirtualHost *:3001>/' /etc/apache2/sites-available/000-default.conf

# Expose port 3001
EXPOSE 3001

# Start Apache
CMD ["apache2-foreground"]
