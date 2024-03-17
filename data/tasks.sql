CREATE DATABASE IF NOT EXISTS `default` DEFAULT CHARACTER SET utf8mb4;

USE `default`;

CREATE TABLE IF NOT EXISTS `task_statuses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;

INSERT INTO `task_statuses` (`title`) VALUES
    ('new'),
    ('pending'),
    ('completed'),
    ('failed');

CREATE TABLE IF NOT EXISTS `tasks` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) DEFAULT NULL,
    `message` VARCHAR(255) DEFAULT NULL,
    `status_id` INT UNSIGNED NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`status_id`) REFERENCES `task_statuses`(`id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8mb4;

INSERT INTO `tasks` (`title`, `message`, `status_id`) VALUES ('test', '{script: test.php}', 1);
