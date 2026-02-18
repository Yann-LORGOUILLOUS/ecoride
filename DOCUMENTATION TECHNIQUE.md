DOCUMENTATION TECHNIQUE

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\

1\. Réflexions initiales technologiques sur le sujet

Dans le cadre du projet EcoRide, l’objectif était de concevoir une application web de covoiturage reposant sur un système de crédits internes, tout en respectant les contraintes pédagogiques du titre professionnel Développeur Web et Web Mobile 

Le premier axe de réflexion a porté sur l’architecture backend. Le choix a été fait d’utiliser PHP 8.2 sans framework afin de maîtriser pleinement les mécanismes fondamentaux : routing, gestion des sessions, séparation des responsabilités, contrôle des accès et interaction avec les bases de données. Une architecture inspirée du modèle MVC, articulée autour d’un Front Controller, a été retenue pour structurer le projet de manière claire et maintenable.

Le projet nécessitait également la gestion de données fortement relationnelles (utilisateurs, trajets, réservations, avis, transactions de crédits). MySQL s’est imposé comme base principale afin de garantir l’intégrité référentielle, les contraintes de clés étrangères et la gestion des transactions critiques liées aux crédits. En complément, MongoDB a été intégré pour le stockage des signalements et incidents. Ce choix répondait à la double exigence pédagogique (maîtrise multi-base) et technique : ces données événementielles ne nécessitent pas de relations fortes et bénéficient d’une structure documentaire souple.

Côté front-end, le projet repose sur HTML5, CSS3 et Bootstrap. Ce choix garantit une structuration sémantique propre, un responsive design cohérent et une rapidité de mise en œuvre adaptée à l’ampleur du projet. JavaScript natif a été privilégié pour limiter les dépendances et conserver une maîtrise complète des interactions côté client.

L’ensemble des choix technologiques a été guidé par trois principes :
* compréhension des mécanismes internes,
* cohérence pédagogique,
* stabilité et maintenabilité.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\

2\. Configuration de l’environnement de travail

Le développement du projet EcoRide a été réalisé en environnement local à l’aide de XAMPP, intégrant Apache, MySQL et PHP 8.2.

Ce choix permet :
* une installation rapide et reproductible,
* une compatibilité immédiate avec PHP et MySQL,
* un environnement stable pour un projet pédagogique.

L’organisation du projet repose sur une séparation en couches :
* Presentation : contrôleurs et vues
* Infrastructure : accès aux bases de données (Repositories, PDO, MongoDB)
* Configuration : paramètres environnementaux

Cette structuration favorise la séparation des responsabilités, la lisibilité du code et sa maintenabilité à long terme.

Visual Studio Code a été utilisé comme environnement de développement principal, avec des extensions telles que PHP Intelephense, GitLens et MongoDB for VS Code. Ce choix améliore la productivité, la détection précoce d’erreurs et la navigation dans le code.

La gestion de version a été assurée via Git, selon une organisation structurée :
* branche main pour les versions stables,
* branche dev pour l’intégration,
* branches feature pour le développement isolé des fonctionnalités.

Concernant la sécurité et les bonnes pratiques, l’environnement a été configuré pour :
* utiliser exclusivement des requêtes préparées PDO,
* protéger les formulaires via des tokens CSRF,
* activer l’affichage des erreurs uniquement en environnement de développement,
* isoler les variables sensibles dans un fichier .env.

L’ensemble de cette configuration vise à garantir :
* stabilité,
* reproductibilité,
* respect des bonnes pratiques professionnelles,
* démonstration d’une maîtrise technique backend complète.

\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\_\

3\. Déploiement de l'application

L’application EcoRide est déployée sur la plateforme cloud Railway à l’aide d’un conteneur Docker personnalisé. Ce choix permet de maîtriser précisément l’environnement d’exécution et d’assurer une cohérence entre le développement local et la production.

Un Dockerfile basé sur l’image officielle php:8.2-cli est utilisé. Lors du build, les extensions nécessaires au projet sont installées, notamment pdo, pdo_mysql, mysqli, zip ainsi que l’extension mongodb, compilée avec le support OpenSSL afin de garantir la compatibilité TLS requise par MongoDB Atlas. Les dépendances PHP sont installées via Composer en mode production (--no-dev --optimize-autoloader) afin de limiter la surface d’attaque et d’optimiser les performances.

L’application est exposée via le serveur PHP intégré pointant vers le dossier public, conformément à l’architecture Front Controller mise en place. Cette configuration permet de centraliser le point d’entrée et de contrôler l’ensemble du routage applicatif.

Les variables sensibles, notamment MONGODB_URI et MONGODB_DB, sont injectées via le système de variables d’environnement Railway et ne sont jamais versionnées dans le dépôt Git. La base MongoDB est hébergée sur MongoDB Atlas et sécurisée par l’utilisation du protocole mongodb+srv (TLS activé) ainsi que par un utilisateur applicatif dédié disposant uniquement des droits readWrite sur la base ecoride.

Cette approche garantit :
* une isolation claire entre l’infrastructure et le code applicatif,
* une configuration sécurisée des accès aux bases de données,
* une reproductibilité du déploiement,
* une conformité aux bonnes pratiques professionnelles en environnement cloud.