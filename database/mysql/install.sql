-- EcoRide - Installation MySQL (schema + données test)
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `ecoride` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ecoride`;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `credits_transactions`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `trips`;
DROP TABLE IF EXISTS `vehicules`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS=1;

-- Structure de la table `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL COMMENT 'identifiant',
  `pseudo` varchar(50) NOT NULL COMMENT 'pseudo',
  `last_name` varchar(100) NOT NULL COMMENT 'nom de famille',
  `first_name` varchar(100) NOT NULL COMMENT 'prénom',
  `email` varchar(120) NOT NULL COMMENT 'email',
  `password_hash` varchar(255) NOT NULL COMMENT 'mot de passe',
  `avatar_url` varchar(255) DEFAULT NULL COMMENT 'url photo/avatar user',
  `preferences_note` varchar(255) DEFAULT NULL,
  `role` enum('user','employee','admin') NOT NULL COMMENT 'rôle',
  `credits` int(11) NOT NULL DEFAULT 20 COMMENT 'crédits d''appli - 20 par défaut',
  `suspended` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'suspension du compte',
  `validated_reports_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date de création du compte'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Structure de la table `vehicules`
CREATE TABLE `vehicules` (
  `id` int(11) NOT NULL COMMENT 'id véhicules',
  `user_id` int(11) NOT NULL COMMENT 'id propriétaire véhicule',
  `license_plate` varchar(20) NOT NULL,
  `first_registration_date` date NOT NULL,
  `brand` varchar(50) NOT NULL COMMENT 'marque véhicule',
  `model` varchar(50) NOT NULL COMMENT 'modèle véhicule',
  `color` varchar(30) NOT NULL,
  `energy_type` enum('electric','hybrid','fuel') NOT NULL COMMENT 'type de motorisation véhicule',
  `seats_total` int(11) NOT NULL COMMENT 'nombre de places véhicule',
  `seats_available_default` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Structure de la table `trips`
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
  `status` enum('pending','planned','ongoing','finished','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Structure de la table `reservations`
CREATE TABLE `reservations` (
  `id` int(11) NOT NULL COMMENT 'id réservation',
  `trip_id` int(11) NOT NULL COMMENT 'id trajet',
  `user_id` int(11) NOT NULL COMMENT 'id utilisateur qui réserve',
  `status` enum('confirmed','cancelled') NOT NULL DEFAULT 'confirmed' COMMENT 'statut réservation',
  `created_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'date réservation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Structure de la table `reviews`
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

-- Structure de la table `credits_transactions`
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

-- Structure de la table `contact_messages`
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `subject` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','archived') NOT NULL DEFAULT 'new',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Index / contraintes
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `vehicules`
  ADD CONSTRAINT `vehicules_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `vehicule_id` (`vehicule_id`),
  ADD KEY `validated_by` (`validated_by`);

ALTER TABLE `trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `trips_ibfk_2` FOREIGN KEY (`vehicule_id`) REFERENCES `vehicules` (`id`),
  ADD CONSTRAINT `trips_ibfk_3` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`);

ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_trip_user` (`trip_id`,`user_id`) USING BTREE,
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_review` (`trip_id`,`author_id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `validated_by` (`validated_by`);

ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`);

ALTER TABLE `credits_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `trip_id` (`trip_id`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `credits_transactions`
  ADD CONSTRAINT `credits_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `credits_transactions_ibfk_2` FOREIGN KEY (`trip_id`) REFERENCES `trips` (`id`),
  ADD CONSTRAINT `credits_transactions_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);


