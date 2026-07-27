FROM php:8.2-apache

# Install MySQL extensions for database connectivity
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copy all project files into Apache's web root
COPY . /var/www/html/

# Create a clean startup script natively inside the container using /bin/sh
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'sed -i "s/80/${PORT:-10000}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf' >> /start.sh && \
    echo 'exec apache2-foreground' >> /start.sh && \
    chmod +x /start.sh

# Execute the startup script
CMD ["/start.sh"]