-- Clean starter database for the Film Reviews application.
-- Import this file into MySQL or MariaDB, then configure includes/config.php to use `films`.

CREATE DATABASE IF NOT EXISTS `films` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `films`;

CREATE TABLE IF NOT EXISTS `reviewer` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) DEFAULT NULL,
  `resetToken` VARCHAR(64) DEFAULT NULL,
  `resetTokenExpiry` DATETIME DEFAULT NULL,
  `isBanned` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviewer_email_unique` (`email`),
  KEY `reviewer_reset_token` (`resetToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `film` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `review` TEXT NOT NULL,
  `reviewer_id` INT NOT NULL,
  `reviewdate` DATE DEFAULT NULL,
  `rating` TINYINT DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `film_reviewer_id` (`reviewer_id`),
  CONSTRAINT `film_reviewer_fk` FOREIGN KEY (`reviewer_id`) REFERENCES `reviewer` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fictional starter data only. No usable credentials, password hashes, or reset tokens are included.
INSERT INTO `reviewer` (`id`, `name`, `email`, `password`, `resetToken`, `resetTokenExpiry`, `isBanned`)
VALUES (1, 'Demo Reviewer', 'demo@example.test', NULL, NULL, NULL, 0)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

INSERT INTO `film` (`id`, `title`, `review`, `reviewer_id`, `reviewdate`, `rating`)
VALUES (1, 'Example Feature', 'A fictional starter review for a clean local installation.', 1, '2026-01-01', 4)
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`), `review` = VALUES(`review`), `reviewdate` = VALUES(`reviewdate`), `rating` = VALUES(`rating`);
