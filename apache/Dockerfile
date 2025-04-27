FROM php:8.3-apache

COPY ./php.ini /usr/local/etc/php/
COPY ./apache2.conf /etc/apache2/sites-available/000-default.conf
COPY ./init.sh /usr/local/bin/init.sh

ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN apt-get update && apt-get install -y \
    vim \ 
    zip 


RUN docker-php-ext-install mysqli pdo pdo_mysql
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
RUN a2enmod rewrite

RUN chmod +x /usr/local/bin/init.sh

WORKDIR /var/www/html/
