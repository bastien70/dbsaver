# Contribute 🗸

In order to contribute, clone the repository and setup either using the [conventional way](../english.md#manual-install)
,
or start by using docker compose (preferred).

# Table of contents

1. [Taskfile](#taskfile)
2. [Docker Compose](#docker-compose-)
    1. [Initial setup](#initial-setup)
        1. [Install dependencies and assets](#install-dependencies-and-assets)
        2. [Create local dotenv](#create-local-dotenv)
        3. [Update hosts file](#update-hosts-file)
        4. [Start docker containers](#start-docker-containers)
        5. [Create a user](#create-a-user)
        6. [Visit the application](#visit-the-application)
    2. [Start developing!](#start-developping)
3. [Override Docker Compose configuration](#override-docker-compose-configuration)
4. [Guidelines](#guidelines)

## Taskfile

While not strictly necessary for contribution, for ease of development a `Taskfile.yml` is present.

Please check the [Taskfile Installation Guide](https://taskfile.dev/installation/) to get started.

## Docker Compose 🐋

In order to develop with docker compose, please check
the [Docker Compose Installation Guide](https://docs.docker.com/compose/install/).

### Initial setup

To start developing we supplied a `docker-compose.yml`. This file contains
several services to ease the development progress.

#### Install dependencies and assets

Start with installing composer dependencies.

```shell
task docker:composer:install
```

> Note: This will install the composer dependencies under the current user's UID/GID.

#### Create local dotenv

In order to use your own environment variables, please copy the `.env` file to `.env.local`.

```shell
cp .env .env.local
```

> Note: You can do the same for the test dotenv file.

#### Update hosts file

As we provided [Caddy](https://caddyserver.com/) with the default docker compose file, the hosts file should be updated
in order to be able to access the URLs `https://dbsaver.local` and `http://mail.dbsaver.local`.

Depending on OS this is either located under `/etc/hosts` (Unix) or `C:\Windows\System32\drivers\etc\hosts` (Windows).

```shell
# Host system 
127.0.0.1 dbsaver.local mail.dbsaver.local

# Virtual machine
192.168.47.129 dbsaver.local mail.dbsaver.local 
```

> Note: The default caddy configuration will allow connections to host port `444`. If for whatever reason
> this port is already taken, you can create an `docker-compose.override.yml` file to change the port.
> This will be discussed in a [later section](#override-docker-compose-configuration).

#### Start docker containers

If this is your first time starting the application with docker you can get started immediately.

```shell
task docker:up
```

> Note: The first time starting the application the images need to be pulled from Dockerhub (speed depends on internet
> connection).

If you are already a contributor, please note that the development image will be updated weekly (and on new release).
This means to get an up-to-date development image of the application, please pull the image and recreate the services.

```shell
task docker:pull
task docker:upp
```

> Note: The development image (tag `dev`) is a derivative of the base image (tag `base`). The only difference with the
> base image is that the development image includes [Xdebug](https://xdebug.org/).

##### The docker entrypoint

After starting the container, the `docker-entrypoint.sh` script will be run, which will update the application. In a
nutshell it boils down to the following:

1. For **dev** and **test** environments it will execute a `composer install`.
2. For **dev** and **prod** environments it will check if a connection to the database can be established.
3. For **every** environment it will run doctrine commands to create a database (if not exits) and run migrations if
   needed.

This will make sure that when you (re)start the container, your dependencies, assets and database will be up-to-date.

> Note: For debugging purposes it's possible to set `APP_DEBUG="1"` in your dotenv in order for the entrypoint to always
> run a `composer install` after restarting the container.

#### Create a user

With everything up and running, let's create a user.

```shell
task docker:app:create-user
```

#### Visit the application

Now if everything is set up correctly, you can visit `https://dbsaver.local:444` and find yourself at the login screen.
Log in with the recently created user, and you'll be greeted by the welcome page.

### Start developping!

You're up all set to start your development adventure!

## Override Docker Compose configuration

If you either find yourself in need of changing the Dockerfile and need to test those adaptations, or
if you just want to test out the production image locally, you can override
the current docker compose configuration by adding a `docker-compose.override.yml` file in the root of the project.

The following example gives you a basic idea on how such a configuration could look like.

```yaml
# docker-compose.override.yml
version: '3.9'

x-app-volume: &app-volume docker_ci_app:/app

x-env-file: &env-file
  env_file:
    - .env.local

services:
  dbsaver:
    image: bastien70/dbsaver:latest
    build:
      context: .
      dockerfile: Dockerfile
      target: prod
    <<: *env-file
    user: "dbsaver"
    volumes:
      - *app-volume

  mysql:
    <<: *env-file

  caddy:
    ports:
      - "446:443"
    volumes:
      - *app-volume

volumes:
  docker_ci_mysql:
  docker_ci_app:

networks:
  docker_ci_network:
    driver: bridge
```

Please take note about the above example:

- The variables defined at the top as `x-var: &var` are
  called [YAML anchors](https://support.atlassian.com/bitbucket-cloud/docs/yaml-anchors/).
- The `image` key is not strictly necessary in this context, but it will make sure that the locally built image will be
  tagged as `latest`. If `image` is supplied without `build` context, using the `latest` tag will pull the latest
  production image of the application[^latest-tag].
- As the production image already contains dependencies and assets, instead of mounting the host directory to the
  app directory inside the container (`.:/app`) like stated in the default docker compose configuration, we will mount a
  named volume `docker_ci_app` from the host machine. This volume needs to be shared with your webserver in order for it
  to access the files in `/public`. For more information on docker volumes, please check
  the [official documentation](https://docs.docker.com/storage/volumes/).
- The `user: "dbsaver"` key is only needed in case you want to test the production image locally. This is due the fact
  that the owner of the files in the production image is `dbsaver`.
- After adding the override config you can do `task docker:down docker:up`, to first down the existing services and up
  the new services with the override config.

[^latest-tag]: Please note that the `latest` tag is used for example
purposes. [Never use the latest tag for production environment, but instead use a <major.minor> tag](https://github.com/hexops/dockerfile#do-not-use-latest-pin-your-image-tags)
.

## Guidelines

In order to assure the quality of new features the following checks need to pass before you submit a PR.

```shell
// Task including PHPUnit (setup, test and cleanup), PHPStan and PHPCS (dry-run)
task docker:app:contribute

// Separate commands
task docker:app:test
task docker:app:phpstan
task docker:php:cs:dry
```

> Note: The `docker:app:contribute` includes the following subtasks:
> - `task: docker:down`
> - `task: docker:app:test`
>   - `task: docker:up:test`
>   - `task: docker:app:test:setup-db`
>   - `task: docker:app:test:setup-fixtures`
>   - `task: docker:app:phpunit`
>   - `task: docker:app:test:cleanup`
> - `task: docker:app:phpcs:dry`
> - `task: docker:app:phpstan`
> - `task: docker:up`
>
> This means the contribution task will first down the current active (dev) services, run PHPUnit (which will also up
> test services), PHPStan, PHPCS, and afterwards will up the (dev) services again.

If changes are made to the Dockerfile, additional linter and vulnerability checks should be run.

```shell
// Task including linter (Hadolint) + vulnerability checker (Trivy)
task docker:contribute

// Separate commands
task docker:lint
task docker:security
```

> Note: Both the application and docker tasks mentioned above are also run in the CI.
