ARG PHP_FPM_ALPINE_TAG=8.1
ARG COMPOSER_TAG=2.1.14
ARG BUILD_ENV=dev
ARG PROJECT_NAME=api

# Vendor
FROM composer:$COMPOSER_TAG AS dependencies
COPY app/composer.* /app/
COPY app/symfony.lock /app/
RUN composer install --ignore-platform-reqs --no-scripts --no-dev
LABEL image=dependencies

FROM php:${PHP_FPM_ALPINE_TAG}-fpm-alpine AS base

# PHP
COPY .docker/config/php/php.ini.production $PHP_INI_DIR/php.ini

# Tooling
RUN apk --update add zip libzip-dev curl

# PHP Extenstions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions gd intl && \
    docker-php-ext-install pdo_mysql zip

FROM base as dev
ARG PROJECT_NAME=api

COPY .docker/config/php/php.ini.development $PHP_INI_DIR/php.ini
RUN install-php-extensions xdebug @composer-2.0.2
WORKDIR /var/www/${PROJECT_NAME}

# Copy in app
COPY app /var/www/${PROJECT_NAME}

# Copy in vendor
COPY --from=dependencies app/vendor vendor

# Install dev deps
RUN composer install --no-scripts

RUN mkdir -p var/log var/cache && \
    chmod -R 777 var/log var/cache

HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1