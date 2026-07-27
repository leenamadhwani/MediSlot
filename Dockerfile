FROM php:8.2-apache

# Install MySQL extensions for database connectivity
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all project files into Apache's web root
COPY . /var/www/html/

# Update Apache port dynamically at runtime using Render's $PORT variable and start Apache
CMD sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf && apache2-foreground