# syntax=docker/dockerfile:1

# Production image of the Symfony API + admin, served by FrankenPHP (Caddy + PHP).
FROM dunglas/frankenphp:1-php8.3

WORKDIR /app

RUN install-php-extensions pdo_pgsql intl opcache zip \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini $PHP_INI_DIR/conf.d/zz-app.ini
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV APP_ENV=prod \
    APP_DEBUG=0 \
    COMPOSER_ALLOW_SUPERUSER=1

# Dependencies first, so this layer stays cached while only the code changes
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-progress --no-interaction

COPY . .
RUN composer dump-autoload --classmap-authoritative --no-dev \
    && php bin/console assets:install public \
    && php bin/console cache:warmup \
    && chmod +x docker/entrypoint.sh

# Render injects PORT; locally the container listens on 80
ENV PORT=80
EXPOSE 80

ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--adapter", "caddyfile"]
