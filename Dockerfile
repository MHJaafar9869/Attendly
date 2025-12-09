FROM dunglas/frankenphp:1.9-php8.4-bookworm AS base

WORKDIR /var/www/html

ARG HOST_UID
ARG HOST_GID

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=UTC \
    XDG_CONFIG_HOME=/var/www/html/config \
    XDG_DATA_HOME=/var/www/html/data \
    XDEBUG_MODE=off

RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y curl git zip unzip libpng-dev libicu-dev nodejs npm netcat-openbsd default-mysql-client && \
    rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
    bcmath gd intl mbstring opcache pdo_mysql redis zip pcntl

RUN curl -sLS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g ${HOST_UID} appuser && \
    useradd -u ${HOST_GID} -g appuser -m -s /bin/bash appuser

COPY composer.json composer.lock ./

RUN composer install --no-interaction --prefer-dist --no-scripts --no-dev

COPY . .
COPY .env.docker .env

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY Docker/Caddyfile /etc/caddy/Caddyfile

COPY Docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80 5173

USER appuser

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
