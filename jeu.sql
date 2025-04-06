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
-- Base de données : `jeu`
--

-- --------------------------------------------------------

--
-- Structure de la table `batiment_modele`
--

DROP TABLE IF EXISTS `batiment_modele`;
CREATE TABLE IF NOT EXISTS `batiment_modele` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `niveau_max` int DEFAULT NULL,
  `cout_construction` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `batiment_modele`
--

INSERT INTO `batiment_modele` (`id`, `nom`, `niveau_max`, `cout_construction`) VALUES
(1, 'Mine de fer', 5, 'Fer:50,Energie:20'),
(2, 'Centrale solaire', 3, 'Fer:30,Eau:10');

-- --------------------------------------------------------

--
-- Structure de la table `planete`
--

DROP TABLE IF EXISTS `planete`;
CREATE TABLE IF NOT EXISTS `planete` (
  `id` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `systeme_id` int DEFAULT NULL,
  `niveau_colonisation` int DEFAULT NULL,
  `ressources` text,
  PRIMARY KEY (`id`),
  KEY `systeme_id` (`systeme_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `planete`
--

INSERT INTO `planete` (`id`, `nom`, `systeme_id`, `niveau_colonisation`, `ressources`) VALUES
(1, 'Gaïa', 1001, 4, 'Fer:100,Eau:50,Energie:30'),
(2, 'Nova', 1002, 3, 'Fer:80,Energie:40');

-- --------------------------------------------------------

--
-- Structure de la table `systeme`
--

DROP TABLE IF EXISTS `systeme`;
CREATE TABLE IF NOT EXISTS `systeme` (
  `id` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `systeme`
--

INSERT INTO `systeme` (`id`, `nom`) VALUES
(1001, 'Andromeda'),
(1002, 'Centauri');

-- --------------------------------------------------------

--
-- Structure de la table `technologie`
--

DROP TABLE IF EXISTS `technologie`;
CREATE TABLE IF NOT EXISTS `technologie` (
  `id` int NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `description` text,
  `cout_recherche` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `technologie`
--

INSERT INTO `technologie` (`id`, `nom`, `description`, `cout_recherche`) VALUES
(1, 'Fusion', 'Permet d\'alimenter les moteurs à fusion.', 200),
(2, 'Propulsion ionique', 'Augmente la vitesse des vaisseaux.', 300);

-- --------------------------------------------------------

--
-- Structure de la table `technologie_dependance`
--

DROP TABLE IF EXISTS `technologie_dependance`;
CREATE TABLE IF NOT EXISTS `technologie_dependance` (
  `id_technologie` int DEFAULT NULL,
  `id_dependance` int DEFAULT NULL,
  KEY `id_technologie` (`id_technologie`),
  KEY `id_dependance` (`id_dependance`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `technologie_dependance`
--

INSERT INTO `technologie_dependance` (`id_technologie`, `id_dependance`) VALUES
(2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `vaisseau_modele`
--

DROP TABLE IF EXISTS `vaisseau_modele`;
CREATE TABLE IF NOT EXISTS `vaisseau_modele` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `capacite_cargo` int DEFAULT NULL,
  `vitesse_base` int DEFAULT NULL,
  `consommation_base` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `vaisseau_modele`
--

INSERT INTO `vaisseau_modele` (`id`, `nom`, `type`, `capacite_cargo`, `vitesse_base`, `consommation_base`) VALUES
(1, 'Eclaireur', 'Petit', 50, 10, 5),
(2, 'Colonisateur', 'Moyen', 200, 6, 15);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
