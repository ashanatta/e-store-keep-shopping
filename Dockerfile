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

COPY Backend/composer.json Backend/composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

COPY Backend/ .
RUN composer dump-autoload --optimize

RUN chmod -R 775 storage bootstrap/cache \
    && mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views \
    && chown -R www-data:www-data storage bootstrap/cache

# Nginx: serve Laravel public/ and pass PHP to FPM
RUN echo 'server { \
    listen 80 default_server; \
    root /var/www/html/public; \
    index index.php; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
        fastcgi_read_timeout 300; \
    } \
}' > /etc/nginx/sites-available/default

EXPOSE 80

CMD php artisan migrate --force \
    && php-fpm -D \
    && nginx -g "daemon off;"
