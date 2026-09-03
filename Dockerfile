FROM php:8.2-apache

WORKDIR /var/www/html

COPY . .

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN a2enmod rewrite

EXPOSE 80

CMD ["apache2-foreground"]