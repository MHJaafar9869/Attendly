FROM dunglas/frankenphp:1.9-php8.4-bookworm AS base

ARG WWWUSER=1000
ARG WWWGROUP=1000
ARG NODE_VERSION=22

WORKDIR /var/www/html
COPY . /var/www/html
COPY Caddyfile /etc/caddy/Caddyfile

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=UTC \
    XDG_CONFIG_HOME=/var/www/html/config \
    XDG_DATA_HOME=/var/www/html/data \
    XDEBUG_MODE=off

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y curl git zip unzip libpng-dev libicu-dev nodejs npm sqlite3 && \
    rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    bcmath curl gd intl mbstring opcache pdo_mysql redis zip pcntl

RUN curl -sLS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

RUN groupadd -f -g ${WWWGROUP} attendly && \
    useradd -ms /bin/bash -g attendly -u ${WWWUSER} attendly && \
    mkdir -p /var/www/html/config/caddy /var/www/html/data/caddy && \
    git config --global --add safe.directory /var/www/html && \
    chown -R attendly:attendly /var/www/html

USER attendly

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN [ -f package.json ] && npm install && npm run build || true

EXPOSE 80 5173
