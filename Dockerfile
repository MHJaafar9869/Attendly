FROM dunglas/frankenphp:1.9-php8.4-bookworm AS base

WORKDIR /var/www/html

ARG HOST_UID
ARG HOST_GID

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=UTC \
    XDG_CONFIG_HOME=/var/www/html/config \
    XDG_DATA_HOME=/var/www/html/data \
    XDEBUG_MODE=off

RUN apt-get update && \
    apt-get install -y \
        curl \
        # Tool for transferring data via URLs.
        git \
        # Version control for fetching dependencies.
        zip \
        # Utility for file compression operations.
        unzip \
        # Required by Composer to extract archives.
        libpng-dev \
        # PNG library for GD PHP extension.
        libicu-dev \
        # Unicode library for INTL PHP extension.
        netcat-openbsd \
        # Used by entrypoint to wait for services.
        netcat-traditional \
        # Used by entrypoint to wait for services.
        default-mysql-client \
        # Command-line tool for MySQL interaction.
    && rm -rf /var/lib/apt/lists/*
    # Cleans package cache to reduce size.

RUN install-php-extensions \
    bcmath \
    # Enables arbitrary precision mathematics for PHP.
    gd \
    # Image manipulation library support for PHP.
    intl \
    # Unicode and localization support for PHP.
    mbstring \
    # Multibyte string functions support (essential for modern PHP).
    opcache \
    # PHP code caching for performance.
    pdo_mysql \
    # Database access for MySQL/MariaDB.
    redis \
    # Cache and session handler extension.
    zip \
    # Supports reading and writing Zip files.
    pcntl
    # Process control and signal handling.

RUN curl -sLS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g ${HOST_GID} appuser && \
    useradd -u ${HOST_UID} -g appuser -m -s /bin/bash appuser

COPY composer.json composer.lock ./

RUN composer install --no-interaction --prefer-dist --no-scripts --no-dev

COPY . .
COPY .env.docker .env
COPY Docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

FROM dunglas/frankenphp:1.9-php8.4-bookworm AS final

ARG HOST_UID
ARG HOST_GID

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=UTC \
    XDG_CONFIG_HOME=/var/www/html/config \
    XDG_DATA_HOME=/var/www/html/data \
    XDEBUG_MODE=off

WORKDIR /var/www/html

RUN groupadd -g ${HOST_GID} appuser && \
    useradd -u ${HOST_UID} -g appuser -m -s /bin/bash appuser

RUN apt-get update && apt-get install -y netcat-traditional && \
        rm -rf /var/lib/apt/lists/*

RUN install-php-extensions pdo_mysql mbstring opcache

COPY --from=base /usr/local/bin/composer /usr/local/bin/composer
COPY --from=base /usr/local/bin/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY --from=base /var/www/html /var/www/html
COPY --from=base /var/www/html/Docker/Caddyfile /etc/caddy/Caddyfile

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80 5173

USER appuser

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
