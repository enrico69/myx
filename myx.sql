-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Sam 22 Octobre 2016 à 17:47
-- Version du serveur: 5.5.40-0ubuntu0.14.04.1
-- Version de PHP: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `myx`
--

-- --------------------------------------------------------

--
-- Structure de la table `myx_author`
--

CREATE TABLE IF NOT EXISTS `myx_author` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Slug` (`slug`),
  KEY `Name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_book`
--

CREATE TABLE IF NOT EXISTS `myx_book` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `year` int(11) NOT NULL,
  `editor` int(11) DEFAULT NULL,
  `format` int(11) DEFAULT NULL,
  `language` int(11) DEFAULT NULL,
  `addition_date` date NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `isbn` varchar(13) DEFAULT NULL,
  `keywords` varchar(100) DEFAULT NULL,
  `slug` varchar(100) NOT NULL,
  `last_user_id` int(11) DEFAULT NULL,
  `last_modified` date DEFAULT NULL,
  `material` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Slug` (`slug`),
  KEY `Title` (`title`),
  KEY `Language` (`language`),
  KEY `Format` (`format`),
  KEY `Editor` (`editor`),
  KEY `User_Id` (`user_id`),
  KEY `Material` (`material`),
  KEY `last_user_id` (`last_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_comment`
--

CREATE TABLE IF NOT EXISTS `myx_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Book_Id` (`book_id`),
  KEY `User_Id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_editor`
--

CREATE TABLE IF NOT EXISTS `myx_editor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Slug` (`slug`),
  KEY `Name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_format`
--

CREATE TABLE IF NOT EXISTS `myx_format` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_is_kind`
--

CREATE TABLE IF NOT EXISTS `myx_is_kind` (
  `kind_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  PRIMARY KEY (`kind_id`,`book_id`),
  KEY `IDX_2BC111604C9BAEBF` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `myx_kind`
--

CREATE TABLE IF NOT EXISTS `myx_kind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_language`
--

CREATE TABLE IF NOT EXISTS `myx_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_location`
--

CREATE TABLE IF NOT EXISTS `myx_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Slug` (`slug`),
  KEY `Name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_material`
--

CREATE TABLE IF NOT EXISTS `myx_material` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `myx_material`
--

INSERT INTO `myx_material` (`id`, `name`) VALUES
(1, 'Paper'),
(2, 'Numeric'),
(3, 'Audio');

-- --------------------------------------------------------

--
-- Structure de la table `myx_note`
--

CREATE TABLE IF NOT EXISTS `myx_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(50) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `FileName` (`filename`),
  KEY `Book_Id` (`book_id`),
  KEY `User_Id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `myx_publications`
--

CREATE TABLE IF NOT EXISTS `myx_publications` (
  `author_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  PRIMARY KEY (`author_id`,`book_id`),
  KEY `IDX_DB5F2C9C4C9BAEBF` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `myx_stored_in`
--

CREATE TABLE IF NOT EXISTS `myx_stored_in` (
  `location_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  PRIMARY KEY (`location_id`,`book_id`),
  KEY `IDX_6D43DE394C9BAEBF` (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `myx_user`
--

CREATE TABLE IF NOT EXISTS `myx_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `surname` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(180) NOT NULL,
  `username` varchar(180) NOT NULL,
  `username_canonical` varchar(180) NOT NULL,
  `email_canonical` varchar(180) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  `locale` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E26A5EBD92FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_E26A5EBDA0D96FBF` (`email_canonical`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `myx_book`
--
ALTER TABLE `myx_book`
  ADD CONSTRAINT `Books_Editor_FK` FOREIGN KEY (`Editor`) REFERENCES `myx_editor` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Books_Format_FK` FOREIGN KEY (`Format`) REFERENCES `myx_format` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Books_Language_FK` FOREIGN KEY (`Language`) REFERENCES `myx_language` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Books_Material_FK` FOREIGN KEY (`Material`) REFERENCES `myx_material` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Books_User_FK` FOREIGN KEY (`User_Id`) REFERENCES `myx_user` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Editor` FOREIGN KEY (`editor`) REFERENCES `myx_editor` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Format` FOREIGN KEY (`format`) REFERENCES `myx_format` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Language` FOREIGN KEY (`language`) REFERENCES `myx_language` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_LastUser` FOREIGN KEY (`last_user_id`) REFERENCES `myx_user` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Material` FOREIGN KEY (`material`) REFERENCES `myx_material` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_User` FOREIGN KEY (`user_id`) REFERENCES `myx_user` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `myx_comment`
--
ALTER TABLE `myx_comment`
  ADD CONSTRAINT `Comment_Book_FK` FOREIGN KEY (`Book_Id`) REFERENCES `myx_book` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Comment_User_FK` FOREIGN KEY (`User_Id`) REFERENCES `myx_user` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_CommentBook` FOREIGN KEY (`book_id`) REFERENCES `myx_book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_CommentUser` FOREIGN KEY (`user_id`) REFERENCES `myx_user` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `myx_is_kind`
--
ALTER TABLE `myx_is_kind`
  ADD CONSTRAINT `FK_Kind_Book` FOREIGN KEY (`book_id`) REFERENCES `myx_book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_kind_book_id` FOREIGN KEY (`Book_Id`) REFERENCES `myx_book` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_Kind_Id` FOREIGN KEY (`kind_id`) REFERENCES `myx_kind` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_kind_kind_id` FOREIGN KEY (`Kind_Id`) REFERENCES `myx_kind` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `myx_note`
--
ALTER TABLE `myx_note`
  ADD CONSTRAINT `FK_NoteBook` FOREIGN KEY (`book_id`) REFERENCES `myx_book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_NoteUser` FOREIGN KEY (`user_id`) REFERENCES `myx_user` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Note_Book_FK` FOREIGN KEY (`Book_Id`) REFERENCES `myx_book` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Note_User_FK` FOREIGN KEY (`User_Id`) REFERENCES `myx_user` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `myx_publications`
--
ALTER TABLE `myx_publications`
  ADD CONSTRAINT `FK_PubBook` FOREIGN KEY (`book_id`) REFERENCES `myx_book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_PubAuthor` FOREIGN KEY (`author_id`) REFERENCES `myx_author` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Publication_Author_FK` FOREIGN KEY (`Author_Id`) REFERENCES `myx_author` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Publication_Book_FK` FOREIGN KEY (`Book_Id`) REFERENCES `myx_book` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `myx_stored_in`
--
ALTER TABLE `myx_stored_in`
  ADD CONSTRAINT `FK_LocationBook` FOREIGN KEY (`book_id`) REFERENCES `myx_book` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `FK_LocationId` FOREIGN KEY (`location_id`) REFERENCES `myx_location` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Stored_In_Book_FK` FOREIGN KEY (`Book_Id`) REFERENCES `myx_book` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Stored_In_Location_FK` FOREIGN KEY (`Location_Id`) REFERENCES `myx_location` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