-- Données initiales
INSERT INTO `users` (`id`, `pseudo`, `last_name`, `first_name`, `email`, `password_hash`, `avatar_url`, `preferences_note`, `role`, `credits`, `suspended`, `validated_reports_count`, `created_at`) VALUES
(1, 'ecoride_platform', 'ECORIDE', 'PLATFORM', 'platform@ecoride.local', 'platform_internal_account', NULL, NULL, 'admin', 4, 0, 0, '2026-02-14 11:33:50'),
(4, 'amina', 'EL AMRANI', 'Amina', 'amina@ecoride.local', 'Password123!', 'https://numero.com/wp-content/uploads/2022/10/avatar-film2.jpg', NULL, 'user', 14, 0, 1, '2026-02-01 17:36:13'),
(5, 'lucas', 'MARTIN', 'Lucas', 'lucas@ecoride.local', 'Password123!', NULL, NULL, 'user', 11, 0, 0, '2026-02-01 17:36:13'),
(6, 'camille', 'DURAND', 'Camille', 'camille@ecoride.local', 'Password123!', NULL, NULL, 'user', 20, 0, 0, '2026-02-01 17:36:13'),
(7, 'youssef', 'BENALI', 'Youssef', 'youssef@ecoride.local', 'Password123!', NULL, 'Musique métal à fond', 'user', 19, 0, 0, '2026-02-01 17:36:13'),
(8, 'marie', 'LEFEVRE', 'Marie', 'marie@ecoride.local', 'Password123!', NULL, NULL, 'user', 20, 0, 0, '2026-02-01 17:36:13'),
(9, 'thomas', 'BERNARD', 'Thomas', 'thomas@ecoride.local', 'Password123!', NULL, NULL, 'user', 20, 0, 0, '2026-02-01 17:36:13'),
(10, 'ines', 'GARCIA', 'Inès', 'ines@ecoride.local', 'Password123!', NULL, NULL, 'user', 20, 0, 0, '2026-02-01 17:36:13'),
(11, 'kevin', 'ROUSSEAU', 'Kévin', 'kevin@ecoride.local', 'Password123!', NULL, NULL, 'user', 20, 0, 0, '2026-02-01 17:36:13'),
(12, 'sarah', 'COHEN', 'Sarah', 'sarah@ecoride.local', 'Password123!', NULL, NULL, 'user', 20, 0, 0, '2026-02-01 17:36:13'),
(13, 'julien', 'MOREAU', 'Julien', 'julien@ecoride.local', 'Password123!', NULL, NULL, 'user', 20, 0, 0, '2026-02-01 17:36:13'),
(14, 'moderateur', 'DUPUIS', 'Alex', 'moderateur@ecoride.local', 'Password123!', NULL, NULL, 'employee', 20, 0, 0, '2026-02-01 17:36:13'),
(15, 'yann', 'LORGOUILLOUS', 'Yann', 'yann@ecoride.local', 'Password123!', 'https://avatars.githubusercontent.com/u/215014255?v=4', NULL, 'admin', 14, 0, 0, '2026-02-01 17:36:13'),
(16, 'Test1', 'NomTest', 'PrénomTest', 'test@ecoride.local', '$2y$10$Ng2ddBX1epm7IpRMnBmWjO0/dlJSrz5gZy0qvGhsFqKSMyNZnsR.2', NULL, NULL, 'user', 20, 0, 0, '2026-02-04 20:35:14'),
(17, 'Test2', 'NomTest2', 'PrenomTest2', 'test2@ecoride.local', '$2y$10$A.V6wHcUeSwnWNQQMnhyE.5sILQKLgWXocUyKw17GTbsEYmBnuavK', NULL, NULL, 'user', 20, 0, 0, '2026-02-04 20:36:02'),
(18, 'Test3', 'NomTest3', 'PrenomTest3', 'Test3@ecoride.local', '$2y$10$hAnq7NRDh9/iWjMfDzbFuOXTQjGO0ABiDlkdAgZtLWTGedGIWLczO', NULL, NULL, 'employee', 20, 0, 0, '2026-02-07 17:11:32'),
(19, 'Test4', 'Test4', 'Test4', 'test4@ecoride.local', '$2y$10$me2u33MBN0iJs6gGdgkXqemc6e/rNX3MOjtMSQUlrnMWXUkATpqFi', NULL, NULL, 'user', 20, 0, 0, '2026-02-14 12:14:23');

INSERT INTO `vehicules` (`id`, `user_id`, `license_plate`, `first_registration_date`, `brand`, `model`, `color`, `energy_type`, `seats_total`, `seats_available_default`, `created_at`) VALUES
(2, 4, 'TEMP-2', '2000-01-01', 'Renault', 'Zoé', 'Inconnue', 'electric', 4, 3, '2026-02-08 15:38:11'),
(3, 5, 'TEMP-3', '2000-01-01', 'Peugeot', '308', 'Inconnue', 'fuel', 4, 3, '2026-02-08 15:38:11'),
(4, 6, 'TEMP-4', '2000-01-01', 'Toyota', 'Yaris', 'Inconnue', 'hybrid', 4, 3, '2026-02-08 15:38:11'),
(5, 7, 'TEMP-5', '2000-01-01', 'Tesla', 'Model 3', 'Inconnue', 'electric', 4, 3, '2026-02-08 15:38:11'),
(6, 9, 'TEMP-6', '2000-01-01', 'Dacia', 'Sandero', 'Inconnue', 'fuel', 4, 3, '2026-02-08 15:38:11'),
(7, 10, 'TEMP-7', '2000-01-01', 'Hyundai', 'Ioniq', 'Inconnue', 'hybrid', 4, 3, '2026-02-08 15:38:11'),
(8, 7, 'AA-123-BB', '2026-01-01', 'Mercedes', 'Classe C', 'Rouge', 'electric', 4, 3, '2026-02-08 15:44:54');

