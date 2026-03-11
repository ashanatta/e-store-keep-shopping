# NGINX + PHP-FPM (no Apache). Use this when Railway Root Directory = Backend.
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize

RUN chmod -R 775 storage bootstrap/cache \
    && mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache

RUN echo 'server { listen 80 default_server; root /var/www/html/public; index index.php; location / { try_files $uri $uri/ /index.php?$query_string; } location ~ \.php$ { fastcgi_pass 127.0.0.1:9000; fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; include fastcgi_params; fastcgi_read_timeout 300; } }' > /etc/nginx/sites-available/default

EXPOSE 80

RUN printf '%s\n' '#!/bin/sh' 'set -e' 'echo "NGINX+PHP-FPM starting"' 'php artisan migrate --force' 'php-fpm -D' 'exec nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

CMD ["/start.sh"]
