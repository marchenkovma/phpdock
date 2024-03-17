CREATE DATABASE IF NOT EXISTS `default`;

USE `default`;

CREATE TABLE IF NOT EXISTS `tasks` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `message` VARCHAR(255) COLLATE utf8_unicode_ci NOT NULL,
    `status` ENUM('pending', 'completed', 'failed', 'new') DEFAULT 'new',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;