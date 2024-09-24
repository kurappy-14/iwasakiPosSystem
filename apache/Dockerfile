FROM php:8.3-apache

COPY ./php.ini /usr/local/etc/php/
# COPY ./000-default.conf /etc/apache2/sites-enabled/
# COPY ./apache2.conf /etc/apache2/

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN apt-get update && apt-get install -y \
    vim \ 
    zip

RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
RUN a2enmod rewrite

WORKDIR /var/www/html/

CMD ["apache2-foreground"]
