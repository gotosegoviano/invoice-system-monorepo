
FROM php:8.4-fpm-alpine AS vendor

RUN apk add --no-cache git unzip

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist
COPY . .
RUN composer dump-autoload --optimize

FROM php:8.4-fpm-alpine

RUN apk add --no-cache nginx bash curl libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev libxml2-dev zip unzip

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=vendor /app /var/www/html
WORKDIR /var/www/html
RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 8080

CMD sh -c "php-fpm -D && nginx -g 'daemon off;'"