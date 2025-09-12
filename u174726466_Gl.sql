-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 12 sep. 2025 à 23:02
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
-- Structure de la table `achats`
--

CREATE TABLE `achats` (
  `id` int(11) NOT NULL,
  `num_achat` varchar(20) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  `versement` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `achats`
--

INSERT INTO `achats` (`id`, `num_achat`, `date`, `id_user`, `versement`) VALUES
(12, 'BA-00001', '2025-09-07 00:00:00', 14, 15000.00);

-- --------------------------------------------------------

--
-- Structure de la table `achats_details`
--

CREATE TABLE `achats_details` (
  `id` int(11) NOT NULL,
  `id_achat` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `prix_achat` decimal(10,2) NOT NULL,
  `quantite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `achats_details`
--

INSERT INTO `achats_details` (`id`, `id_achat`, `id_produit`, `prix_achat`, `quantite`) VALUES
(5, 12, 68, 15000.00, 1);

-- --------------------------------------------------------

--
-- Structure de la table `balance`
--

CREATE TABLE `balance` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `inventaire` decimal(10,0) NOT NULL,
  `equipements` decimal(10,0) NOT NULL,
  `fonctionnement` decimal(10,0) NOT NULL,
  `caisse` decimal(10,0) NOT NULL,
  `credit_clients` decimal(10,0) NOT NULL,
  `credit_fournisseurs` decimal(10,0) NOT NULL,
  `capital` decimal(10,0) NOT NULL,
  `commentaire` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `balance`
--

INSERT INTO `balance` (`id`, `date`, `inventaire`, `equipements`, `fonctionnement`, `caisse`, `credit_clients`, `credit_fournisseurs`, `capital`, `commentaire`) VALUES
(1, '2025-09-01 04:07:58', 2253500, 0, 0, 544000, 0, 0, 2297500, ''),
(4, '2025-09-09 08:04:33', 2227700, 0, 0, 594000, 0, 0, 2821700, ''),
(7, '2025-09-12 10:48:22', 2227700, 1019180, 0, 254510, 0, 0, 3501390, 'Mise a jour solde apres payement panneau publicitaire de route aziz 13000DA et apres retrait sidali 30000DA'),
(8, '2025-09-12 10:54:34', 2227700, 1019180, 0, 604510, 0, 0, 3851390, 'Ajout 350000 au capital younes'),
(10, '2025-09-12 15:14:01', 2227700, 1019180, 250000, 604510, 0, 0, 4101390, '');

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
  `commentaire` text DEFAULT NULL,
  `etat` enum('commande','appel','interesse','vente') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `commandes`
--

INSERT INTO `commandes` (`id`, `telephone`, `id_produit`, `date`, `id_client`, `adresse`, `commentaire`, `etat`) VALUES
(7, '0661 85 16 80', 64, '2025-09-04 11:20:38', 1, NULL, 'Pour nadhir Belgrad même modèle avec 16 Go', 'commande'),
(8, '0542371965', 66, '2025-09-11 10:29:10', 9, 'Sliman', 'Sliman, besoin laptop max 20 millions pour 3D neuf carton generation puissante doubles carte graphique ecran slim 2k ou 4k', 'commande');

-- --------------------------------------------------------

--
-- Structure de la table `commandes_details`
--

CREATE TABLE `commandes_details` (
  `id` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `etat` varchar(50) NOT NULL,
  `comment` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `comptabilite`
--

CREATE TABLE `comptabilite` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `inventaire` decimal(15,2) DEFAULT 0.00,
  `caisse` decimal(15,2) DEFAULT 0.00,
  `credit_clients` decimal(15,2) DEFAULT 0.00,
  `credit_fournisseurs` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `equipements`
--

CREATE TABLE `equipements` (
  `id` int(11) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `code` varchar(50) NOT NULL,
  `prix_achat` decimal(10,0) NOT NULL,
  `quantite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `equipements`
--

INSERT INTO `equipements` (`id`, `designation`, `code`, `prix_achat`, `quantite`) VALUES
(1, 'Loyer', 'L01', 396000, 1),
(2, 'Portail local', 'P02', 95000, 1),
(3, 'Armoire laptop', 'A03', 23000, 6),
(4, 'Chaise comptoir marran', 'C04', 10000, 1),
(5, 'Modem 4g', 'M05', 3000, 1),
(6, 'Comptoir caisse showroom', 'C06', 15000, 1),
(7, 'Écran samsung', 'E07', 6000, 1),
(8, 'Citerne et suppresseur', 'C08', 35000, 1),
(9, 'Lampe led showroom', 'L09', 1440, 12),
(10, 'barreaudage fenêtre showroom', 'B10', 10000, 1),
(11, 'Modem 4g djaweb', 'M11', 3800, 1),
(12, 'Armoire metalique', 'A12', 12000, 3),
(13, 'Armoire melamine 90*1m90', 'A13', 8000, 1),
(14, 'Bureau 1m20', 'B14', 4000, 1),
(15, 'Chaise accoudoire', 'C15', 1, 6500),
(16, 'Imprimante hp m15', 'I16', 28000, 1),
(17, 'Panneau publicitaire 2,2m*2m', 'P17', 25000, 1),
(18, 'Comptoir showroom', 'C18', 20600, 1),
(19, 'Bureau blanc 1m40', 'B19', 13700, 1),
(20, 'Table de maintenance', 'T20', 4500, 3),
(21, 'Chaise visiteur noire', 'C21', 4700, 2),
(22, 'chaises marron de réparation', 'C22', 11700, 2),
(23, 'Bureau directeur', 'B23', 20000, 1),
(24, 'Chaise directeur', 'C24', 20000, 1),
(25, 'Photocopieur kyocera 1028', 'P25', 40000, 1),
(26, 'Table réparation 60*40', 'T26', 3000, 4),
(27, 'Écran lenovo réparation', 'E27', 6000, 1),
(28, 'Point d\'accès tenda', 'P28', 4000, 1);

-- --------------------------------------------------------

--
-- Structure de la table `fonctionnement`
--

CREATE TABLE `fonctionnement` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `designation` varchar(255) NOT NULL,
  `versement` decimal(10,2) DEFAULT 0.00,
  `depense` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `fonctionnement`
--

INSERT INTO `fonctionnement` (`id`, `date`, `designation`, `versement`, `depense`) VALUES
(1, '2025-09-12 14:49:10', 'Ajout capital fonctionnement', 250000.00, 0.00);

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
(17, 'Lap01', 'Dell latitude3420', 'I5-11eme\r\n8 ram\r\n512ssd\r\nBatterie +7h', 13, 'dell_latitude3420.jpeg', 65000.00, 78000.00, 'expose', '2025-08-28 13:50:56'),
(19, 'Lap02', 'Dell latitude E7240', 'I5-4eme\r\n8 ram\r\n64 ssd\r\n13.3 pousse , batterie 3h 76%', 1, 'dell_latitude_e7240.jpg', 27600.00, 34000.00, 'expose', '2025-08-30 08:33:59'),
(21, 'Lap 03', 'Hp pavilon g7', 'I3-3eme\r\nIntel core\r\n2:30h batterie', 1, 'hp_pavilon_g7.jpg', 19100.00, 27500.00, 'expose', '2025-08-30 08:36:32'),
(22, 'Lap 04', 'Hp 635', 'Amd e300\r\n4 ram \r\n500hhd\r\nIntegrée \r\n15,6 pousse\r\nLecteur cd , entrée microphone et ecouteur+ lecteur sd', 1, 'hp_635.jpg', 19100.00, 27500.00, 'expose', '2025-08-30 08:40:14'),
(23, 'Lap 05', 'Packard bell', 'Packard bell i3 2eme 15.6 500hdd 4h battery', 1, 'packard_bell.jpg', 19100.00, 27500.00, 'expose', '2025-08-30 08:43:37'),
(24, 'Lap06', 'Dell latitude E5540', 'I3-4eme\r\n8 ram\r\n500 hhd\r\nIntel intégrée\r\n15,6 pouces\r\nLecteur cd, hdmi, vga ,possibilité 2eme batterie \r\nGrand clavier lumineux , lecteur carte memoire , 4 port usb , batterie. +4h , lecteur impreinte', 1, 'dell_latitude_e5540.jpg', 28600.00, 44500.00, 'expose', '2025-08-30 08:46:06'),
(25, 'Lap 07', 'Hp probook', 'i3 5eme \r\n1tb hdd \r\n8gb ram \r\nbatterie 3h\r\nlecteur cd\r\nhdmi et vga\r\n4 port usb', 1, 'hp_probook.jpg', 28600.00, 35500.00, 'expose', '2025-08-30 08:48:11'),
(27, 'Lap 08', 'Dell latitude E7240', 'I5-4eme\r\n8 ram \r\n256 ssd', 2, 'dell_latitude_e7250.jpg', 27600.00, 39000.00, 'expose', '2025-08-30 08:51:42'),
(28, 'Lap 09', 'Dell latitude E7270', 'I5-7eme\r\n8 ram\r\n256 ssd', 1, 'dell_latitude_e7270.jpg', 37600.00, 44000.00, 'expose', '2025-08-30 08:53:01'),
(29, 'Lap 10', 'Dell latitude 5310', 'I5-10eme\r\n8gb \r\n256ssd \r\nbattery 4h \r\nlecteur sd et sim\r\nhdmi type c et standard', 2, 'dell_latitude_5310.jpg', 58600.00, 68500.00, 'expose', '2025-08-30 09:10:30'),
(30, 'Lap 11', 'Dell latitude 7380', 'I7-6eme\r\nRam 16 \r\nIntel integree\r\nBatterie 6h', 1, 'dell_latitude_7380.jpg', 38600.00, 54000.00, 'expose', '2025-08-30 09:14:19'),
(31, 'lap 12', 'Dell latitude 5590', 'I7-8eme\r\nIntel hd graphics 620\r\n8 ram\r\n256ssd', 1, 'dell_latitude_5590.jpg', 47600.00, 65000.00, 'expose', '2025-08-30 09:15:21'),
(32, 'Lap 13', 'Hp elitebook 850', 'I5-7eme\r\n8 ram\r\n512 ssd\r\nIntel intégrée\r\n15,6 pousse\r\nLecteur sim', 1, 'hp_elitebook_850.jpg', 47500.00, 59000.00, 'expose', '2025-08-30 09:20:29'),
(33, 'Lap 14', 'Thinkpad X13 yoga gen1', 'I5-10310u', 1, 'thinkpad_x13_yoga_gen1.jpg', 61500.00, 78000.00, 'test', '2025-08-30 09:22:13'),
(35, 'Lap 16', 'Dell latitude 7300', 'I5-8eme\r\n8 ram\r\n256ssd \r\nIntel intégrée\r\n14 pousse \r\nLecteur sd et carte memoire', 1, 'dell_latitude_7300.jpg', 46600.00, 58000.00, 'expose', '2025-08-30 09:24:36'),
(36, 'Lap 17', 'Thinkpad X280', 'I5-8eme\r\n8 ram\r\n256 nv\r\nIntel hd graphique 620\r\n13,3 pousse\r\nBatterie 7h 30min', 0, 'thinkpad_x280.jpg', 40500.00, 48000.00, 'expose', '2025-08-30 09:26:35'),
(38, 'Lap 19', 'Hp probook', 'I3-5eme\r\n8gb\r\n500GB HDD\r\nLecteur CD\r\nVGA et HDMI\r\n4 ports USB\r\nlecteur empreinte \r\nbetterie 3h', 2, 'hp_probook.jpg', 28600.00, 38000.00, 'expose', '2025-08-30 09:28:18'),
(39, 'Lap 20', 'Lenovo G580', 'I7-3eme\r\nIntel hd graphics 4000\r\n700g 8 ram', 1, 'lenovo_g580.jpg', 28600.00, 37000.00, 'expose', '2025-08-30 09:29:18'),
(42, 'Lap 22', 'Hp elitebook', 'I7-6eme\r\n16g ram\r\n256 ssd', 1, 'hp_elitebook.jpg', 38600.00, 54000.00, 'expose', '2025-08-30 09:33:12'),
(43, 'Lap 23', 'Hp probook 640 G2', 'I5-6eme', 1, 'hp_probook_640_g2.jpg', 37600.00, 48000.00, 'expose', '2025-08-30 10:02:18'),
(45, 'Lap 24', 'Thinkpad X260', 'I7-7eme\r\n8g ram\r\n256 gb\r\nIntel hd graphics 620\r\nBatterie 2h 30min', 1, 'thinkpad_x260.jpg', 37600.00, 52000.00, 'expose', '2025-08-30 11:13:42'),
(46, 'Lap 25', 'Lenovo E480', 'I5-8eme\r\n8g ram\r\nStock 256ssd\r\n5h batterie', 1, 'lenovo_e480.jpg', 47600.00, 58000.00, 'expose', '2025-08-30 11:15:35'),
(47, 'Lap 26', 'Hp elitebook 840 G5', 'I5-8eme\r\n8 ram\r\n256ssd\r\nIntel intégrée\r\n14 pousse\r\nLecteur sim  \r\nBatterie +5', 1, 'hp_elitebook_840_g5.jpg', 47600.00, 61000.00, 'expose', '2025-08-30 11:18:39'),
(49, 'Lap 28', 'Hp elitebook', 'I5-8eme\r\n8 ram\r\nIntel integrée\r\nEcran tactile \r\nLecteur sim', 1, 'hp_elitebook_1757504125.jpg', 46600.00, 68000.00, 'expose', '2025-08-30 11:29:33'),
(50, 'Lap 29', 'Hp probook', 'I5-4eme', 1, 'hp_probook.jpg', 28600.00, 36000.00, 'expose', '2025-08-30 11:30:33'),
(51, 'Lap 30', 'Thinkpad edge e335', 'AMD e2000', 0, 'thinkpad_edge_e335.jpg', 19200.00, 23500.00, 'expose', '2025-08-30 11:31:53'),
(52, 'Lap 31', 'Lenovo g50', 'I5-5eme\r\n8 ram\r\nIntel intégrée\r\nBatterie 5h', 1, 'lenovo_g50.jpg', 38600.00, 48000.00, 'expose', '2025-08-31 10:16:31'),
(53, 'Lap 32', 'Thinkpad', 'I7-6eme', 1, NULL, 38600.00, 48000.00, 'test', '2025-08-31 10:21:25'),
(54, 'Lap 33', 'Dell latitude 7490', 'I7-8eme\r\n16 ram\r\n256 ssd\r\n7h batterie', 1, 'dell_latitude_7490.jpg', 47600.00, 68000.00, 'expose', '2025-08-31 10:24:50'),
(55, 'Lap 34', 'Macbook pro 2013', 'i5 3eme\r\n13.3 pouces \r\n8gb ram \r\ncycle 1113\r\n500 HDD\r\nLecteur CD', 1, 'macbook_pro.jpg', 24000.00, 38000.00, 'expose', '2025-08-31 10:39:54'),
(56, 'Lap 35', 'Asus x515', 'I3-11eme\r\n8 ram\r\n256 ssd\r\n7h batterie', 1, 'asus_x515.jpg', 63600.00, 78500.00, 'expose', '2025-08-31 10:41:16'),
(58, 'Lap 36', 'Dell latitude7380', 'I7-6eme\r\n8 ram\r\n128ssd\r\nBatterie 5h', 1, 'dell_latitude7380.jpg', 38600.00, 48000.00, 'expose', '2025-08-31 10:44:11'),
(59, 'Lap 37', 'Hp probook', 'I3-4eme\r\n8 ram\r\n320hdd\r\nIntel intégrée\r\n15,6 pouces\r\nLecteur cd \r\nVga et hdmi\r\n4 portes usb\r\nEmpreinte \r\n3h batterie', 1, 'hp_probook.jpg', 28600.00, 36000.00, 'expose', '2025-08-31 10:50:57'),
(61, 'Lap 38', 'Dell latitude E7240', 'I5-4eme\r\n8 ram\r\n128ssd', 1, 'dell_latitude_e7240.jpg', 28100.00, 36000.00, 'expose', '2025-08-31 10:56:32'),
(63, 'Imp01', 'Imprimante kyocera fs-1040', 'Imprimante laser noire occasion, garantie 03 mois', 0, 'imprimante_kyocera_fs-1040.jpg', 17000.00, 25000.00, 'expose', '2025-09-01 09:02:27'),
(64, 'Lap14', 'Thinkpad X13 yoga gen1', 'i5-10eme', 0, 'thinkpad_x13.jpg', 61500.00, 78000.00, 'expose', '2025-09-01 09:39:12'),
(66, 'Lap40', 'Hp elitebook i7 6eme', 'Hp elitebook \r\ni7 6eme \r\n8gb ram\r\n256SSD\r\nclavier lumineux\r\nbatterie 100% batterie 6h\r\nPort vga\r\nlecteur SIM \r\ndouble chargeurs type c et standard\r\n2 port usb\"', 1, 'hp_elitebook_i7_6eme_1757265703.jpg', 38600.00, 51000.00, 'expose', '2025-09-01 22:52:59'),
(67, 'UC01', 'Lenovo mini UC', 'Mini\r\n-Sans chargeur-', 1, 'lenovo_mini_uc_1757267428.jpg', 17000.00, 25000.00, 'expose', '2025-09-03 12:11:37'),
(68, 'Lap 41', 'LEGEND', 'Cpu N351\r\nRam 2', 1, 'legend_1757345518.jpg', 15000.00, 25500.00, 'expose', '2025-09-04 09:54:27'),
(71, '41', 'Asus X571 GT', 'I5-9300h\r\nRam 8\r\n500ssd\r\n15 pouce\r\nCarte graphic:nvidea GTX 1650\r\nBatterie 3h', 0, 'asus_x571_gt.jpg', NULL, 11500.00, 'expose', '2025-09-10 11:15:04'),
(72, 'Lap 42', 'Dell 5410 tactile i7 10eme', 'i7 10eme\r\nTactile\r\n16gb ram\r\n256SSD', 1, NULL, NULL, 0.00, 'expose', '2025-09-12 21:00:39');

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

--
-- Déchargement des données de la table `produits_sn`
--

INSERT INTO `produits_sn` (`id`, `id_produit`, `sn`, `created_at`) VALUES
(5, 53, '00330-50340-64362-AAOEM', '2025-09-01 09:19:16'),
(6, 33, 'R910M5DB', '2025-09-01 09:20:38');

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
(6, 'ahmed', '123', 'user', '77444'),
(7, 'salim', '123', 'user', '999'),
(8, 'Mahmoud', '$2y$10$VCat9E.zjHHQiW5XkL4UkukBRF/FS5XcBPJwFGRbwOWoDKrggWW3K', 'user', '7888'),
(9, 'djamel', '$2y$10$.ih5Pj9fy3FRBcpDV/B8OeOfF1d.KjINQ6gvTtXrhk.68MDCvP7fa', 'admin', '0553930821'),
(11, 'Yazid', '123', 'provider', '90003'),
(12, 'Nadhir Belgrad', '$2y$10$ujmA5fKMi9CsIEpIGzt.oOy5t7pkIN6U08MK2xfEkvAGOml1aRfN2', 'user', '455'),
(13, 'Mounir', '$2y$10$RC8iF9XGDSvwxUQiRJibQ.M/shBdktRTSEmH6LTFafzxp52QO3P/S', 'user', ''),
(14, 'Nadhir Belgrad -Client-', '$2y$10$lSQmTAFhgpp8fHiP4P4NFuMKSb6yDSAJCmINuXe8KYY.5CcixLvOu', 'provider', '055555555'),
(15, 'younes', '$2y$10$g6xI9sRUZgGWPvCk2Y/ICej8K9yC8Aj/Jg/2nFrlNwcBvAj6WaS8.', 'admin', '0698766626'),
(16, 'Younes ouznadji', '$2y$10$G1NBCRj1Pnk/HltEBRajxe9zsaN7cA.dh8MBL1QqrU0TkZrUj2HcK', 'user', '');

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
(17, 'BV-00001', '2025-09-03 00:00:00', 12, 65000.00),
(18, 'BV-00018', '2025-09-12 00:00:00', 16, 0.00);

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
(27, 17, 64, 65000.00, 1),
(28, 18, 36, 52500.00, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `achats`
--
ALTER TABLE `achats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Index pour la table `achats_details`
--
ALTER TABLE `achats_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_achat` (`id_achat`),
  ADD KEY `id_produit` (`id_produit`);

--
-- Index pour la table `balance`
--
ALTER TABLE `balance`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `commandes_details`
--
ALTER TABLE `commandes_details`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `comptabilite`
--
ALTER TABLE `comptabilite`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `equipements`
--
ALTER TABLE `equipements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `fonctionnement`
--
ALTER TABLE `fonctionnement`
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
-- AUTO_INCREMENT pour la table `achats`
--
ALTER TABLE `achats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `achats_details`
--
ALTER TABLE `achats_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `balance`
--
ALTER TABLE `balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `commandes_details`
--
ALTER TABLE `commandes_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `comptabilite`
--
ALTER TABLE `comptabilite`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `equipements`
--
ALTER TABLE `equipements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `fonctionnement`
--
ALTER TABLE `fonctionnement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT pour la table `produits_sn`
--
ALTER TABLE `produits_sn`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `ventes`
--
ALTER TABLE `ventes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `ventes_details`
--
ALTER TABLE `ventes_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `achats`
--
ALTER TABLE `achats`
  ADD CONSTRAINT `achats_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `achats_details`
--
ALTER TABLE `achats_details`
  ADD CONSTRAINT `achats_details_ibfk_1` FOREIGN KEY (`id_achat`) REFERENCES `achats` (`id`),
  ADD CONSTRAINT `achats_details_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id`);

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
