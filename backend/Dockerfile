
FROM php:8.4-fpm-alpine AS vendor

RUN apk add --no-cache git unzip

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist
COPY . .
RUN composer dump-autoload --optimize

FROM php:8.4-fpm-alpine

RUN apk add --no-cache nginx bash curl libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev libxml2-dev zip unzip gettext-base

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=vendor /app /var/www/html
WORKDIR /var/www/html
RUN chmod -R 775 storage bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 8080

CMD sh -c "envsubst '\$PORT' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default.conf.tmp && mv /etc/nginx/conf.d/default.conf.tmp /etc/nginx/conf.d/default.conf && php-fpm -D && nginx -g 'daemon off;'"