# Use an official PHP Apache image
FROM php:8.2-apache

# Install the mysqli extension needed for your database connection
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy your application files to the web server's root directory
COPY . /var/www/html/

# Expose port 80 for web traffic
EXPOSE 80