FROM php:8.2-fpm-alpine

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache \
    libpq-dev \
    libzip-dev \
    unzip \
    \
 && docker-php-ext-install pdo pdo_pgsql

ARG USER_UID=1000
USER $USER_UID

WORKDIR /srv
COPY . /srv