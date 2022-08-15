# DbSaver

[![Package version](https://img.shields.io/github/v/release/bastien70/dbsaver.svg?style=flat-square)](https://github.com/bastien70/dbsaver/releases)
[![Build Status](https://img.shields.io/github/workflow/status/bastien70/dbsaver/Continuous%20Integration/main?style=flat-square)](https://github.com/bastien70/dbsaver/actions?query=workflow%3A"Continuous+Integration"+branch%3Amain)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)
[![Code coverage](https://img.shields.io/codecov/c/github/bastien70/dbsaver?style=flat-square)](https://codecov.io/gh/bastien70/dbsaver/branch/main)

![Database list](docs/images/database-list.png?raw=true)

DbSaver is an application written by **Bastien LOUGHIN** allowing you to make automatic daily backups (and manual backups) for your MySQL databases.
All you have to do is fill the credentials to access the databases, configure a CRON job... and it's done.
Passwords will be automatically hashed.

Then, using DbSaver, you can access your databases backups by browsing the **Backups** tab.

You can then use DbSaver to access databases backups by going to the **Backups** tab.
Backups can be saved **locally** or in **S3 cloud** (AWS, Scaleway, ...).

/!\ DbSaver only backups databases. Files (like image uploads) are not saved.

# Table of contents

1. [Prerequisites](#prerequisites)
1. [Manual install](#manual-install)
1. [Install using Task](#task-install)
1. [Configure the CRON job](#cron)
1. [Use the application](#use-app)
    1. [Login](#login)
    1. [Manage storage spaces](#storage-spaces)
    1. [Manage databases](#databases)
    1. [Manage backups](#backups)
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
1. If you want to run Docker containers (currently only for local emails with MailCatcher): `task docker-start` et `task docker-stop` (requires Docker and Docker Compose)
1. To start the server: `task start` (to stop it: `task stop`)

## Configure the CRON job <a name="cron"></a>

Configure the CRON task allowing you to activate the verification of backups to be made. **Set it to run every day**.

When you add databases, you can each time configure the periodicity of the backups to be made.
Therefore, if you have configured a database so that a backup is made every week, the CRON task will check the current date and compare it with the date of the last backup of your database, in order to decide whether to establish a backup or not.

Here is the command to run: `php bin/console app:backup`

Initialize a CRON job on your server or computer:

`[path to php] [path to project root]/bin/console app:backup`

## Use the application <a name="use-app"></a>

After deploying the application on your server (or launching it locally) access the login page.
For the example the host will be `127.0.0.1:8000`.

### Login <a name="login"></a>

Access the app: https://127.0.0.1:8000/login

You'll be asked to log in. Enter your account credentials (that you created using the `php bin/console app:make-user` command).

![Authentication](docs/images/login.png?raw=true)

You will be redirected to https://127.0.0.1:8000/

![Homepage](docs/images/home.png?raw=true)

### Manage storage spaces <a name="storage-spaces"></a>

To create a storage space (locally or using S3), click on the `Storage spaces` tab then on the one you want. Then click on `Add storage space`.
Fill in the information for your storage space and validate.

![Add storage space](docs/images/adapter-create.png?raw=true)

You will find this storage space in the list.

![Storage space list](docs/images/adapter-list.png?raw=true)

### Manage databases <a name="databases"></a>

To create a database, click the `Databases` tab, then the `Add a database` button.
Fill your database information.

Then, check the **backup options** according to your needs.

Finally, configure the **periodicity** at which a backup must be executed for your database (example: every day, every 2 weeks, every 3 months, ...)

![Add a database](docs/images/database-create.png?raw=true)

Then, for every database you add, you will be able to see its backups, update its credentials, delete the database from the app (and its backups) or launch a manual backup.

![Database list](docs/images/database-list.png?raw=true)

According to the frequency of the CRON job you configured, automatic backups will be performed.

### Manage backups <a name="backups"></a>

To access your databases backups, click the `Backups` tab.

![Backup list](docs/images/backup-list.png?raw=true)

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

```bash
task ci
task test
```

Note: you can run these commands without Task, have a look to the [Taskfile.yaml](Taskfile.yaml) file to see which commands will run.

## Changelog <a name="changelog"></a>

See the [changelog](CHANGELOG.md).
