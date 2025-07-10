FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git zip unzip libpng-dev libzip-dev default-mysql-client

RUN docker-php-ext-install pdo pdo_mysql zip gd

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-interaction --optimize-autoloader

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf

RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]