INSERT INTO `trips` (`id`, `driver_id`, `vehicule_id`, `city_from`, `city_to`, `departure_datetime`, `arrival_datetime`, `price_credits`, `seats_available`, `smoking_allowed`, `pets_allowed`, `driver_notes`, `validated_by`, `validated_at`, `status`, `created_at`) VALUES
(2, 4, 2, 'Paris', 'Reims', '2026-08-03 07:30:00', '2026-08-03 09:15:00', 6, 1, 0, 0, 'Départ ponctuel, arrêt possible 5 min.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(3, 5, 3, 'Lyon', 'Grenoble', '2026-08-04 18:00:00', '2026-08-04 19:20:00', 5, 2, 0, 0, 'Trajet après le travail.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(4, 6, 4, 'Bordeaux', 'Arcachon', '2026-08-06 10:00:00', '2026-08-06 11:00:00', 4, 3, 0, 1, 'Petits animaux OK si transportés.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(5, 7, 5, 'Lille', 'Bruxelles', '2026-08-07 08:15:00', '2026-08-07 09:40:00', 7, 2, 0, 0, 'Passeport/CI nécessaire selon situation.', NULL, NULL, 'finished', '2026-02-05 15:47:00'),
(6, 9, 6, 'Nantes', 'Rennes', '2026-08-09 14:00:00', '2026-08-09 15:10:00', 4, 3, 1, 0, 'Fumeur uniquement fenêtre ouverte.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(7, 10, 7, 'Toulouse', 'Carcassonne', '2026-08-10 09:00:00', '2026-08-10 10:10:00', 5, 3, 0, 0, 'Musique douce, conduite souple.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(8, 4, 2, 'Strasbourg', 'Colmar', '2026-08-12 12:30:00', '2026-08-12 13:15:00', 3, 3, 0, 0, 'Bagage cabine OK.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(9, 5, 3, 'Marseille', 'Aix-en-Provence', '2026-08-14 08:00:00', '2026-08-14 08:35:00', 3, 1, 0, 0, 'Trajet court, ponctualité SVP.', NULL, NULL, 'finished', '2026-02-05 15:47:00'),
(10, 6, 4, 'Montpellier', 'Nîmes', '2026-08-16 16:45:00', '2026-08-16 17:30:00', 3, 3, 0, 0, 'Départ proche centre-ville.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(11, 7, 5, 'Nice', 'Cannes', '2026-08-18 07:20:00', '2026-08-18 08:05:00', 4, 2, 0, 0, 'Pas de gros bagages.', NULL, NULL, 'ongoing', '2026-02-05 15:47:00'),
(12, 9, 6, 'Dijon', 'Besançon', '2026-08-21 17:10:00', '2026-08-21 18:25:00', 5, 3, 0, 0, 'Pause possible si besoin.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(13, 10, 7, 'Tours', 'Orléans', '2026-08-23 09:40:00', '2026-08-23 10:40:00', 4, 3, 0, 1, 'Petits animaux acceptés.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(14, 4, 2, 'Clermont-Ferrand', 'Saint-Étienne', '2026-08-25 06:50:00', '2026-08-25 08:40:00', 6, 2, 0, 0, 'Départ tôt, merci d’être à l’heure.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(15, 5, 3, 'Annecy', 'Chambéry', '2026-08-27 18:30:00', '2026-08-27 19:15:00', 4, 2, 0, 0, 'Trajet régulier.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(16, 6, 4, 'Avignon', 'Arles', '2026-08-30 11:00:00', '2026-08-30 11:50:00', 4, 3, 0, 0, 'Confort OK, clim si besoin.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(17, 7, 5, 'Paris', 'Rouen', '2026-09-02 18:10:00', '2026-09-02 19:40:00', 6, 1, 0, 0, 'Départ après 18h.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(18, 9, 6, 'Rennes', 'Brest', '2026-09-05 08:30:00', '2026-09-05 10:50:00', 8, 3, 0, 0, 'Trajet long, pause café possible.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(19, 10, 7, 'Lyon', 'Valence', '2026-09-08 07:10:00', '2026-09-08 08:20:00', 5, 3, 0, 0, 'Silence apprécié le matin.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(20, 4, 2, 'Toulouse', 'Albi', '2026-09-12 13:20:00', '2026-09-12 14:25:00', 5, 3, 0, 0, 'Conduite zen, arrivée à l’heure.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(21, 6, 4, 'Bordeaux', 'Poitiers', '2026-09-18 16:00:00', '2026-09-18 18:05:00', 7, 2, 0, 0, 'Autoroute, pas de détour.', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(22, 7, 5, 'Paris', 'Marseille', '2026-09-12 07:30:00', '2026-09-12 19:00:00', 15, 3, 1, 1, 'Pause toutes les 2h pour recharge et WC', NULL, NULL, 'planned', '2026-02-05 15:47:00'),
(23, 7, 8, 'Paris', 'Chartres', '2026-01-10 18:30:00', '2026-01-10 19:45:00', 6, 2, 0, 0, 'Trajet calme, ponctualité.', NULL, NULL, 'finished', '2026-02-08 16:27:46'),
(24, 7, 8, 'Melun', 'Fontainebleau', '2026-01-18 09:15:00', '2026-01-18 10:00:00', 4, 3, 0, 1, 'Petit animal ok si en caisse.', NULL, NULL, 'finished', '2026-02-08 16:27:46'),
(25, 7, 8, 'Orléans', 'Tours', '2026-01-25 17:10:00', '2026-01-25 18:20:00', 5, 2, 1, 0, 'Fumeur fenêtre ouverte.', NULL, NULL, 'finished', '2026-02-08 16:27:46'),
(26, 7, 8, 'Paris', 'Brest', '2026-02-14 11:00:00', '2026-02-14 21:00:00', 10, 1, 0, 0, 'Chill', NULL, NULL, 'cancelled', '2026-02-14 11:00:25');

INSERT INTO `reservations` (`id`, `trip_id`, `user_id`, `status`, `created_at`) VALUES
(2, 2, 7, 'confirmed', '2026-02-04 21:21:13'),
(3, 17, 4, 'confirmed', '2026-02-04 21:37:23'),
(4, 26, 5, 'confirmed', '2026-02-14 11:41:32'),
(5, 9, 7, 'confirmed', '2026-02-14 14:12:09'),
(6, 2, 15, 'confirmed', '2026-02-15 11:50:14');

INSERT INTO `reviews` (`id`, `trip_id`, `author_id`, `rating`, `comment`, `status`, `created_at`, `validated_by`, `validated_at`) VALUES
(2, 15, 6, 5, 'Nickel', 'approved', '2026-02-05 20:32:38', 14, '2026-02-05 20:41:25'),
(3, 23, 4, 5, 'Conduite souple, super ambiance. Je recommande.', 'approved', '2026-02-08 16:27:46', 14, '2026-02-08 16:27:46'),
(4, 24, 5, 4, 'Trajet nickel, juste un léger retard au départ.', 'approved', '2026-02-08 16:27:46', 14, '2026-02-08 16:27:46'),
(5, 25, 6, 5, 'Parfait. Très ponctuel et voiture confortable.', 'approved', '2026-02-08 16:27:46', 14, '2026-02-08 16:27:46'),
(6, 9, 7, 5, 'Parfait !', 'approved', '2026-02-14 14:13:03', 14, '2026-02-14 14:13:36');

INSERT INTO `credits_transactions` (`id`, `user_id`, `trip_id`, `type`, `amount`, `created_at`, `created_by`, `comment`) VALUES
(1, 7, 2, 'reservation', -6, '2026-02-04 21:21:13', NULL, 'Réservation du trajet'),
(2, 4, 17, 'reservation', -6, '2026-02-04 21:37:23', NULL, 'Réservation du trajet'),
(3, 5, 26, 'reservation', -10, '2026-02-14 11:41:32', NULL, 'Réservation du trajet'),
(4, 7, 26, 'driver_payout', 8, '2026-02-14 11:42:08', NULL, 'Paiement conducteur (trajet terminé)'),
(5, 1, 26, 'platform_fee', 2, '2026-02-14 11:42:08', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(6, 7, 9, 'reservation', -3, '2026-02-14 14:12:09', NULL, 'Réservation du trajet'),
(7, 5, 9, 'driver_payout', 1, '2026-02-14 14:12:35', NULL, 'Paiement conducteur (trajet terminé)'),
(8, 1, 9, 'platform_fee', 2, '2026-02-14 14:12:35', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(9, 15, 2, 'reservation', -6, '2026-02-15 11:50:14', NULL, 'Réservation du trajet');

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Youssef', 'youssef@ecoride.local', 'Merci bcp !', 'Le site est génial merci !', 'read', '2026-02-08 17:05:48');

-- Données factices supplémentaires (stats) à partir du 01/01/2026
INSERT INTO `trips` (`id`, `driver_id`, `vehicule_id`, `city_from`, `city_to`, `departure_datetime`, `arrival_datetime`, `price_credits`, `seats_available`, `smoking_allowed`, `pets_allowed`, `driver_notes`, `validated_by`, `validated_at`, `status`, `created_at`) VALUES
(27, 7, 5, 'Rouen', 'Le Havre', '2026-01-13 19:15:00', '2026-01-13 20:15:00', 6, 1, 0, 0, 'Pause courte possible si besoin.', 14, '2026-01-13 05:32:33', 'finished', '2026-01-12 15:35:44'),
(28, 9, 6, 'Lyon', 'Grenoble', '2026-02-09 17:20:00', '2026-02-09 18:38:00', 9, 3, 0, 0, 'Ambiance calme, pas d''appels.', 14, '2026-02-08 15:34:04', 'finished', '2026-02-08 00:05:15'),
(29, 5, 3, 'Poitiers', 'Bordeaux', '2026-02-12 08:40:00', '2026-02-12 10:46:00', 4, 3, 0, 0, 'Pause courte possible si besoin.', 14, '2026-02-07 12:09:01', 'finished', '2026-02-07 05:56:20'),
(30, 6, 4, 'Paris', 'Lille', '2026-01-28 09:40:00', '2026-01-28 12:16:00', 4, 2, 0, 1, 'Ambiance calme, pas d''appels.', 14, '2026-01-23 08:45:02', 'finished', '2026-01-19 00:36:50'),
(31, 5, 3, 'Rennes', 'Brest', '2026-02-15 08:50:00', '2026-02-15 11:08:00', 5, 3, 0, 0, 'Départ ponctuel, conduite souple.', 14, '2026-02-07 17:28:57', 'finished', '2026-02-05 15:37:15'),
(32, 7, 5, 'Angers', 'Nantes', '2026-01-06 16:10:00', '2026-01-06 17:10:00', 5, 2, 0, 0, 'Pause courte possible si besoin.', 14, '2026-01-05 00:00:19', 'finished', '2026-01-01 19:46:46'),
(33, 7, 5, 'Rennes', 'Brest', '2026-01-04 20:20:00', '2026-01-04 22:38:00', 10, 2, 1, 0, 'Pause courte possible si besoin.', 14, '2026-01-03 23:22:32', 'finished', '2026-01-03 21:20:36'),
(34, 5, 3, 'Nantes', 'Rennes', '2026-02-08 16:10:00', '2026-02-08 17:22:00', 4, 1, 0, 0, 'Pas de détour, on file droit.', 14, '2026-02-05 19:17:38', 'finished', '2026-01-31 19:54:34'),
(35, 5, 3, 'Rennes', 'Brest', '2026-02-05 13:50:00', '2026-02-05 16:08:00', 6, 2, 0, 0, 'Pas de détour, on file droit.', 14, '2026-02-04 00:40:50', 'finished', '2026-02-03 19:24:34'),
(36, 9, 6, 'Nice', 'Cannes', '2026-01-01 19:00:00', '2026-01-01 19:42:00', 8, 1, 1, 0, 'Pause courte possible si besoin.', 14, '2026-01-01 16:47:17', 'finished', '2026-01-01 14:58:14'),
(37, 7, 5, 'Angers', 'Nantes', '2026-01-24 18:10:00', '2026-01-24 19:10:00', 5, 1, 0, 1, 'Ambiance calme, pas d''appels.', 14, '2026-01-24 10:46:01', 'finished', '2026-01-24 02:41:05'),
(38, 5, 3, 'Nice', 'Cannes', '2026-02-11 09:30:00', '2026-02-11 10:12:00', 4, 3, 0, 0, NULL, 14, '2026-02-10 08:05:13', 'finished', '2026-02-09 13:54:11'),
(39, 7, 5, 'Nice', 'Cannes', '2026-01-29 07:50:00', '2026-01-29 08:32:00', 9, 2, 0, 1, 'Pause courte possible si besoin.', 14, '2026-01-28 14:38:35', 'finished', '2026-01-27 15:17:53'),
(40, 7, 8, 'Avignon', 'Arles', '2026-02-04 08:30:00', '2026-02-04 09:24:00', 9, 2, 1, 1, 'Recharge/pause café si trajet long.', 14, '2026-01-30 17:09:26', 'finished', '2026-01-27 06:56:03'),
(41, 7, 5, 'Poitiers', 'Bordeaux', '2026-01-13 16:40:00', '2026-01-13 18:46:00', 6, 2, 0, 1, 'Pause courte possible si besoin.', 14, '2026-01-13 00:25:02', 'finished', '2026-01-12 02:54:25'),
(42, 7, 8, 'Rennes', 'Brest', '2026-02-12 20:15:00', '2026-02-12 22:33:00', 7, 3, 1, 1, NULL, 14, '2026-02-10 14:30:45', 'finished', '2026-02-05 15:36:09'),
(43, 7, 8, 'Tours', 'Orléans', '2026-02-12 16:00:00', '2026-02-12 17:06:00', 4, 3, 0, 0, 'Recharge/pause café si trajet long.', 14, '2026-02-08 11:11:48', 'finished', '2026-02-06 10:26:59'),
(44, 9, 6, 'Poitiers', 'Bordeaux', '2026-01-14 12:45:00', '2026-01-14 14:51:00', 10, 3, 0, 0, NULL, 14, '2026-01-06 01:22:58', 'finished', '2026-01-04 18:45:19'),
(45, 7, 8, 'Nice', 'Cannes', '2026-01-05 19:00:00', '2026-01-05 19:42:00', 3, 2, 0, 0, 'Recharge/pause café si trajet long.', 14, '2026-01-05 16:38:04', 'finished', '2026-01-04 19:13:33'),
(46, 9, 6, 'Nice', 'Cannes', '2026-01-13 07:45:00', '2026-01-13 08:27:00', 9, 1, 0, 0, 'Pause courte possible si besoin.', 14, '2026-01-06 11:13:39', 'finished', '2026-01-04 21:10:09');

INSERT INTO `reservations` (`id`, `trip_id`, `user_id`, `status`, `created_at`) VALUES
(7, 27, 4, 'confirmed', '2026-01-12 14:49:09'),
(8, 28, 15, 'confirmed', '2026-02-07 17:54:06'),
(9, 28, 8, 'confirmed', '2026-02-06 21:03:41'),
(10, 29, 17, 'confirmed', '2026-02-09 15:41:02'),
(11, 30, 10, 'confirmed', '2026-01-26 16:42:02'),
(12, 30, 16, 'confirmed', '2026-01-27 17:29:21'),
(13, 31, 10, 'confirmed', '2026-02-08 14:46:10'),
(14, 31, 4, 'cancelled', '2026-02-11 21:19:54'),
(15, 32, 8, 'cancelled', '2026-01-04 07:48:17'),
(16, 33, 13, 'confirmed', '2026-01-01 23:51:36'),
(17, 33, 6, 'cancelled', '2026-01-02 05:25:25'),
(18, 34, 11, 'cancelled', '2026-02-07 02:08:42'),
(19, 35, 19, 'confirmed', '2026-01-30 19:05:36'),
(20, 36, 4, 'confirmed', '2026-01-01 09:32:45'),
(21, 37, 19, 'confirmed', '2026-01-21 00:17:35'),
(22, 38, 8, 'confirmed', '2026-02-04 17:25:39'),
(23, 39, 15, 'confirmed', '2026-01-23 04:21:21'),
(24, 39, 16, 'confirmed', '2026-01-26 19:32:07'),
(25, 40, 19, 'confirmed', '2026-02-01 01:51:57'),
(26, 40, 11, 'cancelled', '2026-01-31 12:47:27'),
(27, 41, 19, 'confirmed', '2026-01-11 12:42:07'),
(28, 41, 16, 'confirmed', '2026-01-07 17:27:35'),
(29, 42, 8, 'confirmed', '2026-02-11 01:24:49'),
(30, 43, 16, 'confirmed', '2026-02-07 14:06:36'),
(31, 43, 17, 'confirmed', '2026-02-11 09:54:22'),
(32, 44, 15, 'confirmed', '2026-01-11 20:10:08'),
(33, 45, 13, 'confirmed', '2026-01-02 04:24:49'),
(34, 46, 5, 'confirmed', '2026-01-11 05:34:27');

INSERT INTO `reviews` (`id`, `trip_id`, `author_id`, `rating`, `comment`, `status`, `created_at`, `validated_by`, `validated_at`) VALUES
(7, 27, 4, 5, 'RAS, nickel.', 'approved', '2026-01-15 22:15:00', 14, '2026-01-15 22:50:00'),
(8, 28, 15, 4, 'Conduite souple, super ambiance. Je recommande.', 'approved', '2026-02-11 19:38:00', 14, '2026-02-11 19:49:00'),
(9, 28, 8, 4, 'Conduite souple, super ambiance. Je recommande.', 'approved', '2026-02-10 20:38:00', 14, '2026-02-10 22:12:00'),
(10, 29, 17, 5, 'Trajet agréable, conducteur ponctuel.', 'approved', '2026-02-12 16:46:00', 14, '2026-02-12 18:36:00'),
(11, 30, 10, 5, 'Petit retard au départ mais trajet impeccable.', 'approved', '2026-01-30 13:16:00', 14, '2026-01-30 14:43:00'),
(12, 30, 16, 5, 'Trajet agréable, conducteur ponctuel.', 'approved', '2026-01-28 16:16:00', 14, '2026-01-28 17:33:00'),
(13, 31, 10, 4, 'RAS, nickel.', 'approved', '2026-02-15 12:08:00', 14, '2026-02-15 12:50:00'),
(14, 33, 13, 3, 'Conduite souple, super ambiance. Je recommande.', 'approved', '2026-01-05 23:38:00', 14, '2026-01-06 01:30:00'),
(15, 35, 19, 4, 'Trajet agréable, conducteur ponctuel.', 'approved', '2026-02-05 18:08:00', 14, '2026-02-05 18:35:00'),
(16, 36, 4, 3, 'Voiture propre, rien à redire.', 'approved', '2026-01-02 22:42:00', 14, '2026-01-02 23:50:00'),
(17, 37, 19, 3, 'Voiture propre, rien à redire.', 'approved', '2026-01-25 00:10:00', 14, '2026-01-25 00:40:00'),
(18, 38, 8, 4, 'Conduite souple, super ambiance. Je recommande.', 'approved', '2026-02-13 11:12:00', 14, '2026-02-13 12:02:00'),
(19, 39, 15, 4, 'Conduite souple, super ambiance. Je recommande.', 'approved', '2026-01-29 14:32:00', 14, '2026-01-29 16:05:00'),
(20, 39, 16, 4, 'Voiture propre, rien à redire.', 'approved', '2026-01-30 13:32:00', 14, '2026-01-30 15:08:00'),
(21, 40, 19, 4, 'RAS, nickel.', 'approved', '2026-02-04 13:24:00', 14, '2026-02-04 13:31:00'),
(22, 41, 19, 4, 'Petit retard au départ mais trajet impeccable.', 'approved', '2026-01-15 21:46:00', 14, '2026-01-15 22:34:00'),
(23, 41, 16, 5, 'Petit retard au départ mais trajet impeccable.', 'approved', '2026-01-15 19:46:00', 14, '2026-01-15 20:15:00'),
(24, 42, 8, 4, 'Trajet agréable, conducteur ponctuel.', 'approved', '2026-02-13 00:33:00', 14, '2026-02-13 02:08:00'),
(25, 43, 16, 3, 'Conduite souple, super ambiance. Je recommande.', 'approved', '2026-02-12 23:06:00', 14, '2026-02-12 23:43:00'),
(26, 43, 17, 5, 'RAS, nickel.', 'approved', '2026-02-14 21:06:00', 14, '2026-02-14 21:50:00'),
(27, 44, 15, 4, 'Trajet agréable, conducteur ponctuel.', 'approved', '2026-01-14 19:51:00', 14, '2026-01-14 21:09:00'),
(28, 45, 13, 5, 'Conduite souple, super ambiance. Je recommande.', 'approved', '2026-01-05 22:42:00', 14, '2026-01-05 23:05:00'),
(29, 46, 5, 4, 'Petit retard au départ mais trajet impeccable.', 'approved', '2026-01-14 14:27:00', 14, '2026-01-14 15:41:00');

INSERT INTO `credits_transactions` (`id`, `user_id`, `trip_id`, `type`, `amount`, `created_at`, `created_by`, `comment`) VALUES
(10, 4, 27, 'reservation', -6, '2026-01-12 14:49:09', NULL, 'Réservation du trajet'),
(11, 7, 27, 'driver_payout', 4, '2026-01-13 20:31:00', NULL, 'Paiement conducteur (trajet terminé)'),
(12, 1, 27, 'platform_fee', 2, '2026-01-13 20:31:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(13, 15, 28, 'reservation', -9, '2026-02-07 17:54:06', NULL, 'Réservation du trajet'),
(14, 9, 28, 'driver_payout', 7, '2026-02-09 18:43:00', NULL, 'Paiement conducteur (trajet terminé)'),
(15, 1, 28, 'platform_fee', 2, '2026-02-09 18:43:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(16, 8, 28, 'reservation', -9, '2026-02-06 21:03:41', NULL, 'Réservation du trajet'),
(17, 9, 28, 'driver_payout', 7, '2026-02-09 18:56:00', NULL, 'Paiement conducteur (trajet terminé)'),
(18, 1, 28, 'platform_fee', 2, '2026-02-09 18:56:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(19, 17, 29, 'reservation', -4, '2026-02-09 15:41:02', NULL, 'Réservation du trajet'),
(20, 5, 29, 'driver_payout', 2, '2026-02-12 11:04:00', NULL, 'Paiement conducteur (trajet terminé)'),
(21, 1, 29, 'platform_fee', 2, '2026-02-12 11:04:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(22, 10, 30, 'reservation', -4, '2026-01-26 16:42:02', NULL, 'Réservation du trajet'),
(23, 6, 30, 'driver_payout', 2, '2026-01-28 12:22:00', NULL, 'Paiement conducteur (trajet terminé)'),
(24, 1, 30, 'platform_fee', 2, '2026-01-28 12:22:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(25, 16, 30, 'reservation', -4, '2026-01-27 17:29:21', NULL, 'Réservation du trajet'),
(26, 6, 30, 'driver_payout', 2, '2026-01-28 12:17:00', NULL, 'Paiement conducteur (trajet terminé)'),
(27, 1, 30, 'platform_fee', 2, '2026-01-28 12:17:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(28, 10, 31, 'reservation', -5, '2026-02-08 14:46:10', NULL, 'Réservation du trajet'),
(29, 5, 31, 'driver_payout', 3, '2026-02-15 11:14:00', NULL, 'Paiement conducteur (trajet terminé)'),
(30, 1, 31, 'platform_fee', 2, '2026-02-15 11:14:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(31, 13, 33, 'reservation', -10, '2026-01-01 23:51:36', NULL, 'Réservation du trajet'),
(32, 7, 33, 'driver_payout', 8, '2026-01-04 22:42:00', NULL, 'Paiement conducteur (trajet terminé)'),
(33, 1, 33, 'platform_fee', 2, '2026-01-04 22:42:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(34, 19, 35, 'reservation', -6, '2026-01-30 19:05:36', NULL, 'Réservation du trajet'),
(35, 5, 35, 'driver_payout', 4, '2026-02-05 16:15:00', NULL, 'Paiement conducteur (trajet terminé)'),
(36, 1, 35, 'platform_fee', 2, '2026-02-05 16:15:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(37, 4, 36, 'reservation', -8, '2026-01-01 09:32:45', NULL, 'Réservation du trajet'),
(38, 9, 36, 'driver_payout', 6, '2026-01-01 19:50:00', NULL, 'Paiement conducteur (trajet terminé)'),
(39, 1, 36, 'platform_fee', 2, '2026-01-01 19:50:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(40, 19, 37, 'reservation', -5, '2026-01-21 00:17:35', NULL, 'Réservation du trajet'),
(41, 7, 37, 'driver_payout', 3, '2026-01-24 19:19:00', NULL, 'Paiement conducteur (trajet terminé)'),
(42, 1, 37, 'platform_fee', 2, '2026-01-24 19:19:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(43, 8, 38, 'reservation', -4, '2026-02-04 17:25:39', NULL, 'Réservation du trajet'),
(44, 5, 38, 'driver_payout', 2, '2026-02-11 10:17:00', NULL, 'Paiement conducteur (trajet terminé)'),
(45, 1, 38, 'platform_fee', 2, '2026-02-11 10:17:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(46, 15, 39, 'reservation', -9, '2026-01-23 04:21:21', NULL, 'Réservation du trajet'),
(47, 7, 39, 'driver_payout', 7, '2026-01-29 08:46:00', NULL, 'Paiement conducteur (trajet terminé)'),
(48, 1, 39, 'platform_fee', 2, '2026-01-29 08:46:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(49, 16, 39, 'reservation', -9, '2026-01-26 19:32:07', NULL, 'Réservation du trajet'),
(50, 7, 39, 'driver_payout', 7, '2026-01-29 08:50:00', NULL, 'Paiement conducteur (trajet terminé)'),
(51, 1, 39, 'platform_fee', 2, '2026-01-29 08:50:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(52, 19, 40, 'reservation', -9, '2026-02-01 01:51:57', NULL, 'Réservation du trajet'),
(53, 7, 40, 'driver_payout', 7, '2026-02-04 09:32:00', NULL, 'Paiement conducteur (trajet terminé)'),
(54, 1, 40, 'platform_fee', 2, '2026-02-04 09:32:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(55, 19, 41, 'reservation', -6, '2026-01-11 12:42:07', NULL, 'Réservation du trajet'),
(56, 7, 41, 'driver_payout', 4, '2026-01-13 18:49:00', NULL, 'Paiement conducteur (trajet terminé)'),
(57, 1, 41, 'platform_fee', 2, '2026-01-13 18:49:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(58, 16, 41, 'reservation', -6, '2026-01-07 17:27:35', NULL, 'Réservation du trajet'),
(59, 7, 41, 'driver_payout', 4, '2026-01-13 18:48:00', NULL, 'Paiement conducteur (trajet terminé)'),
(60, 1, 41, 'platform_fee', 2, '2026-01-13 18:48:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(61, 8, 42, 'reservation', -7, '2026-02-11 01:24:49', NULL, 'Réservation du trajet'),
(62, 7, 42, 'driver_payout', 5, '2026-02-12 22:43:00', NULL, 'Paiement conducteur (trajet terminé)'),
(63, 1, 42, 'platform_fee', 2, '2026-02-12 22:43:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(64, 16, 43, 'reservation', -4, '2026-02-07 14:06:36', NULL, 'Réservation du trajet'),
(65, 7, 43, 'driver_payout', 2, '2026-02-12 17:10:00', NULL, 'Paiement conducteur (trajet terminé)'),
(66, 1, 43, 'platform_fee', 2, '2026-02-12 17:10:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(67, 17, 43, 'reservation', -4, '2026-02-11 09:54:22', NULL, 'Réservation du trajet'),
(68, 7, 43, 'driver_payout', 2, '2026-02-12 17:16:00', NULL, 'Paiement conducteur (trajet terminé)'),
(69, 1, 43, 'platform_fee', 2, '2026-02-12 17:16:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(70, 15, 44, 'reservation', -10, '2026-01-11 20:10:08', NULL, 'Réservation du trajet'),
(71, 9, 44, 'driver_payout', 8, '2026-01-14 14:54:00', NULL, 'Paiement conducteur (trajet terminé)'),
(72, 1, 44, 'platform_fee', 2, '2026-01-14 14:54:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(73, 13, 45, 'reservation', -3, '2026-01-02 04:24:49', NULL, 'Réservation du trajet'),
(74, 7, 45, 'driver_payout', 1, '2026-01-05 19:45:00', NULL, 'Paiement conducteur (trajet terminé)'),
(75, 1, 45, 'platform_fee', 2, '2026-01-05 19:45:00', NULL, 'Taxe plateforme (2 crédits / réservation)'),
(76, 5, 46, 'reservation', -9, '2026-01-11 05:34:27', NULL, 'Réservation du trajet'),
(77, 9, 46, 'driver_payout', 7, '2026-01-13 08:29:00', NULL, 'Paiement conducteur (trajet terminé)'),
(78, 1, 46, 'platform_fee', 2, '2026-01-13 08:29:00', NULL, 'Taxe plateforme (2 crédits / réservation)');

-- AUTO_INCREMENT
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
ALTER TABLE `vehicules` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `trips` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
ALTER TABLE `reservations` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
ALTER TABLE `reviews` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
ALTER TABLE `credits_transactions` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;
ALTER TABLE `contact_messages` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

