-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 07 jan. 2026 à 23:29
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
-- Structure de la table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `id` int(11) NOT NULL COMMENT 'id réservation',
  `trip_id` int(11) NOT NULL COMMENT 'id trajet',
  `user_id` int(11) NOT NULL COMMENT 'id utilisateur qui réserve',
  `status` enum('confirmed','cancelled') NOT NULL DEFAULT 'confirmed' COMMENT 'statut réservation',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date réservation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `trip_id`, `user_id`, `status`, `created_at`) VALUES
(1, 1, 3, 'confirmed', '2026-01-07 19:09:43');

-- --------------------------------------------------------

--
-- Structure de la table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL COMMENT 'id avis',
  `trip_id` int(11) NOT NULL COMMENT 'id trajet',
  `author_id` int(11) NOT NULL COMMENT 'id auteur avis',
  `rating` int(11) NOT NULL COMMENT 'note 1-5',
  `comment` text NOT NULL COMMENT 'commentaire',
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending' COMMENT 'statut avis',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date avis'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reviews`
--

INSERT INTO `reviews` (`id`, `trip_id`, `author_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(1, 1, 3, 1, 'Nickel', 'pending', '2026-01-07 19:19:20');

-- --------------------------------------------------------

--
-- Structure de la table `trips`
--

DROP TABLE IF EXISTS `trips`;
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
  `status` enum('planned','ongoing','finished','cancelled') NOT NULL DEFAULT 'planned' COMMENT 'statut du trajet'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `trips`
--

INSERT INTO `trips` (`id`, `driver_id`, `vehicule_id`, `city_from`, `city_to`, `departure_datetime`, `arrival_datetime`, `price_credits`, `seats_available`, `status`) VALUES
(1, 1, 1, 'Paris', 'Lyon', '2026-02-16 09:00:00', '2026-02-16 14:00:00', 5, 4, 'planned');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'identifiant',
  `pseudo` varchar(50) NOT NULL COMMENT 'pseudo',
  `last_name` varchar(100) NOT NULL COMMENT 'nom de famille',
  `first_name` varchar(100) NOT NULL COMMENT 'prénom',
  `email` varchar(120) NOT NULL COMMENT 'email',
  `password_hash` varchar(255) NOT NULL COMMENT 'mot de passe',
  `role` enum('user','employee','admin') NOT NULL COMMENT 'rôle',
  `credits` int(11) NOT NULL DEFAULT 20 COMMENT 'crédits d''appli - 20 par défaut',
  `suspended` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'suspension du compte',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date de création du compte'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `pseudo`, `last_name`, `first_name`, `email`, `password_hash`, `role`, `credits`, `suspended`, `created_at`) VALUES
(1, 'chauffeur1', 'CURIE', 'Marie', 'chauffeur1@ecoride.fr', 'test', 'user', 20, 0, '2026-01-07 18:51:03'),
(3, 'user2', 'LOVELACE', 'Ada', 'user2@ecoride.fr', 'test2', 'user', 20, 0, '2026-01-07 19:09:22');

-- --------------------------------------------------------

--
-- Structure de la table `vehicules`
--

DROP TABLE IF EXISTS `vehicules`;
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
(1, 1, 'Mercedes', 'CLA', 'electric', 4);

--
-- Index pour les tables déchargées
--

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
  ADD KEY `author_id` (`author_id`);

--
-- Index pour la table `trips`
--
ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `vehicule_id` (`vehicule_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id trajet', AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'identifiant', AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id véhicules', AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

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
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`);

--
-- Contraintes pour la table `trips`
--
ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `trips_ibfk_2` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`);

--
-- Contraintes pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD CONSTRAINT `vehicules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
