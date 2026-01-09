# EcoRide – Application de covoiturage

## Présentation du projet

EcoRide est une application de covoiturage développée dans le cadre d’un projet d’évaluation (ECF) pour la formation **Développeur Web & Web Mobile**.

L’objectif du projet est de concevoir une application permettant :
- la consultation de trajets de covoiturage,
- la réservation de places par des utilisateurs,
- la gestion des trajets par des conducteurs,
- la gestion des avis et des signalements,
- la modération par des employés et administrateurs.

Le projet est développé progressivement, en suivant une démarche structurée.

---

## Phase préparatoire

Avant de commencer le développement, une phase préparatoire a été réalisée afin de mettre en place un environnement de travail fonctionnel et fiable.

### Vérification de l’environnement local
- Installation et vérification de **XAMPP** (Apache, PHP, MySQL)
- Vérification du bon fonctionnement du serveur local
- Accès à phpMyAdmin pour la gestion des bases de données

Cette étape permet de s’assurer que l’environnement de développement est opérationnel avant d’aller plus loin.

### Création du dépôt GitHub
- Création d’un dépôt GitHub dédié au projet EcoRide
- Initialisation du dépôt en local
- Lien entre le dépôt local et le dépôt distant GitHub

L’utilisation de GitHub permet de versionner le projet et de suivre son évolution.

### Mise en place de la structure du projet
- Création de l’arborescence du projet via **Visual Studio Code**
- Organisation des dossiers (code, base de données, documentation)
- Mise en place des premiers fichiers nécessaires au projet

Cette étape vise à partir sur une base propre et organisée.

### Vérification de la connexion au dépôt distant
- Premier commit de test
- Envoi du projet sur le dépôt GitHub
- Vérification que la synchronisation fonctionne correctement

Cette vérification garantit que le versioning est fonctionnel dès le début du projet.

---

## Conception de la base de données

Une phase de conception des données a été réalisée avant le développement applicatif.

### Base de données relationnelle (MySQL)
La base de données MySQL stocke les données métier :
- utilisateurs,
- véhicules,
- trajets,
- réservations,
- avis.

Les relations entre les tables sont assurées par des clés étrangères et des contraintes d’unicité afin de garantir la cohérence des données.

Un dump SQL est versionné dans le dépôt afin de pouvoir recréer la base facilement.

### Base de données NoSQL (MongoDB)
MongoDB est utilisée pour la gestion des signalements :
- incidents liés aux trajets,
- problèmes liés à l’application.

Ces données, de type événementiel, sont stockées dans une collection dédiée, avec un cycle de vie simple (signalement ouvert / résolu).

---

## Design & UX

Avant de commencer le développement en PHP, une phase de design a été menée afin de cadrer le projet.

### Charte graphique
Une charte graphique simple a été définie à l’aide de **Figma**, avec pour objectifs :
- assurer une cohérence visuelle,
- faciliter l’intégration HTML/CSS,
- rester sobre et fonctionnelle.

Elle comprend :
- une palette de couleurs limitée,
- une typographie sans-serif pour la lisibilité,
- un bouton de référence pour les actions principales,
- un logo typographique simple.

### Outils utilisés
- Figma (charte graphique)
- Trello (organisation et suivi des tâches)

Les maquettes fonctionnelles (wireframes) seront réalisées avant le début du développement.

---

## Technologies utilisées
- PHP (sans framework)
- MySQL
- MongoDB Atlas
- Apache (XAMPP)
- Git / GitHub
- Visual Studio Code
- Figma

---

## Avancement du projet
Le projet est actuellement en phase de **design et préparation fonctionnelle**.  
Le développement PHP débutera après validation des maquettes et des parcours utilisateurs.
