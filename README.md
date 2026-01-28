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

## Rôles et permissions

L’application EcoRide repose sur une gestion de rôles permettant de séparer clairement les responsabilités et les accès.

Trois types de comptes sont prévus :

- **Utilisateur**  
  Peut consulter les trajets, réserver des places, proposer des trajets, gérer ses véhicules, consulter et utiliser ses crédits, déposer des avis et signaler un trajet ou un problème.

- **Employé / Modérateur**  
  Est chargé de la modération de la plateforme. Il valide les trajets proposés, les avis déposés par les utilisateurs et traite les signalements liés aux trajets ou à l’utilisation de l’application.

- **Administrateur**  
  Dispose d’un accès global à la plateforme. Il peut gérer les comptes utilisateurs (création, suspension, réactivation), traiter les signalements techniques et consulter les statistiques globales de l’application.


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

## Système de crédits

EcoRide intègre un système de crédits servant de monnaie interne à la plateforme.  
Ces crédits n’ont aucune valeur monétaire et sont uniquement utilisés pour faciliter les échanges entre utilisateurs.

Le principe est le suivant :
- chaque utilisateur reçoit un capital de crédits à l’inscription,
- les crédits sont utilisés pour réserver des trajets en tant que passager,
- les crédits sont gagnés en proposant des trajets en tant que conducteur,
- certaines actions positives (notation, bon comportement) peuvent générer des crédits supplémentaires.

Le calcul précis des crédits liés aux trajets (distance, conditions particulières) n’est volontairement pas automatisé.  
Les trajets doivent être validés manuellement par un modérateur, afin de garantir l’équité, la simplicité du système et d’éviter une complexité technique excessive dans le cadre du projet. Cet aspect pourra évoluer avec la plateforme en fonction du nombre d'utilisateurs.

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

### Conception des interfaces (Figma)

L’ensemble des interfaces de l’application a été conçu sur Figma en amont du développement.

La conception s’est appuyée sur :
- l’utilisation de composants réutilisables,
- la mise en place d’auto-layouts pour garantir la cohérence et la maintenabilité,
- une approche responsive permettant l’adaptation des interfaces aux formats desktop et mobile sans duplication inutile des écrans.

Les wireframes couvrent l’intégralité des parcours utilisateurs, modérateurs et administrateurs, afin d’anticiper les besoins fonctionnels et de faciliter le passage au développement.

---

### Outils utilisés
- **Figma** : conception de la charte graphique et des wireframes
- **Trello** : organisation et suivi des étapes du projet

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

Le projet est actuellement en phase de **finalisation du design et de préparation au développement**.  
L’ensemble des wireframes (pages publiques, tableaux de bord utilisateur, modérateur et administrateur) a été conçu sur Figma.  
Les prochaines étapes consistent à entamer le développement de l’application à partir des maquettes validées.
