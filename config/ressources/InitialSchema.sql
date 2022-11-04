-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 01 nov. 2022 à 19:48
-- Version du serveur : 10.4.24-MariaDB
-- Version de PHP : 7.4.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `anti-virus-api`
--
CREATE DATABASE IF NOT EXISTS `anti-virus-api` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `anti-virus-api`;

-- --------------------------------------------------------

--
-- Structure de la table `action`
--

CREATE TABLE `action` (
                          `id` int(11) NOT NULL,
                          `action_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `command_to_run` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `enabled` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `action`
--

INSERT INTO `action` (`id`, `action_name`, `command_to_run`, `enabled`) VALUES
    (1, 'Scan', 'clamscan', 1);

-- --------------------------------------------------------

--
-- Structure de la table `action_requested`
--

CREATE TABLE `action_requested` (
                                    `id` int(11) NOT NULL,
                                    `hosted_file_id` int(11) DEFAULT NULL,
                                    `action_parameters` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                    `date_of_demand` datetime NOT NULL,
                                    `start_time` datetime DEFAULT NULL,
                                    `end_time` datetime DEFAULT NULL,
                                    `accomplished` tinyint(1) NOT NULL,
                                    `action_results` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
                                    `action_id` int(11) DEFAULT NULL,
                                    `hosted_file_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
                                    `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `banned_email`
--

CREATE TABLE `banned_email` (
                                `id` int(11) NOT NULL,
                                `ip` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
                                `last_try` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
                                               `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
                                               `executed_at` datetime DEFAULT NULL,
                                               `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
                                                                                           ('DoctrineMigrations\\Version20220725133251', '2022-11-01 19:46:18', 270),
                                                                                           ('DoctrineMigrations\\Version20220727131553', '2022-11-01 19:46:19', 83),
                                                                                           ('DoctrineMigrations\\Version20220814093450', '2022-11-01 19:46:19', 15),
                                                                                           ('DoctrineMigrations\\Version20220814210004', '2022-11-01 19:46:19', 82),
                                                                                           ('DoctrineMigrations\\Version20220815094709', '2022-11-01 19:46:19', 84),
                                                                                           ('DoctrineMigrations\\Version20220903175409', '2022-11-01 19:46:19', 69),
                                                                                           ('DoctrineMigrations\\Version20221009140948', '2022-11-01 19:46:19', 12),
                                                                                           ('DoctrineMigrations\\Version20221016142404', '2022-11-01 19:46:19', 11),
                                                                                           ('DoctrineMigrations\\Version20221023194938', '2022-11-01 19:46:19', 68),
                                                                                           ('DoctrineMigrations\\Version20221027141216', '2022-11-01 19:46:19', 112),
                                                                                           ('DoctrineMigrations\\Version20221027151430', '2022-11-01 19:46:19', 81),
                                                                                           ('DoctrineMigrations\\Version20221027172514', '2022-11-01 19:46:19', 57),
                                                                                           ('DoctrineMigrations\\Version20221027203053', '2022-11-01 19:46:19', 180),
                                                                                           ('DoctrineMigrations\\Version20221031195702', '2022-11-01 19:46:20', 214),
                                                                                           ('DoctrineMigrations\\Version20221031200957', '2022-11-01 19:46:20', 45),
                                                                                           ('DoctrineMigrations\\Version20221101153434', '2022-11-01 19:46:20', 144),
                                                                                           ('DoctrineMigrations\\Version20221101184217', '2022-11-01 19:42:22', 83);

-- --------------------------------------------------------

--
-- Structure de la table `flood`
--

CREATE TABLE `flood` (
                         `id` int(11) NOT NULL,
                         `ip` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `last_try` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `hosted_file`
--

CREATE TABLE `hosted_file` (
                               `id` int(11) NOT NULL,
                               `user_id` int(11) DEFAULT NULL,
                               `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `client_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `upload_date` datetime NOT NULL,
                               `expiration_date` datetime DEFAULT NULL,
                               `virtual_directory` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `size` double NOT NULL,
                               `scaned` tinyint(1) NOT NULL,
                               `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `download_counter` bigint(20) NOT NULL,
                               `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `upload_localisation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `copyright_issue` tinyint(1) NOT NULL,
                               `conversions_available` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `file_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `authorized_users` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                               `infected` tinyint(1) NOT NULL DEFAULT 0,
                               `scan_result` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `hosted_file`
--

INSERT INTO `hosted_file` (`id`, `user_id`, `name`, `client_name`, `upload_date`, `expiration_date`, `virtual_directory`, `size`, `scaned`, `description`, `download_counter`, `url`, `upload_localisation`, `copyright_issue`, `conversions_available`, `file_password`, `authorized_users`, `infected`, `scan_result`) VALUES
                                                                                                                                                                                                                                                                                                                             (1, 1, '5fg4h61h6dfh65f6fgh6fh6fgh46fg6d5f.jpg', 'test-cli-name1.jpg', '2022-11-01 19:46:46', '2024-07-05 06:00:00', 'test-dir1', 99191951951, 0, 'ééé ààà desc', 0, 'file-1', '127.0.0.1', 0, 'jpg,png', NULL, NULL, 0, NULL),
                                                                                                                                                                                                                                                                                                                             (2, 1, '54hd6f5h6dfg5h4d6fgh6fdg5h65fz6rd6f5gh.png', 'test1.jpg', '2022-11-01 19:46:46', '2024-07-05 06:00:00', 'test-dir1', 6516165161, 0, 'ééé bbb desc', 0, 'file-2', '127.0.0.1', 0, 'jpg,png', NULL, NULL, 0, NULL),
                                                                                                                                                                                                                                                                                                                             (3, 2, 'df5g4h6df5g4h6f5g4h6fdgh6f5g1h6fg51h.mp4', 'test-cli-name2.jpg', '2022-11-01 19:46:46', '2024-07-05 06:00:00', 'test-dir2', 99191951951, 0, 'ééé ààà desc', 0, 'file-3', '127.0.0.1', 0, 'jpg,png', NULL, NULL, 0, NULL),
                                                                                                                                                                                                                                                                                                                             (4, 2, 'f65g4hdfg1hfgh6f5g1h5fgh9fhgff1ghf65.pdf', 'test2.jpg', '2022-11-01 19:46:46', '2024-07-05 06:00:00', 'test-dir2', 6516165161, 0, 'ééé bbb desc', 0, 'file-4', '127.0.0.1', 0, 'jpg,png', NULL, NULL, 0, NULL);



-- --------------------------------------------------------

--
-- Structure de la table `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
                                  `id` int(11) NOT NULL,
                                  `refresh_token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
                                  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                  `valid` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
                        `id` int(11) NOT NULL,
                        `login` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
                        `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                        `total_space_used_mo` double DEFAULT NULL,
                        `authorized_size_mo` double DEFAULT 100,
                        `registration_date` datetime DEFAULT NULL,
                        `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `zip_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `preferred_language` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `type_of_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `avatar_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                        `date_of_birth` datetime DEFAULT NULL,
                        `is_banned` tinyint(1) DEFAULT NULL,
                        `email_confirmed` tinyint(1) DEFAULT 0,
                        `last_connexion_date` datetime DEFAULT NULL,
                        `secret_token_for_validation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `login`, `roles`, `password`, `total_space_used_mo`, `authorized_size_mo`, `registration_date`, `email`, `phone_number`, `city`, `country`, `zip_code`, `preferred_language`, `type_of_account`, `description`, `avatar_picture`, `date_of_birth`, `is_banned`, `email_confirmed`, `last_connexion_date`, `secret_token_for_validation`) VALUES
                                                                                                                                                                                                                                                                                                                                                                       (1, 'admin', '[\"ROLE_ADMIN\"]', '$2y$13$Svpv6gb1ztlU/oulucYQUe3Q.e7BaAVQfared3UPHrLtTlKc1BbP.', 0, 100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL),
                                                                                                                                                                                                                                                                                                                                                                       (2, 'user', '[\"ROLE_USER\"]', '$2y$13$FTqqL1QLNyJ1h6MUx2b/O.TBQDCDUt9oVf7V7E5mWW/QfBpCgrJ66', 0, 100, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `action`
--
ALTER TABLE `action`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `action_requested`
--
ALTER TABLE `action_requested`
    ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3C5408B2EE73CD22` (`hosted_file_id`),
  ADD KEY `IDX_3C5408B29D32F035` (`action_id`),
  ADD KEY `IDX_3C5408B2A76ED395` (`user_id`);

--
-- Index pour la table `banned_email`
--
ALTER TABLE `banned_email`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
    ADD PRIMARY KEY (`version`);

--
-- Index pour la table `flood`
--
ALTER TABLE `flood`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `hosted_file`
--
ALTER TABLE `hosted_file`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1D3B660EF47645AE` (`url`),
  ADD KEY `IDX_1D3B660EA76ED395` (`user_id`);


--
-- Index pour la table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9BACE7E1C74F2195` (`refresh_token`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
    ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649AA08CB10` (`login`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `action`
--
ALTER TABLE `action`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `action_requested`
--
ALTER TABLE `action_requested`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `banned_email`
--
ALTER TABLE `banned_email`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `flood`
--
ALTER TABLE `flood`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `hosted_file`
--
ALTER TABLE `hosted_file`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `action_requested`
--
ALTER TABLE `action_requested`
    ADD CONSTRAINT `FK_3C5408B29D32F035` FOREIGN KEY (`action_id`) REFERENCES `action` (`id`),
  ADD CONSTRAINT `FK_3C5408B2A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_3C5408B2EE73CD22` FOREIGN KEY (`hosted_file_id`) REFERENCES `hosted_file` (`id`);

--
-- Contraintes pour la table `hosted_file`
--
ALTER TABLE `hosted_file`
    ADD CONSTRAINT `FK_1D3B660EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
