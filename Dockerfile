FROM php:7.4-alpine

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

COPY . .

RUN chown -R www-data:www-data \
    /var/www/html/storage \
    /var/www/html/bootstrap/cache

CMD php artisan serve --host=0.0.0.0 --port=8000
