-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 14 avr. 2025 à 15:56
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `utilisateur`
--

-- --------------------------------------------------------

--
-- Structure de la table `batiments`
--

DROP TABLE IF EXISTS `batiments`;
CREATE TABLE IF NOT EXISTS `batiments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text,
  `cout_construction` int NOT NULL,
  `temps_construction` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `batiments`
--

INSERT INTO `batiments` (`id`, `nom`, `description`, `cout_construction`, `temps_construction`) VALUES
(1, 'Laboratoire de recherche', 'Permet de mener des recherches avancées.', 5000, 24),
(2, 'Chantier spatial', 'Permet de construire des vaisseaux spatiaux.', 10000, 48),
(3, 'Mine', 'Permet d\'extraire des ressources.', 3000, 12),
(4, 'Centrale électrique', 'Fournit de l\'énergie pour vos installations.', 4000, 18);

-- --------------------------------------------------------

--
-- Structure de la table `laboratoire`
--

DROP TABLE IF EXISTS `laboratoire`;
CREATE TABLE IF NOT EXISTS `laboratoire` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `niveau` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `laboratoire`
--

INSERT INTO `laboratoire` (`id`, `utilisateur_id`, `niveau`) VALUES
(1, 1, 5),
(2, 2, 3);

-- --------------------------------------------------------

--
-- Structure de la table `planete`
--

DROP TABLE IF EXISTS `planete`;
CREATE TABLE IF NOT EXISTS `planete` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `systeme_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `planete`
--

INSERT INTO `planete` (`id`, `nom`, `utilisateur_id`, `systeme_id`) VALUES
(1, 'Panete4-1', 1, 3),
(2, 'Nova', 2, 1002);

-- --------------------------------------------------------

--
-- Structure de la table `ressource`
--

DROP TABLE IF EXISTS `ressource`;
CREATE TABLE IF NOT EXISTS `ressource` (
  `id` int NOT NULL AUTO_INCREMENT,
  `planete_id` int DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `quantite` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `planete_id` (`planete_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ressource`
--

INSERT INTO `ressource` (`id`, `planete_id`, `type`, `quantite`) VALUES
(1, 1, 'Fer', 120),
(2, 1, 'Eau', 80),
(3, 2, 'Fer', 90),
(4, 2, 'Energie', 60);

-- --------------------------------------------------------

--
-- Structure de la table `systeme_decouvert`
--

DROP TABLE IF EXISTS `systeme_decouvert`;
CREATE TABLE IF NOT EXISTS `systeme_decouvert` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `systeme_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `systeme_decouvert`
--

INSERT INTO `systeme_decouvert` (`id`, `utilisateur_id`, `systeme_id`) VALUES
(1, 1, 3),
(2, 2, 1002);

-- --------------------------------------------------------

--
-- Structure de la table `technologie_utilisateur`
--

DROP TABLE IF EXISTS `technologie_utilisateur`;
CREATE TABLE IF NOT EXISTS `technologie_utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `technologie_id` int DEFAULT NULL,
  `niveau` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `technologie_utilisateur`
--

INSERT INTO `technologie_utilisateur` (`id`, `utilisateur_id`, `technologie_id`, `niveau`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 0),
(3, 2, 1, 0),
(8, 1, 1, 1),
(5, 1, 2, 1),
(7, 1, 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `nom_utilisateur`, `email`, `mot_de_passe`) VALUES
(1, 'AlphaCommander', 'alpha@example.com', 'motdepasse1'),
(2, 'BetaExplorer', 'beta@example.com', 'motdepasse2'),
(3, 'admin', 'julien1203@gmail.com', 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur_batiments`
--

DROP TABLE IF EXISTS `utilisateur_batiments`;
CREATE TABLE IF NOT EXISTS `utilisateur_batiments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int NOT NULL,
  `batiment_id` int NOT NULL,
  `niveau` int NOT NULL DEFAULT '1',
  `temps_fin` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`),
  KEY `batiment_id` (`batiment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur_batiments`
--

INSERT INTO `utilisateur_batiments` (`id`, `utilisateur_id`, `batiment_id`, `niveau`, `temps_fin`) VALUES
(9, 1, 4, 2, NULL),
(8, 1, 3, 2, NULL),
(7, 1, 3, 2, NULL),
(6, 1, 1, 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vaisseau`
--

DROP TABLE IF EXISTS `vaisseau`;
CREATE TABLE IF NOT EXISTS `vaisseau` (
  `id` int NOT NULL AUTO_INCREMENT,
  `utilisateur_id` int DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `capacite_cargo` int DEFAULT NULL,
  `vitesse` int DEFAULT NULL,
  `consommation_energie` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `vaisseau`
--

INSERT INTO `vaisseau` (`id`, `utilisateur_id`, `nom`, `type`, `capacite_cargo`, `vitesse`, `consommation_energie`) VALUES
(1, 1, 'Eclaireur', 'Petit', 50, 10, 5),
(2, 2, 'Colonisateur', 'Moyen', 200, 6, 15);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
