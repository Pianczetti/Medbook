CREATE TABLE IF NOT EXISTS `PREFIX_medbook_price_rule` (
    `id_price_rule` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `date_from` DATE DEFAULT NULL,
    `date_to` DATE DEFAULT NULL,
    `day_of_week` TINYINT(1) UNSIGNED DEFAULT NULL,
    `time_from` TIME DEFAULT NULL,
    `time_to` TIME DEFAULT NULL,
    `modifier_type` ENUM('percent','fixed') NOT NULL,
    `modifier_value` DECIMAL(10,2) NOT NULL,
    `priority` INT(11) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_price_rule`),
    INDEX `idx_medbook_price_rule_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_deposit_rule` (
    `id_deposit_rule` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED DEFAULT NULL,
    `deposit_type` ENUM('percent','fixed') NOT NULL,
    `deposit_value` DECIMAL(10,2) NOT NULL,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_deposit_rule`),
    INDEX `idx_medbook_deposit_rule_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_addon` (
    `id_addon` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED DEFAULT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `is_active` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    `position` INT(11) UNSIGNED NOT NULL DEFAULT 0,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_addon`),
    INDEX `idx_medbook_addon_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_addon_lang` (
    `id_addon` INT(11) UNSIGNED NOT NULL,
    `id_lang` INT(11) UNSIGNED NOT NULL,
    `name` VARCHAR(128) NOT NULL,
    `description` TEXT DEFAULT NULL,
    PRIMARY KEY (`id_addon`, `id_lang`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_booking_addon` (
    `id_booking` INT(11) UNSIGNED NOT NULL,
    `id_addon` INT(11) UNSIGNED NOT NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_booking`, `id_addon`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_refund_rule` (
    `id_refund_rule` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED DEFAULT NULL,
    `hours_before` INT(11) UNSIGNED NOT NULL,
    `refund_percent` DECIMAL(5,2) NOT NULL,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_refund_rule`),
    INDEX `idx_medbook_refund_rule_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_cart_data` (
    `id_cart_data` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_cart` INT(11) UNSIGNED NOT NULL,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `booking_date` DATE NOT NULL,
    `time_start` TIME NOT NULL,
    `time_end` TIME NOT NULL,
    `addons_json` TEXT DEFAULT NULL,
    `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `deposit_amount` DECIMAL(10,2) NOT NULL DEFAULT 0,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_cart_data`),
    INDEX `idx_medbook_cart_data_cart` (`id_cart`),
    INDEX `idx_medbook_cart_data_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

ALTER TABLE `PREFIX_medbook_resource` ADD `base_price` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `duration_minutes`;
ALTER TABLE `PREFIX_medbook_resource` ADD `min_duration_minutes` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `base_price`;
ALTER TABLE `PREFIX_medbook_resource` ADD `max_duration_minutes` INT(11) UNSIGNED NOT NULL DEFAULT 0 AFTER `min_duration_minutes`;

ALTER TABLE `PREFIX_medbook_booking` ADD `id_order` INT(11) UNSIGNED DEFAULT NULL AFTER `id_customer`;
ALTER TABLE `PREFIX_medbook_booking` ADD `total_price` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `notes`;
ALTER TABLE `PREFIX_medbook_booking` ADD `deposit_paid` DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER `total_price`;
