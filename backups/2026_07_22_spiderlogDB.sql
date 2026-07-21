-- Adminer 5.5.0 MySQL 8.0.46 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260721204353',	'2026-07-21 20:44:38',	2183),
('DoctrineMigrations\\Version20260721205537',	'2026-07-21 20:56:07',	153),
('DoctrineMigrations\\Version20260721213610',	'2026-07-21 21:37:28',	85);

DROP TABLE IF EXISTS `feeding_schedule`;
CREATE TABLE `feeding_schedule` (
  `id` int NOT NULL AUTO_INCREMENT,
  `interval_days` int NOT NULL,
  `species_id` int NOT NULL,
  `life_stage_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_2775B149B2A1D860` (`species_id`),
  KEY `IDX_2775B1494B3860AC` (`life_stage_id`),
  CONSTRAINT `FK_2775B1494B3860AC` FOREIGN KEY (`life_stage_id`) REFERENCES `life_stage` (`id`),
  CONSTRAINT `FK_2775B149B2A1D860` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `feeding_schedule` (`id`, `interval_days`, `species_id`, `life_stage_id`) VALUES
(1,	7,	1,	11),
(2,	7,	1,	12),
(3,	7,	1,	13),
(4,	7,	1,	4),
(5,	7,	2,	1),
(6,	7,	2,	2),
(7,	9,	2,	3),
(8,	15,	2,	4),
(9,	3,	3,	1),
(10,	7,	3,	2),
(11,	9,	3,	3),
(12,	15,	3,	4);

DROP TABLE IF EXISTS `feedings`;
CREATE TABLE `feedings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `quantity` int NOT NULL,
  `spider_id` int NOT NULL,
  `food_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7974D9367D8B25C` (`spider_id`),
  KEY `IDX_7974D936BA8E87C4` (`food_id`),
  CONSTRAINT `FK_7974D9367D8B25C` FOREIGN KEY (`spider_id`) REFERENCES `spider` (`id`),
  CONSTRAINT `FK_7974D936BA8E87C4` FOREIGN KEY (`food_id`) REFERENCES `food` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `feedings` (`id`, `date`, `quantity`, `spider_id`, `food_id`) VALUES
(1,	'2026-04-28',	2,	1,	7),
(2,	'2026-04-29',	1,	2,	1),
(3,	'2026-04-29',	1,	3,	1),
(4,	'2026-05-03',	1,	2,	1),
(5,	'2026-05-06',	1,	2,	1),
(6,	'2026-05-06',	1,	1,	5),
(7,	'2026-05-10',	1,	2,	1),
(8,	'2026-05-11',	1,	3,	1),
(9,	'2026-05-14',	1,	2,	1),
(10,	'2026-05-16',	1,	2,	1),
(11,	'2026-05-20',	1,	1,	5),
(12,	'2026-05-21',	1,	2,	1),
(13,	'2026-05-27',	1,	2,	6),
(14,	'2026-05-27',	1,	1,	7),
(15,	'2026-05-29',	1,	3,	1),
(16,	'2026-06-03',	1,	2,	1),
(17,	'2026-06-06',	1,	3,	1),
(18,	'2026-06-08',	1,	2,	2),
(19,	'2026-06-13',	1,	3,	1),
(20,	'2026-06-13',	1,	1,	7),
(21,	'2026-06-14',	1,	2,	7),
(22,	'2026-06-18',	1,	1,	7),
(23,	'2026-06-18',	1,	2,	7),
(24,	'2026-06-23',	1,	2,	2),
(25,	'2026-06-23',	1,	1,	1),
(26,	'2026-06-30',	1,	1,	7),
(27,	'2026-07-08',	1,	1,	7),
(28,	'2026-07-15',	1,	1,	7),
(29,	'2026-07-15',	1,	2,	7),
(30,	'2026-07-16',	1,	2,	2),
(31,	'2026-07-21',	1,	3,	2);

DROP TABLE IF EXISTS `food`;
CREATE TABLE `food` (
  `id` int NOT NULL AUTO_INCREMENT,
  `food_species_id` int NOT NULL,
  `food_size_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D43829F7D5E468C1` (`food_species_id`),
  KEY `IDX_D43829F7612741F` (`food_size_id`),
  CONSTRAINT `FK_D43829F7612741F` FOREIGN KEY (`food_size_id`) REFERENCES `food_size` (`id`),
  CONSTRAINT `FK_D43829F7D5E468C1` FOREIGN KEY (`food_species_id`) REFERENCES `food_species` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `food` (`id`, `food_species_id`, `food_size_id`) VALUES
(1,	1,	1),
(2,	1,	2),
(3,	1,	3),
(4,	1,	4),
(5,	2,	1),
(6,	3,	5),
(7,	4,	5),
(8,	5,	1);

DROP TABLE IF EXISTS `food_size`;
CREATE TABLE `food_size` (
  `id` int NOT NULL AUTO_INCREMENT,
  `size` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `food_size` (`id`, `size`) VALUES
(1,	'S'),
(2,	'M'),
(3,	'L'),
(4,	'XL'),
(5,	'Standard');

DROP TABLE IF EXISTS `food_species`;
CREATE TABLE `food_species` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `food_species` (`id`, `name`) VALUES
(1,	'Heimchen'),
(2,	'Wüstenheuschrecke'),
(3,	'Pinky Made'),
(4,	'Goldfliege'),
(5,	'Dubia Schabe');

DROP TABLE IF EXISTS `life_stage`;
CREATE TABLE `life_stage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `life_stage` (`id`, `name`) VALUES
(1,	'Sling / Spiderling'),
(2,	'Juvenile'),
(3,	'Sub-Adult'),
(4,	'Adult'),
(5,	'FH1'),
(6,	'FH2'),
(7,	'FH3'),
(8,	'FH4'),
(9,	'FH5'),
(10,	'FH6'),
(11,	'FH7'),
(12,	'FH8'),
(13,	'FH9'),
(14,	'FH10');

DROP TABLE IF EXISTS `molt`;
CREATE TABLE `molt` (
  `id` int NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `leg_span_cm` double DEFAULT NULL,
  `notes` longtext,
  `spider_id` int NOT NULL,
  `life_stage_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_35A3ED517D8B25C` (`spider_id`),
  KEY `IDX_35A3ED514B3860AC` (`life_stage_id`),
  CONSTRAINT `FK_35A3ED514B3860AC` FOREIGN KEY (`life_stage_id`) REFERENCES `life_stage` (`id`),
  CONSTRAINT `FK_35A3ED517D8B25C` FOREIGN KEY (`spider_id`) REFERENCES `spider` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `molt` (`id`, `date`, `leg_span_cm`, `notes`, `spider_id`, `life_stage_id`) VALUES
(1,	'2026-04-16',	NULL,	NULL,	2,	12),
(2,	'2026-07-10',	NULL,	'Häutung vom 29.06.2026 - 10.07.2026',	2,	13),
(3,	'2026-07-14',	NULL,	'13./14.07.2026\r\n\r\nBeinspannweite konnte aufgrund der zusammengezogenen Haut nicht ermittelt werden.',	3,	1);

DROP TABLE IF EXISTS `species`;
CREATE TABLE `species` (
  `id` int NOT NULL AUTO_INCREMENT,
  `latin_name` varchar(255) NOT NULL,
  `common_name` varchar(255) DEFAULT NULL,
  `habitat` varchar(20) NOT NULL,
  `temp_min` double NOT NULL,
  `temp_max` double NOT NULL,
  `humidity_min` double NOT NULL,
  `humidity_max` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `species` (`id`, `latin_name`, `common_name`, `habitat`, `temp_min`, `temp_max`, `humidity_min`, `humidity_max`) VALUES
(1,	'Phidippus regius',	'Regal jumping spider',	'terrestrial',	24,	28,	55,	65),
(2,	'Grammostola pulchra',	'Brazilian Black Tarantula',	'terrestrial',	22,	26,	60,	70),
(3,	'Grammostola pulchripes',	'Chaco Golden Knee Tarantula',	'terrestrial',	20,	24,	60,	70);

DROP TABLE IF EXISTS `spider`;
CREATE TABLE `spider` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `picture_link` varchar(255) DEFAULT NULL,
  `coloration` varchar(255) NOT NULL,
  `date_aquired` date NOT NULL,
  `date_of_death` date DEFAULT NULL,
  `archived_at` date DEFAULT NULL,
  `species_id` int NOT NULL,
  `initial_life_stage_id` int NOT NULL,
  `locality` varchar(255) DEFAULT NULL,
  `sex` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6AD76A84B2A1D860` (`species_id`),
  KEY `IDX_6AD76A846E94A218` (`initial_life_stage_id`),
  CONSTRAINT `FK_6AD76A846E94A218` FOREIGN KEY (`initial_life_stage_id`) REFERENCES `life_stage` (`id`),
  CONSTRAINT `FK_6AD76A84B2A1D860` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `spider` (`id`, `name`, `picture_link`, `coloration`, `date_aquired`, `date_of_death`, `archived_at`, `species_id`, `initial_life_stage_id`, `locality`, `sex`) VALUES
(1,	'Alpollo',	NULL,	'schwarz-weiß, Ghostface auf dem Abdomen, blau-grün-metallene Cheliceren',	'2026-03-14',	NULL,	NULL,	1,	4,	'Florida',	'male'),
(2,	'Alberta',	NULL,	'grau-schwarz mit einem Hauch rosa',	'2026-03-31',	NULL,	NULL,	1,	11,	'Nord-Florida',	'female'),
(3,	'Noctis',	NULL,	'sling: braun mit schwarzem Abdomen, adult: komplett samt-schwarz',	'2026-04-15',	NULL,	NULL,	2,	1,	'Brasilien / Uruguay ',	NULL);

-- 2026-07-21 22:16:50 UTC
