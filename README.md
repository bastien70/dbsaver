# DbSaver

[![Package version](https://img.shields.io/github/v/release/bastien70/dbsaver.svg?style=flat-square)](https://github.com/bastien70/dbsaver/releases)
[![Build Status](https://img.shields.io/github/workflow/status/bastien70/dbsaver/Continuous%20Integration/main?style=flat-square)](https://github.com/bastien70/dbsaver/actions?query=workflow%3A"Continuous+Integration"+branch%3Amain)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)
[![Code coverage](https://img.shields.io/codecov/c/github/bastien70/dbsaver?style=flat-square)](https://codecov.io/gh/bastien70/dbsaver/branch/main)

![Alt text](./public/images/liste_bases.png?raw=true "Liste des bases de données")

DbSaver est une application réalisée par <b>Bastien LOUGHIN</b>, vous permettant de réaliser des backups quotidiens automatiques (et manuels) pour vos bases de données MySQL.
Vous n'avez qu'à renseigner les identifiants pour accéder aux différentes bases de données, configurer une tâche CRON... et c'est fini.
Les mots de passe seront automatiquement cryptés.

Vous pourrez ensuite grâce à DbSaver accéder aux différents backups de vos bases de données en vous rendant sur l'onglet <b>Backups</b>.
Ceux-ci peuvent être sauvegardés en <b>local</b> ou sur le cloud d'Amazon <b>AWS S3</b>.

/!\ DbSaver sauvegarde uniquement les données stockées dans les bases de données. Les fichiers (uploads clients par exemple) ne sont pas sauvegardés.

# Table des matières

1. [Pré-requis](#preRequis)
1. [Installation manuelle](#manual-install)
1. [Installation avec Task](#task-install)
1. [Configuration de la tâche CRON](#cron)
1. [Backups en local ou sur AWS S3](#backups)
    1. [Local](#backupLocal)
    1. [AWS S3](#backupAws)
1. [Utiliser l'application](#app)
1. [Licence](#licence)
1. [Contribuer](#contribute)
    
    
## Pré-requis <a name="preRequis"></a>

* PHP 8
* Composer
* Symfony CLI (si vous souhaitez lancer le projet en local)

## Installation manuelle <a name="manual-install"></a>

1. `git clone https://github.com/bastien70/dbsaver.git`
1. `cd dbsaver`
1. Configurez la variable d'environnement dans un fichier `.env.local` : `DATABASE_URL` (regardez dans le fichier `.env` pour voir la structure de la variable).
1. `composer install` (installation des dépendances)
1. `php bin/console app:regenerate-app-secret` (régénération de la clé secrète permettant de crypter les mots de passes de vos bases de données)
1. `php bin/console d:d:c` (création de la base de données)
1. `php bin/console d:m:m -n` (migration des tables)
1. `php bin/console app:make-user` (création de votre compte d'accès)

## Installation avec Task <a name="task-install"></a>

Requiert [Symfony CLI](https://symfony.com/download) et [Task](https://taskfile.dev/) installés.

1. `git clone https://github.com/bastien70/dbsaver.git`
1. `cd dbsaver`
1. Configurez la variable d'environnement dans un fichier `.env.local` : `DATABASE_URL` (regardez dans le fichier `.env` pour voir la structure de la variable).
1. `task install`

## Configuration de la tâche CRON <a name="cron"></a>

Configurer une tâche CRON vous servira à lancer automatiquement et à la fréquence désirée, un backup de toutes vos bases de données.

Le fichier responsable de lancer la commande se trouve sur `[projet]/src/Command/BackupCommand.php`

La commande à effectuer pour la lancer est la suivante : `php bin/console app:backup`

Initialisez une tâche CRON sur votre serveur ou PC :

`[chemin vers php] [chemin vers la racine du projet]/bin/console app:backup`

## Backups en local ou sur AWS S3 <a name="backups"></a>

### Local <a name="backupLocal"></a>

L'application est configurée pour sauvegarder les backups en local. Si vous venez d'installer l'application, vous n'avez rien à faire.
Sinon, voici les changements à effectuer :

Ouvrez le fichier `[projet]/config/packages/vich_uploader.yaml`.

Remplacez le contenu par :

```yaml
### UTILISEZ CETTE CONFIGURATION SI VOUS SOUHAITEZ STOCKER LES FICHIERS EN LOCAL ###
vich_uploader:
    db_driver: orm
    mappings:
        backups:
            uri_prefix: /files/backups
            upload_destination: '%kernel.project_dir%/public/files/backups'


### UTILISEZ CETTE CONFIGURATION SI VOUS SOUHAITEZ STOCKER LES FICHIERS SUR LE CLOUD AWS S3
#vich_uploader:
#    db_driver: orm
#    storage: gaufrette
#    mappings:
#        backups:
#            uri_prefix: '%uploads_base_url%'
#            upload_destination: backup_fs
```

Modifiez également la variable d'environnement `BACKUP_LOCAL` dans le fichier `.env` comme ceci :
`BACKUP_LOCAL=1`

### AWS S3 <a name="backupAws"></a>

Modifiez dans le fichier `.env` les variables d'environnement suivantes pour les faire correspondre à ceux renseignés par AWS S3.

```
###> AWS_S3 ###
AWS_S3_ACCESS_ID="your aws_s3 access id"
AWS_S3_ACCESS_SECRET="your aws_s3 access secret"
AWS_S3_BUCKET_NAME="your aws_s3 bucket name"
AWS_S3_REGION="eu-west-3"
###< AWS S3 ###
```

Modifiez également la variable `BACKUP_LOCAL` comme ceci :
`BACKUP_LOCAL=0`

Ouvrez le fichier `[projet]/config/packages/vich_uploader.yaml`.

Remplacez le contenu par :

```yaml
### UTILISEZ CETTE CONFIGURATION SI VOUS SOUHAITEZ STOCKER LES FICHIERS EN LOCAL ###
#vich_uploader:
#    db_driver: orm
#    mappings:
#        backups:
#            uri_prefix: /files/backups
#            upload_destination: '%kernel.project_dir%/public/files/backups'


### UTILISEZ CETTE CONFIGURATION SI VOUS SOUHAITEZ STOCKER LES FICHIERS SUR LE CLOUD AWS S3
vich_uploader:
    db_driver: orm
    storage: gaufrette
    mappings:
        backups:
            uri_prefix: '%uploads_base_url%'
            upload_destination: backup_fs
```

## Utiliser l'application <a name="app"></a>

Après avoir déployé l'application sur votre serveur (ou l'avoir lancée en local), accédez à la page de connexion :
Pour l'exemple, le nom de domaine rattaché à l'application sera `127.0.0.1:8000`.

Lancez l'application : `https://127.0.0.1:8000`
Vous serez invité à vous connecter. Entrez les identifiants de votre compte (que vous avez créé avec la commande `php bin/console app:make-user`)

![Alt text](./public/images/authentification.png?raw=true "Authentification")

Vous serez redirigés vers `https://127.0.0.1:8000/dbsaver`

![Alt text](./public/images/accueil.png?raw=true "Accueil")

Pour créer une base de données, cliquez sur l'onglet `Bases de données`, puis sur le bouton `Ajouter une base de données`.
Remplissez les informations de votre base de données et validez.

![Alt text](./public/images/creer_base.png?raw=true "Ajouter une base de données")

Vous aurez dès lors, pour chaque base de données ajoutée, la possibilité de consulter les backups, d'éditer les informations de la base de données,
supprimer la base de données de l'application (ainsi que ses backups),ou lancer un backup manuel.

![Alt text](./public/images/liste_bases.png?raw=true "Liste des bases de données")

Selon la fréquence de votre tâche CRON que vous avez configurée, un backup automatique sera effectué.

Pour accéder aux backups de vos bases, cliquez sur l'onglet `Backups`.

![Alt text](./public/images/liste_backups.png?raw=true "Liste des backups")

Vous aurez la possibilité de supprimer ou télécharger un Backup.

## Licence <a name="licence"></a>

Cette application est protégée par une licence MIT : [LICENCE](LICENSE)

## Contribuer <a name="contribute"></a>

Avant de faire une pull request, n'oubliez pas de lancer les vérifications d'usage:

```bash
composer ci
```
