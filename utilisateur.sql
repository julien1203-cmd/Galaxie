-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : dim. 06 avr. 2025 à 17:54
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
-- Structure de la table `batiment`
--

DROP TABLE IF EXISTS `batiment`;
CREATE TABLE IF NOT EXISTS `batiment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) DEFAULT NULL,
  `niveau` int DEFAULT NULL,
  `planete_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `planete_id` (`planete_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `batiment`
--

INSERT INTO `batiment` (`id`, `nom`, `niveau`, `planete_id`) VALUES
(1, 'Mine de fer', 2, 1),
(2, 'Centrale solaire', 1, 1),
(3, 'Mine d\'eau', 1, 2);

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
(1, 'Gaïa', 1, 1001),
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
(1, 1, 1001),
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
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `technologie_utilisateur`
--

INSERT INTO `technologie_utilisateur` (`id`, `utilisateur_id`, `technologie_id`, `niveau`) VALUES
(1, 1, 1, 1),
(2, 1, 2, 0),
(3, 2, 1, 0);

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
