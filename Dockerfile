# syntax=docker/dockerfile:1

FROM composer:2 AS composer-deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev

FROM node:22-alpine AS node-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=composer-deps /app/vendor ./vendor
RUN npm run build

FROM php:8.4-fpm-alpine AS php-fpm
RUN apk add --no-cache postgresql-libs libzip icu-libs oniguruma \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS postgresql-dev libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath zip intl opcache \
    && apk del .build-deps

WORKDIR /var/www/html
COPY --from=composer-deps /app/vendor ./vendor
COPY . .
COPY --from=node-build /app/public/build ./public/build
RUN rm -rf storage/framework/cache/data/* storage/framework/sessions/* storage/framework/views/* \
    && php artisan package:discover --ansi \
    && php artisan storage:link \
    && chown -R www-data:www-data /var/www/html

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER www-data
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

FROM caddy:2-alpine AS caddy
COPY Caddyfile /etc/caddy/Caddyfile
COPY --from=php-fpm /var/www/html/public /var/www/html/public

FROM php:8.4-cli-alpine AS dev
ARG UID=1000
ARG GID=1000
RUN apk add --no-cache postgresql-libs libzip icu-libs oniguruma shadow \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS postgresql-dev libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring bcmath zip intl opcache \
    && apk del .build-deps \
    && addgroup -g "$GID" dev \
    && adduser -D -u "$UID" -G dev dev
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY docker/php/dev-entrypoint.sh /usr/local/bin/dev-entrypoint.sh
RUN chmod +x /usr/local/bin/dev-entrypoint.sh \
    && mkdir -p vendor \
    && chown -R dev:dev /var/www/html

USER dev
EXPOSE 8000
ENTRYPOINT ["dev-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
