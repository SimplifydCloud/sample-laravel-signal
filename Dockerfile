FROM composer:2 AS composer
FROM php:8.4-apache-bookworm AS runtime
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libonig-dev libxml2-dev unzip \
    && docker-php-ext-install pdo_pgsql pdo_mysql mbstring dom \
    && sed -i 's/Listen 80$/Listen 8080/' /etc/apache2/ports.conf \
    && rm -rf /var/lib/apt/lists/*
WORKDIR /var/www/html
COPY vhost.conf /etc/apache2/sites-available/000-default.conf
FROM runtime AS build
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --no-dev --optimize \
    && mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && rm -f bootstrap/cache/packages.php bootstrap/cache/services.php
FROM runtime
ENV APP_ENV=production APP_DEBUG=false LOG_CHANNEL=stderr
COPY --from=build /var/www/html .
RUN chown -R www-data:www-data bootstrap/cache storage database
EXPOSE 8080
CMD ["apache2-foreground"]
