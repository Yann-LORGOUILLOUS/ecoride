# EcoRide – Application de covoiturage

## Présentation
EcoRide est une application de covoiturage développée dans le cadre d’un projet d’évaluation (ECF) pour la formation Développeur Web & Web Mobile.

L’objectif est de proposer une plateforme permettant :
- la recherche de trajets
- la participation à des covoiturages
- la gestion des avis
- la modération et le traitement des signalements

---

## Avancement du projet

### Conception de la base de données
La base de données relationnelle (MySQL) a été conçue et implémentée avec les tables suivantes :
- users
- vehicles
- trips
- reservations
- reviews

Les relations, clés étrangères et contraintes d’unicité ont été mises en place afin de garantir la cohérence des données.

### Base NoSQL
Une base MongoDB (Atlas) est utilisée pour la gestion des signalements :
- incidents de trajets
- problèmes liés à l’application

Une collection `reports` permet de stocker ces signalements avec un cycle de vie simple (open / resolved).

---

## Technologies utilisées
- PHP (sans framework)
- MySQL
- MongoDB Atlas
- phpMyAdmin
- MongoDB Compass
