#!/bin/sh

CONTAINER_PREFIX="[dbsaver]"
set -e

if [ "${1#-}" != "$1" ]; then
  set -- php-fpm "$@"
fi

if [ "$1" = "php-fpm" ] || [ "$1" = "php" ]; then
  echo "${CONTAINER_PREFIX} Found '$APP_ENV' environment."

  COMPOSER_DEV="--no-dev"
  if [ "$APP_ENV" != "prod" ]; then
    COMPOSER_DEV=""
  fi

  VERBOSE=""
  if [ "$APP_ENV" != "prod" ] || [ "$APP_DEBUG" = "1" ]; then
    VERBOSE="-vvv"
    echo "${CONTAINER_PREFIX}[composer] Installing composer dependencies..."
    composer install --prefer-dist --no-progress --no-interaction --classmap-authoritative --no-cache $COMPOSER_DEV
  fi

  # Only create database and run migrations for non-test environments
  if [ "$APP_ENV" != "test" ]; then
    echo "${CONTAINER_PREFIX} Check database connection..."
    REACH_DATABASE_ATTEMPT=30
    until [ $REACH_DATABASE_ATTEMPT -eq 0 ] || DATABASE_ERROR=$(bin/console dbal:run-sql "SELECT 1" 2>&1); do
      LAST_EXIT_CODE=$?

      SQL_RESPONSE_CODE=$(echo "$DATABASE_ERROR" | grep -Po 'SQLSTATE\[[A-Z0-9]{5}(?=\])]\s{1}\[\K[0-9]+(?=\])' | head -1)
      if [ -z "$SQL_RESPONSE_CODE" ]; then
        break
      fi

      if [ "$SQL_RESPONSE_CODE" -ne 2002 ]; then
        if [ "$LAST_EXIT_CODE" -eq 255 ]; then
          REACH_DATABASE_ATTEMPT=0
          break
        fi
      fi

      sleep 1
      REACH_DATABASE_ATTEMPT=$((REACH_DATABASE_ATTEMPT - 1))
      echo "${CONTAINER_PREFIX} Waiting for database connection... $REACH_DATABASE_ATTEMPT attempts left."
    done

    if [ $REACH_DATABASE_ATTEMPT -eq 0 ]; then
      echo "$CONTAINER_PREFIX The database is either not up or reachable:"
      echo "$DATABASE_ERROR"
      exit 1
    fi

    echo "${CONTAINER_PREFIX} Database connection ready!"
    echo "${CONTAINER_PREFIX}[doctrine] Create database..."
    bin/console doctrine:database:create --if-not-exists --no-interaction $VERBOSE
    echo "${CONTAINER_PREFIX}[doctrine] Run migrations..."
    bin/console doctrine:migrations:migrate --allow-no-migration --no-interaction $VERBOSE
  fi
fi

exec "$@"
