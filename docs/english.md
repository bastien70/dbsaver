# DbSaver

[![Package version](https://img.shields.io/github/v/release/bastien70/dbsaver.svg?style=flat-square)](https://github.com/bastien70/dbsaver/releases)
[![Build Status](https://img.shields.io/github/workflow/status/bastien70/dbsaver/Continuous%20Integration/main?style=flat-square)](https://github.com/bastien70/dbsaver/actions?query=workflow%3A"Continuous+Integration"+branch%3Amain)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)
[![Code coverage](https://img.shields.io/codecov/c/github/bastien70/dbsaver?style=flat-square)](https://codecov.io/gh/bastien70/dbsaver/branch/main)

![Database list](images/database-list-en.png?raw=true)

*[Cliquez ici pour accéder à la documentation en français](french.md)*

DbSaver is an application written by **Bastien LOUGHIN** allowing you to make automatic daily backups (and manual
backups) for your MySQL databases.
All you have to do is fill the credentials to access the databases, configure a CRON job... and it's done.
Passwords will be automatically hashed.

Then, using DbSaver, you can access your databases backups by browsing the **Backups** tab.

Vous pourrez ensuite grâce à DbSaver accéder aux différentes sauvegardes de vos bases de données en vous rendant sur
l'onglet **Sauvegardes**.
Backups can be saved **locally** or on Amazon's cloud **AWS S3**.

/!\ DbSaver only backups databases. Files (like image uploads) are not saved.

# Table of contents

1. [Prerequisites](#prerequisites)
1. [Manual install](#manual-install)
1. [Install using Task](#task-install)
1. [Configure the CRON job](#cron)
1. [Configure backup storage](#storage-config)
    1. [Locally](#local-storage)
    1. [On AWS S3](#aws-storage)
1. [Use the application](#use-app)
1. [Update the application](#update-app)
1. [License](#license)
1. [Contribute](#contribute)
1. [Changelog](#changelog)

## Prerequisites <a name="prerequisites"></a>

* PHP 8.1+
* Composer
* Symfony CLI (if you want to run the project locally)

## Manual install <a name="manual-install"></a>

1. `git clone https://github.com/bastien70/dbsaver.git`
1. `cd dbsaver`
1. `composer install` (install dependencies)
1. `php bin/console app:post-install` (project configuration)
1. `php bin/console app:regenerate-app-secret` (regenerate the secret key allowing to hash databases passwords)
1. `php bin/console d:d:c` (create database)
1. `php bin/console d:m:m -n` (migrate tables)
1. `php bin/console app:make-user` (create your account)

## Install using Task <a name="task-install"></a>

Requires [Symfony CLI](https://symfony.com/download) and [Task](https://taskfile.dev/) to be installed.

1. `git clone https://github.com/bastien70/dbsaver.git`
1. `cd dbsaver`
1. `task install`
1. If you want to run Docker containers (currently only for local emails with MailCatcher): `task docker-start`
   et `task docker-stop` (requires Docker and Docker Compose)
1. To start the server: `task start` (to stop it: `task stop`)

## With Docker Compose 🐋

Minimal example with [docker compose](https://docs.docker.com/compose/install/) for production.

```yaml
version: '3.9'

services:
  dbsaver:
    image: bastien70/dbsaver:1.3
    env_file:
      - env.dbsaver
    volumes:
      - dbsaver_app:/app/public

volumes:
  dbsaver_app:
```

## Configure the CRON job <a name="cron"></a>

Configuring a CRON job allows you to automatically backup databases at the frequency of your choice.

Here is the command to run: `php bin/console app:backup`

Initialize a CRON job on your server or computer:

`[path to php] [path to project root]/bin/console app:backup`

## Configure backup storage <a name="storage-config"></a>

### Locally <a name="local-storage"></a>

The application is configured to store backups locally by default.
If you just installed the app there's nothing to do.
Else here are the modifications to apply:

Open file `[project]/config/packages/vich_uploader.yaml` and replace its content with the following code:

```yaml
vich_uploader:
   db_driver: orm
   mappings:
      backups:
         uri_prefix: /files/backups
         upload_destination: '%kernel.project_dir%/public/files/backups'
   metadata:
      type: attribute
```

You also need to add/update the `BACKUP_LOCAL` environment variable in `.env.local` like this:
`BACKUP_LOCAL=1`

### On AWS S3 <a name="aws-storage"></a>

Create/update the following environment variables in the `.env.local` file to match the ones from AWS S3:

```
###> AWS_S3 ###
AWS_S3_ACCESS_ID="your aws_s3 access id"
AWS_S3_ACCESS_SECRET="your aws_s3 access secret"
AWS_S3_BUCKET_NAME="your aws_s3 bucket name"
AWS_S3_REGION="eu-west-3"
###< AWS S3 ###
```

You also need to add/update the `BACKUP_LOCAL` environment variable in `.env.local` like this:
`BACKUP_LOCAL=0`

Open file `[project]/config/packages/vich_uploader.yaml` and replace its content with the following code:

```yaml
vich_uploader:
   db_driver: orm
   storage: gaufrette
   mappings:
      backups:
         uri_prefix: '%uploads_base_url%'
         upload_destination: backup_fs
   metadata:
      type: attribute
```

## Use the application <a name="use-app"></a>

After deploying the application on your server (or launching it locally) access the login page.
For the example the host will be `127.0.0.1:8000`.

Access the app: https://127.0.0.1:8000/login

You'll be asked to log in. Enter your account credentials (that you created using the `php bin/console app:make-user`
command).

![Authentication](images/login-en.png?raw=true)

You will be redirected to https://127.0.0.1:8000/

![Homepage](images/home-en.png?raw=true)

To create a database, click the `Databases` tab, then the `Add a database` button.
Fill your database information and submit.

![Add a database](images/database-create-en.png?raw=true)

Then, for every database you add, you will be able to see its backups, update its credentials, delete the database from
the app (and its backups) or launch a manual backup.

![Database list](images/database-list-en.png?raw=true)

According to the frequency of the CRON job you configured, automatic backups will be performed.

To access your databases backups, click the `Backups` tab.

![Backup list](images/backup-list-en.png?raw=true)

You will be able to download or delete a backup.

## Update the application <a name="update-app"></a>

If you have Task and Symfony CLI installed, just run this command: `task update`

Else run the following commands instead:

```bash
git pull --rebase
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:post-install --only-missing
```

## License <a name="license"></a>

This application is protected by a MIT license: [LICENCE](../LICENSE).

## Contribute <a name="contribute"></a>

Before making a pull request, don't forget to run these commands (requires Task and Docker Compose):

```shell
task docker:app:contribute
```

For more information on how to start with development with Docker compose, please check the
following [README](../docs/contribute/english.md).

> Note: You can run these commands without Task, have a look to at Taskfile.yml file to see which commands will run.

## Changelog <a name="changelog"></a>

See the [changelog](../CHANGELOG.md).
