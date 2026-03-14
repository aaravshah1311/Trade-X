-- 1. CLEANUP: Remove all existing tables to start fresh
DROP TABLE IF EXISTS `responses`;
DROP TABLE IF EXISTS `team_portfolio`;
DROP TABLE IF EXISTS `portfolio_categories`;
DROP TABLE IF EXISTS `questions`;
DROP TABLE IF EXISTS `quiz_status`;
DROP TABLE IF EXISTS `teams`;

-- 2. CREATE TEAMS: Pre-configured for 10 teams
CREATE TABLE `teams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. CREATE STATUS: Tracks if the game is Start/Stop
CREATE TABLE `quiz_status` (
  `id` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. CREATE QUESTIONS: Supports up to 4 options (can be NULL)
CREATE TABLE `questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` char(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. CREATE CATEGORIES: The 7 specific investment sectors
CREATE TABLE `portfolio_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. CREATE PORTFOLIO: Tracks team share quantities
CREATE TABLE `team_portfolio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`team_id`) REFERENCES `teams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. CREATE RESPONSES: Tracks quiz answers and correctness
CREATE TABLE `responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `team_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `submitted_answer` char(1) NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`team_id`) REFERENCES `teams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. INITIAL DATA: Populate Status, Teams, and Categories
INSERT INTO `quiz_status` (`id`, `is_active`) VALUES (1, 0);

INSERT INTO `teams` (`team_name`, `password`) VALUES 
('Team 1', 'pass1'), ('Team 2', 'pass2'), ('Team 3', 'pass3'), ('Team 4', 'pass4'), ('Team 5', 'pass5'),
('Team 6', 'pass6'), ('Team 7', 'pass7'), ('Team 8', 'pass8'), ('Team 9', 'pass9'), ('Team 10', 'pass10');

INSERT INTO `portfolio_categories` (`category_name`) VALUES 
('NeuroMind AI'), 
('Syntax Systems'), 
('GlobalPulse'), 
('LogicSphere'), 
('MythoLogic Corp'), 
('SportsCorp'), 
('CinePlix');