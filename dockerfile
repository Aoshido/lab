FROM php:8.2-fpm

WORKDIR /var/www/html

# Install dependencies
RUN apt-get update && \
    apt-get install -y \
        git \
        unzip \
        libzip-dev \
        zlib1g-dev \
        libpq-dev \
        supervisor \
        librabbitmq-dev \
        libicu-dev

# Install RabbitMQ PHP extension
RUN pecl install amqp && \
    docker-php-ext-enable amqp

# Install Apache and enable required modules
RUN apt-get install -y apache2 && \
    a2enmod rewrite && \
    a2enmod proxy && \
    a2enmod proxy_fcgi && \
    a2enmod proxy_http

# Install PostgreSQL driver and intl extension
RUN docker-php-ext-install pdo pdo_pgsql intl

# Configure Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Enable the required site configuration
RUN a2ensite 000-default

# Create Apache run and log directories
RUN mkdir -p /var/run/apache2 /var/log/apache2

# Set valid values for Apache variables
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data

# Install Composer globally
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php -r "unlink('composer-setup.php');"

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Copy supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80
EXPOSE 80

# Start supervisord to manage Apache and PHP-FPM
CMD ["/usr/bin/supervisord", "-n"]
