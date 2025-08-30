-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 30 août 2025 à 10:41
-- Version du serveur : 10.11.10-MariaDB
-- Version de PHP : 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `u174726466_Gl`
--

-- --------------------------------------------------------

--
-- Structure de la table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `clients`
--

INSERT INTO `clients` (`id`, `name`, `telephone`, `details`, `created_at`) VALUES
(1, 'Said', '888', 'mai', '2025-08-25 16:28:46'),
(2, 'Saleh', '77', 'mi', '2025-08-25 16:28:46'),
(3, 'Mahmoud', '22', 'dd', '2025-08-25 16:31:39'),
(4, 'Moussa', '98', 'ss', '2025-08-25 16:31:39');

-- --------------------------------------------------------

--
-- Structure de la table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `quantite` int(11) DEFAULT 0,
  `photo` varchar(255) DEFAULT NULL,
  `prix_achat` decimal(10,2) DEFAULT NULL,
  `prix_vente` decimal(10,2) NOT NULL,
  `etat` enum('expose','vendue','garantie','test') DEFAULT 'expose',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `code`, `name`, `description`, `quantite`, `photo`, `prix_achat`, `prix_vente`, `etat`, `created_at`) VALUES
(17, 'Lap01', 'Dell latitude3420', 'I5-11', 10, 'dell_latitude3420.jpeg', 65000.00, 78000.00, 'expose', '2025-08-28 13:50:56'),
(18, 'Lap01', 'Dell Latitude 3420', 'i5-11', 2, NULL, 65000.00, 78000.00, 'test', '2025-08-28 13:52:45'),
(19, 'Lap02', 'Dell latitude E7240', 'I5-4eme', 1, 'dell_latitude_e7240.jpg', NULL, 34000.00, 'expose', '2025-08-30 08:33:59'),
(21, 'Lap 02', 'Hp pavilon g7', 'I3-3eme', 1, 'hp_pavilon_g7.jpg', NULL, 23000.00, 'expose', '2025-08-30 08:36:32'),
(22, 'Lap 04', 'Hp 635', 'Amd e300', 1, 'hp_635.jpg', NULL, 23000.00, 'expose', '2025-08-30 08:40:14'),
(23, 'Lap 05', 'Packard bell', 'I3-2eme', 1, 'packard_bell.jpg', NULL, 23000.00, 'expose', '2025-08-30 08:43:37'),
(24, 'Lap06', 'Dell latitude E5540', 'I3-4eme', 1, 'dell_latitude_e5540.jpg', NULL, 44500.00, 'expose', '2025-08-30 08:46:06'),
(25, 'Lap 06', 'Hp probook', 'I3-5eme', 1, 'hp_probook.jpg', NULL, 35500.00, 'expose', '2025-08-30 08:48:11'),
(26, 'Lap 07', 'Hp probook', 'I3-5eme', 1, 'hp_probook.jpg', NULL, 34000.00, 'expose', '2025-08-30 08:48:53'),
(27, 'Lap 08', 'Dell latitude E7250', 'I5-4eme', 1, 'dell_latitude_e7250.jpg', NULL, 39000.00, 'expose', '2025-08-30 08:51:42'),
(28, 'Lap 09', 'Dell latitude E7270', 'I5-7eme', 0, 'dell_latitude_e7270.jpg', NULL, 44000.00, 'expose', '2025-08-30 08:53:01'),
(29, 'Lap 10', 'Dell latitude 5310', 'I5-10eme', 2, 'dell_latitude_5310.jpg', NULL, 68500.00, 'expose', '2025-08-30 09:10:30'),
(30, 'Lap 11', 'Dell latitude 7380', 'I7-6eme', 1, 'dell_latitude_7380.jpg', NULL, 54000.00, 'expose', '2025-08-30 09:14:19'),
(31, 'lap 12', 'Dell latitude 5590', 'I7-8eme', 1, 'dell_latitude_5590.jpg', NULL, 77000.00, 'expose', '2025-08-30 09:15:21'),
(32, 'Lap 13', 'Hp elitebook 850', 'I5-7eme', 2, 'hp_elitebook_850.jpg', NULL, 59000.00, 'expose', '2025-08-30 09:20:29'),
(33, 'Lap 14', 'Thinkpad X13 yoga gen1', 'I5-10310u', 1, 'thinkpad_x13_yoga_gen1.jpg', NULL, 68400.00, 'expose', '2025-08-30 09:22:13'),
(34, 'Lap 15', 'Dell laitude 5310', 'I5-10eme', 1, 'dell_laitude_5310.jpg', NULL, 68500.00, 'expose', '2025-08-30 09:23:33'),
(35, 'Lap 16', 'Dell latitude 7300', 'I5-8eme', 1, 'dell_latitude_7300.jpg', NULL, 58000.00, 'expose', '2025-08-30 09:24:36'),
(36, 'Lap 17', 'Thinkpad X280', 'I5-8eme', 1, 'thinkpad_x280.jpg', NULL, 48000.00, 'expose', '2025-08-30 09:26:35'),
(37, 'Lap 18', 'Dell latitude E7240', 'I5-4eme', 2, 'dell_latitude_e7240.jpg', NULL, 39000.00, 'expose', '2025-08-30 09:27:28'),
(38, 'Lap 19', 'Hp probook', 'I3-5eme', 2, 'hp_probook.jpg', NULL, 34000.00, 'expose', '2025-08-30 09:28:18'),
(39, 'Lap 20', 'Lenovo G580', 'I7-3eme', 1, 'lenovo_g580.jpg', NULL, 37000.00, 'expose', '2025-08-30 09:29:18'),
(40, 'Lap 21', 'Hp elitebook', 'I7-6eme', 1, 'hp_elitebook.jpg', NULL, 51000.00, 'expose', '2025-08-30 09:31:57'),
(42, 'Lap 22', 'Hp elitebook', 'I7-6eme', 1, 'hp_elitebook.jpg', NULL, 54000.00, 'expose', '2025-08-30 09:33:12'),
(43, 'Lap 23', 'Hp probook 640 G2', 'I5-6eme', 1, 'hp_probook_640_g2.jpg', NULL, 48000.00, 'expose', '2025-08-30 10:02:18');

