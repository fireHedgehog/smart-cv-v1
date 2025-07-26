FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    nginx supervisor libzip-dev zip unzip mariadb-client \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# 👉 用 ARG 控制 UID/GID，默认 33（生产用）
ARG WWW_DATA_UID=33
ARG WWW_DATA_GID=33
RUN usermod -u $WWW_DATA_UID www-data && groupmod -g $WWW_DATA_GID www-data

COPY . .

RUN composer install --no-dev --optimize-autoloader \
    && mkdir -p storage/logs \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY ./nginx.conf /etc/nginx/conf.d/default.conf
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000
CMD ["/usr/bin/supervisord"]
