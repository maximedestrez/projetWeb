-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 12 mai 2025 à 00:46
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
-- Base de données : `projetweb`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `categorie` enum('voiture','carte','vetement','livre') NOT NULL,
  `kilometrage` int(11) DEFAULT NULL,
  `etat` varchar(100) DEFAULT NULL,
  `taille` varchar(50) DEFAULT NULL,
  `auteur` varchar(100) DEFAULT NULL,
  `photos` varchar(255) DEFAULT NULL,
  `vendeur_id` int(11) NOT NULL,
  `date_ajout` timestamp NULL DEFAULT current_timestamp(),
  `vendu` tinyint(1) DEFAULT 0,
  `date_vente` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id`, `nom`, `description`, `prix`, `categorie`, `kilometrage`, `etat`, `taille`, `auteur`, `photos`, `vendeur_id`, `date_ajout`, `vendu`, `date_vente`) VALUES
(1, 'Peugeot 208', 'Voiture en très bon état, année 2018, révisée récemment.', 8900.00, 'voiture', 74500, NULL, NULL, NULL, NULL, 3, '2025-05-09 14:53:15', 1, '2025-05-12 00:28:03'),
(2, 'Carte Pokémon Dracaufeu', 'Carte Dracaufeu 1ère édition, très rare.', 250.00, 'carte', NULL, 'Comme neuve', NULL, NULL, 'uploads/dracaufeu.jpg', 3, '2025-05-09 14:53:15', 0, NULL),
(3, 'Blouson en cuir', 'Blouson noir en cuir véritable, porté une seule fois.', 120.00, 'vetement', NULL, 'Excellent état', 'L', NULL, 'uploads/blouson.jpg', 3, '2025-05-09 14:53:15', 1, '2025-05-12 00:30:03'),
(4, '1984 - George Orwell', 'Roman dystopique classique, édition collector.', 15.00, 'livre', NULL, 'Bon état', NULL, 'George Orwell', 'uploads/1984.jpg', 3, '2025-05-09 14:53:15', 0, NULL),
(5, 'asd', 'asd', 123.00, 'carte', NULL, 'qweqwe', NULL, NULL, NULL, 0, '2025-05-11 21:57:07', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `lue` tinyint(1) DEFAULT 0,
  `date_notification` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `lue`, `date_notification`) VALUES
(1, 2, 'Votre article a été marqué comme livré !', 1, '2025-05-09 19:14:02'),
(2, 3, 'max a confirmé la réception de l\'article', 1, '2025-05-09 19:17:39'),
(3, 3, 'Votre article  a été acheté par max', 0, '2025-05-11 00:26:03'),
(4, 3, 'Votre article  a été acheté par max', 0, '2025-05-11 00:26:24'),
(5, 3, 'Votre article  a été acheté par max', 0, '2025-05-11 00:30:08'),
(6, 3, 'Votre article  a été acheté par max', 0, '2025-05-11 00:35:14'),
(7, 3, 'Votre article  a été acheté par max', 0, '2025-05-11 00:49:58'),
(8, 3, 'Votre commande #3 a été expédiée !', 0, '2025-05-11 00:54:33'),
(9, 3, 'max a confirmé la réception de la commande #3', 0, '2025-05-11 01:01:53'),
(10, 3, 'Votre article  a été acheté par max', 0, '2025-05-11 22:23:24'),
(11, 3, 'Votre article a été acheté par max', 0, '2025-05-11 22:28:03'),
(12, 3, 'Votre article a été acheté par max', 0, '2025-05-11 22:30:03');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id` int(11) NOT NULL,
  `acheteur_id` int(10) UNSIGNED NOT NULL,
  `article_id` int(10) UNSIGNED NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `vendeur_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `acheteur_id` int(10) UNSIGNED NOT NULL,
  `vendeur_id` int(10) UNSIGNED NOT NULL,
  `article_id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `statut` enum('en_attente','payé','livré','confirmé') DEFAULT 'en_attente',
  `date_transaction` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transactions`
--

INSERT INTO `transactions` (`id`, `acheteur_id`, `vendeur_id`, `article_id`, `montant`, `statut`, `date_transaction`) VALUES
(1, 2, 3, 1, 8900.00, 'confirmé', '2025-05-09 18:53:16'),
(2, 3, 3, 1, 8900.00, 'payé', '2025-05-11 00:35:14'),
(3, 3, 3, 2, 250.00, 'confirmé', '2025-05-11 00:49:58'),
(4, 2, 3, 1, 8900.00, 'payé', '2025-05-11 22:23:24'),
(5, 2, 3, 1, 8900.00, 'payé', '2025-05-11 22:28:03'),
(8, 2, 3, 3, 120.00, 'payé', '2025-05-11 22:30:03');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(10) UNSIGNED NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `adresse` text DEFAULT NULL,
  `est_vendeur` tinyint(1) DEFAULT 0,
  `iban` varchar(34) DEFAULT NULL,
  `solde` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom`, `prenom`, `email`, `password`, `adresse`, `est_vendeur`, `iban`, `solde`) VALUES
(2, 'max', 'max', 'max', '$2y$10$h2tJ76753.CihKQs0quVeuq6UUDAVTBGuV81nWaRIrZ2wJeyNIY1q', 'max', 1, 'efgerqerggregrergeegrgrereg', 956530.00),
(3, 'des', 'max', 'maxime.destrez@gmail.com', '$2y$10$dHTVqWOSD3Zz/FEdKwhOhe2PBb9r19qwXKy5nisInxbu9.8XIn7Ja', '02949vgfzdvrzhgeqrvqeferb', 1, 'FR76 3000 6000 0112 3456 7890 189', 30053520.00),
(4, 'aze', 'aze', 'aze', '$2y$10$r1sYQe1H4cyfwfeYwoKSKOLthchVQKVoAW9K4M/QVBAD34WwMTA8q', 'aze', 0, NULL, 0.00);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendeur_id` (`vendeur_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `panier_ibfk_1` (`acheteur_id`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_acheteur` (`acheteur_id`),
  ADD KEY `fk_vendeur` (`vendeur_id`),
  ADD KEY `fk_article` (`article_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`acheteur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_acheteur` FOREIGN KEY (`acheteur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vendeur` FOREIGN KEY (`vendeur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
