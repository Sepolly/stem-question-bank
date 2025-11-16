# syntax = docker/dockerfile:experimental

ARG PHP_VERSION=8.3
ARG NODE_VERSION=22.18.0

##########################
#       BASE IMAGE
##########################
FROM ubuntu:22.04 as base
LABEL fly_launch_runtime="laravel"

ARG PHP_VERSION

ENV DEBIAN_FRONTEND=noninteractive \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/composer \
    COMPOSER_MAX_PARALLEL_HTTP=24 \
    PHP_PM_MAX_CHILDREN=10 \
    PHP_PM_START_SERVERS=3 \
    PHP_MIN_SPARE_SERVERS=2 \
    PHP_MAX_SPARE_SERVERS=4 \
    PHP_DATE_TIMEZONE=UTC \
    PHP_DISPLAY_ERRORS=Off \
    PHP_ERROR_REPORTING=22527 \
    PHP_MEMORY_LIMIT=256M \
    PHP_MAX_EXECUTION_TIME=90 \
    PHP_POST_MAX_SIZE=100M \
    PHP_UPLOAD_MAX_FILE_SIZE=100M \
    PHP_ALLOW_URL_FOPEN=Off

# Install PHP, Composer, Nginx, Supervisor, Cron
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY .fly/php/ondrej_ubuntu_php.gpg /etc/apt/trusted.gpg.d/ondrej_ubuntu_php.gpg
ADD .fly/php/packages/${PHP_VERSION}.txt /tmp/php-packages.txt

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        gnupg2 ca-certificates git-core curl zip unzip sqlite3 rsync \
        nginx supervisor cron vim-tiny htop \
    && echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu jammy main" \
        > /etc/apt/sources.list.d/ondrej-ubuntu-php-focal.list \
    && apt-get update \
    && apt-get -y --no-install-recommends install $(cat /tmp/php-packages.txt) \
    && ln -sf /usr/sbin/php-fpm${PHP_VERSION} /usr/sbin/php-fpm \
    && mkdir -p /var/www/html/public \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Copy config
COPY .fly/nginx/ /etc/nginx/
COPY .fly/fpm/ /etc/php/${PHP_VERSION}/fpm/
COPY .fly/supervisor/ /etc/supervisor/
COPY .fly/entrypoint.sh /entrypoint
COPY .fly/start-nginx.sh /usr/local/bin/start-nginx
RUN chmod 754 /usr/local/bin/start-nginx \
    && chmod +x /entrypoint

# Copy Laravel project
COPY . /var/www/html
WORKDIR /var/www/html

##########################
#  INSTALL PHP PACKAGES
##########################
RUN composer install --no-dev --optimize-autoloader \
    && mkdir -p storage/logs storage/framework cache bootstrap/cache \
    && php artisan optimize:clear \
    && chown -R www-data:www-data /var/www/html \
    && echo "MAILTO=\"\"\n* * * * * www-data /usr/bin/php /var/www/html/artisan schedule:run" \
        > /etc/cron.d/laravel \
    && sed -i'' '/->withMiddleware(function (Middleware \$middleware) {/a\
        \$middleware->trustProxies(at: "*");\
    ' bootstrap/app.php


##########################
#     BUILD ASSETS
##########################
FROM node:${NODE_VERSION} as assets

WORKDIR /app

COPY . .
COPY --from=base /var/www/html/vendor vendor

RUN if [ -f "vite.config.js" ] || [ -f "vite.config.ts" ]; then \
        ASSET_CMD="build"; \
    else \
        ASSET_CMD="production"; \
    fi; \
    if [ -f "yarn.lock" ]; then \
        yarn install --frozen-lockfile && yarn $ASSET_CMD; \
    elif [ -f "pnpm-lock.yaml" ]; then \
        corepack enable && corepack prepare pnpm@latest --activate \
            && pnpm install --frozen-lockfile && pnpm run $ASSET_CMD; \
    elif [ -f "package-lock.json" ]; then \
        npm ci --no-audit && npm run $ASSET_CMD; \
    else \
        npm install && npm run $ASSET_CMD; \
    fi;


##########################
#   FINAL RUNTIME IMAGE
##########################
FROM base

# Merge built assets with existing public/
COPY --from=assets /app/public /var/www/html/public-npm

RUN rsync -ar /var/www/html/public-npm/ /var/www/html/public/ \
    && rm -rf /var/www/html/public-npm \
    && chown -R www-data:www-data /var/www/html

EXPOSE 8080
ENTRYPOINT ["/entrypoint"]
