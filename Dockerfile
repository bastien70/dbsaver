ARG PHP_VERSION=8.1

FROM php:${PHP_VERSION}-fpm-alpine AS base

# hadolint ignore=DL3022
COPY --from=composer:2.3 /usr/bin/composer /usr/local/bin/composer

# hadolint ignore=DL3022
COPY --from=mlocati/php-extension-installer:1.5 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions intl opcache pdo_mysql xsl zip \
    && apk add --no-cache --upgrade grep

WORKDIR /app

EXPOSE 9000

ENTRYPOINT ["/app/docker-entrypoint.sh"]

CMD ["php-fpm"]

# Build dev
FROM bastien70/dbsaver:base AS dev

RUN install-php-extensions xdebug

RUN ln -sf "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"

# Build prod
FROM bastien70/dbsaver:base AS prod

ENV UID=10000
ENV GID=10001
ENV USER=dbsaver

COPY . /app

# Dotenv files are ignored in .dockerignore, as it forces the user to explicitely pass (system) environment variables to the container.
# But, due to currently having symfony/dotenv as dependency, and in order to not break existing configuration, we need to initialise
# an empty dotenv file.

# hadolint ignore=SC2086
RUN touch .env \
    && mkdir -p var/cache var/log var/storage \
    && ln -sf "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini" \
    && addgroup \
    --gid $GID \
    --system $USER \
    && adduser \
    --uid $UID \
    --disabled-password \
    --gecos "" \
    --ingroup $USER \
    --no-create-home \
    $USER \
    && chown -R $UID:$GID /app

USER $USER