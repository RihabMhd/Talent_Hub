SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0; -- Disable foreign key checks temporarily

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `talent_hub`
--
DROP DATABASE IF EXISTS `talent_hub`;
CREATE DATABASE IF NOT EXISTS `talent_hub`;
USE `talent_hub`;

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

DROP TABLE IF EXISTS `candidates`;
CREATE TABLE `candidates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `skills` text,
  `experience_annee` int DEFAULT '0',
  `expected_salaire` decimal(10,2) DEFAULT NULL,
  `cv_path` varchar(255) DEFAULT NULL,
  `role` enum('candidat') DEFAULT 'candidat',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_candidates_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `nom`, `email`, `password`, `telephone`, `skills`, `experience_annee`, `expected_salaire`, `cv_path`, `role`, `is_active`, `created_at`, `updated_at`, `user_id`) VALUES
(7, 'Amine Bennani', 'amine.bennani@email.com', '$2y$10$hashedpassword2', '0600000002', 'Java, Spring Boot, PostgreSQL', 3, '9000.00', 'cv_amine.pdf', 'candidat', 1, '2026-01-21 12:33:13', '2026-01-21 12:33:13', 9),
(8, 'Fatima Idrissi', 'fatima.idrissi@email.com', '$2y$10$hashedpassword3', '0600000003', 'JavaScript, React, HTML, CSS', 1, '6000.00', 'cv_fatima.pdf', 'candidat', 1, '2026-01-21 12:33:13', '2026-01-21 12:33:13', 10);

-- --------------------------------------------------------

--
-- Table structure for table `candidatures`
--

DROP TABLE IF EXISTS `candidatures`;
CREATE TABLE `candidatures` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `offre_id` int NOT NULL,
  `message_motivation` text,
  `cv_path` varchar(255) NOT NULL,
  `status` enum('en_attente','acceptee','refusee') DEFAULT 'en_attente',
  `date_postulation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `offre_id` (`offre_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidatures`
--

INSERT INTO `candidatures` (`id`, `user_id`, `offre_id`, `message_motivation`, `cv_path`, `status`, `date_postulation`) VALUES
(1, 1, 1, 'Je suis très intéressé par ce poste de développeur full stack. Avec mes 6 ans d\'expérience en React et Node.js, je pense pouvoir apporter une vraie valeur ajoutée à votre équipe.', '/uploads/cv/hamid_cv.pdf', 'en_attente', '2026-01-16 10:30:00'),
(2, 9, 1, 'Passionné par le développement web, j\'ai travaillé sur plusieurs projets similaires. Mon expertise en architecture microservices serait un atout pour vos projets.', '/uploads/cv/amine_bennani_cv.pdf', 'acceptee', '2026-01-16 14:00:00'),
(3, 10, 4, 'Designer UI/UX avec 4 ans d\'expérience, je suis spécialisée dans la création d\'interfaces utilisateur modernes et accessibles. J\'aimerais contribuer à vos projets innovants.', '/uploads/cv/fatima_idrissi_cv.pdf', 'en_attente', '2026-01-18 09:00:00'),
(4, 11, 3, 'Data scientist avec une forte expertise en machine learning et analyse prédictive. J\'ai participé à plusieurs projets de data science dans le secteur bancaire.', '/uploads/cv/mehdi_elamrani_cv.pdf', 'acceptee', '2026-01-15 11:00:00'),
(5, 12, 2, 'Développeuse mobile Flutter depuis 3 ans, j\'ai créé plusieurs applications cross-platform avec plus de 50k téléchargements. Très motivée pour rejoindre votre équipe.', '/uploads/cv/nadia_chakir_cv.pdf', 'en_attente', '2026-01-17 10:00:00'),
(6, 13, 5, 'Développeur Laravel expérimenté, j\'ai travaillé sur des plateformes e-commerce de grande envergure. Maîtrise des bonnes pratiques et des design patterns.', '/uploads/cv/youssef_berrada_cv.pdf', 'refusee', '2026-01-14 15:00:00'),
(7, 14, 6, 'Ingénieure DevOps passionnée par l\'automatisation et le cloud. Certifiée AWS Solutions Architect, j\'ai géré des infrastructures complexes en production.', '/uploads/cv/sofia_lamrani_cv.pdf', 'acceptee', '2026-01-19 09:00:00'),
(8, 15, 8, 'Développeur React Native avec une solide expérience en développement mobile. J\'ai publié plusieurs applications sur les stores iOS et Android.', '/uploads/cv/rachid_hassani_cv.pdf', 'en_attente', '2026-01-20 11:00:00'),
(9, 16, 10, 'Spécialiste marketing digital avec expertise en SEO, SEM et analytics. J\'ai augmenté le trafic organique de 150% dans mon précédent poste.', '/uploads/cv/houda_ziani_cv.pdf', 'en_attente', '2026-01-21 10:00:00'),
(10, 1, 11, 'Bien que je sois expérimenté, je suis très intéressé par ce poste junior pour travailler avec Python et contribuer à vos projets d\'analyse de données.', '/uploads/cv/hamid_cv_2.pdf', 'refusee', '2026-01-11 14:30:00'),
(11, 9, 7, 'Chef de projet certifié Scrum Master avec 5 ans d\'expérience en gestion de projets digitaux. Habitué à travailler en méthodologie Agile.', '/uploads/cv/amine_bennani_cv_2.pdf', 'en_attente', '2026-01-13 16:00:00'),
(12, 10, 2, 'Intéressée par le développement mobile, je souhaite élargir mes compétences en Flutter après 2 ans d\'expérience en développement iOS natif.', '/uploads/cv/fatima_idrissi_cv_2.pdf', 'refusee', '2026-01-17 14:00:00'),
(13, 11, 13, 'Étudiant en dernière année d\'ingénierie informatique, je cherche un stage pour mettre en pratique mes connaissances en développement web moderne.', '/uploads/cv/mehdi_elamrani_cv_stage.pdf', 'acceptee', '2026-01-09 10:00:00'),
(14, 12, 14, 'Analyste BI avec expertise en création de tableaux de bord Power BI et requêtes SQL complexes. Passionnée par la visualisation de données.', '/uploads/cv/nadia_chakir_cv_2.pdf', 'en_attente', '2026-01-08 11:00:00'),
(15, 13, 6, 'Expérience de 3 ans en administration système et automatisation. Je souhaite évoluer vers un rôle DevOps pour gérer des infrastructures cloud.', '/uploads/cv/youssef_berrada_cv_2.pdf', 'en_attente', '2026-01-19 15:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(9, 'Cybersécurité'),
(3, 'Data Science'),
(4, 'Design UI/UX'),
(2, 'Développement Mobile'),
(1, 'Développement Web'),
(8, 'DevOps'),
(7, 'Finance'),
(10, 'Gestion de Projet'),
(5, 'Marketing Digital'),
(6, 'Ressources Humaines');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE `companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_entreprise` varchar(150) NOT NULL,
  `adresse_entreprise` varchar(255) DEFAULT NULL,
  `site_web` varchar(150) DEFAULT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `nom_entreprise`, `adresse_entreprise`, `site_web`, `user_id`) VALUES
(1, 'TechCorp Morocco', 'Casablanca TechnoParc, Casablanca', 'https://www.techcorp.ma', 2),
(2, 'InnovateLab', 'Twin Center, Boulevard Zerktouni, Casablanca', 'https://www.innovatelab.ma', 5),
(3, 'DigitalWave Solutions', 'Rabat Technopolis, Rabat', 'https://www.digitalwave.ma', 6),
(4, 'DataTech Analytics', 'Marina Casablanca, Casablanca', 'https://www.datatech.ma', 7),
(5, 'CloudFirst Systems', 'Marrakech Tech Park, Marrakech', 'https://www.cloudfirst.ma', 8);

-- --------------------------------------------------------

--
-- Table structure for table `offres`
--

DROP TABLE IF EXISTS `offres`;
CREATE TABLE `offres` (
  `id` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `salaire` float DEFAULT NULL,
  `lieu` varchar(150) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'active',
  `category_id` int NOT NULL,
  `company_id` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offres`
--

INSERT INTO `offres` (`id`, `titre`, `description`, `salaire`, `lieu`, `status`, `category_id`, `company_id`, `created_at`, `deleted_at`) VALUES
(1, 'Développeur Full Stack Senior', 'Nous recherchons un développeur full stack expérimenté pour rejoindre notre équipe. Vous travaillerez sur des projets innovants utilisant React, Node.js et PostgreSQL. Expérience minimale de 5 ans requise.', 18000, 'Casablanca', 'active', 1, 1, '2026-01-15 10:00:00', NULL),
(2, 'Développeur Mobile Flutter', 'Rejoignez notre équipe mobile pour créer des applications cross-platform avec Flutter. Vous participerez au développement de notre application mobile qui compte déjà plus de 100k utilisateurs.', 14000, 'Casablanca', 'active', 2, 2, '2026-01-16 09:00:00', NULL),
(3, 'Data Scientist', 'Nous cherchons un data scientist pour analyser nos données et créer des modèles prédictifs. Maîtrise de Python, TensorFlow et des statistiques requise.', 20000, 'Rabat', 'active', 3, 3, '2026-01-14 11:00:00', NULL),
(4, 'Designer UI/UX', 'Créez des expériences utilisateur exceptionnelles. Vous travaillerez en étroite collaboration avec nos équipes de développement pour concevoir des interfaces modernes et intuitives.', 12000, 'Casablanca', 'active', 4, 1, '2026-01-17 14:00:00', NULL),
(5, 'Développeur Backend Laravel', 'Développeur PHP/Laravel expérimenté recherché pour maintenir et améliorer notre plateforme e-commerce. Connaissance des architectures microservices souhaitée.', 15000, 'Casablanca', 'active', 1, 2, '2026-01-13 10:30:00', NULL),
(6, 'Ingénieur DevOps', 'Rejoignez notre équipe infrastructure. Vous serez responsable de l\'automatisation, du déploiement continu et de la gestion de notre infrastructure cloud (AWS/Azure).', 22000, 'Rabat', 'active', 8, 3, '2026-01-18 09:00:00', NULL),
(7, 'Chef de Projet Digital', 'Nous recherchons un chef de projet expérimenté en méthodologie Agile/Scrum pour piloter nos projets de transformation digitale.', 17000, 'Casablanca', 'active', 10, 4, '2026-01-12 15:00:00', NULL),
(8, 'Développeur React Native', 'Développeur mobile React Native pour créer et maintenir nos applications iOS et Android. Expérience avec Redux et les API REST requise.', 16000, 'Marrakech', 'active', 2, 5, '2026-01-19 10:00:00', NULL),
(9, 'Expert en Cybersécurité', 'Protégez nos systèmes et données. Vous effectuerez des audits de sécurité, des tests de pénétration et développerez nos stratégies de sécurité.', 25000, 'Rabat', 'active', 9, 3, '2026-01-11 11:00:00', NULL),
(10, 'Spécialiste Marketing Digital', 'Gérez nos campagnes digitales, optimisez notre SEO et analysez les performances. Expérience avec Google Analytics et les réseaux sociaux requise.', 11000, 'Casablanca', 'active', 5, 1, '2026-01-20 09:30:00', NULL),
(11, 'Développeur Python Junior', 'Poste idéal pour débutant motivé. Vous travaillerez sur des projets d\'automatisation et d\'analyse de données sous la supervision de développeurs seniors.', 8000, 'Casablanca', 'active', 1, 2, '2026-01-10 14:00:00', NULL),
(12, 'Architecte Cloud AWS', 'Concevez et implémentez des solutions cloud évolutives et sécurisées sur AWS. Certification AWS Solutions Architect souhaitée.', 28000, 'Casablanca', 'active', 8, 4, '2026-01-09 10:00:00', NULL),
(13, 'Développeur Full Stack (Stage)', 'Stage de 6 mois pour étudiant en informatique. Travail sur des projets réels avec technologies modernes (React, Node.js, MongoDB).', 3500, 'Rabat', 'active', 1, 3, '2026-01-08 11:00:00', NULL),
(14, 'Analyste Business Intelligence', 'Créez des tableaux de bord et des rapports pour aider à la prise de décision. Maîtrise de SQL, Power BI ou Tableau requise.', 13000, 'Casablanca', 'active', 3, 4, '2026-01-07 09:00:00', NULL),
(15, 'Développeur iOS Swift', 'Développeur natif iOS pour créer des applications performantes. Expérience avec SwiftUI et Combine appréciée.', 17000, 'Marrakech', 'inactive', 2, 5, '2026-01-06 10:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `offre_tag`
--

DROP TABLE IF EXISTS `offre_tag`;
CREATE TABLE `offre_tag` (
  `offre_id` int NOT NULL,
  `tag_id` int NOT NULL,
  PRIMARY KEY (`offre_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offre_tag`
--

INSERT INTO `offre_tag` (`offre_id`, `tag_id`) VALUES
(1, 1),
(8, 1),
(13, 1),
(1, 2),
(8, 2),
(13, 2),
(5, 3),
(5, 4),
(3, 5),
(9, 5),
(11, 5),
(11, 6),
(1, 7),
(13, 7),
(5, 8),
(14, 8),
(1, 9),
(11, 9),
(14, 9),
(13, 10),
(1, 11),
(2, 11),
(5, 11),
(6, 11),
(9, 11),
(12, 11),
(6, 12),
(12, 12),
(6, 13),
(9, 13),
(12, 13),
(6, 14),
(8, 15),
(2, 16),
(15, 17),
(3, 19),
(3, 20),
(3, 21),
(4, 22),
(4, 23),
(10, 24),
(10, 25),
(10, 26),
(10, 27),
(7, 28),
(2, 29),
(7, 29),
(7, 30);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admin'),
(3, 'candidat'),
(2, 'recruteur');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `nom`) VALUES
(23, 'Adobe XD'),
(29, 'Agile'),
(13, 'AWS'),
(14, 'Azure'),
(27, 'Content Marketing'),
(6, 'Django'),
(11, 'Docker'),
(22, 'Figma'),
(16, 'Flutter'),
(25, 'Google Analytics'),
(1, 'JavaScript'),
(30, 'Jira'),
(33, 'JS8.2'),
(18, 'Kotlin'),
(12, 'Kubernetes'),
(4, 'Laravel'),
(19, 'Machine Learning'),
(10, 'MongoDB'),
(8, 'MySQL'),
(7, 'Node.js'),
(3, 'PHP'),
(31, 'PHP8'),
(9, 'PostgreSQL'),
(5, 'Python'),
(21, 'PyTorch'),
(2, 'React'),
(15, 'React Native'),
(28, 'Scrum'),
(24, 'SEO'),
(26, 'Social Media'),
(17, 'Swift'),
(20, 'TensorFlow');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `email_verified_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `password`, `role_id`, `is_active`, `email_verified_at`, `created_at`, `is_verified`, `verified_at`, `updated_at`) VALUES
(1, 'hamid', 'hamid', 'c@clinic.com', '$2y$10$r4UqxU1KVABege8k09YGHO3m6jEE7OW2qzBscfgz16P.c.bGLnoA.', 3, 1, NULL, '2026-01-19 22:36:48', 1, '2026-01-21 12:04:53', '2026-01-21 12:04:53'),
(2, 'yassin', 'yassin', 'R@clinic.com', '$2y$10$y6eQHC0CnmOk2T/d2Q/EmOWsbQwSQJhea5KYVwZpotQv1/jbnklEC', 2, 1, NULL, '2026-01-19 22:41:34', 1, '2026-01-21 12:05:42', '2026-01-21 12:05:42'),
(4, 'Admin', 'Super', 'admin@talenthub.com', '$2y$10$y6eQHC0CnmOk2T/d2Q/EmOWsbQwSQJhea5KYVwZpotQv1/jbnklEC', 1, 1, NULL, '2026-01-19 22:46:05', 1, '2026-01-21 11:23:17', '2026-01-21 11:23:17'),
(5, 'Bennis', 'Sara', 'sara.bennis@techcorp.ma', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 2, 1, '2026-01-15 10:00:00', '2026-01-15 09:30:00', 0, NULL, NULL),
(6, 'Alami', 'Karim', 'karim.alami@innovate.ma', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 2, 1, '2026-01-14 11:00:00', '2026-01-14 10:45:00', 0, NULL, NULL),
(7, 'Tazi', 'Leila', 'leila.tazi@digitalwave.ma', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 2, 1, '2026-01-13 14:00:00', '2026-01-13 13:30:00', 0, NULL, NULL),
(8, 'Chraibi', 'Omar', 'omar.chraibi@datatech.ma', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 2, 1, '2026-01-12 09:00:00', '2026-01-12 08:30:00', 0, NULL, NULL),
(9, 'Bennani', 'Amine', 'amine.bennani@email.com', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 3, 1, '2026-01-10 10:00:00', '2026-01-10 09:00:00', 0, NULL, NULL),
(10, 'Idrissi', 'Fatima', 'fatima.idrissi@email.com', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 3, 1, '2026-01-11 11:00:00', '2026-01-11 10:30:00', 0, NULL, NULL),
(11, 'El Amrani', 'Mehdi', 'mehdi.elamrani@email.com', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 3, 1, '2026-01-12 12:00:00', '2026-01-12 11:30:00', 0, NULL, NULL),
(12, 'Chakir', 'Nadia', 'nadia.chakir@email.com', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 3, 1, '2026-01-13 13:00:00', '2026-01-13 12:30:00', 0, NULL, NULL),
(13, 'Berrada', 'Youssef', 'youssef.berrada@email.com', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 3, 1, '2026-01-14 14:00:00', '2026-01-14 13:30:00', 0, NULL, NULL),
(14, 'Lamrani', 'Sofia', 'sofia.lamrani@email.com', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 3, 1, '2026-01-15 15:00:00', '2026-01-15 14:30:00', 0, NULL, NULL),
(15, 'Hassani', 'Rachid', 'rachid.hassani@email.com', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 3, 1, '2026-01-16 16:00:00', '2026-01-16 15:30:00', 0, NULL, NULL),
(16, 'Ziani', 'Houda', 'houda.ziani@email.com', '$2y$10$zYIYJUWZrTFblit4CwQWiuEyeG3KW2lxz36nUEarQHj.niRkmDWAa', 3, 1, '2026-01-17 17:00:00', '2026-01-17 16:30:00', 0, NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `fk_candidates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `candidatures`
--
ALTER TABLE `candidatures`
  ADD CONSTRAINT `candidatures_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `candidatures_ibfk_2` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`);

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `offres`
--
ALTER TABLE `offres`
  ADD CONSTRAINT `offres_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `offres_ibfk_2` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `offre_tag`
--
ALTER TABLE `offre_tag`
  ADD CONSTRAINT `offre_tag_ibfk_1` FOREIGN KEY (`offre_id`) REFERENCES `offres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offre_tag_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

SET FOREIGN_KEY_CHECKS = 1; -- Re-enable foreign key checks
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;