FROM php:7.4-apache

# Install system dependencies + PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        mbstring \
        intl \
        zip \
        gd \
        soap \
        pcntl \
        bcmath \
    && a2enmod rewrite headers expires \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && rm -rf /var/lib/apt/lists/*

# Laravel app directory
WORKDIR /var/www/html

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy project
COPY . /var/www/html

# Point Apache document root to Laravel public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Laravel permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["apache2-foreground"]