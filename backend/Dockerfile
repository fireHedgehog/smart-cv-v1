# backend/Dockerfile
FROM php:8.3-fpm

# 安装扩展和 Composer
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip mariadb-client \
    && docker-php-ext-install pdo_mysql

# 安装 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install

EXPOSE 9000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
