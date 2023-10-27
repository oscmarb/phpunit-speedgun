FROM php:8.0-alpine3.13

RUN mkdir /app
WORKDIR /app

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
