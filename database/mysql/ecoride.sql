-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 01 fév. 2026 à 18:34
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `credits_transactions`
--

CREATE TABLE `credits_transactions` (
  `id` int(11) NOT NULL COMMENT 'id transaction',
  `user_id` int(11) NOT NULL COMMENT 'user concerné par la transaction',
  `trip_id` int(11) DEFAULT NULL COMMENT 'trajet concerné par la transaction (si applicable)',
  `type` varchar(50) NOT NULL COMMENT 'nature de la transaction',
  `amount` int(11) NOT NULL COMMENT 'nombre de crédits de la transaction',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date de la transaction',
  `created_by` int(11) DEFAULT NULL COMMENT 'admin/moderateur ayant effectué la transaction (si applicable)',
  `comment` varchar(255) DEFAULT NULL COMMENT 'commentaire éventuel'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL COMMENT 'id réservation',
  `trip_id` int(11) NOT NULL COMMENT 'id trajet',
  `user_id` int(11) NOT NULL COMMENT 'id utilisateur qui réserve',
  `status` enum('confirmed','cancelled') NOT NULL DEFAULT 'confirmed' COMMENT 'statut réservation',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date réservation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL COMMENT 'id avis',
  `trip_id` int(11) NOT NULL COMMENT 'id trajet',
  `author_id` int(11) NOT NULL COMMENT 'id auteur avis',
  `rating` int(11) NOT NULL COMMENT 'note 1-5',
  `comment` text NOT NULL COMMENT 'commentaire',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending' COMMENT 'statut avis',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date avis',
  `validated_by` int(11) DEFAULT NULL COMMENT 'id modérateur ayant validé/refusé l''avis',
  `validated_at` datetime DEFAULT NULL COMMENT 'date de validation/refus'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `trips`
--

CREATE TABLE `trips` (
  `id` int(11) NOT NULL COMMENT 'id trajet',
  `driver_id` int(11) NOT NULL COMMENT 'id conducteur',
  `vehicule_id` int(11) NOT NULL COMMENT 'id véhicule',
  `city_from` varchar(80) NOT NULL COMMENT 'ville départ',
  `city_to` varchar(80) NOT NULL COMMENT 'ville arrivée',
  `departure_datetime` datetime NOT NULL COMMENT 'date départ',
  `arrival_datetime` datetime NOT NULL COMMENT 'date arrivée',
  `price_credits` int(11) NOT NULL COMMENT 'crédits nécessaires',
  `seats_available` int(11) NOT NULL COMMENT 'places disponibles',
  `smoking_allowed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Fumeurs autorisés (0 = non, 1 = oui)',
  `pets_allowed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Animaux autorisés (0 = non, 1 = oui)',
  `driver_notes` varchar(255) DEFAULT NULL COMMENT 'Précisions conducteur pour ce trajet',
  `validated_by` int(11) DEFAULT NULL COMMENT 'id modérateur ayant validé/refusé le trajet',
  `validated_at` datetime DEFAULT NULL COMMENT 'date de validation/refus',
  `status` enum('planned','ongoing','finished','cancelled') NOT NULL DEFAULT 'planned' COMMENT 'statut du trajet'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `trips`
--

INSERT INTO `trips` (`id`, `driver_id`, `vehicule_id`, `city_from`, `city_to`, `departure_datetime`, `arrival_datetime`, `price_credits`, `seats_available`, `smoking_allowed`, `pets_allowed`, `driver_notes`, `validated_by`, `validated_at`, `status`) VALUES
(2, 4, 2, 'Paris', 'Reims', '2026-08-03 07:30:00', '2026-08-03 09:15:00', 6, 3, 0, 0, 'Départ ponctuel, arrêt possible 5 min.', NULL, NULL, 'planned'),
(3, 5, 3, 'Lyon', 'Grenoble', '2026-08-04 18:00:00', '2026-08-04 19:20:00', 5, 2, 0, 0, 'Trajet après le travail.', NULL, NULL, 'planned'),
(4, 6, 4, 'Bordeaux', 'Arcachon', '2026-08-06 10:00:00', '2026-08-06 11:00:00', 4, 3, 0, 1, 'Petits animaux OK si transportés.', NULL, NULL, 'planned'),
(5, 7, 5, 'Lille', 'Bruxelles', '2026-08-07 08:15:00', '2026-08-07 09:40:00', 7, 2, 0, 0, 'Passeport/CI nécessaire selon situation.', NULL, NULL, 'planned'),
(6, 9, 6, 'Nantes', 'Rennes', '2026-08-09 14:00:00', '2026-08-09 15:10:00', 4, 3, 1, 0, 'Fumeur uniquement fenêtre ouverte.', NULL, NULL, 'planned'),
(7, 10, 7, 'Toulouse', 'Carcassonne', '2026-08-10 09:00:00', '2026-08-10 10:10:00', 5, 3, 0, 0, 'Musique douce, conduite souple.', NULL, NULL, 'planned'),
(8, 4, 2, 'Strasbourg', 'Colmar', '2026-08-12 12:30:00', '2026-08-12 13:15:00', 3, 3, 0, 0, 'Bagage cabine OK.', NULL, NULL, 'planned'),
(9, 5, 3, 'Marseille', 'Aix-en-Provence', '2026-08-14 08:00:00', '2026-08-14 08:35:00', 3, 2, 0, 0, 'Trajet court, ponctualité SVP.', NULL, NULL, 'planned'),
(10, 6, 4, 'Montpellier', 'Nîmes', '2026-08-16 16:45:00', '2026-08-16 17:30:00', 3, 3, 0, 0, 'Départ proche centre-ville.', NULL, NULL, 'planned'),
(11, 7, 5, 'Nice', 'Cannes', '2026-08-18 07:20:00', '2026-08-18 08:05:00', 4, 2, 0, 0, 'Pas de gros bagages.', NULL, NULL, 'planned'),
(12, 9, 6, 'Dijon', 'Besançon', '2026-08-21 17:10:00', '2026-08-21 18:25:00', 5, 3, 0, 0, 'Pause possible si besoin.', NULL, NULL, 'planned'),
(13, 10, 7, 'Tours', 'Orléans', '2026-08-23 09:40:00', '2026-08-23 10:40:00', 4, 3, 0, 1, 'Petits animaux acceptés.', NULL, NULL, 'planned'),
(14, 4, 2, 'Clermont-Ferrand', 'Saint-Étienne', '2026-08-25 06:50:00', '2026-08-25 08:40:00', 6, 2, 0, 0, 'Départ tôt, merci d’être à l’heure.', NULL, NULL, 'planned'),
(15, 5, 3, 'Annecy', 'Chambéry', '2026-08-27 18:30:00', '2026-08-27 19:15:00', 4, 2, 0, 0, 'Trajet régulier.', NULL, NULL, 'planned'),
(16, 6, 4, 'Avignon', 'Arles', '2026-08-30 11:00:00', '2026-08-30 11:50:00', 4, 3, 0, 0, 'Confort OK, clim si besoin.', NULL, NULL, 'planned'),
(17, 7, 5, 'Paris', 'Rouen', '2026-09-02 18:10:00', '2026-09-02 19:40:00', 6, 2, 0, 0, 'Départ après 18h.', NULL, NULL, 'planned'),
(18, 9, 6, 'Rennes', 'Brest', '2026-09-05 08:30:00', '2026-09-05 10:50:00', 8, 3, 0, 0, 'Trajet long, pause café possible.', NULL, NULL, 'planned'),
(19, 10, 7, 'Lyon', 'Valence', '2026-09-08 07:10:00', '2026-09-08 08:20:00', 5, 3, 0, 0, 'Silence apprécié le matin.', NULL, NULL, 'planned'),
(20, 4, 2, 'Toulouse', 'Albi', '2026-09-12 13:20:00', '2026-09-12 14:25:00', 5, 3, 0, 0, 'Conduite zen, arrivée à l’heure.', NULL, NULL, 'planned'),
(21, 6, 4, 'Bordeaux', 'Poitiers', '2026-09-18 16:00:00', '2026-09-18 18:05:00', 7, 2, 0, 0, 'Autoroute, pas de détour.', NULL, NULL, 'planned');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'identifiant',
  `pseudo` varchar(50) NOT NULL COMMENT 'pseudo',
  `last_name` varchar(100) NOT NULL COMMENT 'nom de famille',
  `first_name` varchar(100) NOT NULL COMMENT 'prénom',
  `email` varchar(120) NOT NULL COMMENT 'email',
  `password_hash` varchar(255) NOT NULL COMMENT 'mot de passe',
  `avatar_url` varchar(255) DEFAULT NULL COMMENT 'url photo/avatar user',
  `role` enum('user','employee','admin') NOT NULL COMMENT 'rôle',
  `credits` int(11) NOT NULL DEFAULT 20 COMMENT 'crédits d''appli - 20 par défaut',
  `suspended` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'suspension du compte',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date de création du compte'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `last_name`, `first_name`, `email`, `password_hash`, `avatar_url`, `role`, `credits`, `suspended`, `created_at`) VALUES
(4, 'amina', 'EL AMRANI', 'Amina', 'amina@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(5, 'lucas', 'MARTIN', 'Lucas', 'lucas@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(6, 'camille', 'DURAND', 'Camille', 'camille@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(7, 'youssef', 'BENALI', 'Youssef', 'youssef@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(8, 'marie', 'LEFEVRE', 'Marie', 'marie@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(9, 'thomas', 'BERNARD', 'Thomas', 'thomas@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(10, 'ines', 'GARCIA', 'Inès', 'ines@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(11, 'kevin', 'ROUSSEAU', 'Kévin', 'kevin@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(12, 'sarah', 'COHEN', 'Sarah', 'sarah@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(13, 'julien', 'MOREAU', 'Julien', 'julien@ecoride.local', 'Password123!', NULL, 'user', 20, 0, '2026-02-01 17:36:13'),
(14, 'moderateur', 'DUPUIS', 'Alex', 'moderateur@ecoride.local', 'Password123!', NULL, 'employee', 20, 0, '2026-02-01 17:36:13'),
(15, 'yann', 'LORGOUILLOUS', 'Yann', 'yann@ecoride.local', 'Password123!', NULL, 'admin', 20, 0, '2026-02-01 17:36:13');

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

CREATE TABLE `vehicules` (
  `id` int(11) NOT NULL COMMENT 'id véhicules',
  `user_id` int(11) NOT NULL COMMENT 'id propriétaire véhicule',
  `brand` varchar(50) NOT NULL COMMENT 'marque véhicule',
  `model` varchar(50) NOT NULL COMMENT 'modèle véhicule',
  `energy_type` enum('electric','hybrid','fuel') NOT NULL COMMENT 'type de motorisation véhicule',
  `seats_total` int(11) NOT NULL COMMENT 'nombre de places véhicule'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id`, `user_id`, `brand`, `model`, `energy_type`, `seats_total`) VALUES
(2, 4, 'Renault', 'Zoé', 'electric', 4),
(3, 5, 'Peugeot', '308', 'fuel', 4),
(4, 6, 'Toyota', 'Yaris', 'hybrid', 4),
(5, 7, 'Tesla', 'Model 3', 'electric', 4),
(6, 9, 'Dacia', 'Sandero', 'fuel', 4),
(7, 10, 'Hyundai', 'Ioniq', 'hybrid', 4);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `credits_transactions`
--
ALTER TABLE `credits_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_trip_user` (`trip_id`,`user_id`) USING BTREE,
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_review` (`trip_id`,`author_id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `validated_by` (`validated_by`);

--
-- Index pour la table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `vehicule_id` (`vehicule_id`),
  ADD KEY `validated_by` (`validated_by`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `credits_transactions`
--
ALTER TABLE `credits_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id transaction';

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id réservation', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id avis', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `trips`
--
ALTER TABLE `trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id trajet', AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identifiant', AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id véhicules', AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `credits_transactions`
--
ALTER TABLE `credits_transactions`
  ADD CONSTRAINT `credits_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `credits_transactions_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `credits_transactions_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `trips_ibfk_2` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`),
  ADD CONSTRAINT `trips_ibfk_3` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD CONSTRAINT `vehicules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
