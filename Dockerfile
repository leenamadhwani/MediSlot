FROM php:8.2-apache

# Install MySQL extensions for database connectivity
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all project files into Apache's web root
COPY . /var/www/html/

# Copy start script, fix Windows CRLF line endings automatically, and make it executable
COPY start.sh /usr/local/bin/start.sh
RUN sed -i -e 's/\r$//' /usr/local/bin/start.sh && chmod +x /usr/local/bin/start.sh

CMD ["/usr/local/bin/start.sh"]