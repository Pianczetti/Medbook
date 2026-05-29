CREATE TABLE IF NOT EXISTS `PREFIX_medbook_confirm_token` (
    `id_token` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_booking` INT(11) UNSIGNED NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `action` VARCHAR(16) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME DEFAULT NULL,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_token`),
    UNIQUE KEY `uniq_medbook_confirm_token` (`token`),
    INDEX `idx_medbook_confirm_token_booking` (`id_booking`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_reminder` (
    `id_reminder` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_booking` INT(11) UNSIGNED NOT NULL,
    `reminder_type` VARCHAR(32) NOT NULL DEFAULT 'email',
    `sent_at` DATETIME NOT NULL,
    `status` VARCHAR(16) NOT NULL DEFAULT 'sent',
    PRIMARY KEY (`id_reminder`),
    INDEX `idx_medbook_reminder_booking` (`id_booking`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_recurrence_config` (
    `id_recurrence_config` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `interval_days` INT(11) NOT NULL DEFAULT 30,
    `tolerance_days` INT(11) NOT NULL DEFAULT 3,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_recurrence_config`),
    INDEX `idx_medbook_recurrence_config_resource` (`id_resource`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_suggested_visit` (
    `id_suggested_visit` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_booking` INT(11) UNSIGNED NOT NULL,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `id_customer` INT(11) UNSIGNED NOT NULL,
    `suggested_date_from` DATE NOT NULL,
    `suggested_date_to` DATE NOT NULL,
    `status` VARCHAR(16) NOT NULL DEFAULT 'pending',
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_suggested_visit`),
    INDEX `idx_medbook_suggested_visit_booking` (`id_booking`),
    INDEX `idx_medbook_suggested_visit_customer` (`id_customer`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `PREFIX_medbook_waitlist` (
    `id_waitlist` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_resource` INT(11) UNSIGNED NOT NULL,
    `id_customer` INT(11) UNSIGNED DEFAULT NULL,
    `customer_name` VARCHAR(128) NOT NULL,
    `customer_email` VARCHAR(255) NOT NULL,
    `customer_phone` VARCHAR(32) DEFAULT NULL,
    `preferred_dates_json` TEXT NOT NULL,
    `preferred_times_json` TEXT DEFAULT NULL,
    `is_priority` TINYINT(1) NOT NULL DEFAULT 0,
    `status` VARCHAR(16) NOT NULL DEFAULT 'active',
    `notified_at` DATETIME DEFAULT NULL,
    `date_add` DATETIME NOT NULL,
    PRIMARY KEY (`id_waitlist`),
    INDEX `idx_medbook_waitlist_resource` (`id_resource`),
    INDEX `idx_medbook_waitlist_status` (`status`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8mb4;

ALTER TABLE `PREFIX_medbook_booking` ADD COLUMN `no_show` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;

ALTER TABLE `PREFIX_medbook_booking` ADD COLUMN `admin_notes` TEXT DEFAULT NULL AFTER `notes`;

ALTER TABLE `PREFIX_medbook_booking` ADD COLUMN `confirmed_at` DATETIME DEFAULT NULL AFTER `date_upd`;