-- --------------------------------------------------------

--
-- Structure de la table `produits_sn`
--

CREATE TABLE `produits_sn` (
  `id` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `sn` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rank` varchar(50) NOT NULL,
  `telephone` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `rank`, `telephone`) VALUES
(1, 'sidali', '$2y$10$ixt1y.ZMZ2kGnj.Di2quMeFPAQK5ZG66Tr96mQXjXtiRqH7u0Edr2', 'admin', ''),
(2, 'ali', '$2y$10$wYgAn//1Zl2FOj5r7ROmIuMs9vSTy4lGB1lYWZ0LaZkwDE1d1FS4G', 'user', ''),
(5, 'Imad', '$2y$10$mYeVyE8d5.RL4QvLmDabreoU3e/Dkf/21ft7NW3IG7JkB7uykXhk.', 'manager', ''),
(6, 'ahmed', '123', 'user', '777'),
(7, 'salim', '123', 'user', '999'),
(8, 'Mahmoud', '$2y$10$VCat9E.zjHHQiW5XkL4UkukBRF/FS5XcBPJwFGRbwOWoDKrggWW3K', 'user', '7888');

-- --------------------------------------------------------

--
-- Structure de la table `ventes`
--

CREATE TABLE `ventes` (
  `id` int(11) NOT NULL,
  `num_vente` varchar(50) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  `versement` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ventes`
--

INSERT INTO `ventes` (`id`, `num_vente`, `date`, `id_user`, `versement`) VALUES
(1, 'BV-00001', '2025-08-30 00:00:00', 7, 44000.00),
(2, 'BV-00002', '2025-08-30 00:00:00', 8, 78000.00);

-- --------------------------------------------------------

--
-- Structure de la table `ventes_details`
--

CREATE TABLE `ventes_details` (
  `id` int(11) NOT NULL,
  `id_vente` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `prix_vente` decimal(10,2) NOT NULL,
  `quantite` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `ventes_details`
--

INSERT INTO `ventes_details` (`id`, `id_vente`, `id_produit`, `prix_vente`, `quantite`) VALUES
(16, 1, 28, 44000.00, 1),
(17, 2, 18, 78000.00, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_code_etat` (`code`,`etat`);

--
-- Index pour la table `produits_sn`
--
ALTER TABLE `produits_sn`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Index pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ventes_users` (`id_user`);

--
-- Index pour la table `ventes_details`
--
ALTER TABLE `ventes_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_vente` (`id_vente`),
  ADD KEY `id_produit` (`id_produit`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pour la table `produits_sn`
--
ALTER TABLE `produits_sn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `ventes`
--
ALTER TABLE `ventes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `ventes_details`
--
ALTER TABLE `ventes_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `produits_sn`
--
ALTER TABLE `produits_sn`
  ADD CONSTRAINT `produits_sn_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ventes`
--
ALTER TABLE `ventes`
  ADD CONSTRAINT `fk_ventes_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `ventes_details`
--
ALTER TABLE `ventes_details`
  ADD CONSTRAINT `ventes_details_ibfk_1` FOREIGN KEY (`id_vente`) REFERENCES `ventes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ventes_details_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
