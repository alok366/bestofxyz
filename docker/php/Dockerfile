FROM php:8.4-fpm-bookworm

ARG UID=1000
ARG GID=1000

# Install system dependencies & MariaDB client CLI
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libzip-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions for Illuminate 11 & Redis
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions \
    pdo_mysql \
    redis \
    bcmath \
    pcntl \
    intl \
    zip \
    gd \
    opcache

# Install Composer v2
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Remap www-data UID/GID to match host developer (avoids Linux file permission lockouts)
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

WORKDIR /var/www/html

EXPOSE 9000
CMD ["php-fpm"]
