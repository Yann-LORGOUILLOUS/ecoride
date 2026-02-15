# EcoRide -- Déploiement en local

Ce document explique comment installer et lancer l'application EcoRide
en environnement local.

------------------------------------------------------------------------

## 1. Prérequis

-   PHP 8.2 ou supérieur
-   Apache (XAMPP recommandé)
-   MySQL
-   MongoDB
-   Composer

L'extension PHP `mongodb` doit être activée.

------------------------------------------------------------------------

## 2. Installation du projet

1.  Placer le dossier du projet dans le répertoire serveur (ex :
    `htdocs` pour XAMPP).
2.  Ouvrir un terminal à la racine du projet.
3.  Installer les dépendances :

``` bash
composer install
```

------------------------------------------------------------------------

## 3. Configuration

Créer un fichier `.env` à la racine du projet (copie de `env.example` si
présent).

Renseigner au minimum :

``` env
MONGODB_URI="mongodb://127.0.0.1:27017"
MONGODB_DB="ecoride"
MONGODB_COLLECTION="reports"
```

Adapter si vous utilisez MongoDB Atlas.

Les paramètres MySQL sont définis dans `public/index.php`. Par défaut :

-   Host : 127.0.0.1
-   Base : ecoride
-   User : root
-   Password : (vide)

Modifier si nécessaire.

------------------------------------------------------------------------

## 4. Base de données MySQL

Créer une base nommée `ecoride`, puis importer :

`database/mysql/ecoride.sql`

Via phpMyAdmin ou en ligne de commande :

``` bash
mysql -u root -p ecoride < database/mysql/ecoride.sql
```

------------------------------------------------------------------------

## 5. MongoDB

Créer une base `ecoride`.

La collection `reports` sera créée automatiquement lors des premiers
signalements si elle n'existe pas.

------------------------------------------------------------------------

## 6. Lancement

### Avec XAMPP

1.  Démarrer Apache et MySQL.
2.  Accéder à :
    http://localhost/ecoride/public

Si des erreurs 404 apparaissent, vérifier que : - `mod_rewrite` est
activé - `AllowOverride All` est autorisé

### Avec le serveur PHP intégré

Depuis la racine :

``` bash
php -S 127.0.0.1:8000 -t public
```

Puis ouvrir :

    http://127.0.0.1:8000

------------------------------------------------------------------------

L'application est alors accessible en local.
