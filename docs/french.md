# DbSaver

[![Package version](https://img.shields.io/github/v/release/bastien70/dbsaver.svg?style=flat-square)](https://github.com/bastien70/dbsaver/releases)
[![Build Status](https://img.shields.io/github/workflow/status/bastien70/dbsaver/Continuous%20Integration/main?style=flat-square)](https://github.com/bastien70/dbsaver/actions?query=workflow%3A"Continuous+Integration"+branch%3Amain)
[![License](https://img.shields.io/badge/license-MIT-red.svg?style=flat-square)](LICENSE)
[![Code coverage](https://img.shields.io/codecov/c/github/bastien70/dbsaver?style=flat-square)](https://codecov.io/gh/bastien70/dbsaver/branch/main)

![Liste des bases de données](images/database-list-fr.png?raw=true)

*[Click here to access English docs](english.md)*

DbSaver est une application réalisée par **Bastien LOUGHIN**, vous permettant de réaliser des sauvegardes quotidiennes automatiques (et manuelles) pour vos bases de données MySQL.
Vous n'avez qu'à renseigner les identifiants pour accéder aux différentes bases de données, configurer une tâche CRON... et c'est fini.
Les mots de passe seront automatiquement cryptés.

Vous pourrez ensuite grâce à DbSaver accéder aux différentes sauvegardes de vos bases de données en vous rendant sur l'onglet **Sauvegardes**.
Celles-ci peuvent être sauvegardées en **local** ou sur différents services de cloud utilisant **S3** (AWS, Scaleway, ...).

/!\ DbSaver sauvegarde uniquement les données stockées dans les bases de données. Les fichiers (uploads clients par exemple) ne sont pas sauvegardés.

# Table des matières

1. [Pré-requis](#prerequisites)
2. [Installation manuelle](#manual-install)
3. [Installation avec Task](#task-install)
4. [Configuration de la tâche CRON](#cron)
5. [Utiliser l'application](#use-app)
   1. [Connexion](#login)
   2. [Gérer les espaces de stockage](#storage-spaces)
   3. [Gérer les bases de données](#databases)
   4. [Gérer les backups](#backups)
6. [Mettre à jour l'application](#update-app)
7. [Licence](#license)
8. [Contribuer](#contribute)
9. [Changelog](#changelog)
    
    
## Pré-requis <a name="prerequisites"></a>

* PHP 8.1+
* Composer
* Symfony CLI (si vous souhaitez lancer le projet en local)

## Installation manuelle <a name="manual-install"></a>

1. `git clone https://github.com/bastien70/dbsaver.git`
1. `cd dbsaver`
1. `composer install` (installation des dépendances)
1. `php bin/console app:post-install` (configuration du projet)
1. `php bin/console app:regenerate-app-secret` (régénération de la clé secrète permettant de crypter les mots de passes de vos bases de données)
1. `php bin/console d:d:c` (création de la base de données)
1. `php bin/console d:m:m -n` (migration des tables)
1. `php bin/console app:make-user` (création de votre compte d'accès)

## Installation avec Task <a name="task-install"></a>

Requiert [Symfony CLI](https://symfony.com/download) et [Task](https://taskfile.dev/) installés.

1. `git clone https://github.com/bastien70/dbsaver.git`
1. `cd dbsaver`
1. `task install`
1. Si vous souhaitez lancer les conteneurs Docker (actuellement uniquement pour les mails en local avec MailCatcher) : `task docker-start` et `task docker-stop` (requiert Docker et Docker Compose)
1. Pour démarrer le serveur : `task start` (pour l'arrêter : `task stop`)

## Configuration de la tâche CRON <a name="cron"></a>

Configurer une tâche CRON vous servira à lancer automatiquement et à la fréquence désirée, la sauvegarde de toutes vos bases de données.

La commande à effectuer pour la lancer est la suivante : `php bin/console app:backup`

Initialisez une tâche CRON sur votre serveur ou PC :

`[chemin vers php] [chemin vers la racine du projet]/bin/console app:backup`

## Utiliser l'application <a name="use-app"></a>

Après avoir déployé l'application sur votre serveur (ou l'avoir lancée en local), accédez à la page de connexion.
Pour l'exemple, l'hôte attaché à l'application sera `127.0.0.1:8000`.

### Connexion <a name="login"></a>
Accédez à l'application : https://127.0.0.1:8000/login

Vous serez invité à vous connecter. Entrez les identifiants de votre compte (que vous avez créé avec la commande `php bin/console app:make-user`).

![Authentification](images/login-fr.png?raw=true)

Vous serez redirigé vers https://127.0.0.1:8000/

![Accueil](images/home-fr.png?raw=true)

### Gérer les espaces de stockage <a name="storage-spaces"></a>

Pour créer un espace de stockage (local ou utilisant S3), cliquez sur l'onglet `Espaces de stockage` puis sur celui que vous désirez. Cliquez ensuite sur `Ajouter un espace de stockage`.
Remplissez les informations de votre espace de stockage et validez.

![Ajouter une espace de stockage](images/adapter-create-fr.png?raw=true)

Vous retrouverez dès lors cet espace de stockage dans la liste.

![Liste des espaces de stockage](images/adapter-list-fr.png?raw=true)

### Gérer les bases de données <a name="databases"></a>

Pour créer une base de données, cliquez sur l'onglet `Bases de données`, puis sur le bouton `Ajouter une base de données`.
Remplissez les informations de votre base de données et validez.

![Ajouter une base de données](images/database-create-fr.png?raw=true)

Vous aurez dès lors, pour chaque base de données ajoutée, la possibilité de consulter les sauvegardes, d'éditer les informations de la base de données,
supprimer la base de données de l'application (ainsi que ses sauvegardes), ou lancer une sauvegarde manuelle.

![Liste des bases de données](images/database-list-fr.png?raw=true)

Selon la fréquence de la tâche CRON que vous avez configurée, une sauvegarde automatique sera effectuée.

### Gérer les backups <a name="backups"></a>

Pour accéder aux sauvegardes de vos bases de données, cliquez sur l'onglet `Sauvegardes`.

![Liste des sauvegardes](images/backup-list-fr.png?raw=true)

Vous aurez la possibilité de supprimer ou télécharger une sauvegarde.

## Mettre à jour l'application <a name="update-app"></a>

Si Task et Symfony CLI sont installés sur votre système, vous avez simplement à lancer cette commande : `task update`

Sinon, lancez les commandes suivantes :

```bash
git pull --rebase
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console app:post-install --only-missing
```

## Licence <a name="license"></a>

Cette application est protégée par une licence MIT : [LICENCE](../LICENSE).

## Contribuer <a name="contribute"></a>

Avant de faire une pull request, n'oubliez pas de lancer les vérifications d'usage (nécessite Task et Docker Compose) :

```bash
task ci
task test
```

Note : vous pouvez lancer ces commandes sans Task, regardez le fichier Taskfile.yml pour voir quelles commandes sont exécutées.

## Changelog <a name="changelog"></a>

Voir le [changelog](../CHANGELOG.md).
